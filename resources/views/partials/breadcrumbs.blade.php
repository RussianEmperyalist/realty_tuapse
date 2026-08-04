<ul class="breadcrumb">
    @foreach ($breadcrumbs as $breadcrumb)
        <li class="breadcrumb-item">
            @if (!empty($breadcrumb['url']))
                <a class="path" href="{{ $breadcrumb['url'] }}">{{ $breadcrumb['label'] }}</a>
            @else
                <a href="javascript: void(0);">{{ $breadcrumb['label'] }}</a>
            @endif
        </li>
    @endforeach
</ul>
