@php
  $communeCode = config('app.default_commune_code');
  $communeName = \App\Models\Commune::where('code', $communeCode)->value('nom') ?? 'Commune';
@endphp
<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="csrf-token" content="{{ csrf_token() }}">

  <title>{{ $communeName }}</title>

  <!-- Fonts -->
  <link rel="dns-prefetch" href="//fonts.bunny.net">
  <link href="https://fonts.bunny.net/css?family=Nunito" rel="stylesheet">

  <!-- Scripts -->
  @vite(['resources/sass/app.scss', 'resources/js/app.js'])

  @stack('js')
</head>
<body>
  <div id="app">
    <nav class="navbar navbar-expand-md navbar-light bg-white shadow-sm mb-3">
      <div class="container">
        <a class="navbar-brand" href="{{ url('/') }}">
          {{ $communeName }}
        </a>
      </div>
    </nav>

    <main>
      @yield('content')
    </main>
  </div>
</body>
</html>
