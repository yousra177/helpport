<?php

namespace Database\Seeders;

use App\Models\Problem;
use App\Models\Problems;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create users
        $admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin@gmail.com',
            'phone_number' => '055555555',
            'password' => Hash::make('12345678'),
            'role' => 'admin',
            'departement' => 'general',
        ]);

        $it_user = User::create([
            'name' => 'IT User',
            'email' => 'user.deeis@gmail.com',
            'phone_number' => '055555555',
            'password' => Hash::make('12345678'),
            'role' => 'it_user',
            'departement' => 'deeis',
        ]);

        User::create([
            'name' => 'Head of Department',
            'email' => 'hod@gmail.com',
            'phone_number' => '055555555',
            'password' => Hash::make('12345678'),
            'role' => 'chef_dep',
            'departement' => 'deeis',
        ]);

        User::create([
            'name' => 'IT User dadam',
            'email' => 'user@gmail.com',
            'phone_number' => '055555555',
            'password' => Hash::make('12345678'),
            'role' => 'it_user',
            'departement' => 'dadam',
        ]);

        User::create([
            'name' => 'Head of Department dadam',
            'email' => 'head.dadam@gmail.com',
            'phone_number' => '055555555',
            'password' => Hash::make('12345678'),
            'role' => 'chef_dep',
            'departement' => 'dadam',
        ]);

        User::create([
            'name' => 'IT User dda',
            'email' => 'user.dda@gmail.com',
            'phone_number' => '055555555',
            'password' => Hash::make('12345678'),
            'role' => 'it_user',
            'departement' => 'dda',
        ]);

        User::create([
            'name' => 'Head of Department dda',
            'email' => 'head.dda@gmail.com',
            'phone_number' => '055555555',
            'password' => Hash::make('12345678'),
            'role' => 'chef_dep',
            'departement' => 'dda',
        ]);

        User::create([
            'name' => 'Head of Department diei',
            'email' => 'head.diei@gmail.com',
            'phone_number' => '055555555',
            'password' => Hash::make('12345678'),
            'role' => 'chef_dep',
            'departement' => 'diei',
        ]);

        User::create([
            'name' => 'IT User diei',
            'email' => 'user.diei@gmail.com',
            'phone_number' => '055555555',
            'password' => Hash::make('12345678'),
            'role' => 'it_user',
            'departement' => 'diei',
        ]);

        // Seed problems
        Problems::create([
            'title' => 'System Crash Issue',
            'description' => 'The system crashes when processing large data sets.',
            'type' => 'Software Application Issues',
            'user_id' => 2, // Assigned to IT user
        ]);

        Problems::create([
            'title' => 'Login Issue',
            'description' => 'Users are unable to log in after a recent update.',
            'type' => 'Software Application Issues',
            'user_id' => 4, // Assigned to IT user
        ]);
    }
}
