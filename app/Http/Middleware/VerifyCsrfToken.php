<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

/**
 * Middleware para verificar el token CSRF.
 */
class VerifyCsrfToken extends Middleware
{
  /**
   * Los URIs que deben ser excluidos de la verificación CSRF.
   *
   * @var array<int, string>
   */
  protected $except = [
    //
  ];
}
