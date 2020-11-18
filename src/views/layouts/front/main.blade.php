<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>App Name - @yield('title')</title>

    @stack('styles')

</head>
<body class="hold-transition login-page">

@yield('content')

@stack('scripts')

</body>
</html>