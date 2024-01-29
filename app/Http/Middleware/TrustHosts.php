<?php

namespace App\Http\Middleware;

use Illuminate\Http\Middleware\TrustHosts as Middleware;

/**
 * Middleware para confiar en los hosts.
 */
class TrustHosts extends Middleware
{
  /**
   * Obtener los hosts que deben ser confiados.
   *
   * @return array<int, string|null>
   */
  public function hosts(): array
  {
    return [
      $this->allSubdomainsOfApplicationUrl(),
    ];
  }
}
