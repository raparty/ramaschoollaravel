@extends('layouts.app')

@section('title', 'Marksheet - ' . $student->name)

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4 d-print-none">
    <div>
        <h2>Student Marksheet</h2>
        <p class="text-muted mb-0">{{ $exam->name }} - {{ $student->name }}</p>
    </div>
    <div>
        <button onclick="window.print()" class="btn btn-primary me-2">
            <i class="bi bi-printer"></i> Print
        </button>
        <a href="{{ route('results.index') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Back
        </a>
    </div>
</div>

<div class="card" id="marksheet">
    <div class="card-body p-5">
        <!-- School Header -->
        <div class="text-center mb-4">
            @if(config('app.logo'))
                <img src="{{ asset(config('app.logo')) }}" alt="School Logo" style="height: 80px;" class="mb-2">
            @endif
            <h3><strong>{{ config('app.name', 'School ERP System') }}</strong></h3>
            <p class="mb-1">{{ config('app.address', '') }}</p>
            <p class="mb-0 small">{{ config('app.phone', '') }} | {{ config('app.email', '') }}</p>
        </div>
        
        <div class="text-center mb-4">
            <h4 class="border-bottom border-top py-2"><strong>STUDENT MARKSHEET</strong></h4>
        </div>
        
        <!-- Student and Exam Information -->
        <div class="row mb-4">
            <div class="col-md-8">
                <table class="table table-borderless table-sm">
                    <tr>
                        <td width="180"><strong>Student Name:</strong></td>
                        <td>{{ $student->name }}</td>
                    </tr>
                    <tr>
                        <td><strong>Roll Number:</strong></td>
                        <td>{{ $student->roll_number ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <td><strong>Class:</strong></td>
                        <td>{{ $student->class->name ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <td><strong>Father's Name:</strong></td>
                        <td>{{ $student->father_name ?? 'N/A' }}</td>
                    </tr>
                </table>
            </div>
            <div class="col-md-4 text-end">
                @if($student->photo)
                    <img src="{{ asset('storage/' . $student->photo) }}" alt="{{ $student->name }}" 
                         class="border" style="width: 120px; height: 140px; object-fit: cover;">
                @else
                    <div class="border d-inline-flex align-items-center justify-content-center bg-light" 
                         style="width: 120px; height: 140px; font-size: 48px;">
                        {{ substr($student->name, 0, 1) }}
                    </div>
                @endif
            </div>
        </div>
        
        <div class="row mb-4">
            <div class="col-md-6">
                <table class="table table-borderless table-sm">
                    <tr>
                        <td width="180"><strong>Examination:</strong></td>
                        <td>{{ $exam->name }}</td>
                    </tr>
                    <tr>
                        <td><strong>Academic Year:</strong></td>
                        <td>{{ $exam->academicYear->name }}</td>
                    </tr>
                </table>
            </div>
            <div class="col-md-6">
                <table class="table table-borderless table-sm">
                    <tr>
                        <td width="180"><strong>Exam Period:</strong></td>
                        <td>{{ $exam->start_date->format('d M, Y') }} - {{ $exam->end_date->format('d M, Y') }}</td>
                    </tr>
                    <tr>
                        <td><strong>Date of Issue:</strong></td>
                        <td>{{ now()->format('d M, Y') }}</td>
                    </tr>
                </table>
            </div>
        </div>
        
        <hr>
        
        <!-- Marks Table -->
        <h5 class="mb-3"><strong>Marks Details</strong></h5>
        <div class="table-responsive">
            <table class="table table-bordered">
                <thead class="table-primary">
                    <tr class="text-center">
                        <th width="60">S.No.</th>
                        <th>Subject</th>
                        <th width="120">Max Marks</th>
                        <th width="120">Marks Obtained</th>
                        <th width="100">Grade</th>
                        <th width="100">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $totalMax = 0;
                        $totalObtained = 0;
                        $allPass = true;
                    @endphp
                    
                    @foreach($marks as $index => $mark)
                    @php
                        $totalMax += $mark->total_marks;
                        $totalObtained += $mark->marks_obtained;
                        $percentage = $mark->total_marks > 0 ? ($mark->marks_obtained / $mark->total_marks) * 100 : 0;
                        $isPassing = $mark->marks_obtained >= $exam->passing_marks;
                        if (!$isPassing) $allPass = false;
                        
                        if ($percentage >= 90) {
                            $grade = 'A+';
                            $gradeClass = 'success';
                        } elseif ($percentage >= 80) {
                            $grade = 'A';
                            $gradeClass = 'success';
                        } elseif ($percentage >= 70) {
                            $grade = 'B+';
                            $gradeClass = 'info';
                        } elseif ($percentage >= 60) {
                            $grade = 'B';
                            $gradeClass = 'info';
                        } elseif ($percentage >= 50) {
                            $grade = 'C';
                            $gradeClass = 'warning';
                        } elseif ($percentage >= 40) {
                            $grade = 'D';
                            $gradeClass = 'warning';
                        } else {
                            $grade = 'F';
                            $gradeClass = 'danger';
                        }
                    @endphp
                    <tr>
                        <td class="text-center">{{ $index + 1 }}</td>
                        <td><strong>{{ $mark->subject->name }}</strong></td>
                        <td class="text-center">{{ $mark->total_marks }}</td>
                        <td class="text-center"><strong>{{ $mark->marks_obtained }}</strong></td>
                        <td class="text-center">
                            <span class="badge bg-{{ $gradeClass }}">{{ $grade }}</span>
                        </td>
                        <td class="text-center">
                            @if($isPassing)
                                <span class="badge bg-success">Pass</span>
                            @else
                                <span class="badge bg-danger">Fail</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot class="table-light">
                    <tr>
                        <td colspan="2" class="text-end"><strong>Grand Total:</strong></td>
                        <td class="text-center"><strong>{{ $totalMax }}</strong></td>
                        <td class="text-center"><strong>{{ $totalObtained }}</strong></td>
                        <td colspan="2"></td>
                    </tr>
                </tfoot>
            </table>
        </div>
        
        <!-- Result Summary -->
        @php
            $overallPercentage = $totalMax > 0 ? ($totalObtained / $totalMax) * 100 : 0;
            
            if ($overallPercentage >= 90) {
                $overallGrade = 'A+';
                $overallGradeClass = 'success';
            } elseif ($overallPercentage >= 80) {
                $overallGrade = 'A';
                $overallGradeClass = 'success';
            } elseif ($overallPercentage >= 70) {
                $overallGrade = 'B+';
                $overallGradeClass = 'info';
            } elseif ($overallPercentage >= 60) {
                $overallGrade = 'B';
                $overallGradeClass = 'info';
            } elseif ($overallPercentage >= 50) {
                $overallGrade = 'C';
                $overallGradeClass = 'warning';
            } elseif ($overallPercentage >= 40) {
                $overallGrade = 'D';
                $overallGradeClass = 'warning';
            } else {
                $overallGrade = 'F';
                $overallGradeClass = 'danger';
            }
            
            $finalResult = $allPass ? 'PASS' : 'FAIL';
            $resultClass = $allPass ? 'success' : 'danger';
        @endphp
        
        <div class="row mb-4">
            <div class="col-md-6">
                <div class="card border-{{ $resultClass }}">
                    <div class="card-body">
                        <h6 class="mb-3">Performance Summary</h6>
                        <table class="table table-borderless table-sm mb-0">
                            <tr>
                                <td width="180"><strong>Total Marks:</strong></td>
                                <td>{{ $totalMax }}</td>
                            </tr>
                            <tr>
                                <td><strong>Marks Obtained:</strong></td>
                                <td>{{ $totalObtained }}</td>
                            </tr>
                            <tr>
                                <td><strong>Percentage:</strong></td>
                                <td><strong>{{ number_format($overallPercentage, 2) }}%</strong></td>
                            </tr>
                            <tr>
                                <td><strong>Grade:</strong></td>
                                <td><span class="badge bg-{{ $overallGradeClass }}">{{ $overallGrade }}</span></td>
                            </tr>
                            @if($result)
                            <tr>
                                <td><strong>Class Rank:</strong></td>
                                <td>
                                    @if($result->rank == 1)
                                        <span class="badge bg-warning">🥇 1st Position</span>
                                    @elseif($result->rank == 2)
                                        <span class="badge bg-secondary">🥈 2nd Position</span>
                                    @elseif($result->rank == 3)
                                        <span class="badge bg-secondary">🥉 3rd Position</span>
                                    @else
                                        <strong>{{ $result->rank }}</strong>
                                    @endif
                                </td>
                            </tr>
                            @endif
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card border-{{ $resultClass }}">
                    <div class="card-body text-center">
                        <h6 class="mb-3">Final Result</h6>
                        <h1 class="display-4 text-{{ $resultClass }} mb-0">
                            <strong>{{ $finalResult }}</strong>
                        </h1>
                        @if($allPass)
                            <p class="text-success mt-2 mb-0">
                                <i class="bi bi-check-circle-fill"></i> Congratulations!
                            </p>
                        @else
                            <p class="text-danger mt-2 mb-0">
                                <i class="bi bi-x-circle-fill"></i> Better luck next time
                            </p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Grading Scale -->
        <div class="mb-4">
            <h6><strong>Grading Scale</strong></h6>
            <table class="table table-bordered table-sm">
                <thead class="table-light">
                    <tr class="text-center">
                        <th>Grade</th>
                        <th>A+</th>
                        <th>A</th>
                        <th>B+</th>
                        <th>B</th>
                        <th>C</th>
                        <th>D</th>
                        <th>F</th>
                    </tr>
                </thead>
                <tbody>
                    <tr class="text-center">
                        <td><strong>Percentage</strong></td>
                        <td>90-100</td>
                        <td>80-89</td>
                        <td>70-79</td>
                        <td>60-69</td>
                        <td>50-59</td>
                        <td>40-49</td>
                        <td>&lt;40</td>
                    </tr>
                </tbody>
            </table>
        </div>
        
        @if($exam->description)
        <div class="mb-4">
            <h6><strong>Remarks</strong></h6>
            <p class="mb-0">{{ $exam->description }}</p>
        </div>
        @endif
        
        <hr class="mt-5">
        
        <!-- Signatures -->
        <div class="row mt-5">
            <div class="col-4">
                <p class="text-center">
                    ___________________<br>
                    <small><strong>Class Teacher</strong></small>
                </p>
            </div>
            <div class="col-4">
                <p class="text-center">
                    ___________________<br>
                    <small><strong>Principal</strong></small>
                </p>
            </div>
            <div class="col-4">
                <p class="text-center">
                    ___________________<br>
                    <small><strong>Parent's Signature</strong></small>
                </p>
            </div>
        </div>
        
        <p class="text-center text-muted small mt-4 mb-0">
            This is a computer-generated marksheet. Issued on {{ now()->format('d M, Y h:i A') }}
        </p>
    </div>
</div>

<style>
@media print {
    .d-print-none {
        display: none !important;
    }
    
    body {
        font-size: 11px;
        background: white;
    }
    
    #marksheet {
        border: none;
        box-shadow: none;
        page-break-inside: avoid;
    }
    
    .card {
        border: none;
        box-shadow: none;
    }
    
    .table {
        font-size: 11px;
    }
    
    .table-bordered th,
    .table-bordered td {
        border: 1px solid #000 !important;
    }
    
    h3, h4, h5, h6 {
        color: #000;
    }
    
    .badge {
        border: 1px solid #000;
        padding: 3px 6px;
    }
    
    @page {
        margin: 0.5cm;
        size: A4;
    }
    
    /* Ensure colors print */
    * {
        -webkit-print-color-adjust: exact;
        print-color-adjust: exact;
    }
}

/* Watermark for unpublished results */
@if(!$exam->is_results_published)
@media print {
    #marksheet::before {
        content: "DRAFT";
        position: fixed;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%) rotate(-45deg);
        font-size: 120px;
        color: rgba(220, 53, 69, 0.1);
        font-weight: bold;
        z-index: 1000;
        pointer-events: none;
    }
}
@endif
</style>

@if(!$exam->is_results_published)
<div class="alert alert-warning d-print-none mt-3">
    <i class="bi bi-exclamation-triangle"></i>
    <strong>Note:</strong> This is a draft marksheet. Results have not been officially published yet.
</div>
@endif

@endsection
