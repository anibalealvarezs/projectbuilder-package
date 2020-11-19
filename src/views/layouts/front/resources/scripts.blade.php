@foreach($scripts as $script)
    @push('scripts')
        <!-- {{$script['comment']}} -->
        <script src="{{$script['src']}}"></script>
    @endpush
@endforeach