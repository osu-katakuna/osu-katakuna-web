<?php

namespace App\Http\Middleware;

use Closure;

class HasPermission
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next, $perm)
    {
      if (!auth()->check()) {
          if(!$request->expectsJson()) {
            return redirect('login')->with('redirect', $request->fullUrl());
          }

          return \Response::json([
            "error" => true,
            "message" => "Invalid session."
          ], 401);
      }

      if (auth()->user()->hasPermission($perm)) {
          return $next($request);
      }

      if(!$request->expectsJson()) {
        abort(403, "You don't have the '$perm' permission to access this page.");
      }

      return \Response::json([
        "error" => true,
        "message" => "You don't have the '$perm' permission."
      ], 403);
    }
}
