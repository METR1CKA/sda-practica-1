<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
  /**
   * Determine whether the user can view any models.
   */
  public function isValidRole(User $user): bool
  {
    return $user->role->name == 'ADMIN';
  }
}
