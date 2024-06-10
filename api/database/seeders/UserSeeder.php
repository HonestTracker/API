<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */

     // php artisan db:seed --class=UserSeeder
    public function run(): void
    {
        DB::table('users')->insert([
            'name' => 'honestadmin', 
            'email' => 'admin@honesttracker.nl', 
            'email_verified_at' => now(),
            'password' => Hash::make('Honesttracker123!'), 
            'is_admin' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
