@php
    use Filament\Support\Enums\MaxWidth;
    use Illuminate\Support\Js;

    $isRoot = $isRoot();
@endphp

{{-- format-ignore-start --}}
<x-filament::grid
    :x-data="$isRoot ? '{}' : null"
    :x-on:form-validation-error.window="
        $isRoot
            ? (
                'if ($event.detail.livewireId !== ' . Js::from($this->getId()) . ') {
                    return
                }

                $nextTick(() => {
                    error = $el.querySelector(\'[data-validation-error]\')

                    if (! error) {
                        return
                    }

                    elementToExpand = error

                    while (elementToExpand) {
                        elementToExpand.dispatchEvent(new CustomEvent(\'expand\'))

                        elementToExpand = elementToExpand.parentNode
                    }

                    setTimeout(
                        () =>
                            error.closest(\'[data-field-wrapper]\').scrollIntoView({
                                behavior: \'smooth\',
                                block: \'start\',
                                inline: \'start\',
                            }),
                        200,
                    )
                })'
            )
            : null
    "
    :default="$getColumns('default')"
    :sm="$getColumns('sm')"
    :md="$getColumns('md')"
    :lg="$getColumns('lg')"
    :xl="$getColumns('xl')"
    :two-xl="$getColumns('2xl')"
    :attributes="
        \Filament\Support\prepare_inherited_attributes($getExtraAttributeBag())
            ->class(['fi-fo-component-ctn gap-6'])
    "
>
    @foreach ($getComponents(withHidden: true) as $formComponent)
</x-filament::grid>
{{-- format-ignore-end --}}
