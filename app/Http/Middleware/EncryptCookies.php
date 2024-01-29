<?php

namespace App\Http\Middleware;

use Illuminate\Cookie\Middleware\EncryptCookies as Middleware;

/**
 * Middleware para encriptar cookies.
 */
class EncryptCookies extends Middleware
{
  /**
   * The names of the cookies that should not be encrypted.
   *
   * @var array<int, string>
   */
  protected $except = [
    //
  ];
}
