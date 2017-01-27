<ul class="breadcrumb">
    <li><a href="/">&nbsp;{{ @trans('breadcrumbs::links.home') }}</a></li>
    @foreach ($links as $link)
        @if ($loop->last)
            <li class="active">{{ $link->body }}</li>
        @else
            <li><a href="{{ url($link->uri) }}">{{ $link->body }}</a></li>
        @endif
    @endforeach
</ul>
