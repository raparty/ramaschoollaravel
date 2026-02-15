<?php

namespace App\Http\Controllers;

use App\Models\TransportDriver;
use Illuminate\Http\Request;

/**
 * TransportDriverController
 * 
 * Manages transport drivers
 */
class TransportDriverController extends Controller
{
    /**
     * Display a listing of drivers.
     */
    public function index(Request $request)
    {
        $query = TransportDriver::query()->with('vehicles');

        // Search
        if ($request->filled('search')) {
            $query->search($request->search);
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->status($request->status);
        }

        $drivers = $query->ordered()->paginate(20);

        return view('transport.drivers.index', compact('drivers'));
    }

    /**
     * Show the form for creating a new driver.
     */
    public function create()
    {
        return view('transport.drivers.create');
    }

    /**
     * Store a newly created driver in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'driver_name' => 'required|string|max:100',
            'license_number' => 'nullable|string|max:50',
            'aadhar_number' => 'nullable|string|max:20',
            'contact_number' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'status' => 'required|in:active,inactive',
        ]);

        TransportDriver::create($validated);

        return redirect()->route('transport.drivers.index')
            ->with('success', 'Driver added successfully!');
    }

    /**
     * Show the form for editing the specified driver.
     */
    public function edit(TransportDriver $driver)
    {
        return view('transport.drivers.edit', compact('driver'));
    }

    /**
     * Update the specified driver in storage.
     */
    public function update(Request $request, TransportDriver $driver)
    {
        $validated = $request->validate([
            'driver_name' => 'required|string|max:100',
            'license_number' => 'nullable|string|max:50',
            'aadhar_number' => 'nullable|string|max:20',
            'contact_number' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'status' => 'required|in:active,inactive',
        ]);

        $driver->update($validated);

        return redirect()->route('transport.drivers.index')
            ->with('success', 'Driver updated successfully!');
    }

    /**
     * Remove the specified driver from storage.
     */
    public function destroy(TransportDriver $driver)
    {
        try {
            // Check if driver has assigned vehicles
            $vehicleCount = $driver->vehicles()->count();
            
            if ($vehicleCount > 0) {
                return redirect()->route('transport.drivers.index')
                    ->with('error', "Cannot delete driver. {$vehicleCount} vehicle(s) are assigned to this driver.");
            }

            $driver->delete();

            return redirect()->route('transport.drivers.index')
                ->with('success', 'Driver deleted successfully!');
        } catch (\Exception $e) {
            return redirect()->route('transport.drivers.index')
                ->with('error', 'Error deleting driver: ' . $e->getMessage());
        }
    }
}
