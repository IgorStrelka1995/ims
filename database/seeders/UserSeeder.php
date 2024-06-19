<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::factory()->withRole('admin')->create();

        User::factory(3)->withRole('inventory-manager')->create();

        User::factory(6)->withRole('viewer')->create();
    }
}
