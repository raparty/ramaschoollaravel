<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Hostel;
use App\Models\HostelBlock;
use App\Models\HostelFloor;
use App\Models\HostelRoom;
use App\Models\HostelBed;
use App\Models\HostelLocker;
use App\Models\HostelFurniture;

class HostelInfrastructureSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create Boys Hostel
        $boysHostel = Hostel::create([
            'name' => 'Boys Hostel - Senior Wing',
            'type' => 'Boys',
            'total_capacity' => 120,
            'address' => 'Near Main Sports Ground',
            'description' => 'Senior boys hostel with modern facilities',
            'is_active' => true,
        ]);

        // Create Girls Hostel
        $girlsHostel = Hostel::create([
            'name' => 'Girls Hostel - Senior Wing',
            'type' => 'Girls',
            'total_capacity' => 100,
            'address' => 'Near Academic Block',
            'description' => 'Senior girls hostel with modern facilities',
            'is_active' => true,
        ]);

        // Create Junior Hostel
        $juniorHostel = Hostel::create([
            'name' => 'Junior Hostel',
            'type' => 'Junior',
            'total_capacity' => 80,
            'address' => 'Near Play Area',
            'description' => 'Junior students hostel',
            'is_active' => true,
        ]);

        // Seed infrastructure for Boys Hostel
        $this->seedHostelInfrastructure($boysHostel, 2, 3, 4); // 2 blocks, 3 floors, 4 rooms per floor

        // Seed infrastructure for Girls Hostel  
        $this->seedHostelInfrastructure($girlsHostel, 2, 3, 3); // 2 blocks, 3 floors, 3 rooms per floor

        // Seed infrastructure for Junior Hostel
        $this->seedHostelInfrastructure($juniorHostel, 1, 2, 4); // 1 block, 2 floors, 4 rooms per floor
    }

    /**
     * Seed hostel infrastructure (blocks, floors, rooms, beds, etc.)
     */
    private function seedHostelInfrastructure(Hostel $hostel, int $blockCount, int $floorCount, int $roomCount)
    {
        for ($b = 1; $b <= $blockCount; $b++) {
            $block = HostelBlock::create([
                'hostel_id' => $hostel->id,
                'name' => 'Block ' . chr(64 + $b), // Block A, Block B, etc.
                'total_floors' => $floorCount,
                'description' => 'Block ' . chr(64 + $b) . ' of ' . $hostel->name,
                'is_active' => true,
            ]);

            for ($f = 1; $f <= $floorCount; $f++) {
                $floor = HostelFloor::create([
                    'block_id' => $block->id,
                    'floor_number' => $f,
                    'name' => 'Floor ' . $f,
                    'description' => 'Floor ' . $f . ' of ' . $block->name,
                    'is_active' => true,
                ]);

                for ($r = 1; $r <= $roomCount; $r++) {
                    $roomTypes = ['Double', 'Triple', 'Triple', 'Dormitory'];
                    $roomType = $roomTypes[array_rand($roomTypes)];
                    $maxStrength = match($roomType) {
                        'Single' => 1,
                        'Double' => 2,
                        'Triple' => 3,
                        'Dormitory' => 6,
                        default => 2,
                    };

                    $roomNumber = $b . $f . str_pad($r, 2, '0', STR_PAD_LEFT);
                    
                    $room = HostelRoom::create([
                        'floor_id' => $floor->id,
                        'room_number' => $roomNumber,
                        'room_type' => $roomType,
                        'max_strength' => $maxStrength,
                        'area_sqft' => rand(120, 250),
                        'has_attached_bathroom' => rand(0, 1) == 1,
                        'description' => 'Room ' . $roomNumber,
                        'is_active' => true,
                    ]);

                    // Create beds for the room
                    for ($bed = 1; $bed <= $maxStrength; $bed++) {
                        HostelBed::create([
                            'room_id' => $room->id,
                            'bed_number' => $roomNumber . '-B' . $bed,
                            'qr_code' => 'BED-' . $hostel->id . '-' . $roomNumber . '-B' . $bed,
                            'condition_status' => 'Good',
                            'notes' => null,
                            'is_occupied' => false,
                            'is_active' => true,
                        ]);
                    }

                    // Create lockers for the room
                    for ($locker = 1; $locker <= $maxStrength; $locker++) {
                        HostelLocker::create([
                            'room_id' => $room->id,
                            'locker_number' => $roomNumber . '-L' . $locker,
                            'qr_code' => 'LOC-' . $hostel->id . '-' . $roomNumber . '-L' . $locker,
                            'condition_status' => 'Good',
                            'has_key' => true,
                            'notes' => null,
                            'is_assigned' => false,
                            'is_active' => true,
                        ]);
                    }

                    // Create common furniture items
                    $furnitureItems = [
                        ['item_name' => 'Study Table', 'furniture_type' => 'Table', 'quantity' => $maxStrength],
                        ['item_name' => 'Study Chair', 'furniture_type' => 'Chair', 'quantity' => $maxStrength],
                        ['item_name' => 'Cupboard', 'furniture_type' => 'Cupboard', 'quantity' => $maxStrength],
                        ['item_name' => 'Ceiling Fan', 'furniture_type' => 'Fan', 'quantity' => 2],
                        ['item_name' => 'Tube Light', 'furniture_type' => 'Light', 'quantity' => 2],
                    ];

                    foreach ($furnitureItems as $index => $item) {
                        HostelFurniture::create([
                            'room_id' => $room->id,
                            'asset_code' => 'FRN-' . $hostel->id . '-' . $roomNumber . '-' . ($index + 1),
                            'item_name' => $item['item_name'],
                            'furniture_type' => $item['furniture_type'],
                            'quantity' => $item['quantity'],
                            'condition_status' => 'Good',
                            'purchase_date' => now()->subMonths(rand(6, 24)),
                            'purchase_value' => rand(500, 5000),
                            'notes' => null,
                            'is_active' => true,
                        ]);
                    }
                }
            }
        }
    }
}
