<?php

namespace App\Http\Controllers;

use App\Providers\RouteServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Twilio\Rest\Client;

class TwoFactorController extends Controller
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
      'CONTROLLER' => TwoFactorController::class,
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
   * @return \Illuminate\View\View
   */
  public function create(): View
  {
    Log::info('SEND VIEW TO SET PHONE', [
      'STATUS' => 'SUCCESS',
      'ACTION' => 'Show view to set phone number for send code',
      'USER' => Auth::user() ?? 'GUEST',
      'CONTROLLER' => TwoFactorController::class,
      'METHOD' => 'create',
    ]);

    return view('auth.phone');
  }

  /**
   * Store a newly created resource in storage.
   * 
   * @param  \Illuminate\Http\Request  $request
   * 
   * @return \Illuminate\Http\RedirectResponse
   */
  public function store(Request $request) //: RedirectResponse
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
      'code2fa' => $code,
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
   * @return \Illuminate\View\View
   */
  public function edit(): View
  {
    Log::info('SEND VIEW VERIFY CODE', [
      'STATUS' => 'SUCCESS',
      'ACTION' => 'Show view to verify code',
      'USER' => Auth::user() ?? 'GUEST',
      'CONTROLLER' => TwoFactorController::class,
      'METHOD' => 'create',
    ]);

    return view('auth.verify');
  }

  /**
   * Update the specified resource in storage.
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
    $is_valid = $request->code == $request->user()->twoFA->code2fa;

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
