@php
    $itemsHtml = $table->renderLayout($part->getChildSchema());

    // A part that renders nothing still leaves Livewire's block markers behind, so comments do not count as content.
    $hasItems = filled(trim(preg_replace('/<!--.*?-->/s', '', $itemsHtml) ?? ''));
@endphp

@if ($hasItems)
    <div class="fi-ta-content-header">
        {!! $itemsHtml !!}
    </div>
@endif
