<?php

namespace App\Http\Controllers;

use App\Models\Staff;
use App\Models\Salary;
use App\Http\Requests\ProcessSalaryRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * SalaryController
 * 
 * Manages staff salary processing, generation, and history
 */
class SalaryController extends Controller
{
    /**
     * Display a listing of salaries.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $query = Salary::with('staff.department');

        // Filter by month/year
        if ($request->filled('month') && $request->filled('year')) {
            $query->forPeriod($request->month, $request->year);
        } else {
            // Default to current month
            $query->forPeriod(now()->month, now()->year);
        }

        // Filter by status
        if ($request->filled('status')) {
            if ($request->status === 'paid') {
                $query->paid();
            } elseif ($request->status === 'pending') {
                $query->pending();
            }
        }

        $salaries = $query->orderBy('created_at', 'desc')->paginate(20);

        // Calculate totals
        $totals = [
            'basic' => $salaries->sum('basic_salary'),
            'allowances' => $salaries->sum('allowances'),
            'deductions' => $salaries->sum('deductions'),
            'net' => $salaries->sum('net_salary'),
        ];

        return view('salaries.index', compact('salaries', 'totals'));
    }

    /**
     * Show the form for processing salary.
     *
     * @return \Illuminate\Http\Response
     */
    public function process()
    {
        $staff = Staff::with('department')->active()->orderBy('name')->get();
        $currentMonth = now()->month;
        $currentYear = now()->year;

        return view('salaries.process', compact('staff', 'currentMonth', 'currentYear'));
    }

    /**
     * Store a newly processed salary.
     *
     * @param \App\Http\Requests\ProcessSalaryRequest $request
     * @return \Illuminate\Http\Response
     */
    public function store(ProcessSalaryRequest $request)
    {
        try {
            DB::beginTransaction();

            $data = $request->validated();

            // Check if salary already exists for this staff, month, and year
            $existingSalary = Salary::where('staff_id', $data['staff_id'])
                ->where('month', $data['month'])
                ->where('year', $data['year'])
                ->first();

            if ($existingSalary) {
                return redirect()
                    ->back()
                    ->withInput()
                    ->with('error', 'Salary for this staff member has already been processed for the selected month/year.');
            }

            // Calculate net salary
            $netSalary = $data['basic_salary'] + $data['allowances'] - $data['deductions'];

            $salary = Salary::create([
                'staff_id' => $data['staff_id'],
                'month' => $data['month'],
                'year' => $data['year'],
                'basic_salary' => $data['basic_salary'],
                'allowances' => $data['allowances'],
                'deductions' => $data['deductions'],
                'net_salary' => $netSalary,
                'status' => 'pending',
                'notes' => $data['notes'] ?? null,
            ]);

            DB::commit();

            return redirect()
                ->route('salaries.index')
                ->with('success', 'Salary processed successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Failed to process salary. Please try again.');
        }
    }

    /**
     * Mark salary as paid.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Salary $salary
     * @return \Illuminate\Http\Response
     */
    public function markAsPaid(Request $request, Salary $salary)
    {
        $request->validate([
            'payment_method' => 'required|string|in:cash,bank_transfer,cheque',
        ]);

        try {
            $salary->markAsPaid($request->payment_method);

            return redirect()
                ->back()
                ->with('success', 'Salary marked as paid successfully!');

        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Failed to mark salary as paid. Please try again.');
        }
    }

    /**
     * Generate salary slip.
     *
     * @param \App\Models\Salary $salary
     * @return \Illuminate\Http\Response
     */
    public function slip(Salary $salary)
    {
        $salary->load('staff.department', 'staff.position');

        return view('salaries.slip', compact('salary'));
    }

    /**
     * Display salary history for a staff member.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Staff $staff
     * @return \Illuminate\Http\Response
     */
    public function history(Request $request, Staff $staff)
    {
        $salaries = $staff->salaries()
            ->orderBy('year', 'desc')
            ->orderBy('month', 'desc')
            ->paginate(12);

        // Calculate totals
        $totals = [
            'basic' => $salaries->sum('basic_salary'),
            'allowances' => $salaries->sum('allowances'),
            'deductions' => $salaries->sum('deductions'),
            'net' => $salaries->sum('net_salary'),
        ];

        return view('salaries.history', compact('staff', 'salaries', 'totals'));
    }

    /**
     * Generate salaries for all staff for a given month.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function generateBulk(Request $request)
    {
        $request->validate([
            'month' => 'required|integer|min:1|max:12',
            'year' => 'required|integer|min:2000|max:' . (date('Y') + 1),
        ]);

        try {
            DB::beginTransaction();

            $month = $request->month;
            $year = $request->year;
            $generated = 0;
            $skipped = 0;

            $activeStaff = Staff::active()->get();

            foreach ($activeStaff as $staffMember) {
                // Check if salary already exists
                $exists = Salary::where('staff_id', $staffMember->id)
                    ->where('month', $month)
                    ->where('year', $year)
                    ->exists();

                if (!$exists) {
                    Salary::create([
                        'staff_id' => $staffMember->id,
                        'month' => $month,
                        'year' => $year,
                        'basic_salary' => $staffMember->basic_salary,
                        'allowances' => 0,
                        'deductions' => 0,
                        'net_salary' => $staffMember->basic_salary,
                        'status' => 'pending',
                    ]);
                    $generated++;
                } else {
                    $skipped++;
                }
            }

            DB::commit();

            $message = "Salary generated successfully! Generated: {$generated}, Skipped (already exists): {$skipped}";

            return redirect()
                ->route('salaries.index', ['month' => $month, 'year' => $year])
                ->with('success', $message);

        } catch (\Exception $e) {
            DB::rollBack();
            
            return redirect()
                ->back()
                ->with('error', 'Failed to generate salaries. Please try again.');
        }
    }
}
