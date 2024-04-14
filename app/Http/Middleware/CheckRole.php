<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;


class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  $role
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(Request $request, Closure $next, $role)
    {
        // $role_user = response()->json(auth()->user())->role;
        // if ($role_user!=$role) {
        //     return redirect('Bạn không có quyền');
        // }

        // return $next($request);
        if (auth()->user() && auth()->user()->role) {
            $role_user = auth()->user()->role;
            if ($role_user != $role) {
                response()->json(\App\Http\Controllers\Response::error("Quyền truy cập bị từ chối",403));
            }

            return $next($request);
        }

        return response()->json(\App\Http\Controllers\Response::error("Quyền truy cập bị từ chối",403));
    }
}
