@foreach ($assets as $asset)
    <link
        href="{{ asset($asset->getHref()) }}"
        rel="stylesheet"
    />
@endforeach
