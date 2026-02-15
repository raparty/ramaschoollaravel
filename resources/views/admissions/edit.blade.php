@extends('layouts.app')

@section('title', 'Edit Admission - School ERP')

@section('content')
<div class="mb-4">
    <h2>Edit Student Admission</h2>
    <p class="text-muted">Update student information for {{ $admission->student_name }}</p>
</div>

<div class="card">
    <div class="card-body">
        <form method="POST" action="{{ route('admissions.update', $admission) }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            
            <!-- Personal Information -->
            <h5 class="mb-3 pb-2 border-bottom">Personal Information</h5>
            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="student_name" class="form-label">Student Name <span class="text-danger">*</span></label>
                    <input type="text" 
                           class="form-control @error('student_name') is-invalid @enderror" 
                           id="student_name" 
                           name="student_name" 
                           value="{{ old('student_name', $admission->student_name) }}" 
                           required>
                    @error('student_name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-3">
                    <label for="dob" class="form-label">Date of Birth <span class="text-danger">*</span></label>
                    <input type="date" 
                           class="form-control @error('dob') is-invalid @enderror" 
                           id="dob" 
                           name="dob" 
                           value="{{ old('dob', $admission->dob?->format('Y-m-d')) }}" 
                           required>
                    @error('dob')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-3">
                    <label for="gender" class="form-label">Gender <span class="text-danger">*</span></label>
                    <select class="form-select @error('gender') is-invalid @enderror" 
                            id="gender" 
                            name="gender" 
                            required>
                        <option value="">Select Gender</option>
                        <option value="Male" {{ old('gender', $admission->gender) == 'Male' ? 'selected' : '' }}>Male</option>
                        <option value="Female" {{ old('gender', $admission->gender) == 'Female' ? 'selected' : '' }}>Female</option>
                        <option value="Other" {{ old('gender', $admission->gender) == 'Other' ? 'selected' : '' }}>Other</option>
                    </select>
                    @error('gender')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-3">
                    <label for="blood_group" class="form-label">Blood Group</label>
                    <select class="form-select @error('blood_group') is-invalid @enderror" 
                            id="blood_group" 
                            name="blood_group">
                        <option value="">Select Blood Group</option>
                        @foreach(['A+', 'A-', 'B+', 'B-', 'O+', 'O-', 'AB+', 'AB-'] as $group)
                            <option value="{{ $group }}" {{ old('blood_group', $admission->blood_group) == $group ? 'selected' : '' }}>{{ $group }}</option>
                        @endforeach
                    </select>
                    @error('blood_group')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-3">
                    <label for="aadhaar_no" class="form-label">Aadhaar Number</label>
                    <input type="text" 
                           class="form-control @error('aadhaar_no') is-invalid @enderror" 
                           id="aadhaar_no" 
                           name="aadhaar_no" 
                           value="{{ old('aadhaar_no', $admission->aadhaar_no) }}"
                           maxlength="12"
                           pattern="[0-9]{12}"
                           placeholder="12-digit Aadhaar">
                    @error('aadhaar_no')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-3">
                    <label for="student_pic" class="form-label">Student Photo</label>
                    <input type="file" 
                           class="form-control @error('student_pic') is-invalid @enderror" 
                           id="student_pic" 
                           name="student_pic"
                           accept="image/*">
                    @if($admission->student_pic)
                        <small class="text-muted">Current: {{ $admission->student_pic }}</small>
                    @endif
                    @error('student_pic')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-3">
                    <label for="aadhaar_doc" class="form-label">Aadhaar Document</label>
                    <input type="file" 
                           class="form-control @error('aadhaar_doc') is-invalid @enderror" 
                           id="aadhaar_doc" 
                           name="aadhaar_doc"
                           accept="image/*,application/pdf">
                    @if($admission->aadhaar_doc_path)
                        <small class="text-muted">Current: {{ $admission->aadhaar_doc_path }}</small>
                    @endif
                    @error('aadhaar_doc')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <!-- Academic Information -->
            <h5 class="mb-3 pb-2 border-bottom mt-4">Academic Information</h5>
            <div class="row mb-3">
                <div class="col-md-4">
                    <label for="reg_no" class="form-label">Registration No</label>
                    <input type="text" 
                           class="form-control" 
                           id="reg_no" 
                           value="{{ $admission->reg_no }}" 
                           disabled>
                    <small class="text-muted">Registration number cannot be changed</small>
                </div>
                <div class="col-md-4">
                    <label for="class_id" class="form-label">Class <span class="text-danger">*</span></label>
                    <select class="form-select @error('class_id') is-invalid @enderror" 
                            id="class_id" 
                            name="class_id" 
                            required>
                        <option value="">Select Class</option>
                        @foreach($classes as $class)
                            <option value="{{ $class->id }}" {{ old('class_id', $admission->class_id) == $class->id ? 'selected' : '' }}>
                                {{ $class->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('class_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-4">
                    <label for="admission_date" class="form-label">Admission Date <span class="text-danger">*</span></label>
                    <input type="date" 
                           class="form-control @error('admission_date') is-invalid @enderror" 
                           id="admission_date" 
                           name="admission_date" 
                           value="{{ old('admission_date', $admission->admission_date?->format('Y-m-d')) }}" 
                           required>
                    @error('admission_date')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-12">
                    <label for="past_school_info" class="form-label">Previous School</label>
                    <input type="text" 
                           class="form-control @error('past_school_info') is-invalid @enderror" 
                           id="past_school_info" 
                           name="past_school_info" 
                           value="{{ old('past_school_info', $admission->past_school_info) }}"
                           placeholder="Name of previous school">
                    @error('past_school_info')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <!-- Guardian Information -->
            <h5 class="mb-3 pb-2 border-bottom mt-4">Guardian Information</h5>
            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="guardian_name" class="form-label">Guardian Name <span class="text-danger">*</span></label>
                    <input type="text" 
                           class="form-control @error('guardian_name') is-invalid @enderror" 
                           id="guardian_name" 
                           name="guardian_name" 
                           value="{{ old('guardian_name', $admission->guardian_name) }}" 
                           required>
                    @error('guardian_name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-6">
                    <label for="guardian_phone" class="form-label">Guardian Phone <span class="text-danger">*</span></label>
                    <input type="tel" 
                           class="form-control @error('guardian_phone') is-invalid @enderror" 
                           id="guardian_phone" 
                           name="guardian_phone" 
                           value="{{ old('guardian_phone', $admission->guardian_phone) }}"
                           pattern="[0-9]{10}"
                           maxlength="10"
                           placeholder="10-digit mobile number"
                           required>
                    @error('guardian_phone')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <!-- Form Actions -->
            <div class="d-flex justify-content-end gap-2 mt-4">
                <a href="{{ route('admissions.show', $admission) }}" class="btn btn-secondary">Cancel</a>
                <button type="submit" class="btn btn-primary">Update Admission</button>
            </div>
        </form>
    </div>
</div>
@endsection
