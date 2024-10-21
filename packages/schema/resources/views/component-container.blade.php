@php
    use Filament\Support\Enums\MaxWidth;
    use Illuminate\Support\Js;
    use Illuminate\View\ComponentAttributeBag;

    $isRoot = $isRoot();
@endphp

<div
    {{ $getExtraAttributeBag()
        ->grid($getColumns())
        ->merge([
            'x-data' => $isRoot ? 'filamentSchema({ livewireId: ' . Js::from($this->getId()) . ' })' : null,
            'x-on:form-validation-error.window' => $isRoot ? 'handleFormValidationError' : null,
        ], escape: false)
        ->class(['fi-fo-component-ctn gap-6']) }}
>
    @foreach ($getComponents(withHidden: true) as $schemaComponent)
        @php
            /**
             * Instead of only rendering the hidden components, we should
             * render the `<div>` wrappers for all fields, regardless of
             * if they are hidden or not. This is to solve Livewire DOM
             * diffing issues.
             *
             * Additionally, any `<div>` elements that wrap hidden
             * components need to have `class="hidden"`, so that they
             * don't consume grid space.
             */
            $isHidden = $schemaComponent->isHidden();

            $hiddenJs = $schemaComponent->getHiddenJs();
            $visibleJs = $schemaComponent->getVisibleJs();
        @endphp

        <div
            {{ (new ComponentAttributeBag())
                ->gridColumn($schemaComponent->getColumnSpan(), $schemaComponent->getColumnStart(), $isHidden)
                ->merge([
                    'wire:key' => $schemaComponent->getLivewireKey(),
                ], escape: false)
                ->class([
                    match ($maxWidth = $schemaComponent->getMaxWidth()) {
                        MaxWidth::ExtraSmall, 'xs' => 'max-w-xs',
                        MaxWidth::Small, 'sm' => 'max-w-sm',
                        MaxWidth::Medium, 'md' => 'max-w-md',
                        MaxWidth::Large, 'lg' => 'max-w-lg',
                        MaxWidth::ExtraLarge, 'xl' => 'max-w-xl',
                        MaxWidth::TwoExtraLarge, '2xl' => 'max-w-2xl',
                        MaxWidth::ThreeExtraLarge, '3xl' => 'max-w-3xl',
                        MaxWidth::FourExtraLarge, '4xl' => 'max-w-4xl',
                        MaxWidth::FiveExtraLarge, '5xl' => 'max-w-5xl',
                        MaxWidth::SixExtraLarge, '6xl' => 'max-w-6xl',
                        MaxWidth::SevenExtraLarge, '7xl' => 'max-w-7xl',
                        default => $maxWidth,
                    },
                ]) }}
        >
            @if (! $isHidden)
                <div
                    x-data="{
                        $statePath:
                            {{ $jsStatePath = Js::from($schemaComponent->getStatePath()) }},
                        get $get() {
                            return makeGetUtility(@js($schemaComponent->getContainer()->getStatePath()))
                        },
                        get $set() {
                            return makeSetUtility(@js($schemaComponent->getContainer()->getStatePath()), @js($schemaComponent->isLive()))
                        },
                        get $state() {
                            return $wire.$get({{ $jsStatePath }})
                        },
                    }"
                    @if ($afterStateUpdatedJs = $schemaComponent->getAfterStateUpdatedJs())
                        {{-- format-ignore-start --}}x-init="@foreach ($afterStateUpdatedJs as $js) $wire.$watch({{ $jsStatePath }}, ($state, $old) => eval(@js($js))); @endforeach"
                    {{-- format-ignore-end --}}
                    @endif
                    @if (filled($xShow = match ([filled($hiddenJs), filled($visibleJs)]) {
                             [true, true] => "(! {$hiddenJs}) && ({$visibleJs})",
                             [true, false] => "! {$hiddenJs}",
                             [false, true] => $visibleJs,
                             default => null,
                         }))
                        x-show="{{ $xShow }}"
                    x-cloak
                    @endif
                >
                    {{ $schemaComponent }}
                </div>
            @endif
        </div>
    @endforeach
</div>
