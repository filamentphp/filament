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

        $isCollapsible = $isCollapsible();
        $isCloneable = $isCloneable();
        $isReorderableWithButtons = $isReorderableWithButtons();
        $isItemCreationDisabled = $isItemCreationDisabled();
        $isItemDeletionDisabled = $isItemDeletionDisabled();
        $isItemMovementDisabled = $isItemMovementDisabled();
        $hasItemLabels = $hasItemLabels();
    @endphp

    <div>
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
    </div>

    <div
        {{
            $attributes
                ->merge($getExtraAttributes())
                ->class([
                    'filament-forms-repeater-component space-y-6 rounded-xl',
                    'bg-gray-50 p-6' => $isInset(),
                    'dark:bg-gray-500/10' => $isInset() && config('forms.dark_mode'),
                ])
        }}
    >
        @if (count($containers))
            <ul>
                <x-filament-support::grid
                    :default="$getGridColumns('default')"
                    :sm="$getGridColumns('sm')"
                    :md="$getGridColumns('md')"
                    :lg="$getGridColumns('lg')"
                    :xl="$getGridColumns('xl')"
                    :two-xl="$getGridColumns('2xl')"
                    wire:sortable
                    wire:end.stop="dispatchFormEvent('repeater::moveItems', '{{ $getStatePath() }}', $event.target.sortable.toArray())"
                    class="gap-6"
                >
                    @foreach ($containers as $uuid => $item)
                        <li
                            x-data="{
                                isCollapsed: @js($isCollapsed($item)),
                            }"
                            x-on:repeater-collapse.window="$event.detail === '{{ $getStatePath() }}' && (isCollapsed = true)"
                            x-on:repeater-expand.window="$event.detail === '{{ $getStatePath() }}' && (isCollapsed = false)"
                            wire:key="{{ $this->id }}.{{ $item->getStatePath() }}.{{ $field::class }}.item"
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

                                setTimeout(
                                    () =>
                                        $el.scrollIntoView({
                                            behavior: 'smooth',
                                            block: 'start',
                                            inline: 'start',
                                        }),
                                    200,
                                )
                            "
                            @class([
                                'filament-forms-repeater-component-item relative rounded-xl border border-gray-300 bg-white shadow-sm',
                                'dark:border-gray-600 dark:bg-gray-800' => config('forms.dark_mode'),
                            ])
                        >
                            @if ((! $isItemMovementDisabled) || (! $isItemDeletionDisabled) || $isCloneable || $isCollapsible || $hasItemLabels)
                                <header
                                    @if ($isCollapsible) x-on:click.stop="isCollapsed = ! isCollapsed" @endif
                                    @class([
                                        'flex h-10 items-center overflow-hidden rounded-t-xl border-b bg-gray-50',
                                        'dark:border-gray-700 dark:bg-gray-800' => config('forms.dark_mode'),
                                        'cursor-pointer' => $isCollapsible,
                                    ])
                                >
                                    @unless ($isItemMovementDisabled)
                                        <button
                                            title="{{ __('forms::components.repeater.buttons.move_item.label') }}"
                                            x-on:click.stop
                                            wire:sortable.handle
                                            wire:keydown.prevent.arrow-up="dispatchFormEvent('repeater::moveItemUp', '{{ $getStatePath() }}', '{{ $uuid }}')"
                                            wire:keydown.prevent.arrow-down="dispatchFormEvent('repeater::moveItemDown', '{{ $getStatePath() }}', '{{ $uuid }}')"
                                            type="button"
                                            @class([
                                                'flex h-10 w-10 flex-none items-center justify-center border-r text-gray-400 outline-none transition hover:text-gray-500 focus:bg-gray-500/5',
                                                'dark:border-gray-700 dark:focus:bg-gray-600/20' => config('forms.dark_mode'),
                                            ])
                                        >
                                            <span class="sr-only">
                                                {{ __('forms::components.repeater.buttons.move_item.label') }}
                                            </span>

                                            <x-heroicon-s-switch-vertical
                                                class="h-4 w-4"
                                            />
                                        </button>
                                    @endunless

                                    <p
                                        @class([
                                            'flex-none truncate px-4 text-xs font-medium text-gray-600',
                                            'dark:text-gray-400' => config('forms.dark_mode'),
                                        ])
                                    >
                                        {{ $getItemLabel($uuid) }}
                                    </p>

                                    <div class="flex-1"></div>

                                    <ul
                                        @class([
                                            'flex divide-x rtl:divide-x-reverse',
                                            'dark:divide-gray-700' => config('forms.dark_mode'),
                                        ])
                                    >
                                        @if ($isReorderableWithButtons)
                                            @unless ($loop->first)
                                                <li>
                                                    <button
                                                        title="{{ __('forms::components.repeater.buttons.move_item_up.label') }}"
                                                        type="button"
                                                        wire:click.stop="dispatchFormEvent('repeater::moveItemUp', '{{ $getStatePath() }}', '{{ $uuid }}')"
                                                        wire:target="dispatchFormEvent('repeater::moveItemUp', '{{ $getStatePath() }}', '{{ $uuid }}')"
                                                        wire:loading.attr="disabled"
                                                        @class([
                                                            'flex h-10 w-10 flex-none items-center justify-center text-gray-400 outline-none transition hover:text-gray-500 focus:bg-gray-500/5',
                                                            'dark:border-gray-700 dark:focus:bg-gray-600/20' => config('forms.dark_mode'),
                                                        ])
                                                    >
                                                        <span class="sr-only">
                                                            {{ __('forms::components.repeater.buttons.move_item_up.label') }}
                                                        </span>

                                                        <x-heroicon-s-chevron-up
                                                            class="h-4 w-4"
                                                            wire:loading.remove.delay
                                                            wire:target="dispatchFormEvent('repeater::moveItemUp', '{{ $getStatePath() }}', '{{ $uuid }}')"
                                                        />

                                                        <x-filament-support::loading-indicator
                                                            class="h-4 w-4 text-primary-500"
                                                            wire:loading.delay
                                                            wire:target="dispatchFormEvent('repeater::moveItemUp', '{{ $getStatePath() }}', '{{ $uuid }}')"
                                                            x-cloak
                                                        />
                                                    </button>
                                                </li>
                                            @endunless

                                            @unless ($loop->last)
                                                <li>
                                                    <button
                                                        title="{{ __('forms::components.repeater.buttons.move_item_down.label') }}"
                                                        type="button"
                                                        wire:click.stop="dispatchFormEvent('repeater::moveItemDown', '{{ $getStatePath() }}', '{{ $uuid }}')"
                                                        wire:target="dispatchFormEvent('repeater::moveItemDown', '{{ $getStatePath() }}', '{{ $uuid }}')"
                                                        wire:loading.attr="disabled"
                                                        @class([
                                                            'flex h-10 w-10 flex-none items-center justify-center text-gray-400 outline-none transition hover:text-gray-500 focus:bg-gray-500/5',
                                                            'dark:border-gray-700 dark:focus:bg-gray-600/20' => config('forms.dark_mode'),
                                                        ])
                                                    >
                                                        <span class="sr-only">
                                                            {{ __('forms::components.repeater.buttons.move_item_down.label') }}
                                                        </span>

                                                        <x-heroicon-s-chevron-down
                                                            class="h-4 w-4"
                                                            wire:loading.remove.delay
                                                            wire:target="dispatchFormEvent('repeater::moveItemDown', '{{ $getStatePath() }}', '{{ $uuid }}')"
                                                        />

                                                        <x-filament-support::loading-indicator
                                                            class="h-4 w-4 text-primary-500"
                                                            wire:loading.delay
                                                            wire:target="dispatchFormEvent('repeater::moveItemDown', '{{ $getStatePath() }}', '{{ $uuid }}')"
                                                            x-cloak
                                                        />
                                                    </button>
                                                </li>
                                            @endunless
                                        @endif

                                        @if ($isCloneable)
                                            <li>
                                                <button
                                                    title="{{ __('forms::components.repeater.buttons.clone_item.label') }}"
                                                    wire:click.stop="dispatchFormEvent('repeater::cloneItem', '{{ $getStatePath() }}', '{{ $uuid }}')"
                                                    wire:target="dispatchFormEvent('repeater::cloneItem', '{{ $getStatePath() }}', '{{ $uuid }}')"
                                                    wire:loading.attr="disabled"
                                                    type="button"
                                                    @class([
                                                        'flex h-10 w-10 flex-none items-center justify-center text-gray-400 outline-none transition hover:text-gray-500 focus:bg-gray-500/5',
                                                        'dark:border-gray-700 dark:focus:bg-gray-600/20' => config('forms.dark_mode'),
                                                    ])
                                                >
                                                    <span class="sr-only">
                                                        {{ __('forms::components.repeater.buttons.clone_item.label') }}
                                                    </span>

                                                    <x-heroicon-s-duplicate
                                                        class="h-4 w-4"
                                                        wire:loading.remove.delay
                                                        wire:target="dispatchFormEvent('repeater::cloneItem', '{{ $getStatePath() }}', '{{ $uuid }}')"
                                                    />

                                                    <x-filament-support::loading-indicator
                                                        class="h-4 w-4 text-primary-500"
                                                        wire:loading.delay
                                                        wire:target="dispatchFormEvent('repeater::cloneItem', '{{ $getStatePath() }}', '{{ $uuid }}')"
                                                        x-cloak
                                                    />
                                                </button>
                                            </li>
                                        @endunless

                                        @unless ($isItemDeletionDisabled)
                                            <li>
                                                <button
                                                    title="{{ __('forms::components.repeater.buttons.delete_item.label') }}"
                                                    wire:click.stop="dispatchFormEvent('repeater::deleteItem', '{{ $getStatePath() }}', '{{ $uuid }}')"
                                                    wire:target="dispatchFormEvent('repeater::deleteItem', '{{ $getStatePath() }}', '{{ $uuid }}')"
                                                    wire:loading.attr="disabled"
                                                    type="button"
                                                    @class([
                                                        'flex h-10 w-10 flex-none items-center justify-center text-danger-600 outline-none transition hover:text-danger-500 focus:bg-gray-500/5',
                                                        'dark:text-danger-500 dark:hover:text-danger-400 dark:focus:bg-gray-600/20' => config('forms.dark_mode'),
                                                    ])
                                                >
                                                    <span class="sr-only">
                                                        {{ __('forms::components.repeater.buttons.delete_item.label') }}
                                                    </span>

                                                    <x-heroicon-s-trash
                                                        class="h-4 w-4"
                                                        wire:loading.remove.delay
                                                        wire:target="dispatchFormEvent('repeater::deleteItem', '{{ $getStatePath() }}', '{{ $uuid }}')"
                                                    />

                                                    <x-filament-support::loading-indicator
                                                        class="h-4 w-4 text-primary-500"
                                                        wire:loading.delay
                                                        wire:target="dispatchFormEvent('repeater::deleteItem', '{{ $getStatePath() }}', '{{ $uuid }}')"
                                                        x-cloak
                                                    />
                                                </button>
                                            </li>
                                        @endunless

                                        @if ($isCollapsible)
                                            <li>
                                                <button
                                                    x-bind:title="
                                                        ! isCollapsed
                                                            ? '{{ __('forms::components.repeater.buttons.collapse_item.label') }}'
                                                            : '{{ __('forms::components.repeater.buttons.expand_item.label') }}'
                                                    "
                                                    x-on:click.stop="isCollapsed = ! isCollapsed"
                                                    type="button"
                                                    @class([
                                                        'flex h-10 w-10 flex-none items-center justify-center text-gray-400 outline-none transition hover:text-gray-500 focus:bg-gray-500/5',
                                                        'dark:focus:bg-gray-600/20' => config('forms.dark_mode'),
                                                    ])
                                                >
                                                    <x-heroicon-s-minus-sm
                                                        class="h-4 w-4"
                                                        x-show="! isCollapsed"
                                                    />

                                                    <span
                                                        class="sr-only"
                                                        x-show="! isCollapsed"
                                                    >
                                                        {{ __('forms::components.repeater.buttons.collapse_item.label') }}
                                                    </span>

                                                    <x-heroicon-s-plus-sm
                                                        class="h-4 w-4"
                                                        x-show="isCollapsed"
                                                        x-cloak
                                                    />

                                                    <span
                                                        class="sr-only"
                                                        x-show="isCollapsed"
                                                        x-cloak
                                                    >
                                                        {{ __('forms::components.repeater.buttons.expand_item.label') }}
                                                    </span>
                                                </button>
                                            </li>
                                        @endif
                                    </ul>
                                </header>
                            @endif

                            <div
                                x-bind:class="{
                                    'invisible h-0 !m-0 overflow-y-hidden': isCollapsed,
                                    'p-6': ! isCollapsed,
                                }"
                            >
                                {{ $item }}
                            </div>

                            <div
                                class="p-2 text-center text-xs text-gray-400"
                                x-show="isCollapsed"
                                x-cloak
                            >
                                {{ __('forms::components.repeater.collapsed') }}
                            </div>
                        </li>
                    @endforeach
                </x-filament-support::grid>
            </ul>
        @endif

        @if (! $isItemCreationDisabled)
            <div class="relative flex justify-center">
                <x-forms::button
                    :wire:click="'dispatchFormEvent(\'repeater::createItem\', \'' . $getStatePath() . '\')'"
                    size="sm"
                    outlined
                >
                    {{ $getCreateItemButtonLabel() }}
                </x-forms::button>
            </div>
        @endif
    </div>
</x-dynamic-component>
