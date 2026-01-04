@php
  $communeName = \App\Models\Commune::where('code', session('current_commune_code'))->value('nom') ?? 'Commune';
  $communeLabels = \App\Models\Commune::whereIn('code', config('app.available_commune_codes'))->pluck('nom', 'code')->toArray();
  $sortedCodes = collect(config('app.available_commune_codes'))->sortBy(fn($code) => $communeLabels[$code] ?? $code)->values()->all();
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

        <form action="{{ url('/switch-commune') }}" method="POST" class="d-flex ms-3">
          @csrf
          <select name="code" class="form-select me-2" onchange="this.form.submit()">
            @foreach($sortedCodes as $code)
              <option value="{{ $code }}" {{ session('current_commune_code') == $code ? 'selected' : '' }}>
                {{ $communeLabels[$code] ?? 'Commune ' . $code }}
              </option>
            @endforeach
          </select>
        </form>
      </div>
    </nav>

    <main>
      @yield('content')
    </main>
  </div>
</body>
</html>
