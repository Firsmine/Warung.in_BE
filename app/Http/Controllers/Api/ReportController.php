<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Store;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    // pendapatan
    public function revenue(Request $request) {
        $store  = Store::where('user_id',$request->user()->id)->firstOrFail();
        $period = $request->period ?? 'monthly';
        $format = match($period) { 'daily'=>'%Y-%m-%d','weekly'=>'%Y-%u', default=>'%Y-%m' };

        $data = Order::where('store_id',$store->id)
            ->where('status','completed')
            ->selectRaw("DATE_FORMAT(created_at,'{$format}') as period, SUM(total_price) as revenue, COUNT(*) as total_orders")
            ->groupBy('period')
            ->orderBy('period','desc')
            ->limit(12)->get();

            return response()->json([
            'status'=>'success',
            'data'=>$data
        ]);
    }

    // product best seller
    public function topProducts(Request $request) {
        $store = Store::where('user_id',$request->user()->id)->firstOrFail();
        $data = OrderItem::whereHas('order',fn($q)=>$q->where('store_id',$store->id)->where('status','completed'))
            ->with('product:id,name,image,price')
            ->selectRaw('product_id, SUM(quantity) as total_sold')
            ->groupBy('product_id')->orderByDesc('total_sold')->limit(5)->get();

        return response()->json([
            'status'=>'success',
            'data'=>$data
        ]);
    }

    // order stats
    public function orderStats(Request $request) {
        $store = Store::where('user_id',$request->user()->id)->firstOrFail();
        $stats = Order::where('store_id',$store->id)
            ->selectRaw('status, COUNT(*) as total')
            ->groupBy('status')->pluck('total','status');

        return response()->json([
            'status'=>'success',
            'data'=>$stats
        ]);
    }
}
