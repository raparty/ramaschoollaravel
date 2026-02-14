<?php

namespace App\Http\Controllers;

use App\Models\FeePackage;
use App\Http\Requests\StoreFeePackageRequest;
use App\Http\Requests\UpdateFeePackageRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * FeePackageController
 * 
 * Handles fee package management operations
 * Converts: add_fees_package.php, fees_package.php, delete_fees_package.php
 */
class FeePackageController extends Controller
{
    /**
     * Display a listing of fee packages.
     * Converts: fees_package.php
     */
    public function index(Request $request)
    {
        // Check permission
        $this->authorize('view-fees');

        $query = FeePackage::query();

        // Search functionality
        if ($request->filled('search')) {
            $query->search($request->search);
        }

        // Order by name
        $query->ordered();

        // Paginate results
        $packages = $query->paginate(20)->withQueryString();

        return view('fees.packages.index', compact('packages'));
    }

    /**
     * Show the form for creating a new fee package.
     * Converts: add_fees_package.php (form)
     */
    public function create()
    {
        // Check permission
        $this->authorize('create-fees');

        return view('fees.packages.create');
    }

    /**
     * Store a newly created fee package in database.
     * Converts: add_fees_package.php (processing)
     */
    public function store(StoreFeePackageRequest $request)
    {
        // Check permission
        $this->authorize('create-fees');

        DB::beginTransaction();
        
        try {
            // Get validated data
            $validated = $request->validated();

            // Create fee package
            $package = FeePackage::create($validated);

            DB::commit();

            return redirect()
                ->route('fee-packages.index')
                ->with('success', 'Fee package created successfully!');

        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Failed to create fee package: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified fee package.
     */
    public function show(FeePackage $feePackage)
    {
        // Check permission
        $this->authorize('view-fees');

        return view('fees.packages.show', compact('feePackage'));
    }

    /**
     * Show the form for editing the specified fee package.
     */
    public function edit(FeePackage $feePackage)
    {
        // Check permission
        $this->authorize('edit-fees');

        return view('fees.packages.edit', compact('feePackage'));
    }

    /**
     * Update the specified fee package in database.
     */
    public function update(UpdateFeePackageRequest $request, FeePackage $feePackage)
    {
        // Check permission
        $this->authorize('edit-fees');

        DB::beginTransaction();
        
        try {
            // Get validated data
            $validated = $request->validated();

            // Update fee package
            $feePackage->update($validated);

            DB::commit();

            return redirect()
                ->route('fee-packages.index')
                ->with('success', 'Fee package updated successfully!');

        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Failed to update fee package: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified fee package from database.
     * Converts: delete_fees_package.php
     */
    public function destroy(FeePackage $feePackage)
    {
        // Check permission
        $this->authorize('delete-fees');

        DB::beginTransaction();
        
        try {
            // TODO: Check if package is assigned to any students
            // For now, we'll allow deletion
            
            $feePackage->delete();

            DB::commit();

            return redirect()
                ->route('fee-packages.index')
                ->with('success', 'Fee package deleted successfully!');

        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()
                ->back()
                ->with('error', 'Failed to delete fee package: ' . $e->getMessage());
        }
    }
}
