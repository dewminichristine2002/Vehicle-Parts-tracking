<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\BatchPart;

class BatchPartsSeeder extends Seeder
{
    public function run()
    {
        BatchPart::insert([
            [
                'part_number' => 'M1001CAB00411N',
                'part_name' => 'Brake Pad',
                'quantity' => 50,
                'batch_no' => 'BATCH001',
            ],
            [
                'part_number' => 'M1002ENG00982X',
                'part_name' => 'Engine Oil',
                'quantity' => 30,
                'batch_no' => 'BATCH001',
            ],
            [
                'part_number' => 'M1003TIR00333A',
                'part_name' => 'Tire 15"',
                'quantity' => 20,
                'batch_no' => 'BATCH002',
            ],
        ]);
    }
}
