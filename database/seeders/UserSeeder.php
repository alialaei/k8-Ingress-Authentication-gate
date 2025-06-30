<?php

namespace Database\Seeders;


use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'name' => 'Ali Alaei',
            'email' => 'alialaey@gmail.com',
            'password' => Hash::make('password'), // Use bcrypt for hashing the password
        ]);
    }
}
