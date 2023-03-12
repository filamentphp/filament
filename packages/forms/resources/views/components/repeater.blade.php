<x-dynamic-component
    :component="$getFieldWrapperView()"
    :field="$field"
>
    @php
        $containers = $getChildComponentContainers();

        $addAction = $getAction($getAddActionName());
        $cloneAction = $getAction($getCloneActionName());
        $deleteAction = $getAction($getDeleteActionName());
        $moveDownAction = $getAction($getMoveDownActionName());
        $moveUpAction = $getAction($getMoveUpActionName());
        $reorderAction = $getAction($getReorderActionName());

        $isCollapsible = $isCollapsible();
        $isReorderableWithButtons = $isReorderableWithButtons();
        $hasItemLabels = $hasItemLabels();

        $statePath = $getStatePath();
    @endphp

    <div>
        @if ((count($containers) > 1) && $isCollapsible)
            <div class="space-x-2 rtl:space-x-reverse" x-data="{}">
                <span x-on:click="$dispatch('repeater-collapse', '{{ $statePath }}')">
                    {{ $getAction('collapseAll') }}
                </span>

                <span x-on:click="$dispatch('repeater-expand', '{{ $statePath }}')">
                    {{ $getAction('expandAll') }}
                </span>
            </div>
        @endif
    </div>

    <div
        x-data="{}"
        {{
            $attributes
                ->merge($getExtraAttributes(), escape: false)
                ->class([
                    'filament-forms-repeater-component space-y-6 rounded-xl',
                    'bg-gray-50 p-6 dark:bg-gray-500/10' => $isInset(),
                ])
        }}
    >
        @if (count($containers))
            <ul>
                <x-filament::grid
                    :default="$getGridColumns('default')"
                    :sm="$getGridColumns('sm')"
                    :md="$getGridColumns('md')"
                    :lg="$getGridColumns('lg')"
                    :xl="$getGridColumns('xl')"
                    :two-xl="$getGridColumns('2xl')"
                    x-sortable
                    :wire:end.stop="'mountFormComponentAction(\'' . $statePath . '\', \'reorder\', { items: $event.target.sortable.toArray() })'"
                    class="gap-6"
                >
                    @foreach ($containers as $uuid => $item)
                        <li
                            x-data="{
                                isCollapsed: @js($isCollapsed($item)),
                            }"
                            x-on:repeater-collapse.window="$event.detail === '{{ $statePath }}' && (isCollapsed = true)"
                            x-on:repeater-expand.window="$event.detail === '{{ $statePath }}' && (isCollapsed = false)"
                            wire:key="{{ $this->id }}.{{ $item->getStatePath() }}.{{ $field::class }}.item"
                            x-sortable-item="{{ $uuid }}"
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
                            class="relative rounded-xl bg-white shadow-sm ring-1 ring-gray-900/10 dark:bg-gray-800 dark:ring-gray-50/10"
                        >
                            @if ($reorderAction || $deleteAction || $cloneAction || $isCollapsible || $hasItemLabels)
                                <header
                                    @if ($isCollapsible) x-on:click.stop="isCollapsed = ! isCollapsed" @endif
                                    @class([
                                        'flex items-center h-10 overflow-hidden border-b bg-gray-50 rounded-t-xl dark:bg-gray-800 dark:border-gray-700',
                                        'cursor-pointer' => $isCollapsible,
                                    ])
                                >
                                    @if ($reorderAction)
                                        <div x-sortable-handle>
                                            {{ $reorderAction }}
                                        </div>
                                    @endif

                                    <p class="flex-none px-4 text-xs font-medium text-gray-600 truncate dark:text-gray-400">
                                        {{ $getItemLabel($uuid) }}
                                    </p>

                                    <div class="flex-1"></div>

                                    <ul class="flex divide-x rtl:divide-x-reverse dark:divide-gray-700">
                                        @if ($isReorderableWithButtons)
                                            @if (! $loop->first)
                                                <li class="flex items-center justify-center">
                                                    {{ $moveUpAction(['item' => $uuid]) }}
                                                </li>
                                            @endif

                                            @if (! $loop->last)
                                                <li class="flex items-center justify-center">
                                                    {{ $moveDownAction(['item' => $uuid]) }}
                                                </li>
                                            @endif
                                        @endif

                                        @if ($cloneAction)
                                            <li class="flex items-center justify-center">
                                                {{ $cloneAction(['item' => $uuid]) }}
                                            </li>
                                        @endif

                                        @if ($deleteAction)
                                            <li class="flex items-center justify-center">
                                                {{ $deleteAction(['item' => $uuid]) }}
                                            </li>
                                        @endif

                                        @if ($isCollapsible)
                                            <li x-on:click.stop="isCollapsed = ! isCollapsed">
                                                <div x-show="!isCollapsed">
                                                    {{ $getAction('collapse') }}
                                                </div>

                                                <div x-show="isCollapsed" x-cloak>
                                                    {{ $getAction('expand') }}
                                                </div>
                                            </li>
                                        @endif
                                    </ul>
                                </header>
                            @endif

                            <div class="p-6" x-show="! isCollapsed">
                                {{ $item }}
                            </div>

                            <div class="p-2 text-xs text-center text-gray-400" x-show="isCollapsed" x-cloak>
                                {{ __('filament-forms::components.repeater.collapsed') }}
                            </div>
                        </li>
                    @endforeach
                </x-filament::grid>
            </ul>
        @endif

        @if ($addAction)
            <div class="relative flex justify-center">
                {{ $addAction }}
            </div>
        @endif
    </div>
</x-dynamic-component>
