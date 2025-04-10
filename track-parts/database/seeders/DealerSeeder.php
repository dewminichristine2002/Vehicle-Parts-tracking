<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\Dealer;

class DealerSeeder extends Seeder
{
    public function run(): void
    {
        Dealer::create([
            'name' => 'Ajith',
            'company_name' => 'Ajith spare parts',
            'email' => 'ajith@example.com',
            'password' => Hash::make('ajith123'), // or use bcrypt('...')
            'registered_at' => now(),
        ]);

        Dealer::create([
            'name' => 'Darshani',
            'company_name' => 'Darshani Motors',
            'email' => 'darshani@example.com',
            'password' => Hash::make('darshani123'),
            'registered_at' => now(),
        ]);

        Dealer::create([
            'name' => 'Ravi',
            'company_name' => 'Ravi Traders',
            'email' => 'ravi@example.com',
            'password' => Hash::make('ravi123'),
            'registered_at' => now(),
        ]);
    }
}
