@php
    use Filament\Actions\Action;
    use Filament\Support\Facades\FilamentView;
    use Filament\Support\Icons\Heroicon;
    use Filament\View\PanelsIconAlias;
    use Filament\View\PanelsRenderHook;
    use Illuminate\Support\Arr;

    $debounce = filament()->getGlobalSearchDebounce();
    $keyBindings = filament()->getGlobalSearchKeyBindings();
    $suffix = filament()->getGlobalSearchFieldSuffix();
    $categories = $results?->getCategories()
        ->map(static fn ($groupedResults) => collect($groupedResults))
        ->filter(static fn ($groupedResults): bool => $groupedResults->isNotEmpty());
    $resultsCount = $categories?->sum(static fn ($groupedResults): int => count($groupedResults)) ?? 0;
    $resultsMessage = $resultsCount
        ? trans_choice('filament-panels::global-search.results_count', $resultsCount, ['count' => $resultsCount])
        : __('filament-panels::global-search.no_results_message');
    $id = 'global-search-' . $this->getId();
@endphp

<div class="fi-global-search-ctn">
    {{ FilamentView::renderHook(PanelsRenderHook::GLOBAL_SEARCH_START) }}

    <div
        x-data="filamentGlobalSearch({
                    loadingMessage: @js(__('filament-panels::global-search.loading_message')),
                    failureMessage: @js(__('filament-panels::global-search.failure_message')),
                })"
        x-on:input="if ($event.target === $refs.input) inputChanged($event.target.value)"
        x-on:focusin="if ($event.target === $refs.input) reopen()"
        x-on:focusout="focusLeft($event)"
        x-on:keydown="keydown($event)"
        x-on:click.outside="dismiss()"
        x-on:open-modal.window.capture="
            if ($refs.results.contains(document.activeElement)) {
                dismiss()
                restoreFocus()
            }
        "
        x-on:livewire:navigate.window="navigationStarted($event)"
        x-on:livewire:navigated.window="navigationCompleted()"
        class="fi-global-search"
    >
        <div class="fi-global-search-field">
            <label for="{{ $id }}-input" class="fi-sr-only">
                {{ __('filament-panels::global-search.field.label') }}
            </label>

            <x-filament::input.wrapper
                :prefix-icon="Heroicon::MagnifyingGlass"
                :prefix-icon-alias="PanelsIconAlias::GLOBAL_SEARCH_FIELD"
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
                    id="{{ $id }}-input"
                    aria-controls="{{ $id }}-results"
                    aria-describedby="{{ $id }}-instructions"
                    x-ref="input"
                    wire:model.live.debounce.{{ $debounce }}="search"
                    x-mousetrap.global.{{ collect($keyBindings)->map(fn (string $keyBinding): string => str_replace('+', '-', $keyBinding))->implode('.') }}="$refs.input?.focus()"
                    class="fi-input fi-input-has-inline-prefix"
                />
            </x-filament::input.wrapper>
        </div>

        <p id="{{ $id }}-instructions" class="fi-sr-only">
            {{ __('filament-panels::global-search.field.instructions') }}
        </p>

        <p
            wire:ignore
            role="status"
            aria-atomic="true"
            x-text="status"
            class="fi-sr-only"
        ></p>

        <div
            id="{{ $id }}-results"
            role="region"
            aria-label="{{ __('filament-panels::global-search.results_label') }}"
            data-query="{{ trim($this->search ?? '') }}"
            data-has-results="{{ $results !== null ? 'true' : 'false' }}"
            data-message="{{ $resultsMessage }}"
            x-ref="results"
            x-show="isOpen"
            x-bind:inert="! isOpen"
            x-bind:aria-hidden="! isOpen"
            x-cloak
            x-on:click="linkActivated($event)"
            x-transition:enter-start="fi-transition-enter-start"
            x-transition:leave-end="fi-transition-leave-end"
            class="fi-global-search-results-ctn"
        >
            @if ($results !== null)
                @if ($categories->isEmpty())
                    <p class="fi-global-search-no-results-message">
                        {{ __('filament-panels::global-search.no_results_message') }}
                    </p>
                @else
                    <ul class="fi-global-search-results">
                        @foreach ($categories as $group => $groupedResults)
                            @php
                                $groupHeadingId = $id . '-group-' . $loop->index;
                            @endphp

                            <li class="fi-global-search-result-group">
                                <h3
                                    id="{{ $groupHeadingId }}"
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
                                            $resultHeadingId = $groupHeadingId . '-result-' . $loop->index;
                                        @endphp

                                        <li
                                            @class([
                                                'fi-global-search-result',
                                                'fi-global-search-result-has-actions' => $resultVisibleActions,
                                            ])
                                        >
                                            <a
                                                {{ \Filament\Support\generate_href_html($result->url) }}
                                                class="fi-global-search-result-link"
                                            >
                                                <h4
                                                    id="{{ $resultHeadingId }}"
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
                                                                @if ($isAssoc ??= Arr::isAssoc($result->details))
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
                                                        @php
                                                            $actionAttributes = $action->getExtraAttributes();
                                                            $actionAttributes['aria-describedby'] = trim(($actionAttributes['aria-describedby'] ?? '') . ' ' . $groupHeadingId . ' ' . $resultHeadingId);

                                                            if (in_array($action->getView(), [Action::LINK_VIEW, Action::BUTTON_VIEW, Action::ICON_BUTTON_VIEW, Action::BADGE_VIEW])) {
                                                                $actionAttributes['data-global-search-action'] = true;
                                                            }
                                                        @endphp

                                                        {{ (clone $action)->extraAttributes($actionAttributes) }}
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
            @endif
        </div>
    </div>

    {{ FilamentView::renderHook(PanelsRenderHook::GLOBAL_SEARCH_END) }}
</div>
