<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Role;
use App\Providers\RouteServiceProvider;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Twilio\Rest\Client;
use Illuminate\Support\Facades\Hash;

class TwoFactorAuthController extends Controller
{
  /**
   * Generar un código de verificación.
   * 
   * @return string
   */
  private function generateCode(): string
  {
    $code = rand(100000, 999999);

    return strval($code);
  }

  /**
   * Enviar el código de verificación a través de SMS.
   * 
   * @param  string  $phone
   * @param  string  $code
   * 
   * @return bool
   */
  private function sendSmsCode($phone, $code)
  {
    Log::info('SEND CODE', [
      'STATUS' => 'SUCCESS',
      'ACTION' => 'Send code',
      'PHONE' => $phone,
      'CONTROLLER' => TwoFactorAuthController::class,
      'METHOD' => 'sendSmsCode',
      'CODE' => $code,
    ]);

    try {
      $account_sid = env('TWILIO_SID');

      $auth_token = env('TWILIO_AUTH_TOKEN');

      $twilio_number = env('TWILIO_NUMBER');

      $client = new Client($account_sid, $auth_token);

      $client->messages->create(
        $phone,
        [
          'from' => $twilio_number,
          'body' => "Tu código de verificación es: {$code}"
        ]
      );

      Log::info('SEND CODE', [
        'STATUS' => 'SUCCESS',
        'ACTION' => 'Send code',
      ]);
    } catch (\Exception $e) {
      Log::error('SEND CODE WITH ERROR', [
        'STATUS' => 'ERROR',
        'ACTION' => 'Send code',
        'ERROR' => $e->getMessage(),
        'LINE_CODE' => $e->getLine(),
        'FILE' => $e->getFile(),
        'TRACE' => $e->getTraceAsString(),
      ]);

      return false;
    }

    return true;
  }

  /**
   * Muestra la vista para establecer el número de teléfono.
   * 
   * @return RedirectResponse|View
   */
  public function create()
  {
    $roles = Role::getRoles();

    return !Auth::user()->phone && Auth::user()->role_id == $roles['ADMIN']
      ? view('auth.phone')
      : redirect()->intended(RouteServiceProvider::HOME);
  }

  /**
   * Reenviar el código de verificación a través de SMS.
   * 
   * @param  \Illuminate\Http\Request  $request
   * 
   * @return \Illuminate\Http\RedirectResponse
   */
  public function resend(Request $request): RedirectResponse
  {
    // Generar un código de verificación
    $code = $this->generateCode();

    $request->user()->twoFA()->update([
      'code2fa' => Hash::make($code),
    ]);

    // Enviar el código a través de SMS usando Twilio
    $send = $this->sendSmsCode($request->user()->phone, $code);

    if (!$send) {
      return back()
        ->withErrors(['phone' => 'Error sending code']);
    }

    // Redirigir al usuario
    return redirect()
      ->back()
      ->with('status', 'The verification code has been sent.');
  }

  /**
   * Establecer el número de teléfono y enviar el código de verificación a través de SMS.
   * 
   * @param  \Illuminate\Http\Request  $request
   * 
   * @return \Illuminate\Http\RedirectResponse
   */
  public function store(Request $request): RedirectResponse
  {
    // Validar el número de teléfono
    $request->validate([
      'phone' => ['required', 'string', 'regex:/^\+\d{1,3}[- ]?\d{10}$/']
    ]);

    // Generar un código de verificación
    $code = $this->generateCode();

    // Guardar el código en la bd
    $request->user()->update([
      'phone' => $request->phone,
    ]);

    $request->user()->twoFA()->update([
      'code2fa' => Hash::make($code),
    ]);

    // Enviar el código a través de SMS usando Twilio
    $send = $this->sendSmsCode($request->phone, $code);

    if (!$send) {
      return back()
        ->withErrors(['phone' => 'Error sending code']);
    }

    // Redirigir al usuario
    return redirect()->intended(RouteServiceProvider::HOME);
  }

  /**
   * Muestra la vista para verificar el código.
   * 
   * @return RedirectResponse|View
   */
  public function edit()
  {
    $roles = Role::getRoles();

    return !Auth::user()->twoFA->code2fa_verified && Auth::user()->role_id == $roles['ADMIN']
      ? view('auth.verify')
      : redirect()->intended(RouteServiceProvider::HOME);
  }

  /**
   * Validar el código de verificación.
   * 
   * @param  \Illuminate\Http\Request  $request
   * 
   * @return \Illuminate\Http\RedirectResponse
   */
  public function update(Request $request): RedirectResponse
  {
    // Validar el código de verificación
    $request->validate([
      'code' => ['required', 'string', 'size:6']
    ]);

    // Verificar si el código es correcto
    $is_valid = Hash::check($request->code, $request->user()->twoFA->code2fa);

    if (!$is_valid) {
      // El código es incorrecto, volver a mostrar el formulario de verificación
      return back()
        ->withErrors(['code' => 'The verification code is incorrect.']);
    }

    // Marcar el código como verificado
    $request->user()->twoFA()->update([
      'code2fa_verified' => $is_valid,
    ]);

    // El código es correcto, autenticar al usuario
    Auth::login($request->user());

    // Redirigir al usuario
    return redirect()->intended(RouteServiceProvider::HOME);
  }
}
