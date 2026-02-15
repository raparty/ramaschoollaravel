<?php

namespace App\Http\Controllers;

use App\Models\TransportVehicle;
use App\Models\TransportRoute;
use App\Models\TransportDriver;
use Illuminate\Http\Request;

/**
 * TransportVehicleController
 * 
 * Manages transport vehicles
 */
class TransportVehicleController extends Controller
{
    /**
     * Display a listing of vehicles.
     */
    public function index(Request $request)
    {
        $query = TransportVehicle::query()->with('driver');

        // Search
        if ($request->filled('search')) {
            $query->search($request->search);
        }

        $vehicles = $query->ordered()->paginate(20);

        return view('transport.vehicles.index', compact('vehicles'));
    }

    /**
     * Show the form for creating a new vehicle.
     */
    public function create()
    {
        $routes = TransportRoute::ordered()->get();
        $drivers = TransportDriver::where('status', 'active')->ordered()->get();
        return view('transport.vehicles.create', compact('routes', 'drivers'));
    }

    /**
     * Store a newly created vehicle in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'vechile_no' => 'required|string|max:100|unique:transport_add_vechile,vechile_no',
            'no_of_seats' => 'required|integer|min:1|max:100',
            'route_id' => 'nullable|array',
            'route_id.*' => 'exists:transport_add_route,route_id',
            'driver_id' => 'nullable|exists:transport_drivers,driver_id',
            'insurance_number' => 'nullable|string|max:100',
            'insurance_expiry' => 'nullable|date',
            'permit_number' => 'nullable|string|max:100',
            'permit_expiry' => 'nullable|date',
        ]);

        // Convert route array to comma-separated string (legacy format)
        if (!empty($validated['route_id'])) {
            $validated['route_id'] = implode(',', $validated['route_id']);
        } else {
            $validated['route_id'] = null;
        }

        TransportVehicle::create($validated);

        return redirect()->route('transport.vehicles.index')
            ->with('success', 'Vehicle added successfully!');
    }

    /**
     * Show the form for editing the specified vehicle.
     */
    public function edit(TransportVehicle $vehicle)
    {
        $routes = TransportRoute::ordered()->get();
        $drivers = TransportDriver::where('status', 'active')->ordered()->get();
        
        // Convert comma-separated route IDs to array for checkbox selection
        $selectedRoutes = !empty($vehicle->route_id) ? explode(',', $vehicle->route_id) : [];
        
        return view('transport.vehicles.edit', compact('vehicle', 'routes', 'drivers', 'selectedRoutes'));
    }

    /**
     * Update the specified vehicle in storage.
     */
    public function update(Request $request, TransportVehicle $vehicle)
    {
        $validated = $request->validate([
            'vechile_no' => 'required|string|max:100|unique:transport_add_vechile,vechile_no,' . $vehicle->vechile_id . ',vechile_id',
            'no_of_seats' => 'required|integer|min:1|max:100',
            'route_id' => 'nullable|array',
            'route_id.*' => 'exists:transport_add_route,route_id',
            'driver_id' => 'nullable|exists:transport_drivers,driver_id',
            'insurance_number' => 'nullable|string|max:100',
            'insurance_expiry' => 'nullable|date',
            'permit_number' => 'nullable|string|max:100',
            'permit_expiry' => 'nullable|date',
        ]);

        // Convert route array to comma-separated string (legacy format)
        if (!empty($validated['route_id'])) {
            $validated['route_id'] = implode(',', $validated['route_id']);
        } else {
            $validated['route_id'] = null;
        }

        $vehicle->update($validated);

        return redirect()->route('transport.vehicles.index')
            ->with('success', 'Vehicle updated successfully!');
    }

    /**
     * Remove the specified vehicle from storage.
     */
    public function destroy(TransportVehicle $vehicle)
    {
        try {
            // Check if vehicle has assigned students
            $studentCount = $vehicle->studentAssignments()->count();
            
            if ($studentCount > 0) {
                return redirect()->route('transport.vehicles.index')
                    ->with('error', "Cannot delete vehicle. It has {$studentCount} student(s) assigned.");
            }

            $vehicle->delete();

            return redirect()->route('transport.vehicles.index')
                ->with('success', 'Vehicle deleted successfully!');
        } catch (\Exception $e) {
            return redirect()->route('transport.vehicles.index')
                ->with('error', 'Error deleting vehicle: ' . $e->getMessage());
        }
    }
}
