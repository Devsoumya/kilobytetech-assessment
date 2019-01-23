<?php
namespace App\Http\Middleware;
use Closure;
use Exception;
use App\User;
class CheckAdminMiddleware
{
    public function handle($request, Closure $next, $guard = null)
    {
        $category = $request->auth->category;
        if($category!= 3) {
            return response()->json([
                'error' => 'Only admin can access'
            ], 401);
        }
        return $next($request);
    }
}