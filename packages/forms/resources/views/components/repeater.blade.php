<x-dynamic-component
    :component="$getFieldWrapperView()"
    :id="$getId()"
    :label="$getLabel()"
    :label-sr-only="$isLabelHidden()"
    :helper-text="$getHelperText()"
    :hint="$getHint()"
    :hint-icon="$getHintIcon()"
    :required="$isRequired()"
    :state-path="$getStatePath()"
>
    @php
        $containers = $getChildComponentContainers();

        $isCollapsible = $isCollapsible();
        $isItemCreationDisabled = $isItemCreationDisabled();
        $isItemDeletionDisabled = $isItemDeletionDisabled();
        $isItemMovementDisabled = $isItemMovementDisabled();
    @endphp

    @if ((count($containers) > 1) && $isCollapsible)
        <div class="space-x-2 rtl:space-x-reverse" x-data="{}">
            <x-forms::link
                x-on:click="$dispatch('repeater-collapse', '{{ $getStatePath() }}')"
                tag="button"
                size="sm"
            >
                {{ __('forms::components.repeater.buttons.collapse_all.label') }}
            </x-forms::link>

            <x-forms::link
                x-on:click="$dispatch('repeater-expand', '{{ $getStatePath() }}')"
                tag="button"
                size="sm"
            >
                {{ __('forms::components.repeater.buttons.expand_all.label') }}
            </x-forms::link>
        </div>
    @endif

    <div {{ $attributes->merge($getExtraAttributes())->class([
        'space-y-6 rounded-xl filament-forms-repeater-component',
        'bg-gray-50 p-6' => $isInset(),
        'dark:bg-gray-500/10' => $isInset() && config('forms.dark_mode'),
    ]) }}>
        @if (count($containers))
            <ul
                @class([
                    'grid gap-6 grid-cols-1',
                    'grid-cols-1' => (! $getGrid('default')) || $getGrid('default') === 1,
                    'grid-cols-2' => $getGrid('default') === 2,
                    'grid-cols-3' => $getGrid('default') === 3,
                    'grid-cols-4' => $getGrid('default') === 4,
                    'grid-cols-5' => $getGrid('default') === 5,
                    'grid-cols-6' => $getGrid('default') === 6,
                    'grid-cols-7' => $getGrid('default') === 7,
                    'grid-cols-8' => $getGrid('default') === 8,
                    'grid-cols-9' => $getGrid('default') === 9,
                    'grid-cols-10' => $getGrid('default') === 10,
                    'grid-cols-11' => $getGrid('default') === 11,
                    'grid-cols-12' => $getGrid('default') === 12,
                    'sm:grid-cols-1' => $getGrid('sm') === 1,
                    'sm:grid-cols-2' => $getGrid('sm') === 2,
                    'sm:grid-cols-3' => $getGrid('sm') === 3,
                    'sm:grid-cols-4' => $getGrid('sm') === 4,
                    'sm:grid-cols-5' => $getGrid('sm') === 5,
                    'sm:grid-cols-6' => $getGrid('sm') === 6,
                    'sm:grid-cols-7' => $getGrid('sm') === 7,
                    'sm:grid-cols-8' => $getGrid('sm') === 8,
                    'sm:grid-cols-9' => $getGrid('sm') === 9,
                    'sm:grid-cols-10' => $getGrid('sm') === 10,
                    'sm:grid-cols-11' => $getGrid('sm') === 11,
                    'sm:grid-cols-12' => $getGrid('sm') === 12,
                    'md:grid-cols-1' => $getGrid('md') === 1,
                    'md:grid-cols-2' => $getGrid('md') === 2,
                    'md:grid-cols-3' => $getGrid('md') === 3,
                    'md:grid-cols-4' => $getGrid('md') === 4,
                    'md:grid-cols-5' => $getGrid('md') === 5,
                    'md:grid-cols-6' => $getGrid('md') === 6,
                    'md:grid-cols-7' => $getGrid('md') === 7,
                    'md:grid-cols-8' => $getGrid('md') === 8,
                    'md:grid-cols-9' => $getGrid('md') === 9,
                    'md:grid-cols-10' => $getGrid('md') === 10,
                    'md:grid-cols-11' => $getGrid('md') === 11,
                    'md:grid-cols-12' => $getGrid('md') === 12,
                    'lg:grid-cols-1' => $getGrid('lg') === 1,
                    'lg:grid-cols-2' => $getGrid('lg') === 2,
                    'lg:grid-cols-3' => $getGrid('lg') === 3,
                    'lg:grid-cols-4' => $getGrid('lg') === 4,
                    'lg:grid-cols-5' => $getGrid('lg') === 5,
                    'lg:grid-cols-6' => $getGrid('lg') === 6,
                    'lg:grid-cols-7' => $getGrid('lg') === 7,
                    'lg:grid-cols-8' => $getGrid('lg') === 8,
                    'lg:grid-cols-9' => $getGrid('lg') === 9,
                    'lg:grid-cols-10' => $getGrid('lg') === 10,
                    'lg:grid-cols-11' => $getGrid('lg') === 11,
                    'lg:grid-cols-12' => $getGrid('lg') === 12,
                    'xl:grid-cols-1' => $getGrid('xl') === 1,
                    'xl:grid-cols-2' => $getGrid('xl') === 2,
                    'xl:grid-cols-3' => $getGrid('xl') === 3,
                    'xl:grid-cols-4' => $getGrid('xl') === 4,
                    'xl:grid-cols-5' => $getGrid('xl') === 5,
                    'xl:grid-cols-6' => $getGrid('xl') === 6,
                    'xl:grid-cols-7' => $getGrid('xl') === 7,
                    'xl:grid-cols-8' => $getGrid('xl') === 8,
                    'xl:grid-cols-9' => $getGrid('xl') === 9,
                    'xl:grid-cols-10' => $getGrid('xl') === 10,
                    'xl:grid-cols-11' => $getGrid('xl') === 11,
                    'xl:grid-cols-12' => $getGrid('xl') === 12,
                    '2xl:grid-cols-1' => $getGrid('2xl') === 1,
                    '2xl:grid-cols-2' => $getGrid('2xl') === 2,
                    '2xl:grid-cols-3' => $getGrid('2xl') === 3,
                    '2xl:grid-cols-4' => $getGrid('2xl') === 4,
                    '2xl:grid-cols-5' => $getGrid('2xl') === 5,
                    '2xl:grid-cols-6' => $getGrid('2xl') === 6,
                    '2xl:grid-cols-7' => $getGrid('2xl') === 7,
                    '2xl:grid-cols-8' => $getGrid('2xl') === 8,
                    '2xl:grid-cols-9' => $getGrid('2xl') === 9,
                    '2xl:grid-cols-10' => $getGrid('2xl') === 10,
                    '2xl:grid-cols-11' => $getGrid('2xl') === 11,
                    '2xl:grid-cols-12' => $getGrid('2xl') === 12,
                ])
                wire:sortable
                wire:end.stop="dispatchFormEvent('repeater::moveItems', '{{ $getStatePath() }}', $event.target.sortable.toArray())"
            >
                @foreach ($containers as $uuid => $item)
                    <li
                        x-data="{ isCollapsed: @js($isCollapsed()) }"
                        x-on:repeater-collapse.window="$event.detail === '{{ $getStatePath() }}' && (isCollapsed = true)"
                        x-on:repeater-expand.window="$event.detail === '{{ $getStatePath() }}' && (isCollapsed = false)"
                        wire:key="{{ $item->getStatePath() }}"
                        wire:sortable.item="{{ $uuid }}"
                        @class([
                            'bg-white border border-gray-300 shadow-sm rounded-xl relative',
                            'dark:bg-gray-800 dark:border-gray-600' => config('forms.dark_mode'),
                        ])
                    >
                        @if ((! $isItemMovementDisabled) || (! $isItemDeletionDisabled) || $isCollapsible)
                            <header @class([
                                'flex items-center h-10 overflow-hidden border-b bg-gray-50 rounded-t-xl',
                                'dark:bg-gray-800 dark:border-gray-700' => config('forms.dark_mode'),
                            ])>
                                @unless ($isItemMovementDisabled)
                                    <button
                                        wire:sortable.handle
                                        wire:keydown.prevent.arrow-up="dispatchFormEvent('repeater::moveItemUp', '{{ $getStatePath() }}', '{{ $uuid }}')"
                                        wire:keydown.prevent.arrow-down="dispatchFormEvent('repeater::moveItemDown', '{{ $getStatePath() }}', '{{ $uuid }}')"
                                        type="button"
                                        @class([
                                            'flex items-center justify-center flex-none w-10 h-10 text-gray-400 border-r transition hover:text-gray-300',
                                            'dark:text-gray-400 dark:border-gray-700 dark:hover:text-gray-500' => config('forms.dark_mode'),
                                        ])
                                    >
                                        <span class="sr-only">
                                            {{ __('forms::components.repeater.buttons.move_item_down.label') }}
                                        </span>

                                        <x-heroicon-s-switch-vertical class="w-4 h-4"/>
                                    </button>
                                @endunless

                                <div class="flex-1"></div>

                                <ul @class([
                                    'flex divide-x',
                                    'dark:divide-gray-700' => config('forms.dark_mode'),
                                ])>
                                    @unless ($isItemDeletionDisabled)
                                        <li>
                                            <button
                                                wire:click="dispatchFormEvent('repeater::deleteItem', '{{ $getStatePath() }}', '{{ $uuid }}')"
                                                type="button"
                                                @class([
                                                    'flex items-center justify-center flex-none w-10 h-10 text-danger-600 transition hover:text-danger-500',
                                                    'dark:text-danger-500 dark:hover:text-danger-400' => config('forms.dark_mode'),
                                                ])
                                            >
                                                <span class="sr-only">
                                                    {{ __('forms::components.repeater.buttons.delete_item.label') }}
                                                </span>

                                                <x-heroicon-s-trash class="w-4 h-4"/>
                                            </button>
                                        </li>
                                    @endunless

                                    @if ($isCollapsible)
                                        <li>
                                            <button
                                                x-on:click="isCollapsed = !isCollapsed"
                                                type="button"
                                                @class([
                                                    'flex items-center justify-center flex-none w-10 h-10 text-gray-400 transition hover:text-gray-300',
                                                    'dark:text-gray-400 dark:hover:text-gray-500' => config('forms.dark_mode'),
                                                ])
                                            >
                                                <x-heroicon-s-minus-sm class="w-4 h-4" x-show="! isCollapsed"/>

                                                <span class="sr-only" x-show="! isCollapsed">
                                                    {{ __('forms::components.repeater.buttons.collapse_item.label') }}
                                                </span>

                                                <x-heroicon-s-plus-sm class="w-4 h-4" x-show="isCollapsed" x-cloak/>

                                                <span class="sr-only" x-show="isCollapsed" x-cloak>
                                                    {{ __('forms::components.repeater.buttons.expand_item.label') }}
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
                            {{ __('forms::components.repeater.collapsed') }}
                        </div>
                    </li>
                @endforeach
            </ul>
        @endif

        @if (! $isItemCreationDisabled)
            <div class="relative flex justify-center">
                <x-forms::button
                    :wire:click="'dispatchFormEvent(\'repeater::createItem\', \'' . $getStatePath() . '\')'"
                    size="sm"
                    type="button"
                >
                    {{ $getCreateItemButtonLabel() }}
                </x-forms::button>
            </div>
        @endif
    </div>
</x-dynamic-component>
