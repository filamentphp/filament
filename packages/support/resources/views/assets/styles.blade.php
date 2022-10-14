@php
    $version = \Composer\InstalledVersions::getVersion('filament/support');
@endphp

@foreach ($assets as $asset)
    {{ $asset->getHtml() }}
@endforeach
