@php
    use Filament\Support\Facades\FilamentView;

    $hasInlineLabel = $hasInlineLabel();
    $id = $getId();
    $isDisabled = $isDisabled();
    $isReorderable = $isReorderable();
    $statePath = $getStatePath();
@endphp

<x-dynamic-component
    :component="$getFieldWrapperView()"
    :field="$field"
    :has-inline-label="$hasInlineLabel"
>
    <x-slot
        name="label"
        @class([
            'sm:pt-1.5' => $hasInlineLabel,
        ])
    >
        {{ $getLabel() }}
    </x-slot>

    <div
        @if (FilamentView::hasSpaMode())
            ax-load="visible"
        @else
            ax-load
        @endif
        ax-load-src="{{ \Filament\Support\Facades\FilamentAsset::getAlpineComponentSrc('tags-input', 'filament/forms') }}"
        x-data="tagsInputFormComponent({
                    state: $wire.{{ $applyStateBindingModifiers("\$entangle('{$statePath}')") }},
                    splitKeys: @js($getSplitKeys()),
                })"
        x-ignore
        {{
            $attributes
                ->merge($getExtraAttributes(), escape: false)
                ->merge($getExtraAlpineAttributes(), escape: false)
                ->class([
                    'fi-fo-tags-input block w-full rounded-lg shadow-sm ring-1 transition duration-75',
                    'bg-gray-50 dark:bg-transparent' => $isDisabled,
                    'bg-white focus-within:ring-2 dark:bg-white/5' => ! $isDisabled,
                    'ring-danger-600 focus-within:ring-danger-600 dark:ring-danger-500 dark:focus-within:ring-danger-500' => $errors->has($statePath),
                    'ring-gray-950/10 focus-within:ring-primary-600 dark:focus-within:ring-primary-500' => ! $errors->has($statePath),
                    'dark:ring-white/20' => (! $isDisabled) && (! $errors->has($statePath)),
                    'dark:ring-white/10' => $isDisabled && (! $errors->has($statePath)),
                ])
        }}
    >
        <x-filament::input
            autocomplete="off"
            :autofocus="$isAutofocused()"
            :disabled="$isDisabled"
            :id="$id"
            :list="$id . '-suggestions'"
            :placeholder="$getPlaceholder()"
            type="text"
            x-bind="input"
            :attributes="\Filament\Support\prepare_inherited_attributes($getExtraInputAttributeBag())"
        />

        <datalist id="{{ $id }}-suggestions">
            @foreach ($getSuggestions() as $suggestion)
                <template
                    x-bind:key="@js($suggestion)"
                    x-if="! state.includes(@js($suggestion))"
                >
                    <option value="{{ $suggestion }}" />
                </template>
            @endforeach
        </datalist>

        <div
            @class([
                '[&_.fi-badge-delete-button]:hidden' => $isDisabled,
            ])
        >
            <div wire:ignore>
                <template x-cloak x-if="state?.length">
                    <div
                        @if ($isReorderable)
                            x-on:end.stop="reorderTags($event)"
                            x-sortable
                            data-sortable-animation-duration="{{ $getReorderAnimationDuration() }}"
                        @endif
                        @class([
                            'flex w-full flex-wrap gap-1.5 p-2',
                            'border-t border-t-gray-200 dark:border-t-white/10',
                        ])
                    >
                        <template
                            x-for="(tag, index) in state"
                            x-bind:key="`${tag}-${index}`"
                            class="hidden"
                        >
                            <x-filament::badge
                                :x-bind:x-sortable-item="$isReorderable ? 'index' : null"
                                :x-sortable-handle="$isReorderable ? '' : null"
                                @class([
                                    'cursor-move' => $isReorderable,
                                ])
                            >
                                {{ $getTagPrefix() }}

                                <span
                                    x-text="tag"
                                    class="select-none text-start"
                                ></span>

                                {{ $getTagSuffix() }}

                                <x-slot
                                    name="deleteButton"
                                    x-on:click="deleteTag(tag)"
                                ></x-slot>
                            </x-filament::badge>
                        </template>
                    </div>
                </template>
            </div>
        </div>
    </div>
</x-dynamic-component>
