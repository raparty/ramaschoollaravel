<?php

namespace App\Http\Controllers;

use App\Models\Admission;
use App\Models\StudentFee;
use App\Models\FeeTerm;
use App\Models\ClassModel;
use App\Http\Requests\CollectFeeRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PDF; // For PDF generation (requires barryvdh/laravel-dompdf)

/**
 * FeeController
 * 
 * Handles fee collection, receipts, and reports
 * Converts: add_student_fees.php, fees_reciept.php, student_pending_fees_detail.php
 */
class FeeController extends Controller
{
    /**
     * Show student search form for fee collection.
     * Converts: fees_searchby_name.php
     */
    public function search()
    {
        // Check permission
        $this->authorize('create-fees');

        return view('fees.search');
    }

    /**
     * Show fee collection form for a student.
     * Converts: add_student_fees.php (form)
     */
    public function collect(Request $request)
    {
        // Check permission
        $this->authorize('create-fees');

        $regNo = $request->get('registration_no');
        
        if (empty($regNo)) {
            return redirect()
                ->route('fees.search')
                ->with('error', 'Please select a student first.');
        }

        // Get student details
        $student = Admission::with('class')
            ->where('reg_no', $regNo)
            ->firstOrFail();

        // Calculate pending balance
        $totalPaid = StudentFee::forStudent($regNo)->sum('fees_amount');
        $totalPackage = (float)($student->admission_fee ?? 0);
        $pendingBalance = $totalPackage - $totalPaid;

        // Get fee terms
        $terms = FeeTerm::ordered()->get();

        // Get payment history
        $paymentHistory = StudentFee::forStudent($regNo)
            ->with('term')
            ->recent()
            ->limit(10)
            ->get();

        return view('fees.collect', compact('student', 'pendingBalance', 'terms', 'paymentHistory'));
    }

    /**
     * Process fee payment.
     * Converts: add_student_fees.php (processing)
     */
    public function store(CollectFeeRequest $request)
    {
        // Check permission
        $this->authorize('create-fees');

        DB::beginTransaction();
        
        try {
            $validated = $request->validated();

            // Generate unique receipt number
            $validated['receipt_no'] = StudentFee::generateReceiptNo();
            $validated['payment_date'] = now();
            $validated['session'] = session('academic_session', date('Y'));

            // Create fee record
            $fee = StudentFee::create($validated);

            DB::commit();

            return redirect()
                ->route('fees.receipt', ['receiptNo' => $fee->receipt_no])
                ->with('success', 'Fee payment recorded successfully! Receipt No: ' . $fee->receipt_no);

        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Failed to process fee payment: ' . $e->getMessage());
        }
    }

    /**
     * Display fee receipt.
     * Converts: fees_reciept.php
     */
    public function receipt(Request $request)
    {
        // Check permission
        $this->authorize('view-fees');

        $receiptNo = $request->get('receiptNo');
        
        if (empty($receiptNo)) {
            return redirect()
                ->route('fees.search')
                ->with('error', 'Receipt number is required.');
        }

        // Get fee record
        $fee = StudentFee::with(['student.class', 'term'])
            ->where('receipt_no', $receiptNo)
            ->firstOrFail();

        // Calculate totals for student
        $totalPaid = StudentFee::forStudent($fee->registration_no)->sum('fees_amount');
        $totalPackage = (float)($fee->student->admission_fee ?? 0);
        $pendingBalance = $totalPackage - $totalPaid;

        return view('fees.receipt', compact('fee', 'totalPaid', 'pendingBalance'));
    }

    /**
     * Generate PDF receipt.
     * Converts: fees_reciept.php (PDF download)
     */
    public function generatePDF(Request $request)
    {
        // Check permission
        $this->authorize('view-fees');

        $receiptNo = $request->get('receiptNo');
        
        $fee = StudentFee::with(['student.class', 'term'])
            ->where('receipt_no', $receiptNo)
            ->firstOrFail();

        // Calculate totals
        $totalPaid = StudentFee::forStudent($fee->registration_no)->sum('fees_amount');
        $totalPackage = (float)($fee->student->admission_fee ?? 0);
        $pendingBalance = $totalPackage - $totalPaid;

        // Generate PDF
        $pdf = PDF::loadView('fees.receipt-pdf', compact('fee', 'totalPaid', 'pendingBalance'));
        
        return $pdf->download('receipt-' . $receiptNo . '.pdf');
    }

    /**
     * Display pending fees report.
     * Converts: student_pending_fees_detail.php
     */
    public function pending(Request $request)
    {
        // Check permission
        $this->authorize('view-fees');

        $query = Admission::with('class');

        // Filter by class
        if ($request->filled('class_id')) {
            $query->where('class_id', $request->class_id);
        }

        // Filter by term
        $termId = $request->get('fees_term');

        // Get students
        $students = $query->get();

        // Calculate pending fees for each student
        $pendingData = [];
        foreach ($students as $student) {
            $totalPaid = StudentFee::forStudent($student->reg_no)->sum('fees_amount');
            $totalPackage = (float)($student->admission_fee ?? 0);
            $pending = $totalPackage - $totalPaid;

            if ($pending > 0) {
                $pendingData[] = [
                    'student' => $student,
                    'total_package' => $totalPackage,
                    'total_paid' => $totalPaid,
                    'pending' => $pending,
                ];
            }
        }

        // Get classes for filter
        $classes = ClassModel::ordered()->get();
        $terms = FeeTerm::ordered()->get();

        return view('fees.pending', compact('pendingData', 'classes', 'terms'));
    }

    /**
     * Display fee payment history for a student.
     */
    public function history(Request $request)
    {
        // Check permission
        $this->authorize('view-fees');

        $regNo = $request->get('registration_no');
        
        if (empty($regNo)) {
            return redirect()
                ->route('fees.search')
                ->with('error', 'Please select a student first.');
        }

        $student = Admission::with('class')
            ->where('reg_no', $regNo)
            ->firstOrFail();

        // Get payment history
        $payments = StudentFee::forStudent($regNo)
            ->with('term')
            ->recent()
            ->paginate(20);

        // Calculate totals
        $totalPaid = StudentFee::forStudent($regNo)->sum('fees_amount');
        $totalPackage = (float)($student->admission_fee ?? 0);
        $pendingBalance = $totalPackage - $totalPaid;

        return view('fees.history', compact('student', 'payments', 'totalPaid', 'totalPackage', 'pendingBalance'));
    }

    /**
     * AJAX search for students.
     * Converts: fees_searchby_name.php (AJAX)
     */
    public function searchStudents(Request $request)
    {
        $search = $request->get('q', '');
        
        $students = Admission::with('class')
            ->where(function($query) use ($search) {
                $query->where('student_name', 'LIKE', "%{$search}%")
                      ->orWhere('reg_no', 'LIKE', "%{$search}%");
            })
            ->limit(10)
            ->get(['id', 'reg_no', 'student_name', 'class_id']);

        return response()->json($students);
    }
}
