@php
    $debounce = filament()->getGlobalSearchDebounce();
    $keyBindings = filament()->getGlobalSearchKeyBindings();
    $suffix = filament()->getGlobalSearchFieldSuffix();
@endphp

<div class="fi-global-search-ctn">
    {{ \Filament\Support\Facades\FilamentView::renderHook(\Filament\View\PanelsRenderHook::GLOBAL_SEARCH_START) }}

    <div
        x-on:focus-first-global-search-result.stop="$el.querySelector('.fi-global-search-result-link')?.focus()"
        class="fi-global-search"
    >
        <div x-id="['input']" class="fi-global-search-field">
            <label x-bind:for="$id('input')" class="fi-sr-only">
                {{ __('filament-panels::global-search.field.label') }}
            </label>

            <x-filament::input.wrapper
                :prefix-icon="\Filament\Support\Icons\Heroicon::MagnifyingGlass"
                :prefix-icon-alias="\Filament\View\PanelsIconAlias::GLOBAL_SEARCH_FIELD"
                inline-prefix
                :suffix="$suffix"
                inline-suffix
                wire:target="search"
            >
                <input
                    autocomplete="off"
                    maxlength="1000"
                    placeholder="{{ __('filament-panels::global-search.field.placeholder') }}"
                    type="search"
                    wire:key="global-search.field.input"
                    x-bind:id="$id('input')"
                    x-on:keydown.down.prevent.stop="$dispatch('focus-first-global-search-result')"
                    wire:model.live.debounce.{{ $debounce }}="search"
                    x-mousetrap.global.{{ collect($keyBindings)->map(fn (string $keyBinding): string => str_replace('+', '-', $keyBinding))->implode('.') }}="document.getElementById($id('input')).focus()"
                    class="fi-input fi-input-has-inline-prefix"
                />
            </x-filament::input.wrapper>
        </div>

        @if ($results !== null)
            <div
                x-data="{
                    isOpen: false,

                    open(event) {
                        this.isOpen = true
                    },

                    close(event) {
                        this.isOpen = false
                    },
                }"
                x-init="$nextTick(() => open())"
                x-on:click.away="close()"
                x-on:keydown.escape.window="close()"
                x-on:keydown.up.prevent="$focus.wrap().previous()"
                x-on:keydown.down.prevent="$focus.wrap().next()"
                x-on:open-global-search-results.window="$nextTick(() => open())"
                x-show="isOpen"
                x-transition:enter-start="fi-transition-enter-start"
                x-transition:leave-end="fi-transition-leave-end"
                class="fi-global-search-results-ctn"
            >
                @if ($results->getCategories()->isEmpty())
                    <p class="fi-global-search-no-results-message">
                        {{ __('filament-panels::global-search.no_results_message') }}
                    </p>
                @else
                    <ul class="fi-global-search-results">
                        @foreach ($results->getCategories() as $group => $groupedResults)
                            <li class="fi-global-search-result-group">
                                <h3
                                    class="fi-global-search-result-group-header"
                                >
                                    {{ $group }}
                                </h3>

                                <ul
                                    class="fi-global-search-result-group-results"
                                >
                                    @foreach ($groupedResults as $result)
                                        @php
                                            $resultVisibleActions = $result->getVisibleActions();
                                        @endphp

                                        <li
                                            @class([
                                                'fi-global-search-result',
                                                'fi-global-search-result-has-actions' => $resultVisibleActions,
                                            ])
                                        >
                                            <a
                                                {{ \Filament\Support\generate_href_html($result->url) }}
                                                x-on:click="close()"
                                                class="fi-global-search-result-link"
                                            >
                                                <h4
                                                    class="fi-global-search-result-heading"
                                                >
                                                    {{ $result->title }}
                                                </h4>

                                                @if ($result->details)
                                                    <dl
                                                        class="fi-global-search-result-details"
                                                    >
                                                        @foreach ($result->details as $label => $value)
                                                            <div
                                                                class="fi-global-search-result-detail"
                                                            >
                                                                @if ($isAssoc ??= \Illuminate\Support\Arr::isAssoc($result->details))
                                                                    <dt
                                                                        class="fi-global-search-result-detail-label"
                                                                    >
                                                                        {{ $label }}:
                                                                    </dt>
                                                                @endif

                                                                <dd
                                                                    class="fi-global-search-result-detail-value"
                                                                >
                                                                    {{ $value }}
                                                                </dd>
                                                            </div>
                                                        @endforeach
                                                    </dl>
                                                @endif
                                            </a>

                                            @if ($resultVisibleActions)
                                                <div
                                                    class="fi-global-search-result-actions"
                                                >
                                                    @foreach ($resultVisibleActions as $action)
                                                        {{ $action }}
                                                    @endforeach
                                                </div>
                                            @endif
                                        </li>
                                    @endforeach
                                </ul>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>
        @endif
    </div>

    {{ \Filament\Support\Facades\FilamentView::renderHook(\Filament\View\PanelsRenderHook::GLOBAL_SEARCH_END) }}
</div>
