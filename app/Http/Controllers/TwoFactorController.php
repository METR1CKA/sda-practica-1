<?php

namespace App\Http\Controllers;

use App\Http\Requests\PhoneRequest;
use App\Providers\RouteServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class TwoFactorController extends Controller
{
  private function generateCode(): string
  {
    $code = rand(1000, 9999);

    return strval($code);
  }

  private function sendCode($phone, $code)
  {
    // Usar try-catch para manejar cualquier error
    try {
      // Utilizar la API de Twilio para enviar el código de verificación
      $url = 'https://api.twilio.com/2010-04-01/Accounts/AC5835eaf772bc767e9dc795cba8c6a29e/Messages.json';

      $from = '+17246384834';

      $body = "Tu código de verificación es: {$code}";

      $ch = curl_init();

      curl_setopt($ch, CURLOPT_URL, $url);
      curl_setopt($ch, CURLOPT_POST, 1);
      curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query(['From' => $from, 'To' => $phone, 'Body' => $body]));
      curl_setopt($ch, CURLOPT_USERPWD, "AC5835eaf772bc767e9dc795cba8c6a29e:a6cde25625fd8b2d47cfe0a569333a7f");

      curl_exec($ch);
      curl_close($ch);

      Log::info('SEND CODE', [
        'STATUS' => 'SUCCESS',
        'ACTION' => 'Send code',
        'PHONE' => $phone,
        'CONTROLLER' => TwoFactorController::class,
        'METHOD' => 'sendCode',
        'CODE' => $code,
      ]);
    } catch (\Exception $e) {
      Log::error('SEND CODE WITH ERROR', [
        'STATUS' => 'ERROR',
        'ACTION' => 'Send code',
        'ERROR' => $e->getMessage(),
        'CONTROLLER' => TwoFactorController::class,
        'METHOD' => 'sendCode',
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
   */
  public function store(Request $request)
  {
    // Validar el número de teléfono
    $request->validate([
      'phone' => ['required', 'string', 'regex:/^\+\d{1,3}[- ]?\d{10}$/']
    ]);

    // Generar un código de verificación
    $code = $this->generateCode();

    // Guardar el código en la bd
    $request->user()->phone = $request->phone;

    $request->user()->code2fa = $code;

    $request->user()->save();

    // Enviar el código a través de SMS usando Twilio
    $send = $this->sendCode($request->phone, $code);

    if (!$send) {
      return back()
        ->withErrors(['phone' => 'Error sending code']);
    }

    // Redirigir al usuario
    // return redirect()->intended(RouteServiceProvider::HOME);
    return redirect()->route('2fa.verify-code');
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
   */
  public function update(Request $request)
  {
    // Validar el código de verificación
    $request->validate([
      'code' => ['required', 'string', 'size:4']
    ]);

    // Verificar si el código es correcto
    $is_valid = $request->code == $request->user()->code2fa;

    if (!$is_valid) {
      // El código es incorrecto, volver a mostrar el formulario de verificación
      return back()
        ->withErrors(['code' => 'The verification code is incorrect.']);
    }

    // Marcar el código como verificado
    $request->user()->code2fa_verified = $is_valid;

    $request->user()->save();

    // El código es correcto, autenticar al usuario
    Auth::login($request->user());

    // Redirigir al usuario
    return redirect()->intended(RouteServiceProvider::HOME);
  }
}
