<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Models\User;

class Role extends Model
{
  use HasFactory;

  /**
   * The attributes that are mass assignable.
   *
   * @var array<int, string>
   */
  protected $fillable = [
    'name',
    'description',
  ];

  /**
   * Get the users for the role.
   */
  public function users(): HasMany
  {
    return $this->hasMany(
      User::class,
      'role_id',
      'id'
    );
  }

  // Obtiene los roles de la base de datos
  public static function getRoles()
  {
    // Obtiene los roles de la base de datos
    $currentRoles = DB::table('roles')
      ->select('id', 'name')
      ->orderBy('id', 'asc')
      ->get();

    // Convierte la colección a un array
    $currentRolesArray = $currentRoles->toArray();

    // Mapea los roles a un array asociativo utilizando array_reduce
    $values = array_reduce($currentRolesArray, function ($carry, $role) {
      $carry[$role->name] = $role->id;
      return $carry;
    }, []);

    return $values;
  }
}
