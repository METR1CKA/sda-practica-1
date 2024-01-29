<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

/**
 * Controlador para actualizar el perfil del usuario.
 */
class ProfileController extends Controller
{
  /**
   * Muestra la vista de actualización del perfil.
   * 
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\View\View
   */
  public function edit(Request $request): View
  {
    return view('profile.edit', [
      'user' => $request->user(),
    ]);
  }

  /**
   * Actualiza el perfil del usuario.
   * 
   * @param  \App\Http\Requests\ProfileUpdateRequest  $request
   * @return \Illuminate\Http\RedirectResponse
   * 
   * @throws \Illuminate\Validation\ValidationException
   */
  public function update(ProfileUpdateRequest $request): RedirectResponse
  {
    $req = $request->validated();

    $request->user()->fill($req);

    if ($request->user()->isDirty('email')) {
      $request->user()->email_verified_at = null;
    }

    $request->user()->save();

    return Redirect::route('profile.edit')->with('status', 'profile-updated');
  }

  /**
   * Desactiva el perfil del usuario.
   * 
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\RedirectResponse
   * 
   * @throws \Illuminate\Validation\ValidationException
   */
  public function destroy(Request $request): RedirectResponse
  {
    $request->validateWithBag('userDeletion', [
      'password' => ['required', 'current_password'],
    ]);

    $user = $request->user();

    $user->active = false;

    $user->save();

    Auth::logout();

    $request->session()->invalidate();
    $request->session()->regenerateToken();

    return Redirect::to('/');
  }
}
