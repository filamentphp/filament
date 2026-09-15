@php
    $itemsHtml = $table->renderLayout($part->getChildSchema());
    $hasItems = ! $table->isLayoutHtmlBlank($itemsHtml);
@endphp

@if ($hasItems)
    <div class="fi-ta-content-header">
        {!! $itemsHtml !!}
    </div>
@endif
