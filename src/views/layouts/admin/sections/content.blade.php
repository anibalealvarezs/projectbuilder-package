@extends('builder::layouts.admin.main')
@section('content')
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">

    @include('builder::layouts.admin.sections.content.header')

    @yield('main')
</div>
<!-- /.content-wrapper -->
@endsection