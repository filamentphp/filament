@if (isset($data))
    <script>
        window.filamentData = @js($data);
    </script>
@endif

@foreach ($assets as $asset)
    {{ $asset->getHtml() }}
@endforeach
