<!-- Messages Dropdown Menu -->
<li class="nav-item dropdown">

    @include('dropdown')

    <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">

        @stack('message_item')

        @include('footer')

    </div>
</li>