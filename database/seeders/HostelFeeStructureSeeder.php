<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\HostelFeeStructure;
use App\Models\Hostel;

class HostelFeeStructureSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $hostels = Hostel::all();

        foreach ($hostels as $hostel) {
            // Common fee structures for all hostels
            $feeStructures = [
                [
                    'fee_name' => 'Monthly Accommodation Fee',
                    'amount' => 5000.00,
                    'fee_type' => 'Monthly',
                    'category' => 'Accommodation',
                    'description' => 'Monthly accommodation charges',
                    'is_mandatory' => true,
                    'is_active' => true,
                ],
                [
                    'fee_name' => 'Monthly Food & Mess Fee',
                    'amount' => 4000.00,
                    'fee_type' => 'Monthly',
                    'category' => 'Food',
                    'description' => 'Monthly food and mess charges',
                    'is_mandatory' => true,
                    'is_active' => true,
                ],
                [
                    'fee_name' => 'Quarterly Maintenance Fee',
                    'amount' => 3000.00,
                    'fee_type' => 'Quarterly',
                    'category' => 'Maintenance',
                    'description' => 'Quarterly maintenance and upkeep charges',
                    'is_mandatory' => true,
                    'is_active' => true,
                ],
                [
                    'fee_name' => 'Security Deposit',
                    'amount' => 10000.00,
                    'fee_type' => 'One Time',
                    'category' => 'Security Deposit',
                    'description' => 'Refundable security deposit (one-time)',
                    'is_mandatory' => true,
                    'is_active' => true,
                ],
                [
                    'fee_name' => 'Admission Fee',
                    'amount' => 2000.00,
                    'fee_type' => 'One Time',
                    'category' => 'Other',
                    'description' => 'Hostel admission fee (one-time)',
                    'is_mandatory' => true,
                    'is_active' => true,
                ],
                [
                    'fee_name' => 'Annual Development Fee',
                    'amount' => 5000.00,
                    'fee_type' => 'Yearly',
                    'category' => 'Other',
                    'description' => 'Annual infrastructure development fee',
                    'is_mandatory' => false,
                    'is_active' => true,
                ],
            ];

            foreach ($feeStructures as $fee) {
                HostelFeeStructure::create([
                    'hostel_id' => $hostel->id,
                    'fee_name' => $fee['fee_name'],
                    'amount' => $fee['amount'],
                    'fee_type' => $fee['fee_type'],
                    'category' => $fee['category'],
                    'description' => $fee['description'],
                    'is_mandatory' => $fee['is_mandatory'],
                    'is_active' => $fee['is_active'],
                ]);
            }
        }
    }
}
