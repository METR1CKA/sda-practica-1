<?php

namespace App\Http\Requests;

use App\Models\User;
use App\Rules\Recaptcha;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Request para actualizar el perfil.
 */
class ProfileUpdateRequest extends FormRequest
{
  /**
   * Obtener las reglas de validación que se aplican a la solicitud.
   *
   * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
   */
  public function rules(): array
  {
    return [
      'username' => ['required', 'string', 'max:255'],
      'email' => ['required', 'string', 'lowercase', 'email', 'max:255', Rule::unique(User::class)->ignore($this->user()->id)],
      'g-recaptcha-response' => ['required', new Recaptcha],
    ];
  }
}
