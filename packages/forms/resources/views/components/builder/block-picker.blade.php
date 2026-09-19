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

    $blocks = array_values($blocks);

    $listAttributes = $isSearchable
        ? new FilamentComponentAttributeBag([
            'x-load' => true,
            'x-load-src' => FilamentAsset::getAlpineComponentSrc('builder', 'filament/forms'),
            'x-data' => 'builderBlockPickerFormComponent()',
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

    <x-filament::dropdown.list
        :attributes="\Filament\Support\prepare_inherited_attributes($listAttributes)"
    >
        @if ($isSearchable)
            <div class="fi-fo-builder-block-picker-search-ctn">
                {{ \Filament\Support\generate_icon_html(Heroicon::MagnifyingGlass, FormsIconAlias::COMPONENTS_BUILDER_BLOCK_PICKER_SEARCH_FIELD) }}

                <x-filament::input
                    type="search"
                    data-dropdown-autofocus
                    x-on:dropdown-autofocus="clearSearch()"
                    :attributes="
                        \Filament\Support\prepare_inherited_attributes(
                            new FilamentComponentAttributeBag([
                                'aria-label' => $searchPrompt,
                                'placeholder' => $searchPrompt,
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

                    $blockSearchLabel = null;

                    if ($isSearchable) {
                        $blockSearchLabel = $block->getLabel();

                        if ($blockSearchLabel instanceof Htmlable) {
                            $blockSearchLabel = html_entity_decode(strip_tags($blockSearchLabel->toHtml()), ENT_QUOTES);
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
                    :data-block-label="$blockSearchLabel"
                    :x-show="$isSearchable ? 'isBlockVisible($el)' : null"
                >
                    {{ $block->getLabel() }}
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
</x-filament::dropdown>
