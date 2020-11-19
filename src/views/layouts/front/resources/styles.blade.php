@foreach($styles as $style)
    @push('scripts')
        <!-- {{$style['comment']}} -->
        <link rel="stylesheet" href="{{$script['src']}}">
    @endpush
@endforeach