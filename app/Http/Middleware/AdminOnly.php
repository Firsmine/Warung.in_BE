<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminOnly
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if($request->user()?->role !=='admin'){
            return response()->json([
                'status'=>'error',
                'message'=>'Admin only'
            ], 403);
        };
        return $next($request);
    }
}
