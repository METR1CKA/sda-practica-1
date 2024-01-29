<?php

namespace App\Http\Middleware;

use Illuminate\Http\Middleware\TrustProxies as Middleware;
use Illuminate\Http\Request;

/**
 * Middleware para confiar en los proxies.
 */
class TrustProxies extends Middleware
{
  /**
   * Los proxies que deben ser confiados.
   *
   * @var array<int, string>|string|null
   */
  protected $proxies;

  /**
   * Los encabezados que deben ser confiados.
   *
   * @var int
   */
  protected $headers =
  Request::HEADER_X_FORWARDED_FOR |
    Request::HEADER_X_FORWARDED_HOST |
    Request::HEADER_X_FORWARDED_PORT |
    Request::HEADER_X_FORWARDED_PROTO |
    Request::HEADER_X_FORWARDED_AWS_ELB;
}
