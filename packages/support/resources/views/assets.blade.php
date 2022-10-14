@foreach ($assets as $asset)
    {{ $asset->getHtml() }}
@endforeach
