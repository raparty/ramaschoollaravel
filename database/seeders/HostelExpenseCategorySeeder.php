<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\HostelExpenseCategory;

class HostelExpenseCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Food & Groceries',
                'description' => 'Expenses related to food, groceries, and snacks',
                'requires_approval' => false,
                'is_active' => true,
            ],
            [
                'name' => 'Medical & Healthcare',
                'description' => 'Medical expenses, medicines, and healthcare costs',
                'requires_approval' => true,
                'is_active' => true,
            ],
            [
                'name' => 'Personal Care',
                'description' => 'Personal care items, toiletries, etc.',
                'requires_approval' => false,
                'is_active' => true,
            ],
            [
                'name' => 'Laundry',
                'description' => 'Laundry and dry cleaning expenses',
                'requires_approval' => false,
                'is_active' => true,
            ],
            [
                'name' => 'Stationery',
                'description' => 'Stationery and school supplies',
                'requires_approval' => false,
                'is_active' => true,
            ],
            [
                'name' => 'Transportation',
                'description' => 'Local transport and travel expenses',
                'requires_approval' => true,
                'is_active' => true,
            ],
            [
                'name' => 'Entertainment',
                'description' => 'Movies, games, and entertainment',
                'requires_approval' => true,
                'is_active' => true,
            ],
            [
                'name' => 'Emergency',
                'description' => 'Emergency and urgent expenses',
                'requires_approval' => true,
                'is_active' => true,
            ],
            [
                'name' => 'Other',
                'description' => 'Miscellaneous expenses',
                'requires_approval' => true,
                'is_active' => true,
            ],
        ];

        foreach ($categories as $category) {
            HostelExpenseCategory::create($category);
        }
    }
}
