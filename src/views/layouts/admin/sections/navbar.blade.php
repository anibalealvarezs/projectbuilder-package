@extends('layouts.admin.main')
@section('navbar')
<!-- Navbar -->
<nav class="main-header navbar navbar-expand navbar-white navbar-light">

    @yield('left')

    @yield('middle')

    @yield('right')
</nav>
<!-- /.navbar -->
@endsection