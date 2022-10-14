@php
    $version = \Composer\InstalledVersions::getVersion('filament/support');
@endphp

@foreach ($assets as $asset)
    <script
        src="{{ asset($asset->getSrc()) }}?v={{ $version }}"
        @if ($asset->isAsync()) async @endif
        @if ($asset->isDeferred()) defer @endif
    ></script>
@endforeach

