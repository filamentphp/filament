@php
    $id = $getId();
    $key = $getKey(isAbsolute: false);
    $tabs = $getContainer()->getParentComponent();
    $isContained = $tabs->isContained();
    $livewireProperty = $tabs->getLivewireProperty();

    $childSchema = $getChildSchema();
@endphp

@if (! empty($childSchema->getComponents()))
    @if (blank($livewireProperty))
        <div
            x-bind:class="{
                'fi-active': tab === @js($key),
            }"
            x-on:expand="tab = @js($key)"
            {{
                $attributes
                    ->merge([
                        'aria-labelledby' => $id,
                        'id' => $id,
                        'role' => 'tabpanel',
                        'tabindex' => '0',
                        'wire:key' => $getLivewireKey() . '.container',
                    ], escape: false)
                    ->merge($getExtraAttributes(), escape: false)
                    ->class(['fi-sc-tabs-tab'])
            }}
        >
            {{ $childSchema }}
        </div>
    @elseif (strval($this->{$livewireProperty}) === strval($key))
        <div
            {{
                $attributes
                    ->merge([
                        'aria-labelledby' => $id,
                        'id' => $id,
                        'role' => 'tabpanel',
                        'tabindex' => '0',
                        'wire:key' => $getLivewireKey() . '.container',
                    ], escape: false)
                    ->merge($getExtraAttributes(), escape: false)
                    ->class(['fi-sc-tabs-tab fi-active'])
            }}
        >
            {{ $childSchema }}
        </div>
    @endif
@endif
