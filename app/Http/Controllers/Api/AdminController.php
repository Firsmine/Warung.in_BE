<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Store;
use App\Models\User;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    // total user, toko, pesanan, transaksi hari ini
    public function stats()
    {
        return response()->json([
            'status'=>'success',
            'data'=>[
                'users'=>User::count(),
                'stores'=>Store::count(),
                'orders'=>Order::count(),
                'order_today'=>Order::where('status','completed')
                                ->whereDate('created_at',today())
                                ->sum('total_price')
        ]]);
    }

    // store management - all stores
    public function stores(Request $request)
    {
        return response()->json([
            'status'=>'success',
            'data'=>Store::with('owner:id,name,email')
                    ->paginate(10)]);
    }

    // store management - active/nonactive store
    public function toggleStore($id) {
        $store = Store::findOrFail($id);

        $store->update(['is_open'=>!$store->is_open]);
        return response()->json([
            'status'=>'success',
            'is_open'=>$store->is_open
        ]);
    }

    // user namagement - get all users
    public function users() {
        return response()->json([
            'status'=>'success',
            'data'=>User::paginate(10)
        ]);
    }

    // user namagement - change user's role
    public function changeRole(Request $request, $id) {
        $user = User::findOrFail($id);
        $request->validate([
            'role'=>'required|in:owner,customer,admin'
        ]);

        $user->update(['role'=>$request->role]);
        return response()->json([
            'status'=>'success',
            'data'=>$user->fresh()
        ]);
    }
}
