<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;
use App\Models\User;

/**
 * Request para registrar un usuario.
 */
class RegisterPostRequest extends FormRequest
{
  /**
   * Obtener las reglas de validación que se aplican a la solicitud.
   *
   * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
   */
  public function rules(): array
  {
    return [
      'username' => ['required', 'string', 'max:255'],
      'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class],
      'password' => [
        'required',
        'confirmed',
        Password::min(8)
          ->max(12)
          ->mixedCase()
          ->letters()
          ->numbers()
          ->symbols()
          ->uncompromised(5),
      ],
    ];
  }
}
