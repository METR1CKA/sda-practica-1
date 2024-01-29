<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use App\Models\User;

class UserController extends Controller
{
  public function index(): View
  {
    $this->authorize('isValidRole', Auth::user());

    $users = User::all()->where('id', '!=', Auth::user()->id);

    return view('users.table', [
      'users' => $users,
    ]);
  }

  public function destroy($id): RedirectResponse
  {
    $this->authorize('isValidRole', Auth::user());

    $user = User::find($id);

    if (!$user) {
      return redirect()
        ->back()
        ->withErrors(['error' => 'User not found']);
    }

    $user->active = !$user->active;

    $user->save();

    return redirect()->route('users.index');
  }
}
