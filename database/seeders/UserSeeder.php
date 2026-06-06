<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // test AdminController
        User::create([
            'name' => 'Admin Utama',
            'email' => 'admin@test.com',
            'password' => 'pass123',
            'role' => 'admin',
            'phone' => '081234567890'
        ]);

        // test StoreController & ProductController (Owner)
        User::create([
            'name' => 'Owner Toko A',
            'email' => 'ownerA@test.com',
            'password' => 'pass123',
            'role' => 'owner',
            'phone' => '081234567891'
        ]);

        User::create([
            'name' => 'Owner Toko B',
            'email' => 'ownerB@test.com',
            'password' => 'pass123',
            'role' => 'owner',
            'phone' => '081234567892'
        ]);

        // testing OrderController & ReviewController (customer)
        User::create([
            'name' => 'Always User Bin User',
            'email' => 'user@tes.com',
            'password' => 'pass123',
            'role' => 'customer',
            'phone' => '081234567893'
        ]);
    }
}
