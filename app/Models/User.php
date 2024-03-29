<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use App\Models\Role;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * Modelo para los usuarios.
 */
class User extends Authenticatable implements MustVerifyEmail
{
  use HasApiTokens, HasFactory, Notifiable;

  /**
   * Los atributos que son asignables en masa.
   *
   * @var array<int, string>
   */
  protected $fillable = [
    'username',
    'email',
    'password',
    'role_id',
    'active',
    'phone',
  ];

  /**
   * Los atributos que deben ocultarse para la serialización.
   *
   * @var array<int, string>
   */
  protected $hidden = [
    'password',
    'remember_token',
  ];

  /**
   * Los atributos que deben convertirse a tipos nativos.
   *
   * @var array<string, string>
   */
  protected $casts = [
    'email_verified_at' => 'datetime',
    'password' => 'hashed',
  ];

  /**
   * Obtiene el rol asociado al usuario.
   */
  public function role(): BelongsTo
  {
    return $this->belongsTo(
      Role::class,
      'role_id',
      'id',
    );
  }

  /**
   * Obtiene el código de autenticación de dos factores asociado al usuario.
   */
  public function twoFA(): HasOne
  {
    return $this->hasOne(
      TwoFA::class,
      'user_id',
      'id'
    );
  }
}
