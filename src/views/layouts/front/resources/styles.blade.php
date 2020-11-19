@foreach($styles as $style)
    <!-- {{$style['comment']}} -->
    <link rel="stylesheet" href="{{$style['href']}}">
@endforeach