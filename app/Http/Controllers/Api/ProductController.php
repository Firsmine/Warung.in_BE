<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Models\Store;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProductController extends Controller
{
    // list products by store
    public function byStore($storeId) {
        $store = Store::findOrFail($storeId);
        $isOwner = Auth::check() && Auth::user()->id === $store->user_id;
        $q = Product::with('category')->where('store_id',$storeId);

        if (!$isOwner) $q->where('stock','>',0);
        return response()->json([
            'status'=>'success',
            'data'=>$q->get()
        ]);
    }

    // detail product
    public function show($id) {
        $product = Product::with(['store:id,name','category'])
                    ->withAvg('reviews','rating')
                    ->findOrFail($id);
        return response()->json([
            'status'=>'success',
            'data'=>$product
        ]);
    }

    // make product
    public function store(Request $request) {
        $myStore = Store::where('user_id',$request->user()->id)->firstOrFail();
        $data = $request->validate([
            'name'=>'required',
            'description'=>'nullable',
            'price'=>'required|numeric',
            'stock'=>'required|integer',
            'category_id'=>'required|exists:categories,id',
            'image'=>'nullable'
        ]);
        $product = Product::create([...$data,'store_id'=>$myStore->id]);
        return response()->json([
            'status'=>'success',
            'data'=>$product
        ],201);
    }

    // update store
    public function update(Request $request, $id) {
        $product = Product::findOrFail($id);
        $myStore = Store::where('user_id',$request->user()->id)->first();

        if (!$myStore || $product->store_id !== $myStore->id)
            abort(403);
        $product->update($request->only([
            'name','description','price',
            'stock','category_id','image'
        ]));

        return response()->json([
            'status'=>'success',
            'data'=>$product->fresh()
        ]);
    }

    // delete product
    public function destroy(Request $request, $id) {
        $product = Product::findOrFail($id);
        $myStore = Store::where('user_id',$request->user()->id)->first();

        if (!$myStore || $product->store_id !== $myStore->id)
            abort(403);
        $product->delete();
        return response()->json([
            'status'=>'success',
            'message'=>'Produk dihapus'
        ]);
    }

    // update stock product
    public function updateStock(Request $request, $id) {
        $product = Product::findOrFail($id);
        $myStore = Store::where('user_id',$request->user()->id)->first();
        if (!$myStore || $product->store_id !== $myStore->id)
            abort(403);

        $request->validate(['stock'=>'required|integer|min:0']);
        $product->update(['stock'=>$request->stock]);
        return response()->json([
            'status'=>'success',
            'data'=>$product->fresh()
        ]);
    }

    // product category
    public function categories(){
        $categories = Category::orderBy('name', 'asc')->get();

        return response()->json([
            'status' => 'success',
            'data' => $categories
        ], 200);
    }
}
