<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>App Name - @yield('title')</title>

    @yield('styles')

</head>
<body class="hold-transition sidebar-mini layout-fixed">
<div class="wrapper">

    @yield('navbar')

    @yield('sidebar')

    @yield('content')

    @yield('footer')

    @yield('miscbar')

</div>
<!-- ./wrapper -->

@yield('scripts')

</body>
</html>
