<?php

namespace App\Http\Controllers;

use App\Models\HostelImprestWallet;
use App\Models\HostelExpense;
use App\Models\HostelExpenseCategory;
use App\Models\Admission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

/**
 * HostelImprestWalletController
 * 
 * Manages hostel student imprest wallet operations
 */
class HostelImprestWalletController extends Controller
{
    /**
     * Display a listing of wallets.
     */
    public function index(Request $request)
    {
        $query = HostelImprestWallet::query()->with('student');

        // Filter by active status
        if ($request->filled('is_active')) {
            $query->where('is_active', $request->is_active);
        }

        // Filter by low balance
        if ($request->filled('low_balance')) {
            $query->lowBalance($request->low_balance);
        }

        // Search by student name or registration number
        if ($request->filled('search')) {
            $query->whereHas('student', function ($q) use ($request) {
                $q->where('student_name', 'like', '%' . $request->search . '%')
                    ->orWhere('reg_no', 'like', '%' . $request->search . '%');
            });
        }

        $wallets = $query->ordered()->paginate(20);

        return view('hostel.wallets.index', compact('wallets'));
    }

    /**
     * Show the form for creating a new wallet.
     */
    public function create()
    {
        // Get students who don't have wallets
        $students = Admission::where('is_active', true)
            ->whereDoesntHave('hostelWallet')
            ->orderBy('student_name')
            ->get();

        return view('hostel.wallets.create', compact('students'));
    }

    /**
     * Store a newly created wallet in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'student_id' => 'required|exists:admissions,id|unique:hostel_imprest_wallets,student_id',
            'opening_balance' => 'required|numeric|min:0',
            'wallet_opened_date' => 'required|date',
        ]);

        try {
            DB::beginTransaction();

            $validated['current_balance'] = $validated['opening_balance'];
            $validated['total_credited'] = $validated['opening_balance'];
            $validated['total_debited'] = 0;
            $validated['is_active'] = true;
            $validated['created_by'] = Auth::id();

            $wallet = HostelImprestWallet::create($validated);

            DB::commit();

            return redirect()->route('hostel.wallets.show', $wallet)
                ->with('success', 'Wallet created successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Error creating wallet: ' . $e->getMessage()])
                ->withInput();
        }
    }

    /**
     * Display the specified wallet.
     */
    public function show(HostelImprestWallet $wallet)
    {
        $wallet->load(['student', 'expenses.category', 'expenses.submitter']);

        // Recent transactions
        $recentExpenses = $wallet->expenses()
            ->with(['category', 'submitter', 'approver'])
            ->ordered()
            ->limit(10)
            ->get();

        return view('hostel.wallets.show', compact('wallet', 'recentExpenses'));
    }

    /**
     * Show the form for crediting a wallet.
     */
    public function creditForm(HostelImprestWallet $wallet)
    {
        $wallet->load('student');
        return view('hostel.wallets.credit', compact('wallet'));
    }

    /**
     * Credit amount to wallet.
     */
    public function credit(Request $request, HostelImprestWallet $wallet)
    {
        $validated = $request->validate([
            'amount' => 'required|numeric|min:1',
            'remarks' => 'nullable|string',
        ]);

        try {
            DB::beginTransaction();

            $wallet->credit($validated['amount']);
            $wallet->updated_by = Auth::id();
            $wallet->save();

            DB::commit();

            return redirect()->route('hostel.wallets.show', $wallet)
                ->with('success', 'Amount credited successfully! New balance: ' . number_format($wallet->current_balance, 2));

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Error crediting wallet: ' . $e->getMessage()])
                ->withInput();
        }
    }

    /**
     * Display wallet statement.
     */
    public function statement(Request $request, HostelImprestWallet $wallet)
    {
        $wallet->load('student');

        $query = $wallet->expenses()->with(['category', 'submitter', 'approver']);

        // Filter by date range
        if ($request->filled('from_date') && $request->filled('to_date')) {
            $query->dateRange($request->from_date, $request->to_date);
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->byStatus($request->status);
        }

        $expenses = $query->ordered()->paginate(20);

        return view('hostel.wallets.statement', compact('wallet', 'expenses'));
    }

    /**
     * Toggle wallet active status.
     */
    public function toggleActive(HostelImprestWallet $wallet)
    {
        $wallet->update([
            'is_active' => !$wallet->is_active,
            'updated_by' => Auth::id(),
        ]);

        $status = $wallet->is_active ? 'activated' : 'deactivated';

        return redirect()->route('hostel.wallets.show', $wallet)
            ->with('success', "Wallet {$status} successfully!");
    }
}
