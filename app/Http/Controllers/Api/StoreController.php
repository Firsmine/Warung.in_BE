<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Store;
use Illuminate\Http\Request;

class StoreController extends Controller
{
    // list toko publik
    public function index(Request $request){
        $store = Store::with('owner:id,name')->where('is_open', true);

        if ($request->category) $store->where('category',$request->category);
        if ($request->search)   $store->where('name','like','%'.$request->search.'%');

        return response()->json([
            'status'=>'success',
            'data'=>$store->paginate(12)
        ]);
    }

    // detail toko
        public function show(string $id)
    {
        $store = Store::with([
            'products'=>fn($q)=>$q->where('stock','>',0),'owner:id,name'
        ])->findOrFail($id);

        return response()->json([
            'status'=>'success',
            'data'=>$store
        ]);
    }

    // buat toko (owner)
    public function store(Request $request)
    {
        if ($request->user()->role !== 'owner')
            abort(403,'Hanya owner');

        if (Store::where('user_id',$request->user()->id)->exists())
            abort(422,'Sudah punya toko');

        $data = $request->validate([
            'name'=>'required',
            'description'=>'nullable',
            'address'=>'required',
            'category'=>'required',
            'logo'=>'nullable'
        ]);
        $store = Store::create([...$data,'user_id'=>$request->user()->id]);
        return response()->json([
            'status'=>'success',
            'data'=>$store
        ],201);
    }

    // edit toko (owner)
    public function update(Request $request, string $id)
    {
        $store = Store::findOrFail($id);
        if ($store->user_id !== $request->user()->id)
            abort(403);

        $store->update($request->only([
            'name','description',
            'address','category','logo'
        ]));
        return response()->json([
            'status'=>'success',
            'data'=>$store->fresh()]);
    }

    // tutup toko (owner)
    public function toggle(Request $request, $id) {
        $store = Store::findOrFail($id);

        if ($store->user_id !== $request->user()->id)
            abort(403);

        $store->update(['is_open'=>!$store->is_open]);

        return response()->json([
            'status'=>'success',
            'is_open'=>$store->is_open]);
    }

    // toko milikku (owner)
    public function myStore(Request $request) {
        $store = Store::where('user_id',$request->user()->id)->firstOrFail();

        $store->total_products = $store->products()->count();
        $store->total_orders = $store->orders()->count();
        $store->total_revenue = $store->orders()->where('status','completed')->sum('total_price');
        return response()->json([
            'status'=>'success',
            'data'=>$store]);
    }
}
