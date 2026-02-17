<?php

namespace App\Http\Controllers;

use App\Models\HostelStudentAllocation;
use App\Models\HostelBed;
use App\Models\HostelLocker;
use App\Models\Admission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

/**
 * HostelStudentAllocationController
 * 
 * Manages student bed allocations with check-in/check-out functionality
 */
class HostelStudentAllocationController extends Controller
{
    /**
     * Display a listing of allocations.
     */
    public function index(Request $request)
    {
        $query = HostelStudentAllocation::query()
            ->with(['student', 'bed.room.floor.block.hostel', 'locker']);

        // Filter by status
        if ($request->filled('status')) {
            $query->byStatus($request->status);
        }

        // Filter by student
        if ($request->filled('student_id')) {
            $query->where('student_id', $request->student_id);
        }

        // Search by student name or registration number
        if ($request->filled('search')) {
            $query->whereHas('student', function ($q) use ($request) {
                $q->where('student_name', 'like', '%' . $request->search . '%')
                    ->orWhere('reg_no', 'like', '%' . $request->search . '%');
            });
        }

        $allocations = $query->ordered()->paginate(20);

        return view('hostel.allocations.index', compact('allocations'));
    }

    /**
     * Show the form for creating a new allocation (check-in).
     */
    public function create(Request $request)
    {
        // Get students who don't have active allocations
        $students = Admission::where('is_active', true)
            ->whereDoesntHave('hostelAllocations', function ($query) {
                $query->where('status', 'Active');
            })
            ->orderBy('student_name')
            ->get();

        // Get available beds
        $availableBeds = HostelBed::available()
            ->with('room.floor.block.hostel')
            ->ordered()
            ->get();

        $studentId = $request->get('student_id');
        
        return view('hostel.allocations.create', compact('students', 'availableBeds', 'studentId'));
    }

    /**
     * Store a newly created allocation (check-in).
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'student_id' => 'required|exists:admissions,id',
            'bed_id' => 'required|exists:hostel_beds,id',
            'locker_id' => 'nullable|exists:hostel_lockers,id',
            'check_in_date' => 'required|date',
            'check_in_remarks' => 'nullable|string',
        ]);

        try {
            DB::beginTransaction();

            // Check if student already has an active allocation
            $existingAllocation = HostelStudentAllocation::where('student_id', $validated['student_id'])
                ->where('status', 'Active')
                ->first();

            if ($existingAllocation) {
                return back()->withErrors([
                    'student_id' => 'Student already has an active bed allocation'
                ])->withInput();
            }

            // Check if bed is available
            $bed = HostelBed::find($validated['bed_id']);
            if ($bed->is_occupied || !$bed->is_active || $bed->condition_status !== 'Good') {
                return back()->withErrors([
                    'bed_id' => 'Selected bed is not available for allocation'
                ])->withInput();
            }

            // Check if locker is available (if provided)
            if (!empty($validated['locker_id'])) {
                $locker = HostelLocker::find($validated['locker_id']);
                if ($locker->is_assigned || !$locker->is_active || $locker->condition_status !== 'Good') {
                    return back()->withErrors([
                        'locker_id' => 'Selected locker is not available for allocation'
                    ])->withInput();
                }
            }

            // Check room capacity
            $room = $bed->room;
            if ($room->current_occupancy >= $room->max_strength) {
                return back()->withErrors([
                    'bed_id' => 'Room has reached maximum capacity'
                ])->withInput();
            }

            // Generate receipt number
            $validated['receipt_number'] = 'HST-' . date('Ymd') . '-' . str_pad(HostelStudentAllocation::count() + 1, 5, '0', STR_PAD_LEFT);
            $validated['status'] = 'Active';
            $validated['is_active'] = true;
            $validated['created_by'] = Auth::id();

            // Create allocation
            $allocation = HostelStudentAllocation::create($validated);

            // Update bed status
            $bed->update(['is_occupied' => true]);

            // Update locker status if assigned
            if (!empty($validated['locker_id'])) {
                $locker->update(['is_assigned' => true]);
            }

            DB::commit();

            return redirect()->route('hostel.allocations.show', $allocation)
                ->with('success', 'Student checked in successfully! Receipt: ' . $validated['receipt_number']);

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Error during check-in: ' . $e->getMessage()])
                ->withInput();
        }
    }

    /**
     * Display the specified allocation.
     */
    public function show(HostelStudentAllocation $allocation)
    {
        $allocation->load([
            'student',
            'bed.room.floor.block.hostel',
            'locker',
            'securityDeposit'
        ]);

        return view('hostel.allocations.show', compact('allocation'));
    }

    /**
     * Show the form for checking out a student.
     */
    public function checkoutForm(HostelStudentAllocation $allocation)
    {
        if ($allocation->status !== 'Active') {
            return redirect()->route('hostel.allocations.show', $allocation)
                ->with('error', 'Only active allocations can be checked out');
        }

        $allocation->load(['student', 'bed.room', 'locker', 'securityDeposit']);

        return view('hostel.allocations.checkout', compact('allocation'));
    }

    /**
     * Process student checkout.
     */
    public function checkout(Request $request, HostelStudentAllocation $allocation)
    {
        $validated = $request->validate([
            'check_out_date' => 'required|date|after_or_equal:' . $allocation->check_in_date,
            'check_out_remarks' => 'nullable|string',
        ]);

        try {
            DB::beginTransaction();

            // Update allocation
            $allocation->update([
                'check_out_date' => $validated['check_out_date'],
                'check_out_remarks' => $validated['check_out_remarks'],
                'status' => 'Checked Out',
                'is_active' => false,
                'updated_by' => Auth::id(),
            ]);

            // Update bed status
            $allocation->bed->update(['is_occupied' => false]);

            // Update locker status if assigned
            if ($allocation->locker_id) {
                $allocation->locker->update(['is_assigned' => false]);
            }

            DB::commit();

            return redirect()->route('hostel.allocations.show', $allocation)
                ->with('success', 'Student checked out successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Error during checkout: ' . $e->getMessage()])
                ->withInput();
        }
    }

    /**
     * Cancel an allocation.
     */
    public function cancel(Request $request, HostelStudentAllocation $allocation)
    {
        $validated = $request->validate([
            'cancellation_reason' => 'required|string',
        ]);

        try {
            DB::beginTransaction();

            // Update allocation
            $allocation->update([
                'status' => 'Cancelled',
                'is_active' => false,
                'check_out_remarks' => 'Cancelled: ' . $validated['cancellation_reason'],
                'updated_by' => Auth::id(),
            ]);

            // Update bed status
            $allocation->bed->update(['is_occupied' => false]);

            // Update locker status if assigned
            if ($allocation->locker_id) {
                $allocation->locker->update(['is_assigned' => false]);
            }

            DB::commit();

            return redirect()->route('hostel.allocations.index')
                ->with('success', 'Allocation cancelled successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Error cancelling allocation: ' . $e->getMessage()]);
        }
    }
}
