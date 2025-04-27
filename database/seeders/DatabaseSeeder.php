<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $lenderRole = Role::firstOrCreate(['name' => 'lender']);

        // Create Admin user
        $admin = User::firstOrCreate([
            'email' => 'admin@bayadbuddy.ph',
        ], [
            'first_name' => 'Admin',
            'middle_name' => '',
            'last_name' => 'User',
            'mobile_number' => '09170000000',
            'password' => Hash::make('password'), // Default password: 'password'
            'email_verified_at' => now(),
        ]);
    }
}
