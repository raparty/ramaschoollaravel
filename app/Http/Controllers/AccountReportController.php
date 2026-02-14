<?php

namespace App\Http/Controllers;

use App\Models\Income;
use App\Models\Expense;
use App\Models\AccountCategory;
use Illuminate\Http\Request;
use Carbon\Carbon;

class AccountReportController extends Controller
{
    public function index()
    {
        $categories = AccountCategory::active()->orderBy('type')->orderBy('name')->get();
        return view('reports.accounts.index', compact('categories'));
    }

    public function summary(Request $request)
    {
        $startDate = $request->input('start_date', now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->input('end_date', now()->endOfMonth()->format('Y-m-d'));
        
        $totalIncome = Income::forDateRange($startDate, $endDate)->sum('amount');
        $totalExpense = Expense::forDateRange($startDate, $endDate)->sum('amount');
        $netProfit = $totalIncome - $totalExpense;
        
        // Category-wise breakdown
        $incomeByCategory = Income::forDateRange($startDate, $endDate)
            ->with('category')
            ->get()
            ->groupBy('category_id')
            ->map(fn($items) => [
                'category' => $items->first()->category->name,
                'amount' => $items->sum('amount')
            ]);
        
        $expenseByCategory = Expense::forDateRange($startDate, $endDate)
            ->with('category')
            ->get()
            ->groupBy('category_id')
            ->map(fn($items) => [
                'category' => $items->first()->category->name,
                'amount' => $items->sum('amount')
            ]);
        
        return view('reports.accounts.summary', compact(
            'startDate', 'endDate', 'totalIncome', 'totalExpense', 'netProfit',
            'incomeByCategory', 'expenseByCategory'
        ));
    }

    public function details(Request $request)
    {
        $startDate = $request->input('start_date', now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->input('end_date', now()->endOfMonth()->format('Y-m-d'));
        
        $incomes = Income::with('category')
            ->forDateRange($startDate, $endDate)
            ->orderBy('date', 'desc')
            ->get();
        
        $expenses = Expense::with('category')
            ->forDateRange($startDate, $endDate)
            ->orderBy('date', 'desc')
            ->get();
        
        return view('reports.accounts.details', compact('incomes', 'expenses', 'startDate', 'endDate'));
    }

    public function exportCsv(Request $request)
    {
        $startDate = $request->input('start_date', now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->input('end_date', now()->endOfMonth()->format('Y-m-d'));
        
        $incomes = Income::with('category')
            ->forDateRange($startDate, $endDate)
            ->orderBy('date')
            ->get();
        
        $expenses = Expense::with('category')
            ->forDateRange($startDate, $endDate)
            ->orderBy('date')
            ->get();
        
        $filename = "accounts_report_{$startDate}_to_{$endDate}.csv";
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];
        
        $callback = function() use ($incomes, $expenses) {
            $file = fopen('php://output', 'w');
            
            // Income section
            fputcsv($file, ['INCOME']);
            fputcsv($file, ['Date', 'Category', 'Amount', 'Invoice', 'Description', 'Payment Method']);
            foreach ($incomes as $income) {
                fputcsv($file, [
                    $income->date->format('Y-m-d'),
                    $income->category->name,
                    $income->amount,
                    $income->invoice_number,
                    $income->description,
                    $income->payment_method,
                ]);
            }
            fputcsv($file, ['Total Income', '', $incomes->sum('amount')]);
            fputcsv($file, []);
            
            // Expense section
            fputcsv($file, ['EXPENSE']);
            fputcsv($file, ['Date', 'Category', 'Amount', 'Receipt', 'Description', 'Payment Method']);
            foreach ($expenses as $expense) {
                fputcsv($file, [
                    $expense->date->format('Y-m-d'),
                    $expense->category->name,
                    $expense->amount,
                    $expense->receipt_number,
                    $expense->description,
                    $expense->payment_method,
                ]);
            }
            fputcsv($file, ['Total Expense', '', $expenses->sum('amount')]);
            fputcsv($file, []);
            
            // Summary
            fputcsv($file, ['SUMMARY']);
            fputcsv($file, ['Total Income', $incomes->sum('amount')]);
            fputcsv($file, ['Total Expense', $expenses->sum('amount')]);
            fputcsv($file, ['Net Profit/Loss', $incomes->sum('amount') - $expenses->sum('amount')]);
            
            fclose($file);
        };
        
        return response()->stream($callback, 200, $headers);
    }
}
