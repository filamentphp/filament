@foreach ($assets as $asset)
    <script
        src="{{ asset($asset->getSrc()) }}"
        @if ($asset->isAsync()) async @endif
        @if ($asset->isDeferred()) defer @endif
    ></script>
@endforeach

