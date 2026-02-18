<?php

namespace App\Http\Controllers;

use App\Models\HostelRoom;
use App\Models\HostelFloor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * HostelRoomController
 * 
 * Manages hostel room operations
 */
class HostelRoomController extends Controller
{
    /**
     * Display a listing of rooms.
     */
    public function index(Request $request)
    {
        $query = HostelRoom::query()->with(['floor.block.hostel']);

        // Filter by floor
        if ($request->filled('floor_id')) {
            $query->where('floor_id', $request->floor_id);
        }

        // Filter by room type
        if ($request->filled('room_type')) {
            $query->byType($request->room_type);
        }

        // Filter by active status
        if ($request->filled('is_active')) {
            $query->where('is_active', $request->is_active);
        }

        // Search by room number
        if ($request->filled('search')) {
            $query->where('room_number', 'like', '%' . $request->search . '%');
        }

        $rooms = $query->ordered()->paginate(20);

        return view('hostel.rooms.index', compact('rooms'));
    }

    /**
     * Show the form for creating a new room.
     */
    public function create(Request $request)
    {
        $floors = HostelFloor::active()->with('block.hostel')->ordered()->get();
        $floorId = $request->get('floor_id');
        
        return view('hostel.rooms.create', compact('floors', 'floorId'));
    }

    /**
     * Store a newly created room in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'floor_id' => 'required|exists:hostel_floors,id',
            'room_number' => 'required|string|max:50',
            'room_type' => 'required|in:Single,Double,Triple,Dormitory',
            'max_strength' => 'required|integer|min:1|max:50',
            'area_sqft' => 'nullable|numeric|min:0',
            'has_attached_bathroom' => 'boolean',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $validated['created_by'] = Auth::id();
        $validated['has_attached_bathroom'] = $request->has('has_attached_bathroom');
        $validated['is_active'] = $request->has('is_active');

        HostelRoom::create($validated);

        return redirect()->route('hostel.rooms.index')
            ->with('success', 'Room created successfully!');
    }

    /**
     * Display the specified room.
     */
    public function show(HostelRoom $room)
    {
        $room->load(['floor.block.hostel', 'beds', 'lockers', 'furniture']);
        
        // Calculate statistics
        $stats = [
            'total_beds' => $room->beds()->count(),
            'occupied_beds' => $room->current_occupancy,
            'available_beds' => $room->available_beds,
            'total_lockers' => $room->lockers()->count(),
            'assigned_lockers' => $room->lockers()->where('is_assigned', true)->count(),
            'available_lockers' => $room->lockers()->count() - $room->lockers()->where('is_assigned', true)->count(),
            'total_furniture' => $room->furniture()->count(),
            'occupancy_rate' => $room->max_strength > 0 ? ($room->current_occupancy / $room->max_strength) * 100 : 0,
        ];

        return view('hostel.rooms.show', compact('room', 'stats'));
    }

    /**
     * Show the form for editing the specified room.
     */
    public function edit(HostelRoom $room)
    {
        $floors = HostelFloor::active()->with('block.hostel')->ordered()->get();
        
        return view('hostel.rooms.edit', compact('room', 'floors'));
    }

    /**
     * Update the specified room in storage.
     */
    public function update(Request $request, HostelRoom $room)
    {
        $validated = $request->validate([
            'floor_id' => 'required|exists:hostel_floors,id',
            'room_number' => 'required|string|max:50',
            'room_type' => 'required|in:Single,Double,Triple,Dormitory',
            'max_strength' => 'required|integer|min:1|max:50',
            'area_sqft' => 'nullable|numeric|min:0',
            'has_attached_bathroom' => 'boolean',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        // Validate that max_strength is not less than current occupancy
        if ($validated['max_strength'] < $room->current_occupancy) {
            return back()->withErrors([
                'max_strength' => "Cannot reduce max strength below current occupancy ({$room->current_occupancy})"
            ])->withInput();
        }

        // Validate that max_strength is not less than number of beds
        $totalBeds = $room->beds()->count();
        if ($validated['max_strength'] < $totalBeds) {
            return back()->withErrors([
                'max_strength' => "Cannot reduce max strength below number of beds ({$totalBeds})"
            ])->withInput();
        }

        $validated['updated_by'] = Auth::id();
        $validated['has_attached_bathroom'] = $request->has('has_attached_bathroom');
        $validated['is_active'] = $request->has('is_active');

        $room->update($validated);

        return redirect()->route('hostel.rooms.show', $room)
            ->with('success', 'Room updated successfully!');
    }

    /**
     * Remove the specified room from storage.
     */
    public function destroy(HostelRoom $room)
    {
        try {
            // Check if room has occupied beds
            $occupiedBeds = $room->beds()->where('is_occupied', true)->count();
            
            if ($occupiedBeds > 0) {
                return redirect()->route('hostel.rooms.index')
                    ->with('error', "Cannot delete room. It has {$occupiedBeds} occupied bed(s).");
            }

            $room->delete();

            return redirect()->route('hostel.rooms.index')
                ->with('success', 'Room deleted successfully!');
        } catch (\Exception $e) {
            return redirect()->route('hostel.rooms.index')
                ->with('error', 'Error deleting room: ' . $e->getMessage());
        }
    }
}
