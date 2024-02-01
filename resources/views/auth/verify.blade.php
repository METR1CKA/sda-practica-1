<x-guest-layout>
  <div class="mb-4 text-sm text-gray-600 dark:text-gray-400">
    {{ __('Please enter the two-factor authentication (2FA) code you received via SMS.') }}
  </div>

  <form method="POST" action="{{ route('2fa.verify-code') }}">
    @csrf

    <!-- Code -->
    <div>
      <x-input-label for="code" :value="__('Enter the code')" />

      <x-text-input id="code" class="block mt-1 w-full" type="text" name="code" required autocomplete="code" />

      <x-input-error :messages="$errors->get('code')" class="mt-2" />
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
        {{ __('Send Verification Code') }}
      </x-primary-button>
    </div>
  </form>
</x-guest-layout>