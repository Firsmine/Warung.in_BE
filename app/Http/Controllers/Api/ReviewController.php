<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Review;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    // product review
    public function byProduct($productId) {
        $reviews = Review::with('user:id,name,avatar')->where('product_id',$productId)->latest()->get();
        return response()->json([
            'status'=>'success',
            'data'=>[
                'avg'=>round($reviews->avg('rating'),1),
                'reviews'=>$reviews
        ]]);
    }

    // give review
    public function store(Request $request, $orderId) {
        $order = Order::findOrFail($orderId);
        // pesanan harus milik user
        if ($order->user_id !== $request->user()->id)
            abort(403);
        // pesanan belum selesai
        if ($order->status !== 'completed')
            abort(422,'Ulasan hanya bisa diberikan setelah pesanan selesai');

        $request->validate([
            'product_id'=>'required|exists:products,id',
            'rating'=>'required|integer|min:1|max:5',
            'comment'=>'nullable|string'
        ]);

        // produk harus ada di list pesanan
        $inOrder = $order->items()->where('product_id',$request->product_id)->exists();
        if (!$inOrder)
            abort(422,'Produk tidak ada di pesanan ini');

        $exists = Review::where([
            'user_id'=>$request->user()->id,
            'product_id'=>$request->product_id,
            'order_id'=>$orderId
        ])->exists();
        // beri ulasan cuma bisa sekali
        if ($exists)
            abort(422,'Sudah memberi ulasan');

        $review = Review::create([
            'user_id'=>$request->user()->id,
            'product_id'=>$request->product_id,
            'order_id'=>$orderId,
            'rating'=>$request->rating,
            'comment'=>$request->comment
        ]);
        return response()->json([
            'status'=>'success',
            'data'=>$review
        ],201);
    }
}
