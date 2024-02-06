<?php

namespace App\Http\Middleware;

use App\Models\Role;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
    $check = Auth::check();

    if (!$check) {
      return redirect()->route('login');
    }

    $roles = Role::getRoles();

    $role_id = Auth::user()->role->id;

    if ($check && $role_id == $roles['ADMIN']) {
      $exists_phone = $request->user()->phone;

      $two_factor = $request->user()->code2fa;

      if (!$two_factor && !$exists_phone) {
        return redirect()->route('2fa.send-code');
      }

      $two_factor_verified = $request->user()->code2fa_verified;

      if (!$two_factor_verified) {
        return redirect()->route('2fa.verify-code');
      }
    }

    return $next($request);
  }
}
