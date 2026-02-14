<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\AccountCategory;
use App\Http/Requests/StoreExpenseRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ExpenseController extends Controller
{
    public function index(Request $request)
    {
        $query = Expense::with('category')->orderBy('date', 'desc');
        
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }
        
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->forDateRange($request->start_date, $request->end_date);
        }
        
        $expenses = $query->paginate(20);
        $categories = AccountCategory::expense()->active()->orderBy('name')->get();
        
        // Statistics
        $stats = [
            'total' => Expense::sum('amount'),
            'this_month' => Expense::forMonth(now()->month, now()->year)->sum('amount'),
            'this_year' => Expense::forYear(now()->year)->sum('amount'),
        ];
        
        return view('expenses.index', compact('expenses', 'categories', 'stats'));
    }

    public function create()
    {
        $categories = AccountCategory::expense()->active()->orderBy('name')->get();
        return view('expenses.create', compact('categories'));
    }

    public function store(StoreExpenseRequest $request)
    {
        try {
            DB::beginTransaction();
            
            Expense::create(array_merge(
                $request->validated(),
                ['recorded_by' => Auth::id()]
            ));
            
            DB::commit();
            
            return redirect()->route('expenses.index')
                ->with('success', 'Expense recorded successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()
                ->with('error', 'Failed to record expense. Please try again.');
        }
    }

    public function show(Expense $expense)
    {
        $expense->load('category', 'recorder');
        return view('expenses.show', compact('expense'));
    }

    public function edit(Expense $expense)
    {
        $categories = AccountCategory::expense()->active()->orderBy('name')->get();
        return view('expenses.edit', compact('expense', 'categories'));
    }

    public function update(StoreExpenseRequest $request, Expense $expense)
    {
        try {
            DB::beginTransaction();
            
            $expense->update($request->validated());
            
            DB::commit();
            
            return redirect()->route('expenses.index')
                ->with('success', 'Expense updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()
                ->with('error', 'Failed to update expense. Please try again.');
        }
    }

    public function destroy(Expense $expense)
    {
        try {
            $expense->delete();
            
            return redirect()->route('expenses.index')
                ->with('success', 'Expense deleted successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to delete expense. Please try again.');
        }
    }
}
