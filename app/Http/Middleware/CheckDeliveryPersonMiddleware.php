<?php
namespace App\Http\Middleware;
use Closure;
use Exception;
use App\User;
class CheckDeliveryPersonMiddleware
{
    public function handle($request, Closure $next, $guard = null)
    {
        $category = $request->auth->category;
        if($category!= 2) {
            return response()->json([
                'error' => 'Only Delivery Person can access'
            ], 401);
        }
        return $next($request);
    }
}