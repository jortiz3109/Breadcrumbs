<ul class="breadcrumb">
    @foreach ($links as $link)
        @if ($loop->last)
            <li class="active">{{ $link->body }}</li>
        @else
            <li><a href="{{ route($link->route) }}">{{ $link->body }}</a></li>
        @endif
    @endforeach
</ul>
