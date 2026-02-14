<?php

namespace App\Http\Controllers;

use App\Models\Income;
use App\Models\AccountCategory;
use App\Http\Requests\StoreIncomeRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class IncomeController extends Controller
{
    public function index(Request $request)
    {
        $query = Income::with('category')->orderBy('date', 'desc');
        
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }
        
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->forDateRange($request->start_date, $request->end_date);
        }
        
        $incomes = $query->paginate(20);
        $categories = AccountCategory::income()->active()->orderBy('name')->get();
        
        // Statistics
        $stats = [
            'total' => Income::sum('amount'),
            'this_month' => Income::forMonth(now()->month, now()->year)->sum('amount'),
            'this_year' => Income::forYear(now()->year)->sum('amount'),
        ];
        
        return view('income.index', compact('incomes', 'categories', 'stats'));
    }

    public function create()
    {
        $categories = AccountCategory::income()->active()->orderBy('name')->get();
        return view('income.create', compact('categories'));
    }

    public function store(StoreIncomeRequest $request)
    {
        try {
            DB::beginTransaction();
            
            Income::create(array_merge(
                $request->validated(),
                ['recorded_by' => Auth::id()]
            ));
            
            DB::commit();
            
            return redirect()->route('income.index')
                ->with('success', 'Income recorded successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()
                ->with('error', 'Failed to record income. Please try again.');
        }
    }

    public function show(Income $income)
    {
        $income->load('category', 'recorder');
        return view('income.show', compact('income'));
    }

    public function edit(Income $income)
    {
        $categories = AccountCategory::income()->active()->orderBy('name')->get();
        return view('income.edit', compact('income', 'categories'));
    }

    public function update(StoreIncomeRequest $request, Income $income)
    {
        try {
            DB::beginTransaction();
            
            $income->update($request->validated());
            
            DB::commit();
            
            return redirect()->route('income.index')
                ->with('success', 'Income updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()
                ->with('error', 'Failed to update income. Please try again.');
        }
    }

    public function destroy(Income $income)
    {
        try {
            $income->delete();
            
            return redirect()->route('income.index')
                ->with('success', 'Income deleted successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to delete income. Please try again.');
        }
    }
}
