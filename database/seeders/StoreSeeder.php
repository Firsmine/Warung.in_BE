<?php

namespace Database\Seeders;

use App\Models\Store;
use App\Models\User;
use Illuminate\Database\Seeder;

class StoreSeeder extends Seeder
{
    public function run(): void
    {
        $ownerA = User::where('email', 'ownerA@test.com')->first();
        $ownerB = User::where('email', 'ownerB@test.com')->first();

        // Toko aktif (StoreController index)
        Store::create([
            'user_id' => $ownerA->id,
            'name' => 'Berkah Jaya Elektronik',
            'description' => 'Menyediakan gadget dan komponen elektronik terlengkap.',
            'address' => 'Jl. Technopreneur No. 45',
            'category' => 'elektronik',
            'logo'=>'',
            'is_open' => true,
        ]);

        // Toko tutup (toggle, cek route publik)
        Store::create([
            'user_id' => $ownerB->id,
            'name' => 'Warung Kopi',
            'description' => 'Kopi premium untuk para coffeeholic.',
            'address' => 'Jl. Coding Mulu No. 101',
            'category' => 'kuliner',
            'logo'=>'',
            'is_open' => false,
        ]);
    }
}
