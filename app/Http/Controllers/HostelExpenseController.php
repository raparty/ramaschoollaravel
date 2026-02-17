<?php

namespace App\Http\Controllers;

use App\Models\HostelExpense;
use App\Models\HostelExpenseCategory;
use App\Models\HostelImprestWallet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

/**
 * HostelExpenseController
 * 
 * Manages hostel expense operations with approval workflow
 */
class HostelExpenseController extends Controller
{
    /**
     * Display a listing of expenses.
     */
    public function index(Request $request)
    {
        $query = HostelExpense::query()
            ->with(['wallet.student', 'category', 'submitter', 'approver']);

        // Filter by status
        if ($request->filled('status')) {
            $query->byStatus($request->status);
        }

        // Filter by category
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        // Filter by date range
        if ($request->filled('from_date') && $request->filled('to_date')) {
            $query->dateRange($request->from_date, $request->to_date);
        }

        // Search by student name or bill number
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('bill_number', 'like', '%' . $request->search . '%')
                    ->orWhereHas('wallet.student', function ($sq) use ($request) {
                        $sq->where('student_name', 'like', '%' . $request->search . '%');
                    });
            });
        }

        $expenses = $query->ordered()->paginate(20);
        $categories = HostelExpenseCategory::active()->ordered()->get();

        return view('hostel.expenses.index', compact('expenses', 'categories'));
    }

    /**
     * Show the form for creating a new expense.
     */
    public function create(Request $request)
    {
        $wallets = HostelImprestWallet::active()
            ->with('student')
            ->get();
        
        $categories = HostelExpenseCategory::active()->ordered()->get();
        $walletId = $request->get('wallet_id');

        return view('hostel.expenses.create', compact('wallets', 'categories', 'walletId'));
    }

    /**
     * Store a newly created expense in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'wallet_id' => 'required|exists:hostel_imprest_wallets,id',
            'category_id' => 'required|exists:hostel_expense_categories,id',
            'amount' => 'required|numeric|min:0.01',
            'expense_date' => 'required|date',
            'description' => 'required|string',
            'bill_number' => 'nullable|string|max:50',
            'submitted_by' => 'nullable|exists:hostel_wardens,id',
        ]);

        try {
            DB::beginTransaction();

            // Get wallet
            $wallet = HostelImprestWallet::find($validated['wallet_id']);

            // Check if wallet has sufficient balance
            if (!$wallet->hasSufficientBalance($validated['amount'])) {
                return back()->withErrors([
                    'amount' => 'Insufficient balance in wallet. Current balance: ' . number_format($wallet->current_balance, 2)
                ])->withInput();
            }

            // Check if category requires approval
            $category = HostelExpenseCategory::find($validated['category_id']);
            $validated['status'] = $category->requires_approval ? 'Pending' : 'Approved';
            
            // If auto-approved, set approval details
            if ($validated['status'] === 'Approved') {
                $validated['approved_by'] = Auth::id();
                $validated['approved_at'] = now();
                
                // Debit from wallet
                $wallet->debit($validated['amount']);
            }

            $validated['created_by'] = Auth::id();

            $expense = HostelExpense::create($validated);

            DB::commit();

            $message = $validated['status'] === 'Approved' 
                ? 'Expense recorded and deducted from wallet successfully!' 
                : 'Expense submitted for approval!';

            return redirect()->route('hostel.expenses.show', $expense)
                ->with('success', $message);

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Error creating expense: ' . $e->getMessage()])
                ->withInput();
        }
    }

    /**
     * Display the specified expense.
     */
    public function show(HostelExpense $expense)
    {
        $expense->load(['wallet.student', 'category', 'submitter', 'approver']);

        return view('hostel.expenses.show', compact('expense'));
    }

    /**
     * Show the form for approving an expense.
     */
    public function approveForm(HostelExpense $expense)
    {
        if ($expense->status !== 'Pending') {
            return redirect()->route('hostel.expenses.show', $expense)
                ->with('error', 'Only pending expenses can be approved');
        }

        $expense->load(['wallet.student', 'category', 'submitter']);

        return view('hostel.expenses.approve', compact('expense'));
    }

    /**
     * Approve an expense.
     */
    public function approve(Request $request, HostelExpense $expense)
    {
        if ($expense->status !== 'Pending') {
            return redirect()->route('hostel.expenses.show', $expense)
                ->with('error', 'Only pending expenses can be approved');
        }

        try {
            DB::beginTransaction();

            // Get wallet
            $wallet = $expense->wallet;

            // Check if wallet has sufficient balance
            if (!$wallet->hasSufficientBalance($expense->amount)) {
                return back()->withErrors([
                    'error' => 'Insufficient balance in wallet. Current balance: ' . number_format($wallet->current_balance, 2)
                ]);
            }

            // Debit from wallet
            $wallet->debit($expense->amount);

            // Update expense status
            $expense->update([
                'status' => 'Approved',
                'approved_by' => Auth::id(),
                'approved_at' => now(),
                'updated_by' => Auth::id(),
            ]);

            DB::commit();

            return redirect()->route('hostel.expenses.show', $expense)
                ->with('success', 'Expense approved and amount deducted from wallet!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Error approving expense: ' . $e->getMessage()]);
        }
    }

    /**
     * Show the form for rejecting an expense.
     */
    public function rejectForm(HostelExpense $expense)
    {
        if ($expense->status !== 'Pending') {
            return redirect()->route('hostel.expenses.show', $expense)
                ->with('error', 'Only pending expenses can be rejected');
        }

        $expense->load(['wallet.student', 'category', 'submitter']);

        return view('hostel.expenses.reject', compact('expense'));
    }

    /**
     * Reject an expense.
     */
    public function reject(Request $request, HostelExpense $expense)
    {
        $validated = $request->validate([
            'rejection_reason' => 'required|string',
        ]);

        if ($expense->status !== 'Pending') {
            return redirect()->route('hostel.expenses.show', $expense)
                ->with('error', 'Only pending expenses can be rejected');
        }

        try {
            $expense->update([
                'status' => 'Rejected',
                'rejection_reason' => $validated['rejection_reason'],
                'updated_by' => Auth::id(),
            ]);

            return redirect()->route('hostel.expenses.show', $expense)
                ->with('success', 'Expense rejected!');

        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Error rejecting expense: ' . $e->getMessage()]);
        }
    }

    /**
     * Display pending expenses for approval.
     */
    public function pendingApprovals(Request $request)
    {
        $expenses = HostelExpense::pending()
            ->with(['wallet.student', 'category', 'submitter'])
            ->ordered()
            ->paginate(20);

        return view('hostel.expenses.pending', compact('expenses'));
    }
}
