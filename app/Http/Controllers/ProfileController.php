<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
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
  public function update(ProfileUpdateRequest $request)
  {
    Log::info('REQUEST TO UPDATE PROFILE', [
      'ACTION' => 'Update profile',
      'HTTP-VERB' => $request->method(),
      'URL' => $request->url(),
      'IP' => $request->ip(),
      'USER_AGENT' => $request->userAgent(),
      'SESSION' => $request->session()->all(),
      'USER' => $request->user(),
      'CONTROLLER' => ProfileController::class,
      'METHOD' => 'update',
    ]);

    $req = $request->validated();

    unset($req['g-recaptcha-response']);

    Log::info('VALIDATION TO UPDATE PROFILE PASSED', [
      'STATUS' => 'SUCCESS',
      'ACTION' => 'Update profile',
      'USER' => $request->user(),
    ]);

    $request->user()->fill($req);

    if ($request->user()->isDirty('email')) {
      $request->user()->email_verified_at = null;
    }

    $request->user()->save();

    Log::info('PROFILE UPDATED', [
      'STATUS' => 'SUCCESS',
      'ACTION' => 'Update profile',
      'USER' => $request->user(),
    ]);

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
    Log::info('REQUEST TO DELETE PROFILE', [
      'ACTION' => 'Disabled profile',
      'HTTP-VERB' => $request->method(),
      'URL' => $request->url(),
      'IP' => $request->ip(),
      'USER_AGENT' => $request->userAgent(),
      'SESSION' => $request->session()->all(),
      'USER' => $request->user(),
      'CONTROLLER' => ProfileController::class,
      'METHOD' => 'destroy',
    ]);

    $request->validateWithBag('userDeletion', [
      'password' => ['required', 'current_password'],
    ]);

    Log::info('VALIDATION TO DELETE PROFILE PASSED', [
      'STATUS' => 'SUCCESS',
      'ACTION' => 'Disabled profile',
      'USER' => $request->user(),
    ]);

    $user = $request->user();

    $user->active = false;

    $user->save();

    Log::info('PROFILE DISABLED', [
      'STATUS' => 'SUCCESS',
      'ACTION' => 'Disabled profile',
      'USER' => $request->user(),
    ]);

    Auth::logout();

    $request->session()->invalidate();
    $request->session()->regenerateToken();

    Log::info('USER LOGGED OUT', [
      'STATUS' => 'SUCCESS',
      'ACTION' => 'Disabled profile',
      'USER' => $request->user(),
    ]);

    return Redirect::to('/');
  }
}
