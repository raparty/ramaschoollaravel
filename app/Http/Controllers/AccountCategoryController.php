<?php

namespace App\Http\Controllers;

use App\Models\AccountCategory;
use App\Http\Requests\StoreAccountCategoryRequest;
use Illuminate\Http\Request;

class AccountCategoryController extends Controller
{
    /**
     * Display a listing of the categories.
     */
    public function index(Request $request)
    {
        $type = $request->get('type');
        
        $categories = AccountCategory::query()
            ->when($type, fn($q) => $q->where('type', $type))
            ->orderBy('name')
            ->paginate(20);

        return view('categories.index', compact('categories'));
    }

    /**
     * Show the form for creating a new category.
     */
    public function create()
    {
        return view('categories.create');
    }

    /**
     * Store a newly created category in storage.
     */
    public function store(StoreAccountCategoryRequest $request)
    {
        try {
            AccountCategory::create($request->validated());
            
            return redirect()->route('categories.index')
                ->with('success', 'Category created successfully.');
        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', 'Failed to create category. Please try again.');
        }
    }

    /**
     * Show the form for editing the specified category.
     */
    public function edit(AccountCategory $category)
    {
        return view('categories.edit', compact('category'));
    }

    /**
     * Update the specified category in storage.
     */
    public function update(StoreAccountCategoryRequest $request, AccountCategory $category)
    {
        try {
            $category->update($request->validated());
            
            return redirect()->route('categories.index')
                ->with('success', 'Category updated successfully.');
        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', 'Failed to update category. Please try again.');
        }
    }

    /**
     * Remove the specified category from storage.
     */
    public function destroy(AccountCategory $category)
    {
        try {
            // Check if category has income or expense records
            if ($category->incomes()->count() > 0 || $category->expenses()->count() > 0) {
                return back()->with('error', 'Cannot delete category with existing income or expense records.');
            }
            
            $category->delete();
            
            return redirect()->route('categories.index')
                ->with('success', 'Category deleted successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to delete category. Please try again.');
        }
    }

    /**
     * Toggle the active status of a category.
     */
    public function toggleStatus(AccountCategory $category)
    {
        try {
            $category->update(['is_active' => !$category->is_active]);
            
            $status = $category->is_active ? 'activated' : 'deactivated';
            
            return back()->with('success', "Category {$status} successfully.");
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to update category status. Please try again.');
        }
    }
}
