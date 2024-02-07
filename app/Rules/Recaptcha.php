<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\Http;

class Recaptcha implements ValidationRule
{
  /**
   * Run the validation rule.
   *
   * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
   */
  public function validate(string $attribute, mixed $value, Closure $fail): void
  {
    $URL = env('V3_RECAPTCHA_URL');

    $SECRET = env('V3_RECAPTCHA_SECRET');

    $response = Http::asForm()
      ->post($URL, [
        'secret' => $SECRET,
        'response' => $value,
      ])
      ->object();

    if (!$response->success || $response->score <= 0.7) {
      $fail('The :attribute is invalid.');
    }
  }
}
