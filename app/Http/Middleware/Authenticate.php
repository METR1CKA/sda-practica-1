<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Middleware para autenticar usuarios.
 */
class Authenticate extends Middleware
{
  /**
   * Obtenga la ruta a la que se debe redirigir al usuario cuando no esté autenticado.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return string|null
   */
  protected function redirectTo(Request $request): ?string
  {
    return !$request->expectsJson() && !Auth::check()
      ? route('login')
      : null;
  }
}
