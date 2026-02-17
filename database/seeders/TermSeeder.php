<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class TermSeeder extends Seeder
{
    /**
     * Seed the application's database with a default academic term.
     * 
     * This is required for the exams module to work.
     * Without at least one term, users cannot create exams.
     */
    public function run(): void
    {
        // Check if terms already exist
        if (DB::table('terms')->count() > 0) {
            $this->command->info('Terms already exist. Skipping seeder.');
            return;
        }

        // Create a default academic term for current year
        $currentYear = Carbon::now()->year;
        $nextYear = $currentYear + 1;

        DB::table('terms')->insert([
            [
                'name' => "$currentYear-$nextYear",
                'start_date' => "$currentYear-04-01",
                'end_date' => "$nextYear-03-31",
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        $this->command->info('Default academic term created successfully!');
        $this->command->info("Term: $currentYear-$nextYear");
    }
}
