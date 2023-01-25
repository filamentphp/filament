<x-dynamic-component
    :component="$getFieldWrapperView()"
    :id="$getId()"
    :label="$getLabel()"
    :label-sr-only="$isLabelHidden()"
    :helper-text="$getHelperText()"
    :hint="$getHint()"
    :hint-action="$getHintAction()"
    :hint-color="$getHintColor()"
    :hint-icon="$getHintIcon()"
    :required="$isRequired()"
    :state-path="$getStatePath()"
>
    @php
        $containers = $getChildComponentContainers();

        $isCloneable = $isCloneable();
        $isCollapsible = $isCollapsible();
        $isItemCreationDisabled = $isItemCreationDisabled();
        $isItemDeletionDisabled = $isItemDeletionDisabled();
        $isItemMovementDisabled = $isItemMovementDisabled();
    @endphp

    <div>
        @if ((count($containers) > 1) && $isCollapsible)
            <div class="space-x-2 rtl:space-x-reverse" x-data="{}">
                <x-forms::link
                    x-on:click="$dispatch('builder-collapse', '{{ $getStatePath() }}')"
                    tag="button"
                    size="sm"
                >
                    {{ __('forms::components.builder.buttons.collapse_all.label') }}
                </x-forms::link>

                <x-forms::link
                    x-on:click="$dispatch('builder-expand', '{{ $getStatePath() }}')"
                    tag="button"
                    size="sm"
                >
                    {{ __('forms::components.builder.buttons.expand_all.label') }}
                </x-forms::link>
            </div>
        @endif
    </div>

    <div {{ $attributes->merge($getExtraAttributes())->class([
        'filament-forms-builder-component space-y-6 rounded-xl',
        'bg-gray-50 p-6' => $isInset(),
        'dark:bg-gray-500/10' => $isInset() && config('forms.dark_mode'),
    ]) }}>
        @if (count($containers))
            <ul
                @class([
                    'space-y-12' => (! $isItemCreationDisabled) && (! $isItemMovementDisabled),
                    'space-y-6' => $isItemCreationDisabled || $isItemMovementDisabled,
                ])
                wire:sortable
                wire:end.stop="dispatchFormEvent('builder::moveItems', '{{ $getStatePath() }}', $event.target.sortable.toArray())"
            >
                @php
                    $hasBlockLabels = $hasBlockLabels();
                    $hasBlockNumbers = $hasBlockNumbers();
                @endphp
                <x-filament-support::grid
                    :default="$getGridColumns('default')"
                    :sm="$getGridColumns('sm')"
                    :md="$getGridColumns('md')"
                    :lg="$getGridColumns('lg')"
                    :xl="$getGridColumns('xl')"
                    :two-xl="$getGridColumns('2xl')"
                    wire:sortable
                    wire:end.stop="dispatchFormEvent('builder::moveItems', '{{ $getStatePath() }}', $event.target.sortable.toArray())"
                    class="gap-6"
                >

                @foreach ($containers as $uuid => $item)
                    <li
                        x-data="{
                            isCreateButtonVisible: false,
                            isCollapsed: @js($isCollapsed()),
                        }"
                        x-on:builder-collapse.window="$event.detail === '{{ $getStatePath() }}' && (isCollapsed = true)"
                        x-on:builder-expand.window="$event.detail === '{{ $getStatePath() }}' && (isCollapsed = false)"
                        x-on:click="isCreateButtonVisible = true"
                        x-on:mouseenter="isCreateButtonVisible = true"
                        x-on:click.away="isCreateButtonVisible = false"
                        x-on:mouseleave="isCreateButtonVisible = false"
                        wire:key="{{ $this->id }}.{{ $item->getStatePath() }}.item"
                        wire:sortable.item="{{ $uuid }}"
                        x-on:expand-concealing-component.window="
                            error = $el.querySelector('[data-validation-error]')

                            if (! error) {
                                return
                            }

                            isCollapsed = false

                            if (document.body.querySelector('[data-validation-error]') !== error) {
                                return
                            }

                            setTimeout(() => $el.scrollIntoView({ behavior: 'smooth', block: 'start', inline: 'start' }), 200)
                        "
                        @php
                            $columnSpan = $item->getParentComponent()?->getColumnSpan();
                        @endphp
                        @class([
                            'bg-white border border-gray-300 shadow-sm rounded-xl relative',
                            'dark:bg-gray-800 dark:border-gray-600' => config('forms.dark_mode'),
                            (data_get($columnSpan, 'default', null) ? match (data_get($columnSpan, 'default', null)) {
                                    1 => 'col-span-1',
                                    2 => 'col-span-2',
                                    3 => 'col-span-3',
                                    4 => 'col-span-4',
                                    5 => 'col-span-5',
                                    6 => 'col-span-6',
                                    7 => 'col-span-7',
                                    8 => 'col-span-8',
                                    9 => 'col-span-9',
                                    10 => 'col-span-10',
                                    11 => 'col-span-11',
                                    12 => 'col-span-12',
                                    'full' => 'col-span-full',
                                    default => data_get($columnSpan, 'default', 1),
                                } : null),
                                (data_get($columnSpan, 'sm', null) ? match (data_get($columnSpan, 'sm', null)) {
                                    1 => 'sm:col-span-1',
                                    2 => 'sm:col-span-2',
                                    3 => 'sm:col-span-3',
                                    4 => 'sm:col-span-4',
                                    5 => 'sm:col-span-5',
                                    6 => 'sm:col-span-6',
                                    7 => 'sm:col-span-7',
                                    8 => 'sm:col-span-8',
                                    9 => 'sm:col-span-9',
                                    10 => 'sm:col-span-10',
                                    11 => 'sm:col-span-11',
                                    12 => 'sm:col-span-12',
                                    'full' => 'sm:col-span-full',
                                    default => $sm,
                                } : null),
                                (data_get($columnSpan, 'md', null) ? match (data_get($columnSpan, 'md', null)) {
                                    1 => 'md:col-span-1',
                                    2 => 'md:col-span-2',
                                    3 => 'md:col-span-3',
                                    4 => 'md:col-span-4',
                                    5 => 'md:col-span-5',
                                    6 => 'md:col-span-6',
                                    7 => 'md:col-span-7',
                                    8 => 'md:col-span-8',
                                    9 => 'md:col-span-9',
                                    10 => 'md:col-span-10',
                                    11 => 'md:col-span-11',
                                    12 => 'md:col-span-12',
                                    'full' => 'md:col-span-full',
                                    default => $md,
                                } : null),
                                (data_get($columnSpan, 'lg', null) ? match (data_get($columnSpan, 'lg', null)) {
                                    1 => 'lg:col-span-1',
                                    2 => 'lg:col-span-2',
                                    3 => 'lg:col-span-3',
                                    4 => 'lg:col-span-4',
                                    5 => 'lg:col-span-5',
                                    6 => 'lg:col-span-6',
                                    7 => 'lg:col-span-7',
                                    8 => 'lg:col-span-8',
                                    9 => 'lg:col-span-9',
                                    10 => 'lg:col-span-10',
                                    11 => 'lg:col-span-11',
                                    12 => 'lg:col-span-12',
                                    'full' => 'lg:col-span-full',
                                    default => $lg,
                                } : null),
                                (data_get($columnSpan, 'xl', null) ? match (data_get($columnSpan, 'xl', null)) {
                                    1 => 'xl:col-span-1',
                                    2 => 'xl:col-span-2',
                                    3 => 'xl:col-span-3',
                                    4 => 'xl:col-span-4',
                                    5 => 'xl:col-span-5',
                                    6 => 'xl:col-span-6',
                                    7 => 'xl:col-span-7',
                                    8 => 'xl:col-span-8',
                                    9 => 'xl:col-span-9',
                                    10 => 'xl:col-span-10',
                                    11 => 'xl:col-span-11',
                                    12 => 'xl:col-span-12',
                                    'full' => 'xl:col-span-full',
                                    default => $xl,
                                } : null),
                                (data_get($columnSpan, '2xl', null) ? match (data_get($columnSpan, '2xl', null)) {
                                    1 => '2xl:col-span-1',
                                    2 => '2xl:col-span-2',
                                    3 => '2xl:col-span-3',
                                    4 => '2xl:col-span-4',
                                    5 => '2xl:col-span-5',
                                    6 => '2xl:col-span-6',
                                    7 => '2xl:col-span-7',
                                    8 => '2xl:col-span-8',
                                    9 => '2xl:col-span-9',
                                    10 => '2xl:col-span-10',
                                    11 => '2xl:col-span-11',
                                    12 => '2xl:col-span-12',
                                    'full' => '2xl:col-span-full',
                                    default => $twoXl,
                                } : null),
                        ])
                    >
                        @if ((! $isItemMovementDisabled) || $hasBlockLabels || (! $isItemDeletionDisabled) || $isCollapsible || $isCloneable)
                            <header
                                @if ($isCollapsible) x-on:click.stop="isCollapsed = ! isCollapsed" @endif
                                @class([
                                    'flex items-center h-10 overflow-hidden border-b bg-gray-50 rounded-t-xl',
                                    'dark:bg-gray-800 dark:border-gray-700' => config('forms.dark_mode'),
                                    'cursor-pointer' => $isCollapsible,
                                ])
                            >
                                @unless ($isItemMovementDisabled)
                                    <button
                                        title="{{ __('forms::components.builder.buttons.move_item.label') }}"
                                        x-on:click.stop
                                        wire:sortable.handle
                                        wire:keydown.prevent.arrow-up="dispatchFormEvent('builder::moveItemUp', '{{ $getStatePath() }}', '{{ $uuid }}')"
                                        wire:keydown.prevent.arrow-down="dispatchFormEvent('builder::moveItemDown', '{{ $getStatePath() }}', '{{ $uuid }}')"
                                        type="button"
                                        @class([
                                            'flex items-center justify-center flex-none w-10 h-10 text-gray-400 border-r rtl:border-l rtl:border-r-0 transition hover:text-gray-500',
                                            'dark:border-gray-700' => config('forms.dark_mode'),
                                        ])
                                    >
                                        <span class="sr-only">
                                            {{ __('forms::components.builder.buttons.move_item.label') }}
                                        </span>

                                        <x-heroicon-s-switch-vertical class="w-4 h-4"/>
                                    </button>
                                @endunless

                                @if ($hasBlockLabels)
                                    <p @class([
                                        'flex-none px-4 text-xs font-medium text-gray-600 truncate',
                                        'dark:text-gray-400' => config('forms.dark_mode'),
                                    ])>
                                        @php
                                            $block = $item->getParentComponent();

                                            $block->labelState($item->getRawState());
                                        @endphp

                                        {{ $item->getParentComponent()->getLabel() }}

                                        @php
                                            $block->labelState(null);
                                        @endphp

                                        @if ($hasBlockNumbers)
                                            <small class="font-mono">{{ $loop->iteration }}</small>
                                        @endif
                                    </p>
                                @endif

                                <div class="flex-1"></div>

                                <ul @class([
                                    'flex divide-x rtl:divide-x-reverse',
                                    'dark:divide-gray-700' => config('forms.dark_mode'),
                                ])>
                                    @if ($isCloneable)
                                        <li>
                                            <button
                                                title="{{ __('forms::components.builder.buttons.clone_item.label') }}"
                                                wire:click.stop="dispatchFormEvent('builder::cloneItem', '{{ $getStatePath() }}', '{{ $uuid }}')"
                                                type="button"
                                                @class([
                                                    'flex items-center justify-center flex-none w-10 h-10 text-gray-400 transition hover:text-gray-500',
                                                    'dark:border-gray-700' => config('forms.dark_mode'),
                                                ])
                                            >
                                                <span class="sr-only">
                                                    {{ __('forms::components.builder.buttons.clone_item.label') }}
                                                </span>

                                                <x-heroicon-s-duplicate class="w-4 h-4"/>
                                            </button>
                                        </li>
                                    @endif

                                    @unless ($isItemDeletionDisabled)
                                        <li>
                                            <button
                                                title="{{ __('forms::components.builder.buttons.delete_item.label') }}"
                                                wire:click.stop="dispatchFormEvent('builder::deleteItem', '{{ $getStatePath() }}', '{{ $uuid }}')"
                                                type="button"
                                                @class([
                                                    'flex items-center justify-center flex-none w-10 h-10 text-danger-600 transition hover:text-danger-500',
                                                    'dark:text-danger-500 dark:hover:text-danger-400' => config('forms.dark_mode'),
                                                ])
                                            >
                                                <span class="sr-only">
                                                    {{ __('forms::components.builder.buttons.delete_item.label') }}
                                                </span>

                                                <x-heroicon-s-trash class="w-4 h-4"/>
                                            </button>
                                        </li>
                                    @endunless

                                    @if ($isCollapsible)
                                        <li>
                                            <button
                                                x-bind:title="(! isCollapsed) ? '{{ __('forms::components.builder.buttons.collapse_item.label') }}' : '{{ __('forms::components.builder.buttons.expand_item.label') }}'"
                                                x-on:click.stop="isCollapsed = ! isCollapsed"
                                                type="button"
                                                class="flex items-center justify-center flex-none w-10 h-10 text-gray-400 transition hover:text-gray-500"
                                            >
                                                <x-heroicon-s-minus-sm class="w-4 h-4" x-show="! isCollapsed"/>

                                                <span class="sr-only" x-show="! isCollapsed">
                                                    {{ __('forms::components.builder.buttons.collapse_item.label') }}
                                                </span>

                                                <x-heroicon-s-plus-sm class="w-4 h-4" x-show="isCollapsed" x-cloak/>

                                                <span class="sr-only" x-show="isCollapsed" x-cloak>
                                                    {{ __('forms::components.builder.buttons.expand_item.label') }}
                                                </span>
                                            </button>
                                        </li>
                                    @endif
                                </ul>
                            </header>
                        @endif

                        <div class="p-6" x-show="! isCollapsed">
                            {{ $item }}
                        </div>

                        <div class="p-2 text-xs text-center text-gray-400" x-show="isCollapsed" x-cloak>
                            {{ __('forms::components.builder.collapsed') }}
                        </div>

                        @if ((! $loop->last) && (! $isItemCreationDisabled) && (! $isItemMovementDisabled))
                            <div
                                x-show="isCreateButtonVisible"
                                x-transition
                                class="absolute inset-x-0 bottom-0 flex items-center justify-center h-12 -mb-12"
                            >
                                <x-forms::builder.block-picker
                                    :blocks="$getBlocks()"
                                    :create-after-item="$uuid"
                                    :state-path="$getStatePath()"
                                >
                                    <x-slot name="trigger">
                                        <x-forms::icon-button
                                            :label="$getCreateItemBetweenButtonLabel()"
                                            icon="heroicon-o-plus"
                                        />
                                    </x-slot>
                                </x-forms::builder.block-picker>
                            </div>
                        @endif
                    </li>
                @endforeach
                </x-filament-support::grid>
            </ul>
        @endif

        @if (! $isItemCreationDisabled)
            <x-forms::builder.block-picker
                :blocks="$getBlocks()"
                :state-path="$getStatePath()"
                class="flex justify-center"
            >
                <x-slot name="trigger">
                    <x-forms::button size="sm">
                        {{ $getCreateItemButtonLabel() }}
                    </x-forms::button>
                </x-slot>
            </x-forms::builder.block-picker>
        @endif
    </div>
</x-dynamic-component>
