<?php

namespace App\Http\Middleware;

use Illuminate\Routing\Middleware\ValidateSignature as Middleware;

/**
 * Middleware para validar la firma.
 */
class ValidateSignature extends Middleware
{
  /**
   * Los parámetros que deben ser excluidos de la firma.
   *
   * @var array<int, string>
   */
  protected $except = [
    // 'fbclid',
    // 'utm_campaign',
    // 'utm_content',
    // 'utm_medium',
    // 'utm_source',
    // 'utm_term',
  ];
}
