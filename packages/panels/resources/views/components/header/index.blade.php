@props([
    'actions' => [],
    'actionsAlignment' => null,
    'breadcrumbs' => [],
    'heading' => null,
    'subheading' => null,
])

@php
    use Filament\Support\Facades\FilamentView;
    use Filament\View\PanelsRenderHook;

    use function Filament\Support\is_slot_empty;
@endphp

<header
    {{
        $attributes->class([
            'fi-header',
            'fi-header-has-breadcrumbs' => $breadcrumbs,
        ])
    }}
>
    @if ($breadcrumbs)
        <x-filament::breadcrumbs :breadcrumbs="$breadcrumbs" />
    @endif

    <div class="fi-header-main">
        @capture($headingContent)
            {{ FilamentView::renderHook(PanelsRenderHook::PAGE_HEADER_HEADING_BEFORE, scopes: $this->getRenderHookScopes()) }}

            @if (filled($heading))
                <h1 class="fi-header-heading">
                    {{ $heading }}
                </h1>
            @endif

            {{ FilamentView::renderHook(PanelsRenderHook::PAGE_HEADER_HEADING_AFTER, scopes: $this->getRenderHookScopes()) }}

            @if (filled($subheading))
                <p class="fi-header-subheading">
                    {{ $subheading }}
                </p>
            @endif
        @endcapture

        @php
            $headingContent = $headingContent();
        @endphp

        @if (! is_slot_empty($headingContent))
            <div>{{ $headingContent }}</div>
        @else
            {{ $headingContent }}
        @endif

        @php
            $beforeActions = FilamentView::renderHook(PanelsRenderHook::PAGE_HEADER_ACTIONS_BEFORE, scopes: $this->getRenderHookScopes());
            $afterActions = FilamentView::renderHook(PanelsRenderHook::PAGE_HEADER_ACTIONS_AFTER, scopes: $this->getRenderHookScopes());
        @endphp

        @capture($actionsContent)
            {{ $beforeActions }}

            @if ($actions)
                <x-filament::actions
                    :actions="$actions"
                    :alignment="$actionsAlignment"
                />
            @endif

            {{ $afterActions }}
        @endcapture

        @php
            $actionsContent = $actionsContent();
        @endphp

        @if (! is_slot_empty($actionsContent))
            <div class="fi-header-actions-ctn">
                {{ $actionsContent }}
            </div>
        @else
            {{ $actionsContent }}
        @endif
    </div>
</header>
