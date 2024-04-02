<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width,initial-scale=1.0">
    <meta name="csrf-token" content="">
    <link id="tab-icon" rel="icon" href="">
    <title>Pharmims</title>
    <meta content="Pharmims Inventory System"
        name="description" />
    <!-- vite css and js  -->
    {{-- @vite(['resources/sass/app.scss', 'resources/js/app.js']) --}}
    <link rel="stylesheet" href="{{ Vite::asset('resources/sass/app.scss') }}" id="layout-css">
    <script type="module" src="{{ Vite::asset('resources/js/app.js') }}"></script>
    <script src="https://js.pusher.com/8.2.0/pusher.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/vue/dist/vue.js"></script>
   
    @yield('css')
</head>

<body data-sidebar="dark" data-layout-mode="light">
    <div id="app">
        @yield('content')
    </div>
    <!-- built files will be auto injected -->
    @stack('scripts')
    @yield('js')
    
    <!-- resources/views/welcome.blade.php -->
</body>
</html>
