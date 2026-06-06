<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Models\Store;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        // category
        $catElektronik = Category::create([
            'name' => 'Elektronik',
            'icon'=>'',
        ]);
        $catKuliner = Category::create([
            'name' => 'Kuliner',
            'icon'=>'',
        ]);

        $storeA = Store::where('name', 'Berkah Jaya Elektronik')->first();

        // product ready
        Product::create([
            'store_id' => $storeA->id,
            'category_id' => $catElektronik->id,
            'name' => 'Smartphone Flagship X',
            'description' => 'RAM 12GB, Internal 256GB.',
            'price' => 7500000,
            'stock' => 20,
            'image'=>''
        ]);

        Product::create([
            'store_id' => $storeA->id,
            'category_id' => $catElektronik->id,
            'name' => 'Wireless Earbuds Pro',
            'description' => 'Active Noise Cancelling.',
            'price' => 1200000,
            'stock' => 15,
            'image'=>''
        ]);

        // stock habis (filter stock > 0 StoreController show & ProductController byStore)
        Product::create([
            'store_id' => $storeA->id,
            'category_id' => $catElektronik->id,
            'name' => 'Kabel Data Type-C Lama',
            'description' => 'Kabel data usang.',
            'price' => 15000,
            'stock' => 0,
            'image'=>''
        ]);
    }
}
