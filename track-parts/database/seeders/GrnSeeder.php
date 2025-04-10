<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class GrnSeeder extends Seeder
{
    public function run(): void
    {
        // First GRN
        $grn1 = DB::table('grns')->insertGetId([
            'grn_number' => 'GRN1001',
            'invoice_number' => 'INV1001',
            'grn_date' => '2025-04-10',
            'dealer_id' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('grn_items')->insert([
            [
                'grn_id' => $grn1,
                'global_part_id' => 1,
                'quantity' => 10,
                'grn_unit_price' => 1200.00,
                'dealer_id' => 1, // ✅ added
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'grn_id' => $grn1,
                'global_part_id' => 2,
                'quantity' => 5,
                'grn_unit_price' => 3500.00,
                'dealer_id' => 1, // ✅ added
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ]);

        // Second GRN
        $grn2 = DB::table('grns')->insertGetId([
            'grn_number' => 'GRN1002',
            'invoice_number' => 'INV1002',
            'grn_date' => '2025-04-11',
            'dealer_id' => 2,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('grn_items')->insert([
            [
                'grn_id' => $grn2,
                'global_part_id' => 1,
                'quantity' => 20,
                'grn_unit_price' => 1150.00,
                'dealer_id' => 2, // ✅ added
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'grn_id' => $grn2,
                'global_part_id' => 3,
                'quantity' => 15,
                'grn_unit_price' => 1800.00,
                'dealer_id' => 2, // ✅ added
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ]);

        // Third GRN
        $grn3 = DB::table('grns')->insertGetId([
            'grn_number' => 'GRN1003',
            'invoice_number' => 'INV1003',
            'grn_date' => '2025-04-12',
            'dealer_id' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('grn_items')->insert([
            [
                'grn_id' => $grn3,
                'global_part_id' => 2,
                'quantity' => 8,
                'grn_unit_price' => 3400.00,
                'dealer_id' => 1, // ✅ added
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'grn_id' => $grn3,
                'global_part_id' => 3,
                'quantity' => 12,
                'grn_unit_price' => 2200.00,
                'dealer_id' => 1, // ✅ added
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ]);
    }
}
