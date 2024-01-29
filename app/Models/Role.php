<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Models\User;

/**
 * Modelo para los roles.
 */
class Role extends Model
{
  use HasFactory;

  /**
   * Los atributos que son asignables en masa.
   *
   * @var array<int, string>
   */
  protected $fillable = [
    'name',
    'description',
  ];

  /**
   * Obtiene los usuarios asociados al rol.
   */
  public function users(): HasMany
  {
    return $this->hasMany(
      User::class,
      'role_id',
      'id'
    );
  }

  /**
   * Obtiene los roles de la base de datos.
   */
  public static function getRoles()
  {
    $current_roles = DB::table('roles')
      ->select('id', 'name')
      ->orderBy('id', 'asc')
      ->get();

    $current_roles_array = $current_roles->toArray();

    $values = array_reduce($current_roles_array, function ($carry, $role) {
      $carry[$role->name] = $role->id;
      return $carry;
    }, []);

    return $values;
  }
}
