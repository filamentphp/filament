@props([
    'action',
    'actionAlignment' => null,
    'afterItem' => null,
    'blocks',
    'columns' => null,
    'isSearchable' => false,
    'key',
    'noSearchResultsMessage' => null,
    'searchDebounce' => 0,
    'searchPrompt' => null,
    'trigger',
    'width' => null,
])

@php
    use Filament\Forms\View\FormsIconAlias;
    use Filament\Support\Enums\Alignment;
    use Filament\Support\Enums\GridDirection;
    use Filament\Support\Facades\FilamentAsset;
    use Filament\Support\Icons\Heroicon;
    use Filament\Support\View\ComponentAttributeBag as FilamentComponentAttributeBag;
    use Illuminate\Contracts\Support\Htmlable;
    use Illuminate\Support\Js;
    use Illuminate\Support\Str;

    $listAttributes = $isSearchable
        ? new FilamentComponentAttributeBag([
            'x-load' => true,
            'x-load-src' => FilamentAsset::getAlpineComponentSrc('builder', 'filament/forms'),
            'x-data' => 'builderBlockPickerFormComponent()',
            'x-on:dropdown-escape' => 'handleEscape($event)',
            'data-dropdown-escape' => true,
        ])
        : new FilamentComponentAttributeBag;
@endphp

<x-filament::dropdown
    :placement="
        match ($actionAlignment) {
            Alignment::Start, Alignment::Left => 'bottom-start',
            Alignment::End, Alignment::Right => 'bottom-end',
            default => null,
        }
    "
    shift
    :width="$width"
    :wire:key="$key . '.' . $action->getName() . '.' . $afterItem . '.block-picker.' . md5(serialize([$width, $actionAlignment, filled($blocks), $isSearchable]))"
    :attributes="
        \Filament\Support\prepare_inherited_attributes(
            $attributes->class([
                'fi-fo-builder-block-picker',
                ($actionAlignment instanceof Alignment) ? ('fi-align-' . $actionAlignment->value) : $actionAlignment,
            ]),
        )
    "
>
    <x-slot name="trigger">
        {{ $trigger }}
    </x-slot>

    @if (filled($blocks))
        <x-filament::dropdown.list
            :attributes="\Filament\Support\prepare_inherited_attributes($listAttributes)"
        >
            @if ($isSearchable)
                <div class="fi-fo-builder-block-picker-search-ctn">
                    {{ \Filament\Support\generate_icon_html(Heroicon::MagnifyingGlass, FormsIconAlias::COMPONENTS_BUILDER_BLOCK_PICKER_SEARCH_FIELD) }}

                    <x-filament::input
                        type="search"
                        data-dropdown-autofocus
                        x-ref="searchInput"
                        x-on:dropdown-autofocus="clearSearch()"
                        x-on:keydown.enter.prevent=""
                        :attributes="
                            \Filament\Support\prepare_inherited_attributes(
                                new FilamentComponentAttributeBag([
                                    'aria-label' => e($searchPrompt),
                                    'placeholder' => e($searchPrompt),
                                    'x-model.debounce.' . $searchDebounce => 'search',
                                ]),
                            )
                        "
                    />
                </div>
            @endif

            <div
                {{ (new FilamentComponentAttributeBag)->grid($columns, GridDirection::Column) }}
            >
                @foreach ($blocks as $block)
                    @php
                        $blockIcon = $block->getIcon();
                        $blockLabel = $block->getLabel();

                        $blockSearchLabel = null;

                        if ($isSearchable) {
                            $blockSearchLabel = $blockLabel;

                            if ($blockSearchLabel instanceof Htmlable) {
                                $blockSearchLabel = html_entity_decode(strip_tags($blockSearchLabel->toHtml()), ENT_QUOTES | ENT_HTML5, 'UTF-8');
                            }

                            $blockSearchLabel = Str::lower($blockSearchLabel);
                        }

                        $wireClickActionArguments = ['block' => $block->getName()];

                        if (filled($afterItem)) {
                            $wireClickActionArguments['afterItem'] = $afterItem;
                        }

                        $wireClickActionArguments = Js::from($wireClickActionArguments);

                        $wireClickAction = "mountAction('{$action->getName()}', {$wireClickActionArguments}, { schemaComponent: '{$key}' })";
                    @endphp

                    <x-filament::dropdown.list.item
                        :icon="$blockIcon"
                        x-on:click="close"
                        :wire:click="$wireClickAction"
                        :wire:key="$isSearchable ? md5($wireClickAction . $blockSearchLabel) : null"
                        :data-block-label="$blockSearchLabel"
                        :x-show="$isSearchable ? 'isBlockVisible($el)' : null"
                    >
                        {{ $blockLabel }}
                    </x-filament::dropdown.list.item>
                @endforeach
            </div>

            @if ($isSearchable)
                <div
                    x-cloak
                    x-show="hasNoSearchResults"
                    role="status"
                    aria-live="polite"
                    class="fi-fo-builder-block-picker-no-search-results-message"
                >
                    {{ $noSearchResultsMessage }}
                </div>
            @endif
        </x-filament::dropdown.list>
    @endif
</x-filament::dropdown>
