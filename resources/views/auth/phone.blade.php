<x-guest-layout>
  <div class="mb-4 text-sm text-gray-600 dark:text-gray-400">
    {{ __('Hello ADMIN, please enter your phone number (with country code) for the two-factor authentication (2FA) code.') }}
  </div>

  <form method="POST" action="{{ route('2fa.send-code') }}">
    @csrf

    <!-- Phone -->
    <div>
      <x-input-label for="phone" :value="__('Enter phone with country code (+52, +1, etc)')" />

      <x-text-input id="phone" class="block mt-1 w-full" type="tel" name="phone" required autocomplete="phone" />

      <x-input-error :messages="$errors->get('phone')" class="mt-2" />
    </div>

    <!-- Recaptcha V2 -->
    <div class="form-group mt-3">
      {!! NoCaptcha::renderJs() !!}
      {!! NoCaptcha::display(['data-theme' => 'dark']) !!}
    </div>

    @if ($errors->has('g-recaptcha-response'))
    <div class="form-group mt-3">
      <span class="help-block">
        <strong class="text-red-500">{{ $errors->first('g-recaptcha-response') }}</strong>
      </span>
    </div>
    @endif

    <div class="flex justify-end mt-4">
      <x-primary-button>
        {{ __('Send code') }}
      </x-primary-button>
    </div>
  </form>
</x-guest-layout>