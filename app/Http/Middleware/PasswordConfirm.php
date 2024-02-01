<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class PasswordConfirm
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

    $password_confirmed_at = $request->session()->has('auth.password_confirmed_at');

    if (!$password_confirmed_at && $check) {
      return redirect()->route('password.confirm');
    }

    return $next($request);
  }
}
