@extends('layouts.app')

@section('title', 'Exam Timetable - ' . $exam->name)

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4 d-print-none">
    <div>
        <h2>Exam Timetable</h2>
        <p class="text-muted mb-0">{{ $exam->name }} - {{ $exam->class?->name ?? 'N/A' }}</p>
    </div>
    <div>
        <button onclick="window.print()" class="btn btn-primary me-2">
            <i class="bi bi-printer"></i> Print
        </button>
        <a href="{{ route('exams.show', $exam) }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Back
        </a>
    </div>
</div>

<div class="card" id="timetable">
    <div class="card-body">
        <!-- School Header -->
        <div class="text-center mb-4">
            <h3><strong>{{ config('app.name', 'School ERP System') }}</strong></h3>
            <h5>Examination Timetable</h5>
            <p class="mb-0">{{ $exam->name }} - {{ $exam->academicYear->name }}</p>
        </div>
        
        <hr>
        
        <!-- Exam Information -->
        <div class="row mb-4">
            <div class="col-md-6">
                <table class="table table-borderless table-sm">
                    <tr>
                        <td width="150"><strong>Class:</strong></td>
                        <td>{{ $exam->class?->name ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <td><strong>Exam Type:</strong></td>
                        <td>{{ ucfirst($exam->type) }}</td>
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
                        <td width="150"><strong>Exam Period:</strong></td>
                        <td>{{ $exam->start_date?->format('d M, Y') ?? 'N/A' }} - {{ $exam->end_date?->format('d M, Y') ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <td><strong>Total Marks:</strong></td>
                        <td>{{ $exam->total_marks }}</td>
                    </tr>
                    <tr>
                        <td><strong>Passing Marks:</strong></td>
                        <td>{{ $exam->passing_marks }}</td>
                    </tr>
                </table>
            </div>
        </div>
        
        @if($exam->subjects->count() > 0)
            <!-- Timetable Table -->
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead class="table-primary">
                        <tr>
                            <th width="80" class="text-center">S.No.</th>
                            <th>Subject</th>
                            <th width="150">Date</th>
                            <th width="120">Day</th>
                            <th width="150">Time</th>
                            <th width="120">Duration</th>
                            <th width="100">Max Marks</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $sortedSubjects = $exam->subjects->sortBy(function($subject) {
                                return $subject->pivot->exam_date ?? '9999-12-31';
                            });
                        @endphp
                        
                        @foreach($sortedSubjects as $index => $subject)
                        <tr>
                            <td class="text-center">{{ $index + 1 }}</td>
                            <td><strong>{{ $subject->name }}</strong></td>
                            <td>
                                @if($subject->pivot->exam_date)
                                    {{ \Carbon\Carbon::parse($subject->pivot->exam_date)->format('d M, Y') }}
                                @else
                                    <span class="text-muted">Not scheduled</span>
                                @endif
                            </td>
                            <td>
                                @if($subject->pivot->exam_date)
                                    {{ \Carbon\Carbon::parse($subject->pivot->exam_date)->format('l') }}
                                @else
                                    -
                                @endif
                            </td>
                            <td>
                                @if($subject->pivot->start_time)
                                    {{ \Carbon\Carbon::parse($subject->pivot->start_time)->format('h:i A') }}
                                @else
                                    -
                                @endif
                            </td>
                            <td>{{ $subject->pivot->duration ? $subject->pivot->duration . ' mins' : '-' }}</td>
                            <td class="text-center"><strong>{{ $subject->pivot->max_marks }}</strong></td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="table-light">
                        <tr>
                            <td colspan="6" class="text-end"><strong>Total Subjects:</strong></td>
                            <td class="text-center"><strong>{{ $exam->subjects->count() }}</strong></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
            
            <!-- Instructions -->
            <div class="mt-4">
                <h6><strong>Important Instructions:</strong></h6>
                <ul class="small">
                    <li>Students must report 15 minutes before the exam start time</li>
                    <li>Bring your admit card and student ID card</li>
                    <li>Mobile phones and electronic devices are strictly prohibited</li>
                    <li>Follow the exam rules and regulations</li>
                    <li>No student will be allowed to leave before 30 minutes of the exam</li>
                </ul>
            </div>
            
            <hr class="mt-5">
            
            <!-- Footer -->
            <div class="row mt-4">
                <div class="col-4">
                    <p class="text-center">
                        ___________________<br>
                        <small>Principal's Signature</small>
                    </p>
                </div>
                <div class="col-4">
                    <p class="text-center">
                        ___________________<br>
                        <small>Exam Controller</small>
                    </p>
                </div>
                <div class="col-4">
                    <p class="text-center">
                        ___________________<br>
                        <small>Class Teacher</small>
                    </p>
                </div>
            </div>
            
            <p class="text-center text-muted small mt-4 mb-0">
                This is a computer-generated timetable. Printed on {{ now()->format('d M, Y h:i A') }}
            </p>
        @else
            <div class="text-center text-muted py-5 d-print-none">
                <i class="bi bi-calendar-x display-4 d-block mb-3"></i>
                <h5>No Subjects Scheduled</h5>
                <p>Please assign subjects and schedule exam dates before printing the timetable.</p>
                <a href="{{ route('exams.subjects', $exam) }}" class="btn btn-primary">
                    <i class="bi bi-plus-circle"></i> Assign Subjects
                </a>
            </div>
        @endif
    </div>
</div>

<style>
@media print {
    .d-print-none {
        display: none !important;
    }
    
    body {
        font-size: 12px;
        background: white;
    }
    
    #timetable {
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
    
    h3, h5 {
        color: #000;
    }
    
    @page {
        margin: 1cm;
        size: A4;
    }
}

/* Color coding for scheduled status */
.text-scheduled {
    color: #0d6efd;
}

.text-pending {
    color: #dc3545;
}
</style>
@endsection
