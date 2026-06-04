<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\Store;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    // buat order - customer
    public function store(Request $request)
    {
        // validasi input
        $request->validate([
            'store_id'=>'required|exists:stores,id',
            'items'=>'required|array|min:1',
            'items.*.product_id'=>'required|exists:products,id',
            'items.*.quantity'=>'required|integer|min:1',
        ]);

        return DB::transaction(function() use ($request) {
            $total = 0;
            $itemsData = [];
            foreach ($request->items as $item) {
                $product = Product::lockForUpdate()->find($item['product_id']);
                if ($product->stock < $item['quantity'])
                    abort(422,"Stok {$product->name} tidak cukup");
                $product->decrement('stock',$item['quantity']);
                $subtotal = $product->price * $item['quantity'];
                $total += $subtotal;
                $itemsData[] = [
                    'product_id'=>$product->id,
                    'quantity'=>$item['quantity'],
                    'price'=>$product->price,
                ];
            }
            $order = Order::create([
                'user_id'=>$request->user()->id,
                'store_id'=>$request->store_id,
                'total_price'=>$total,
                'status'=>'pending',
            ]);
            $order->items()->createMany($itemsData);

            return response()->json([
                'status'=>'success',
                'data'=>$order->load('items.product','store:id,name')
            ],201);
        });
    }

    // list order - customer
    public function myOrders(Request $request) {
        $orders = Order::with(['store:id,name','items.product:id,name,image'])
            ->where('user_id',$request->user()->id)
            ->latest()->paginate(10);

        return response()->json([
            'status'=>'success',
            'data'=>$orders
        ]);
    }

    // detail order - customer
    public function show(Request $request, $id)
    {
        $order = Order::with(['items.product','store:id,name,address'])->findOrFail($id);
        if ($order->user_id !== $request->user()->id && $order->store->user_id !== $request->user()->id)
            abort(403);

        return response()->json([
            'status'=>'success',
            'data'=>$order
        ]);
    }

    // new order - owner
    public function storeOrders(Request $request) {
        $store = Store::where('user_id',$request->user()->id)->firstOrFail();
        $q = Order::with(['customer:id,name,phone','items.product:id,name'])
            ->where('store_id',$store->id)->latest();

        if ($request->status)
            $q->where('status',$request->status);

        return response()->json([
            'status'=>'success',
            'data'=>$q->paginate(10)
        ]);
    }

    // update status order - owner
    public function updateStatus(Request $request, $id) {
        $order = Order::findOrFail($id);
        $store = Store::where('user_id',$request->user()->id)->firstOrFail();

        if ($order->store_id !== $store->id)
            abort(403);
        $request->validate([
            'status'=>'required|in:confirmed,ready,completed'
        ]);
        $order->update(['status'=>$request->status]);

        return response()->json([
            'status'=>'success',
            'data'=>$order->fresh()
        ]);
    }

    // cancel order - customer
    public function cancel(Request $request, $id) {
        $order = Order::with('items.product')->findOrFail($id);
        $isCustomer = $order->user_id === $request->user()->id;
        $isOwner    = Store::where('user_id',$request->user()->id)
                    ->where('id',$order->store_id)
                    ->exists();

        if (!$isCustomer && !$isOwner)
            abort(403);
        if ($isCustomer && $order->status !== 'pending')
            abort(422,'Customer hanya bisa cancel pesanan pending');
        if ($order->status === 'completed')
            abort(422,'Tidak bisa cancel pesanan selesai');

        // stok kembali
        foreach ($order->items as $item) { $item->product->increment('stock',$item->quantity); }
        $order->update(['status'=>'cancelled']);

        return response()->json([
            'status'=>'success',
            'message'=>'Pesanan dibatalkan'
        ]);
    }
}
