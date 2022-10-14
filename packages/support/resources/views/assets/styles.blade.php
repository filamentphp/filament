@php
    $version = \Composer\InstalledVersions::getVersion('filament/support');
@endphp

@foreach ($assets as $asset)
    <link
        href="{{ asset($asset->getHref()) }}?v={{ $version }}"
        rel="stylesheet"
    />
@endforeach
