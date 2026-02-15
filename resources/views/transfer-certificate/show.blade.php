@extends('layouts.app')

@section('title', 'Transfer Certificate - ' . $student->student_name)

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Transfer Certificate (TC) Generation</h5>
                <div class="no-print">
                    <button onclick="window.print();" class="btn btn-primary">
                        <i class="bi bi-printer"></i> Print Certificate
                    </button>
                    <a href="{{ route('admissions.show', $student->id) }}" class="btn btn-outline-secondary ms-2">
                        <i class="bi bi-arrow-left"></i> Back to Profile
                    </a>
                </div>
            </div>
            <div class="card-body" style="padding: 40px; background: #fff;">
                <div id="printableTC" style="border: 5px double #333; padding: 30px; line-height: 2; min-height: 600px;">
                    <div style="text-align: center; margin-bottom: 30px;">
                        <h2 style="margin:0; font-weight: bold;">SCHOOL LEAVING CERTIFICATE</h2>
                        <p style="margin:0; font-size: 1.1em;">(Transfer Certificate)</p>
                    </div>

                    <div style="margin-top: 30px; font-size: 1.05em;">
                        <p style="text-align: justify;">
                            This is to certify that <strong>{{ $student->student_name }}</strong>, 
                            Registration No: <strong>{{ $student->reg_no }}</strong>, 
                            son/daughter of <strong>{{ $student->guardian_name ?? 'N/A' }}</strong> 
                            was a bona fide student of this institution.
                        </p>

                        <p style="text-align: justify;">
                            He/She was admitted to Class <strong>{{ $student->class?->name ?? 'N/A' }}</strong> 
                            on <strong>{{ $student->admission_date ? $student->admission_date->format('d-M-Y') : 'N/A' }}</strong>.
                        </p>
                        
                        <p style="text-align: justify;">
                            His/Her Date of Birth according to the Admission Register is 
                            <strong>{{ $student->dob ? $student->dob->format('d-M-Y') : 'N/A' }}</strong>.
                        </p>

                        @if($student->aadhaar_no)
                        <p style="text-align: justify;">
                            Aadhaar Number: <strong>{{ $student->aadhaar_no }}</strong>
                        </p>
                        @endif

                        <p style="text-align: justify; margin-top: 30px;">
                            This certificate is issued on the request of the parent/guardian.
                        </p>
                    </div>

                    <div style="margin-top: 80px; display: flex; justify-content: space-between; align-items: flex-end;">
                        <div>
                            <p style="margin:0;"><strong>Date:</strong> {{ now()->format('d-M-Y') }}</p>
                        </div>
                        <div style="text-align: center;">
                            <div style="border-top: 2px solid #000; padding-top: 5px; min-width: 200px;">
                                <strong>Principal's Signature</strong>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
@media print {
    .no-print, 
    .navbar, 
    .sidebar, 
    nav,
    .card-header .no-print,
    .breadcrumb {
        display: none !important;
    }
    
    body {
        margin: 0;
        padding: 0;
    }
    
    .card {
        border: none !important;
        box-shadow: none !important;
        margin: 0 !important;
    }
    
    .card-body {
        padding: 20px !important;
    }
    
    #printableTC {
        page-break-inside: avoid;
    }
}
</style>
@endsection
