<?php

namespace Database\Seeders;


// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;



class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // \App\Models\User::factory(10)->create();

        \App\Models\User::factory()->create([
            'name' => 'Mehdi',
            'email' => 'majmehdi12@gmail.com',
            'password' => Hash::make('123456'),
            'phone' => '1234567890',
            'role' => 'admin',
        ]);
    }
}
