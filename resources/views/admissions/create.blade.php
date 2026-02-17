@extends('layouts.app')

@section('title', 'New Admission - School ERP')

@section('content')
<div class="mb-4">
    <h2>New Student Admission</h2>
    <p class="text-muted">Add a new student to the school</p>
</div>

<div class="card">
    <div class="card-body">
        <form method="POST" action="{{ route('admissions.store') }}" enctype="multipart/form-data">
            @csrf
            
            <!-- Personal Information -->
            <h5 class="mb-3 pb-2 border-bottom">Personal Information</h5>
            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="student_name" class="form-label">Student Name <span class="text-danger">*</span></label>
                    <input type="text" 
                           class="form-control @error('student_name') is-invalid @enderror" 
                           id="student_name" 
                           name="student_name" 
                           value="{{ old('student_name') }}" 
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
                           value="{{ old('dob') }}" 
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
                        <option value="Male" {{ old('gender') == 'Male' ? 'selected' : '' }}>Male</option>
                        <option value="Female" {{ old('gender') == 'Female' ? 'selected' : '' }}>Female</option>
                        <option value="Other" {{ old('gender') == 'Other' ? 'selected' : '' }}>Other</option>
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
                            <option value="{{ $group }}" {{ old('blood_group') == $group ? 'selected' : '' }}>{{ $group }}</option>
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
                           value="{{ old('aadhaar_no') }}"
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
                    @error('student_pic')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <div class="mt-2">
                        <button type="button" class="btn btn-sm btn-outline-primary" id="capturePhotoBtn">
                            <i class="bi bi-camera"></i> Capture from Camera
                        </button>
                    </div>
                    <input type="hidden" id="student_pic_data" name="student_pic_data">
                    <div id="photoPreview" class="mt-2" style="display: none;">
                        <img id="previewImage" src="" alt="Preview" class="img-thumbnail" style="max-width: 150px;">
                        <button type="button" class="btn btn-sm btn-danger mt-1" id="removePhotoBtn">Remove</button>
                    </div>
                </div>
                <div class="col-md-3">
                    <label for="aadhaar_doc" class="form-label">Aadhaar Document</label>
                    <input type="file" 
                           class="form-control @error('aadhaar_doc') is-invalid @enderror" 
                           id="aadhaar_doc" 
                           name="aadhaar_doc"
                           accept="image/*,application/pdf">
                    @error('aadhaar_doc')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <!-- Academic Information -->
            <h5 class="mb-3 pb-2 border-bottom mt-4">Academic Information</h5>
            <div class="row mb-3">
                <div class="col-md-4">
                    <label for="class_id" class="form-label">Class <span class="text-danger">*</span></label>
                    <select class="form-select @error('class_id') is-invalid @enderror" 
                            id="class_id" 
                            name="class_id" 
                            required>
                        <option value="">Select Class</option>
                        @foreach($classes as $class)
                            <option value="{{ $class->id }}" {{ old('class_id') == $class->id ? 'selected' : '' }}>
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
                           value="{{ old('admission_date', date('Y-m-d')) }}" 
                           required>
                    @error('admission_date')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-4">
                    <label for="past_school_info" class="form-label">Previous School</label>
                    <input type="text" 
                           class="form-control @error('past_school_info') is-invalid @enderror" 
                           id="past_school_info" 
                           name="past_school_info" 
                           value="{{ old('past_school_info') }}"
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
                           value="{{ old('guardian_name') }}" 
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
                           value="{{ old('guardian_phone') }}"
                           pattern="[0-9]{10}"
                           maxlength="10"
                           placeholder="10-digit mobile number"
                           required>
                    @error('guardian_phone')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="emergency_contact" class="form-label">Emergency/Alternate Contact</label>
                    <input type="tel" 
                           class="form-control @error('emergency_contact') is-invalid @enderror" 
                           id="emergency_contact" 
                           name="emergency_contact" 
                           value="{{ old('emergency_contact') }}"
                           pattern="[0-9]{10}"
                           maxlength="10"
                           placeholder="10-digit mobile number">
                    @error('emergency_contact')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-6">
                    <label for="address" class="form-label">Address</label>
                    <textarea class="form-control @error('address') is-invalid @enderror" 
                              id="address" 
                              name="address" 
                              rows="2"
                              maxlength="500"
                              placeholder="Student's complete address">{{ old('address') }}</textarea>
                    @error('address')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-12">
                    <label for="health_issues" class="form-label">Health Issues / Medical Information</label>
                    <textarea class="form-control @error('health_issues') is-invalid @enderror" 
                              id="health_issues" 
                              name="health_issues" 
                              rows="3"
                              maxlength="1000"
                              placeholder="Any allergies, medical conditions, or special health requirements">{{ old('health_issues') }}</textarea>
                    @error('health_issues')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <div class="form-text">Please mention any allergies, chronic conditions, or medications</div>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="d-flex justify-content-end gap-2 mt-4">
                <a href="{{ route('admissions.index') }}" class="btn btn-secondary">Cancel</a>
                <button type="submit" class="btn btn-primary">Save Admission</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Camera capture modal HTML
    const cameraModalHTML = `
        <div class="modal fade" id="cameraModal" tabindex="-1" aria-labelledby="cameraModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="cameraModalLabel">Capture Student Photo</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div id="cameraContainer" style="position: relative;">
                            <video id="cameraVideo" autoplay playsinline style="width: 100%; max-height: 400px; background: #000;"></video>
                            <canvas id="cameraCanvas" style="display: none;"></canvas>
                        </div>
                        <div id="cameraError" class="alert alert-danger mt-3" style="display: none;"></div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="button" class="btn btn-primary" id="takePictureBtn">
                            <i class="bi bi-camera"></i> Take Picture
                        </button>
                    </div>
                </div>
            </div>
        </div>
    `;

    let cameraStream = null;
    let cameraModal = null;

    // Initialize all camera functionality after DOM is ready
    document.addEventListener('DOMContentLoaded', function() {
        // Add modal to page
        document.body.insertAdjacentHTML('beforeend', cameraModalHTML);
        
        // Initialize camera modal
        cameraModal = new bootstrap.Modal(document.getElementById('cameraModal'));

        // Capture photo button click
        document.getElementById('capturePhotoBtn').addEventListener('click', async function() {
            try {
                // Request camera access
                cameraStream = await navigator.mediaDevices.getUserMedia({ 
                    video: { 
                        width: { ideal: 640 },
                        height: { ideal: 480 },
                        facingMode: 'user'
                    } 
                });
                
                const video = document.getElementById('cameraVideo');
                video.srcObject = cameraStream;
                
                // Show modal
                cameraModal.show();
                
                // Hide any previous error
                document.getElementById('cameraError').style.display = 'none';
            } catch (error) {
                const errorDiv = document.getElementById('cameraError');
                errorDiv.textContent = 'Unable to access camera. Please ensure camera permissions are granted.';
                errorDiv.style.display = 'block';
            }
        });

        // Take picture button click
        document.getElementById('takePictureBtn').addEventListener('click', function() {
            const video = document.getElementById('cameraVideo');
            const canvas = document.getElementById('cameraCanvas');
            const context = canvas.getContext('2d');
            
            // Set canvas dimensions to match video
            canvas.width = video.videoWidth;
            canvas.height = video.videoHeight;
            
            // Draw video frame to canvas
            context.drawImage(video, 0, 0, canvas.width, canvas.height);
            
            // Get image data as base64
            const imageData = canvas.toDataURL('image/jpeg', 0.9);
            
            // Store in hidden field
            document.getElementById('student_pic_data').value = imageData;
            
            // Show preview
            const previewImg = document.getElementById('previewImage');
            previewImg.src = imageData;
            document.getElementById('photoPreview').style.display = 'block';
            
            // Clear file input
            document.getElementById('student_pic').value = '';
            
            // Stop camera stream
            stopCamera();
            
            // Close modal
            cameraModal.hide();
        });

        // Remove photo button
        document.getElementById('removePhotoBtn').addEventListener('click', function() {
            document.getElementById('student_pic_data').value = '';
            document.getElementById('student_pic').value = '';
            document.getElementById('photoPreview').style.display = 'none';
        });

        // Stop camera when modal is closed
        document.getElementById('cameraModal').addEventListener('hidden.bs.modal', function() {
            stopCamera();
        });

        // Preview uploaded file
        document.getElementById('student_pic').addEventListener('change', function(e) {
            if (e.target.files && e.target.files[0]) {
                const reader = new FileReader();
                reader.onload = function(event) {
                    const previewImg = document.getElementById('previewImage');
                    previewImg.src = event.target.result;
                    document.getElementById('photoPreview').style.display = 'block';
                    // Clear camera capture data
                    document.getElementById('student_pic_data').value = '';
                };
                reader.readAsDataURL(e.target.files[0]);
            }
        });
    });

    function stopCamera() {
        if (cameraStream) {
            cameraStream.getTracks().forEach(track => track.stop());
            cameraStream = null;
        }
    }
</script>
@endpush
