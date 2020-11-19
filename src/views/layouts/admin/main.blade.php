<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>App Name - @yield('title')</title>

    @include('builder::layouts.admin.resources.styles')
    @stack('style_file')
    @stack('style_custom')

</head>
<body class="hold-transition sidebar-mini layout-fixed">
<div class="wrapper">

    @include('builder::layouts.admin.sections.navbar')

    @include('builder::layouts.admin.sections.sidebar')

    @yield('content')

    @include('builder::layouts.admin.sections.footer')

    @include('builder::layouts.admin.sections.miscbar')

</div>
<!-- ./wrapper -->

@include('builder::layouts.admin.resources.scripts')
@stack('script_file')
@stack('script_custom')

</body>
</html>
