<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class GlobalPartsSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('global_parts')->insert([
            [
                'part_number' => 'P001',
                'part_name' => 'Brake Pad',
                'price' => 3500.00,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'part_number' => 'P002',
                'part_name' => 'Oil Filter',
                'price' => 1200.00,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'part_number' => 'P003',
                'part_name' => 'Air Filter',
                'price' => 1500.00,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
