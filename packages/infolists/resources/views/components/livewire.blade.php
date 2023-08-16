@php
    $isLazy = $isLazy();
    $componentData = $getComponentData();
    $isWithRecord = $isWithRecord();
@endphp
<div>
    @livewire($component, ['lazy' => $isLazy, 'componentData' => $componentData, 'record' => $isWithRecord ? $getRecord() : null])
</div>
