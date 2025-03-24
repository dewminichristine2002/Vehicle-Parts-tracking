<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Batch;

class BatchesSeeder extends Seeder
{
    public function run()
    {
        Batch::insert([
            [
                'batch_no' => 'BATCH001',
                'bill_no' => 'BILL1001',
                'received_date' => '2025-03-20',
            ],
            [
                'batch_no' => 'BATCH002',
                'bill_no' => 'BILL1002',
                'received_date' => '2025-03-21',
            ]
        ]);
    }
}
