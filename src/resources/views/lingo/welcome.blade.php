<!DOCTYPE html>

<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />


<title>{{ config('app.name', 'Lingo') }}</title>

<link rel="preconnect" href="https://fonts.bunny.net" />
<link
  href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600"
  rel="stylesheet"
/>

@if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
  @vite(['resources/css/app.css', 'resources/js/app.js'])
@endif


  </head>

  <body class="bg-[#FDFDFC] dark:bg-[#0a0a0a] text-[#1b1b18] dark:text-[#EDEDEC] min-h-screen flex flex-col justify-center items-center">


<header class="text-center mb-8">
  <h1 class="text-3xl font-semibold mb-3 tracking-tight">
    Bienvenido a <span class="text-emerald-600 dark:text-emerald-400">Lingo</span>
  </h1>
  <p class="text-gray-600 dark:text-gray-400 text-sm">
    Aprende idiomas de forma sencilla y divertida.
  </p>
</header>

@if (Route::has('login'))
  <nav class="flex flex-wrap justify-center gap-4">
    @auth
      <a
        href="{{ url('/lingo/index.html') }}"
        class="px-6 py-2 rounded-md bg-emerald-600 hover:bg-emerald-700 text-white font-medium transition"
      >
        Dashboard
      </a>
    @else
      <a
        href="{{ route('login') }}"
        class="px-6 py-2 rounded-md border border-gray-700 dark:border-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800 transition"
      >
        Log in
      </a>

      @if (Route::has('register'))
        <a
          href="{{ route('register') }}"
          class="px-6 py-2 rounded-md border border-gray-700 dark:border-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800 transition"
        >
          Register
        </a>
      @endif
    @endauth
  </nav>
@endif


<footer class="mt-10 text-center text-sm text-gray-500 dark:text-gray-400">
  <p>© {{ date('Y') }} Lingo — Todos los derechos reservados</p>
</footer>


  </body>
</html>
