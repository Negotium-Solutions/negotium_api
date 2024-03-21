<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run($domain): void
    {
        User::factory([
            'first_name' => 'Admin',
            'last_name' => 'User',
            'email' => 'admin@'.$domain,
            'password' => Hash::make('password')
        ])->create();

        User::factory(4)->domain($domain)->create();
    }
}
