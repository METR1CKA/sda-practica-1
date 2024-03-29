<?php

namespace App\Http\Middleware;

use App\Models\Role;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AdminViewWelcome
{
  /**
   * Handle an incoming request.
   *
   * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
   */
  public function handle(Request $request, Closure $next): Response
  {
    $check = Auth::check();

    if (!$check) {
      return redirect()->route('login');
    }

    $roles = Role::getRoles();

    $role_id = Auth::user()->role->id;

    if ($check && $role_id != $roles['ADMIN']) {
      return redirect()->route('dashboard');
    }

    return $next($request);
  }
}
