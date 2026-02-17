<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\HostelWarden;

class HostelWardenSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $wardens = [
            [
                'name' => 'Mr. Rajesh Kumar',
                'employee_code' => 'WRD001',
                'email' => 'rajesh.kumar@school.edu',
                'phone' => '9876543210',
                'address' => '123 Staff Quarters',
                'gender' => 'Male',
                'date_of_joining' => '2020-01-15',
                'status' => 'Active',
                'notes' => 'Senior warden with 5 years experience',
                'is_active' => true,
            ],
            [
                'name' => 'Mrs. Priya Sharma',
                'employee_code' => 'WRD002',
                'email' => 'priya.sharma@school.edu',
                'phone' => '9876543211',
                'address' => '124 Staff Quarters',
                'gender' => 'Female',
                'date_of_joining' => '2021-03-20',
                'status' => 'Active',
                'notes' => 'Girls hostel warden',
                'is_active' => true,
            ],
            [
                'name' => 'Mr. Anil Verma',
                'employee_code' => 'WRD003',
                'email' => 'anil.verma@school.edu',
                'phone' => '9876543212',
                'address' => '125 Staff Quarters',
                'gender' => 'Male',
                'date_of_joining' => '2019-07-10',
                'status' => 'Active',
                'notes' => 'Boys hostel warden',
                'is_active' => true,
            ],
            [
                'name' => 'Mrs. Sunita Patel',
                'employee_code' => 'WRD004',
                'email' => 'sunita.patel@school.edu',
                'phone' => '9876543213',
                'address' => '126 Staff Quarters',
                'gender' => 'Female',
                'date_of_joining' => '2022-01-05',
                'status' => 'Active',
                'notes' => 'Junior hostel warden',
                'is_active' => true,
            ],
        ];

        foreach ($wardens as $warden) {
            HostelWarden::create($warden);
        }
    }
}
