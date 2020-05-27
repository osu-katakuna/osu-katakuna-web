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
          return redirect('login')->with('redirect', $request->url());
      }

      if (auth()->user()->hasPermission($perm)) {
          return $next($request);
      }

      abort(403, "You don't have the '$perm' permission to access this page.");
    }
}
