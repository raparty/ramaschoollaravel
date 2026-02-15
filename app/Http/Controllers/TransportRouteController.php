<?php

namespace App\Http\Controllers;

use App\Models\TransportRoute;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * TransportRouteController
 * 
 * Manages transport routes/destinations
 */
class TransportRouteController extends Controller
{
    /**
     * Display a listing of routes.
     */
    public function index(Request $request)
    {
        $query = TransportRoute::query();

        // Search
        if ($request->filled('search')) {
            $query->search($request->search);
        }

        $routes = $query->ordered()->paginate(20);

        return view('transport.routes.index', compact('routes'));
    }

    /**
     * Show the form for creating a new route.
     */
    public function create()
    {
        return view('transport.routes.create');
    }

    /**
     * Store a newly created route in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'route_name' => 'required|string|max:255|unique:transport_add_route,route_name',
            'cost' => 'required|numeric|min:0|max:999999.99',
        ]);

        TransportRoute::create($validated);

        return redirect()->route('transport.routes.index')
            ->with('success', 'Route added successfully!');
    }

    /**
     * Show the form for editing the specified route.
     */
    public function edit(TransportRoute $route)
    {
        return view('transport.routes.edit', compact('route'));
    }

    /**
     * Update the specified route in storage.
     */
    public function update(Request $request, TransportRoute $route)
    {
        $validated = $request->validate([
            'route_name' => 'required|string|max:255|unique:transport_add_route,route_name,' . $route->route_id . ',route_id',
            'cost' => 'required|numeric|min:0|max:999999.99',
        ]);

        $route->update($validated);

        return redirect()->route('transport.routes.index')
            ->with('success', 'Route updated successfully!');
    }

    /**
     * Remove the specified route from storage.
     */
    public function destroy(TransportRoute $route)
    {
        try {
            // Check if route has assigned students
            $studentCount = $route->studentAssignments()->count();
            
            if ($studentCount > 0) {
                return redirect()->route('transport.routes.index')
                    ->with('error', "Cannot delete route. It has {$studentCount} student(s) assigned.");
            }

            $route->delete();

            return redirect()->route('transport.routes.index')
                ->with('success', 'Route deleted successfully!');
        } catch (\Exception $e) {
            return redirect()->route('transport.routes.index')
                ->with('error', 'Error deleting route: ' . $e->getMessage());
        }
    }
}
