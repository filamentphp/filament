@props([
    'actions' => [],
    'actionsAlignment' => null,
    'breadcrumbs' => [],
    'heading' => null,
    'overflowActionsIcon' => null,
    'overflowActionsLabel' => null,
    'responsive' => false,
    'responsiveBreakpoint' => 'md',
    'subheading' => null,
])

<header
    {{
        $attributes->class([
            'fi-header',
            'fi-header-has-breadcrumbs' => $breadcrumbs,
            'fi-header-has-responsive-actions' => $responsive && $actions,
        ])
    }}
>
    <div>
        @if ($breadcrumbs)
            <x-filament::breadcrumbs :breadcrumbs="$breadcrumbs" />
        @endif

        @if (filled($heading))
            <h1 class="fi-header-heading">
                {{ $heading }}
            </h1>
        @endif

        @if (filled($subheading))
            <p class="fi-header-subheading">
                {{ $subheading }}
            </p>
        @endif
    </div>

    @php
        $beforeActions = \Filament\Support\Facades\FilamentView::renderHook(\Filament\View\PanelsRenderHook::PAGE_HEADER_ACTIONS_BEFORE, scopes: $this->getRenderHookScopes());
        $afterActions = \Filament\Support\Facades\FilamentView::renderHook(\Filament\View\PanelsRenderHook::PAGE_HEADER_ACTIONS_AFTER, scopes: $this->getRenderHookScopes());
    @endphp

    @if (filled($beforeActions) || $actions || filled($afterActions))
        <div
            @class([
                'fi-header-actions-ctn',
                "fi-header-responsive-from-{$responsiveBreakpoint}" => $responsive && $actions,
            ])
        >
            {{ $beforeActions }}

            @if ($actions)
                @if ($responsive)
                    <x-filament::actions
                        :actions="$actions"
                        :alignment="$actionsAlignment"
                        class="fi-header-actions-expanded"
                    />

                    @php
                        $overflowGroup = \Filament\Actions\ActionGroup::make(
                            collect($actions)
                                ->map(static fn (\Filament\Actions\Action | \Filament\Actions\ActionGroup $action) => $action->getClone())
                                ->all(),
                        )
                            ->dropdownPlacement('bottom-end')
                            ->livewire($this);

                        if (filled($overflowActionsIcon)) {
                            $overflowGroup->icon($overflowActionsIcon);
                        }

                        if (filled($overflowActionsLabel)) {
                            $overflowGroup->label($overflowActionsLabel);
                        }
                    @endphp

                    <div class="fi-header-actions-collapsed">
                        {{ $overflowGroup }}
                    </div>
                @else
                    <x-filament::actions
                        :actions="$actions"
                        :alignment="$actionsAlignment"
                    />
                @endif
            @endif

            {{ $afterActions }}
        </div>
    @endif
</header>
