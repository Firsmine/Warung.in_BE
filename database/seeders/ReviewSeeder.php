<?php

namespace Database\Seeders;

use App\Models\Order;
use App\Models\Product;
use App\Models\Review;
use App\Models\User;
use Illuminate\Database\Seeder;

class ReviewSeeder extends Seeder
{
    public function run(): void
    {
        $customer = User::where('email', 'user@tes.com')->first();
        $product = Product::where('name', 'Smartphone Flagship X')->first();
        $completedOrder = Order::where('status', 'completed')->first();

        Review::create([
            'user_id' => $customer->id,
            'product_id' => $product->id,
            'order_id' => $completedOrder->id,
            'rating' => 5,
            'comment' => 'Barang mewah, original, dan sampai dengan selamat! Mantap.'
        ]);
    }
}
