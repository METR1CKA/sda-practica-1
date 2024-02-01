<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AuthTwoFactor
{
  /**
   * Handle an incoming request.
   *
   * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
   */
  public function handle(Request $request, Closure $next): Response
  {
    $exists_phone = $request->user()->phone;

    $two_factor = $request->user()->code2fa;

    if (!$two_factor && !$exists_phone) {
      return redirect()->route('2fa.send-code');
    }

    $two_factor_verified = $request->user()->code2fa_verified;

    if (!$two_factor_verified) {
      return redirect()->route('2fa.verify-code');
    }

    $password_confirmed_at = $request->session()->has('auth.password_confirmed_at');

    if (!$password_confirmed_at) {
      return redirect()->route('password.confirm');
    }

    return $next($request);
  }
}
