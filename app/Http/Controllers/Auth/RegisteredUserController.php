<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterPostRequest;
use App\Models\Role;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use Exception;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;

class RegisteredUserController extends Controller
{
  /**
   * Display the registration view.
   */
  public function create(): View
  {
    return view('auth.register');
  }

  /**
   * Handle an incoming registration request.
   *
   * @throws \Illuminate\Validation\ValidationException
   */
  public function store(RegisterPostRequest $request): RedirectResponse
  {
    $data = $request->validated();

    DB::beginTransaction();

    try {
      $users = User::all();

      $roles = Role::getRoles();

      $role_id = $users->count() == 0 ? $roles['ADMIN'] : $roles['GENERIC'];

      $user = User::create([
        'username' => $data['username'],
        'email' => $data['email'],
        'password' => Hash::make($data['password']),
        'role_id' => $role_id,
        'active' => true,
      ]);

      DB::commit();
    } catch (Exception $e) {
      DB::rollBack();

      return redirect()
        ->back()
        ->withErrors([
          'password_confirmation' => 'There was an error creating the user.',
        ]);
    }

    event(new Registered($user));

    Auth::login($user);

    return redirect(RouteServiceProvider::HOME);
  }
}
