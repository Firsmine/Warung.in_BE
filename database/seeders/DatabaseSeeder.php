<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            StoreSeeder::class,
            ProductSeeder::class,
            OrderSeeder::class,
            ReviewSeeder::class,
        ]);
    }
}
