<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="csrf-token" content="{{ csrf_token() }}">

  <title>{{ config('app.name', 'Laravel') }}</title>

  <!-- Fonts -->
  <link rel="preconnect" href="https://fonts.bunny.net">
  <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

  <!-- Scripts -->
  @vite(['resources/css/app.css', 'resources/js/app.js'])
  <script src="https://www.google.com/recaptcha/api.js?render=6LcyA2ApAAAAAEx-ghJioxM5aDaQ--_5qxNjwaMD"></script>
  <script>
    document.addEventListener('submit', function(e) {
      e.preventDefault();

      grecaptcha.ready(function() {
        grecaptcha.execute('6LcyA2ApAAAAAEx-ghJioxM5aDaQ--_5qxNjwaMD', {
          action: 'submit'
        }).then(function(token) {
          let form = e.target;

          let input = document.createElement('input');

          input.type = 'hidden';
          input.name = 'g-recaptcha-response';
          input.value = token;

          form.appendChild(input);

          form.submit();
        });
      });
    })
  </script>
</head>

<body class="font-sans antialiased">
  <div class="min-h-screen bg-gray-100 dark:bg-gray-900">
    @include('layouts.navigation')

    <!-- Page Heading -->
    @if (isset($header))
    <header class="bg-white dark:bg-gray-800 shadow">
      <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
        {{ $header }}
      </div>
    </header>
    @endif

    <!-- Page Content -->
    <main>
      {{ $slot }}
    </main>
  </div>
</body>

</html>