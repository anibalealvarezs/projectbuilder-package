@foreach($scripts as $script)
    <!-- {{$script['comment']}} -->
    <script src="{{$script['src']}}"></script>
@endforeach