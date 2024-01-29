<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\Http;

class Recaptcha implements ValidationRule
{
  private $URL = 'https://www.google.com/recaptcha/api/siteverify';

  private $SECRET = '6LcyA2ApAAAAAKJPbX5ncKbs6v4TB0ndC59CqOAi';

  /**
   * Run the validation rule.
   *
   * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
   */
  public function validate(string $attribute, mixed $value, Closure $fail): void
  {
    $response = Http::asForm()
      ->post($this->URL, [
        'secret' => $this->SECRET,
        'response' => $value,
      ])
      ->object();

    if (!$response->success || $response->score <= 0.7) {
      $fail('The :attribute is invalid.');
    }
  }
}
