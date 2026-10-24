@php
    use Inertia\Ssr\SsrState;

    $hasServerRenderedContent = (bool) app(SsrState::class)->setPage($page)->dispatch();
@endphp

<div class="fi-inertia-stage" data-inertia-stage aria-busy="true">
    <div
        class="fi-inertia-loading"
        data-inertia-loading
        role="status"
        @if ($hasServerRenderedContent) hidden @endif
    >
        <x-filament::loading-indicator />
        <span>{{ __('filament-panels::inertia.loading') }}</span>
    </div>

    <div class="fi-inertia-error" data-inertia-error hidden>
        <p role="alert">{{ __('filament-panels::inertia.error') }}</p>
        <x-filament::button type="button" color="gray" data-inertia-retry>
            {{ __('filament-panels::inertia.actions.reload.label') }}
        </x-filament::button>
    </div>

    <div data-inertia-content inert>
        @inertia('filament-inertia')
    </div>
</div>
