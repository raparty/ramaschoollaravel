@extends('layouts.app')

@section('title', 'Collect Fees - School ERP')

@section('content')
<div class="mb-4">
    <h2>Collect Student Fees</h2>
    <p class="text-muted">Search for a student to collect fees</p>
</div>

<div class="row">
    <div class="col-md-8 mx-auto">
        <div class="card">
            <div class="card-body">
                <form method="GET" action="{{ route('fees.collect') }}">
                    <div class="mb-3">
                        <label for="student_search" class="form-label">Search Student</label>
                        <input type="text" 
                               class="form-control form-control-lg" 
                               id="student_search" 
                               placeholder="Enter student name or registration number..."
                               autocomplete="off">
                        <div id="search_results" class="list-group mt-2" style="display: none;"></div>
                    </div>
                    <input type="hidden" name="registration_no" id="registration_no">
                    <button type="submit" class="btn btn-primary w-100" id="proceed_btn" disabled>
                        Proceed to Fee Collection →
                    </button>
                </form>
            </div>
        </div>

        <div class="card mt-4">
            <div class="card-header bg-info text-white">
                <h6 class="mb-0">💡 How to Collect Fees</h6>
            </div>
            <div class="card-body">
                <ol class="mb-0 ps-3">
                    <li class="mb-2">Search for the student by name or registration number</li>
                    <li class="mb-2">Select the student from the search results</li>
                    <li class="mb-2">Enter the fee amount and select the term</li>
                    <li>Generate receipt after successful payment</li>
                </ol>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Student search functionality
    let searchTimeout;
    const searchInput = document.getElementById('student_search');
    const searchResults = document.getElementById('search_results');
    const regNoInput = document.getElementById('registration_no');
    const proceedBtn = document.getElementById('proceed_btn');

    searchInput.addEventListener('input', function() {
        clearTimeout(searchTimeout);
        const query = this.value.trim();

        if (query.length < 2) {
            searchResults.style.display = 'none';
            return;
        }

        searchTimeout = setTimeout(() => {
            fetch(`/fees/search-students?q=${encodeURIComponent(query)}`)
                .then(response => response.json())
                .then(students => {
                    if (students.length > 0) {
                        let html = '';
                        students.forEach(student => {
                            html += `
                                <a href="#" class="list-group-item list-group-item-action student-item" 
                                   data-regno="${student.reg_no}"
                                   data-name="${student.student_name}">
                                    <strong>${student.reg_no}</strong> - ${student.student_name}
                                    <small class="text-muted">(${student.class?.name || 'N/A'})</small>
                                </a>
                            `;
                        });
                        searchResults.innerHTML = html;
                        searchResults.style.display = 'block';

                        // Add click handlers
                        document.querySelectorAll('.student-item').forEach(item => {
                            item.addEventListener('click', function(e) {
                                e.preventDefault();
                                const regno = this.dataset.regno;
                                const name = this.dataset.name;
                                
                                searchInput.value = name;
                                regNoInput.value = regno;
                                searchResults.style.display = 'none';
                                proceedBtn.disabled = false;
                            });
                        });
                    } else {
                        searchResults.innerHTML = '<div class="list-group-item text-muted">No students found</div>';
                        searchResults.style.display = 'block';
                    }
                })
                .catch(error => {
                    console.error('Search error:', error);
                    searchResults.style.display = 'none';
                });
        }, 300);
    });

    // Hide results when clicking outside
    document.addEventListener('click', function(e) {
        if (!searchInput.contains(e.target) && !searchResults.contains(e.target)) {
            searchResults.style.display = 'none';
        }
    });
</script>
@endpush
