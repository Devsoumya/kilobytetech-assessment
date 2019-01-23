<?php
namespace App\Http\Middleware;
use Closure;
use Exception;
use App\User;
class CheckCustomerMiddleware
{
    public function handle($request, Closure $next, $guard = null)
    {
        $category = $request->auth->category;
        if($category!= 1) {
            return response()->json([
                'error' => 'Only Customer can place the order'
            ], 401);
        }
    return $next($request);
    }
}