<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\VehiclePart;

class VehiclePartsSeeder extends Seeder
{
    public function run()
    {
        VehiclePart::insert([
            [
                'part_number' => 'M1001CAB00411N',
                'part_name' => 'Brake Pad',
                'unit_price' => 1200.50,
            ],
            [
                'part_number' => 'M1002ENG00982X',
                'part_name' => 'Engine Oil',
                'unit_price' => 850.00,
            ],
            [
                'part_number' => 'M1003TIR00333A',
                'part_name' => 'Tire 15"',
                'unit_price' => 3000.00,
            ],
        ]);
    }
}

