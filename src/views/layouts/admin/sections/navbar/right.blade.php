@extends('layouts.admin.sections.navbar')
@section('right')
<!-- Right navbar links -->
<ul class="navbar-nav ml-auto">

    @yield('messages')

    @yield('notifications')

</ul>
@endsection