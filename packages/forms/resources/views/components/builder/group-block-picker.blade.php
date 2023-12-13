@props([
    'action',
    'afterItem' => null,
    'groups' => [],
    'blocks',
    'columns' => null,
    'statePath',
    'trigger',
    'width' => null,
])

@php
    $blocksByGroupName = collect($blocks)
        ->groupBy(fn (Filament\Forms\Components\Builder\Block $block) => $block->getGroup())
        ->all();

    $groups = collect($groups)
        ->filter(fn (Filament\Forms\Components\Builder\BlockGroup $group) => isset($blocksByGroupName[$group->getName()]))
        ->all();

    $hasUngroupedBlocks = isset($blocksByGroupName['']);
@endphp

<x-filament::dropdown
    :width="$width"
    {{ $attributes->class(['fi-fo-builder-block-picker']) }}
>
    <x-slot name="trigger">
        {{ $trigger }}
    </x-slot>

    <x-filament::dropdown.list x-data="{
        group: null,
    }">
        <div x-show="group === null">
            @foreach($groups as $group)
                @php
                    $xOnClickAction = 'group = ' . Js::from($group->getName());
                @endphp

                <x-filament::dropdown.list.item
                    :icon="$group->getIcon()"
                    :icon-size="$group->getIconSize()"
                    :x-on:click="$xOnClickAction"
                >
                    <div class="flex items-center justify-between">
                        <div class="space-y-1">
                            <p class="font-medium">
                                {{ $group->getLabel() }}
                            </p>

                            @if($description = $group->getDescription())
                                <p class="text-gray-600 text-xs">
                                    {{ $description }}
                                </p>
                            @endif
                        </div>

                        @svg('heroicon-m-chevron-right', 'w-4 h-4 text-gray-400 group-hover:text-gray-600')
                    </div>
                </x-filament::dropdown.list.item>
            @endforeach

            @if($hasUngroupedBlocks)
                <x-filament::dropdown.list.item
                    icon="heroicon-o-ellipsis-horizontal"
                    :icon-size="Filament\Support\Enums\IconSize::Large"
                    x-on:click="group = ''"
                >
                    <div class="flex items-center justify-between">
                        <p class="font-medium">
                            Other
                        </p>

                        @svg('heroicon-m-chevron-right', 'w-4 h-4 text-gray-400 group-hover:text-gray-600')
                    </div>
                </x-filament::dropdown.list.item>
            @endif
        </div>

        @foreach($groups as $group)
            <div x-show="group === @js($group->getName())">
                <div class="px-1 py-2 flex items-center gap-x-2">
                    <button type="button" x-on:click="group = null" class="appearance-none text-gray-400 hover:text-gray-600">
                        @svg('heroicon-m-chevron-left', 'w-4 h-4')
                    </button>

                    <p class="font-medium text-sm">
                        {{ $group->getLabel() }}
                    </p>
                </div>

                @foreach($blocksByGroupName[$group->getName()] as $block)
                    @php
                        $wireClickActionArguments = ['block' => $block->getName()];

                        if ($afterItem) {
                            $wireClickActionArguments['afterItem'] = $afterItem;
                        }

                        $wireClickActionArguments = \Illuminate\Support\Js::from($wireClickActionArguments);

                        $wireClickAction = "mountFormComponentAction('{$statePath}', '{$action->getName()}', {$wireClickActionArguments})";
                    @endphp

                    <x-filament::dropdown.list.item
                        :icon="$block->getIcon()"
                        x-on:click="close(); $nextTick(() => group = null)"
                        :wire:click="$wireClickAction"
                    >
                        {{ $block->getLabel() }}
                    </x-filament::dropdown.list.item>
                @endforeach
            </div>
        @endforeach

        @if($hasUngroupedBlocks)
            <div x-show="group === ''">
                <div class="px-1 py-2 flex items-center gap-x-2">
                    <button type="button" x-on:click="group = null" class="appearance-none text-gray-400 hover:text-gray-600">
                        @svg('heroicon-m-chevron-left', 'w-4 h-4')
                    </button>

                    <p class="font-medium text-sm">
                        Other
                    </p>
                </div>

                @foreach($blocksByGroupName[''] as $block)
                    @php
                        $wireClickActionArguments = ['block' => $block->getName()];

                        if ($afterItem) {
                            $wireClickActionArguments['afterItem'] = $afterItem;
                        }

                        $wireClickActionArguments = \Illuminate\Support\Js::from($wireClickActionArguments);

                        $wireClickAction = "mountFormComponentAction('{$statePath}', '{$action->getName()}', {$wireClickActionArguments})";
                    @endphp

                    <x-filament::dropdown.list.item
                        :icon="$block->getIcon()"
                        x-on:click="close(); $nextTick(() => group = null)"
                        :wire:click="$wireClickAction"
                    >
                        {{ $block->getLabel() }}
                    </x-filament::dropdown.list.item>
                @endforeach
            </div>
        @endif

        {{-- <x-filament::grid
            :default="$columns['default'] ?? 1"
            :sm="$columns['sm'] ?? null"
            :md="$columns['md'] ?? null"
            :lg="$columns['lg'] ?? null"
            :xl="$columns['xl'] ?? null"
            :two-xl="$columns['2xl'] ?? null"
            direction="column"
        >
            @foreach ($blocks as $block)
                @php
                    $wireClickActionArguments = ['block' => $block->getName()];

                    if ($afterItem) {
                        $wireClickActionArguments['afterItem'] = $afterItem;
                    }

                    $wireClickActionArguments = \Illuminate\Support\Js::from($wireClickActionArguments);

                    $wireClickAction = "mountFormComponentAction('{$statePath}', '{$action->getName()}', {$wireClickActionArguments})";
                @endphp

                <x-filament::dropdown.list.item
                    :icon="$block->getIcon()"
                    x-on:click="close"
                    :wire:click="$wireClickAction"
                >
                    {{ $block->getLabel() }}
                </x-filament::dropdown.list.item>
            @endforeach
        </x-filament::grid> --}}
    </x-filament::dropdown.list>
</x-filament::dropdown>
