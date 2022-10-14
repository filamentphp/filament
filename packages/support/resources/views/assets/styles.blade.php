@foreach ($assets as $asset)
    <link rel="stylesheet" href="{{ $asset->getHref() }}" />
@endforeach
