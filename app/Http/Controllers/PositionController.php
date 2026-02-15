<?php

namespace App\Http\Controllers;

use App\Models\Position;
use Illuminate\Http\Request;

class PositionController extends Controller
{
    /**
     * Display a listing of the positions.
     */
    public function index()
    {
        $positions = Position::withCount('staff')->orderBy('name')->paginate(20);

        return view('positions.index', compact('positions'));
    }

    /**
     * Show the form for creating a new position.
     */
    public function create()
    {
        return view('positions.create');
    }

    /**
     * Store a newly created position in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:staff_positions,name',
            'description' => 'nullable|string',
        ]);

        try {
            Position::create($request->only(['name', 'description']));
            
            return redirect()->route('positions.index')
                ->with('success', 'Position created successfully.');
        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', 'Failed to create position. Please try again.');
        }
    }

    /**
     * Show the form for editing the specified position.
     */
    public function edit(Position $position)
    {
        return view('positions.edit', compact('position'));
    }

    /**
     * Update the specified position in storage.
     */
    public function update(Request $request, Position $position)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:staff_positions,name,' . $position->id,
            'description' => 'nullable|string',
        ]);

        try {
            $position->update($request->only(['name', 'description']));
            
            return redirect()->route('positions.index')
                ->with('success', 'Position updated successfully.');
        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', 'Failed to update position. Please try again.');
        }
    }

    /**
     * Remove the specified position from storage.
     */
    public function destroy(Position $position)
    {
        try {
            // Check if position has staff members
            if ($position->staff()->count() > 0) {
                return back()->with('error', 'Cannot delete position with existing staff members.');
            }
            
            $position->delete();
            
            return redirect()->route('positions.index')
                ->with('success', 'Position deleted successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to delete position. Please try again.');
        }
    }
}
