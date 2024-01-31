<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class UserController extends Controller
{
  public function index(): View
  {
    Log::info('SEND VIEW USERS TABLE', [
      'ACTION' => 'Show view to list users',
      'CONTROLLER' => UserController::class,
      'USER' => Auth::user(),
      'METHOD' => 'index',
    ]);

    $this->authorize('isValidRole', Auth::user());

    $users = User::all()->where('id', '!=', Auth::user()->id);

    Log::info('USERS LISTED', [
      'STATUS' => 'SUCCESS',
      'ACTION' => 'Show view to list users',
      'USER' => Auth::user(),
      'USERS' => $users,
    ]);

    return view('users.table', [
      'users' => $users,
    ]);
  }

  public function destroy($id): RedirectResponse
  {
    Log::info('REQUEST TO DELETE USER', [
      'ACTION' => 'Delete user',
      'CONTROLLER' => UserController::class,
      'USER-AUTH' => Auth::user(),
      'ID' => $id,
      'METHOD' => 'destroy',
    ]);

    $this->authorize('isValidRole', Auth::user());

    $user = User::find($id);

    if (!$user) {
      Log::alert('USER NOT FOUND', [
        'STATUS' => 'ERROR',
        'ACTION' => 'Delete user',
        'USER-AUTH' => Auth::user(),
        'USER' => $user ?? 'NOT FOUND',
      ]);

      return redirect()
        ->back()
        ->withErrors(['error' => 'User not found']);
    }

    $user->active = !$user->active;

    $user->save();

    Log::info('USER DELETED', [
      'STATUS' => 'SUCCESS',
      'ACTION' => 'Delete user',
      'USER-AUTH' => Auth::user(),
      'USER' => $user,
    ]);

    return redirect()->route('users.index');
  }
}
