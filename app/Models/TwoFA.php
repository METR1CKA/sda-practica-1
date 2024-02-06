<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TwoFA extends Model
{
  use HasFactory;

  /**
   * La tabla asociada con el modelo.
   *
   * @var string
   */
  protected $table = 'twofa_users';

  /**
   * Los atributos que son asignables en masa.
   *
   * @var array<int, string>
   */
  protected $fillable = [
    'user_id',
    'code2fa',
    'code2fa_verified',
  ];

  /**
   * Obtiene el rol asociado al usuario.
   */
  public function user(): BelongsTo
  {
    return $this->belongsTo(
      User::class,
      'user_id',
      'id'
    );
  }
}
