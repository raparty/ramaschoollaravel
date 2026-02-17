<?php

namespace App\Http\Controllers;

use App\Models\Hostel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * HostelController
 * 
 * Manages hostel CRUD operations
 */
class HostelController extends Controller
{
    /**
     * Display the hostel management dashboard.
     */
    public function dashboard()
    {
        $stats = [
            'hostels' => \App\Models\Hostel::count(),
            'rooms' => \App\Models\HostelRoom::count(),
            'beds' => \App\Models\HostelBed::count(),
            'active_students' => \App\Models\HostelStudentAllocation::where('status', 'Active')->count(),
            'active_wardens' => \App\Models\HostelWarden::where('is_active', true)->count(),
            'pending_expenses' => \App\Models\HostelExpense::where('status', 'Pending')->count(),
        ];

        return view('hostel.dashboard', compact('stats'));
    }

    /**
     * Display a listing of hostels.
     */
    public function index(Request $request)
    {
        $query = Hostel::query();

        // Filter by type
        if ($request->filled('type')) {
            $query->byType($request->type);
        }

        // Filter by active status
        if ($request->filled('is_active')) {
            $query->where('is_active', $request->is_active);
        }

        // Search by name
        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $hostels = $query->ordered()->paginate(20);

        return view('hostel.index', compact('hostels'));
    }

    /**
     * Show the form for creating a new hostel.
     */
    public function create()
    {
        return view('hostel.create');
    }

    /**
     * Store a newly created hostel in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'type' => 'required|in:Boys,Girls,Junior,Senior',
            'total_capacity' => 'required|integer|min:1',
            'address' => 'nullable|string|max:500',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $validated['created_by'] = Auth::id();
        $validated['is_active'] = $request->has('is_active');

        Hostel::create($validated);

        return redirect()->route('hostel.index')
            ->with('success', 'Hostel created successfully!');
    }

    /**
     * Display the specified hostel.
     */
    public function show(Hostel $hostel)
    {
        $hostel->load(['blocks.floors.rooms.beds', 'wardenAssignments.warden']);
        
        // Calculate statistics
        $stats = [
            'total_blocks' => $hostel->blocks()->count(),
            'total_rooms' => $hostel->blocks()->withCount('floors.rooms')->get()->sum('floors_rooms_count'),
            'total_beds' => $hostel->total_capacity,
            'occupied_beds' => $hostel->total_occupied,
            'available_beds' => $hostel->total_available,
            'occupancy_rate' => $hostel->total_capacity > 0 
                ? round(($hostel->total_occupied / $hostel->total_capacity) * 100, 2) 
                : 0,
        ];

        return view('hostel.show', compact('hostel', 'stats'));
    }

    /**
     * Show the form for editing the specified hostel.
     */
    public function edit(Hostel $hostel)
    {
        return view('hostel.edit', compact('hostel'));
    }

    /**
     * Update the specified hostel in storage.
     */
    public function update(Request $request, Hostel $hostel)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'type' => 'required|in:Boys,Girls,Junior,Senior',
            'total_capacity' => 'required|integer|min:1',
            'address' => 'nullable|string|max:500',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $validated['updated_by'] = Auth::id();
        $validated['is_active'] = $request->has('is_active');

        $hostel->update($validated);

        return redirect()->route('hostel.show', $hostel)
            ->with('success', 'Hostel updated successfully!');
    }

    /**
     * Remove the specified hostel from storage.
     */
    public function destroy(Hostel $hostel)
    {
        try {
            // Check if hostel has active allocations
            $activeAllocations = $hostel->blocks()
                ->withCount(['floors.rooms.beds' => function ($query) {
                    $query->where('is_occupied', true);
                }])
                ->get()
                ->sum('floors_rooms_beds_count');

            if ($activeAllocations > 0) {
                return redirect()->route('hostel.index')
                    ->with('error', "Cannot delete hostel. It has {$activeAllocations} active bed allocation(s).");
            }

            $hostel->delete();

            return redirect()->route('hostel.index')
                ->with('success', 'Hostel deleted successfully!');
        } catch (\Exception $e) {
            return redirect()->route('hostel.index')
                ->with('error', 'Error deleting hostel: ' . $e->getMessage());
        }
    }
}
