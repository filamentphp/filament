@php
    use Filament\Actions\Action;
    use Filament\Actions\ActionGroup;
    use Filament\Schemas\Components\Component;
    use Filament\Support\Enums\VerticalAlignment;

    $fromBreakpoint = $getFromBreakpoint();
    $verticalAlignment = $getVerticalAlignment();

    if (! $verticalAlignment instanceof VerticalAlignment) {
        $verticalAlignment = filled($verticalAlignment) ? (VerticalAlignment::tryFrom($verticalAlignment) ?? $verticalAlignment) : null;
    }
@endphp

<div
    {{
        $attributes
            ->merge($getExtraAttributes(), escape: false)
            ->class([
                'fi-sc-flex',
                'fi-dense' => $isDense(),
                'fi-from-' . ($fromBreakpoint ?? 'default'),
                ($verticalAlignment instanceof VerticalAlignment) ? "fi-vertical-align-{$verticalAlignment->value}" : $verticalAlignment,
            ])
    }}
>
    @foreach ($getChildSchema()->getComponents() as $component)
        @if (($component instanceof Action) || ($component instanceof ActionGroup))
            <div>
                {{ $component }}
            </div>
        @else
            @php
                $hiddenJs = $component->getHiddenJs();
                $visibleJs = $component->getVisibleJs();

                $componentStatePath = $component->getStatePath();
            @endphp

            <div
                x-data="filamentSchemaComponent({
                            path: @js($componentStatePath),
                            containerPath: @js($statePath),
                            isLive: @js($schemaComponent->isLive()),
                            $wire,
                        })"
                @if ($afterStateUpdatedJs = $schemaComponent->getAfterStateUpdatedJs())
                    x-init="{!! implode(';', array_map(
                        fn (string $js): string => '$wire.watch(' . Js::from($componentStatePath) . ', ($state, $old) => ($state !== undefined) && eval(' . Js::from($js) . '))',
                        $afterStateUpdatedJs,
                    )) !!}"
                @endif
                @if (filled($visibilityJs = match ([filled($hiddenJs), filled($visibleJs)]) {
                         [true, true] => "(! ({$hiddenJs})) && ({$visibleJs})",
                         [true, false] => "! ({$hiddenJs})",
                         [false, true] => $visibleJs,
                         default => null,
                     }))
                    x-bind:class="{ 'fi-hidden': ! ({!! $visibilityJs !!}) }"
                    x-cloak
                @endif
                @class([
                    'fi-growable' => ($component instanceof Component) && $component->canGrow(),
                ])
            >
                {{ $component }}
            </div>
        @endif
    @endforeach
</div>
