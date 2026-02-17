<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

/**
 * Main Hostel Seeder
 * 
 * Seeds all hostel-related data
 */
class HostelSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->call([
            HostelInfrastructureSeeder::class,
            HostelWardenSeeder::class,
            HostelFeeStructureSeeder::class,
            HostelExpenseCategorySeeder::class,
        ]);
    }
}
