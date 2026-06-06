<?php

namespace Database\Seeders;

use App\Models\Order;
use App\Models\Product;
use App\Models\Store;
use App\Models\User;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class OrderSeeder extends Seeder
{
    public function run(): void
    {
        $customer = User::where('email', 'user@tes.com')->first();
        $store = Store::where('name', 'Berkah Jaya Elektronik')->first();
        $product1 = Product::where('name', 'Smartphone Flagship X')->first();
        $product2 = Product::where('name', 'Wireless Earbuds Pro')->first();

        // selesai today (report harian & admin stats)
        $order1 = Order::create([
            'user_id' => $customer->id,
            'store_id' => $store->id,
            'total_price' => 7500000,
            'notes'=>'',
            'status' => 'completed',
            'created_at' => Carbon::now(),
        ]);
        $order1->items()->create([
            'product_id' => $product1->id,
            'quantity' => 1,
            'price' => 7500000
        ]);

        // selesai bulan lalu (report bulanan)
        $order2 = Order::create([
            'user_id' => $customer->id,
            'store_id' => $store->id,
            'total_price' => 2400000,
            'notes'=>'',
            'status' => 'completed',
            'created_at' => Carbon::now()->subMonth(),
        ]);
        $order2->items()->create([
            'product_id' => $product2->id,
            'quantity' => 2,
            'price' => 1200000
        ]);

        // pending (updateStatus / cancel pesanan)
        $order3 = Order::create([
            'user_id' => $customer->id,
            'store_id' => $store->id,
            'total_price' => 1200000,
            'notes'=>'',
            'status' => 'pending',
            'created_at' => Carbon::now(),
        ]);
        $order3->items()->create([
            'product_id' => $product2->id,
            'quantity' => 1,
            'price' => 1200000
        ]);
    }
}
