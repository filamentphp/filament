@php
    use Filament\Actions\Action;
    use Filament\Actions\ActionGroup;
    use Filament\Schemas\Components\Component;
    use Filament\Support\Enums\Alignment;
    use Filament\Support\Enums\VerticalAlignment;

    $statePath = $getStatePath();

    $fromBreakpoint = $getFromBreakpoint();
    $verticalAlignment = $getVerticalAlignment();
    $alignment = $getAlignment();

    if (! $verticalAlignment instanceof VerticalAlignment) {
        $verticalAlignment = filled($verticalAlignment) ? (VerticalAlignment::tryFrom($verticalAlignment) ?? $verticalAlignment) : null;
    }

    if (! $alignment instanceof Alignment) {
        $alignment = filled($alignment) ? (Alignment::tryFrom($alignment) ?? $alignment) : null;
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
                ($alignment instanceof Alignment) ? "fi-align-{$alignment->value}" : $alignment,
            ])
    }}
>
    @foreach ($getChildSchema()->getComponents() as $schemaComponent)
        @if (($schemaComponent instanceof Action) || ($schemaComponent instanceof ActionGroup))
            <div>
                {{ $schemaComponent }}
            </div>
        @else
            @php
                $hiddenJs = $schemaComponent->getHiddenJs();
                $visibleJs = $schemaComponent->getVisibleJs();

                $schemaComponentStatePath = $schemaComponent->getStatePath();
            @endphp

            <div
                x-data="filamentSchemaComponent({
                            path: @js($schemaComponentStatePath),
                            containerPath: @js($statePath),
                            $wire,
                        })"
                @if ($afterStateUpdatedJs = $schemaComponent->getAfterStateUpdatedJs())
                    x-init="{!! implode(';', array_map(
                        fn (string $js): string => '$wire; $wire.watch(' . Js::from($schemaComponentStatePath) . ', ($state, $old) => isStateChanged($state, $old) && eval(' . Js::from($js) . '))',
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
                    'fi-growable' => ($schemaComponent instanceof Component) && $schemaComponent->canGrow(),
                ])
            >
                {{ $schemaComponent }}
            </div>
        @endif
    @endforeach
</div>
