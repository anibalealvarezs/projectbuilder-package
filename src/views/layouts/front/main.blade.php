<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>App Name - @yield('title')</title>

    @include('builder::layouts.front.resources.styles')
    {{-- @stack('styles') --}}
    @stack('style_file')
    @stack('style_custom')

</head>
<body class="hold-transition login-page">

@yield('content')

@include('builder::layouts.front.resources.scripts')
{{-- @stack('scripts') --}}
@stack('script_file')
@stack('script_custom')

</body>
</html>