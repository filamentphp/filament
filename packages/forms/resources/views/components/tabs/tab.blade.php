@php
    $id = $getId();
    $isContained = $getContainer()->getParentComponent()->isContained();

    $visibleTabClasses = \Illuminate\Support\Arr::toCssClasses([
        'p-6' => $isContained,
        'mt-6' => ! $isContained,
    ]);

    $invisibleTabClasses = 'invisible h-0 overflow-y-hidden p-0';
@endphp

<div
    x-bind:class="tab === @js($id) ? @js($visibleTabClasses) : @js($invisibleTabClasses)"
    x-on:expand-concealing-component.window="
        $nextTick(() => {
            error = $el.querySelector('[data-validation-error]')

            if (! error) {
                return
            }

            tab = @js($id)

            if (document.body.querySelector('[data-validation-error]') !== error) {
                return
            }

            setTimeout(
                () =>
                    $el.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start',
                        inline: 'start',
                    }),
                200,
            )
        })
    "
    {{
        $attributes
            ->merge([
                'aria-labelledby' => $id,
                'id' => $id,
                'role' => 'tabpanel',
                'tabindex' => '0',
                'wire:key' => "{$this->getId()}.{$getStatePath()}." . \Filament\Forms\Components\Tab::class . ".tabs.{$id}",
            ], escape: false)
            ->merge($getExtraAttributes(), escape: false)
            ->class(['fi-fo-tabs-tab outline-none'])
    }}
>
    {{ $getChildComponentContainer() }}
</div>
