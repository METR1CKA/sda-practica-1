<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\TrimStrings as Middleware;

/**
 * Middleware para recortar cadenas.
 */
class TrimStrings extends Middleware
{
  /**
   * Las rutas que no deben ser recortadas.
   *
   * @var array<int, string>
   */
  protected $except = [
    'current_password',
    'password',
    'password_confirmation',
  ];
}
