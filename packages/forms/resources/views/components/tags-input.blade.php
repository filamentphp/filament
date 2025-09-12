@php
    use Filament\Support\Facades\FilamentView;

    $color = $getColor() ?? 'primary';
    $hasInlineLabel = $hasInlineLabel();
    $id = $getId();
    $isDisabled = $isDisabled();
    $isPrefixInline = $isPrefixInline();
    $isReorderable = (! $isDisabled) && $isReorderable();
    $isSuffixInline = $isSuffixInline();
    $prefixActions = $getPrefixActions();
    $prefixIcon = $getPrefixIcon();
    $prefixLabel = $getPrefixLabel();
    $statePath = $getStatePath();
    $suffixActions = $getSuffixActions();
    $suffixIcon = $getSuffixIcon();
    $suffixLabel = $getSuffixLabel();
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

    <x-filament::input.wrapper
        :disabled="$isDisabled"
        :inline-prefix="$isPrefixInline"
        :inline-suffix="$isSuffixInline"
        :prefix="$prefixLabel"
        :prefix-actions="$prefixActions"
        :prefix-icon="$prefixIcon"
        :prefix-icon-color="$getPrefixIconColor()"
        :suffix="$suffixLabel"
        :suffix-actions="$suffixActions"
        :suffix-icon="$suffixIcon"
        :suffix-icon-color="$getSuffixIconColor()"
        :valid="! $errors->has($statePath)"
        :attributes="
            \Filament\Support\prepare_inherited_attributes($attributes)
                ->merge($getExtraAttributes(), escape: false)
                ->class(['fi-fo-tags-input'])
        "
    >
        <div
            @if (FilamentView::hasSpaMode())
                {{-- format-ignore-start --}}x-load="visible || event (ax-modal-opened)"{{-- format-ignore-end --}}
            @else
                x-load
            @endif
            x-load-src="{{ \Filament\Support\Facades\FilamentAsset::getAlpineComponentSrc('tags-input', 'filament/forms') }}"
            x-data="tagsInputFormComponent({
                        state: $wire.{{ $applyStateBindingModifiers("\$entangle('{$statePath}')") }},
                        splitKeys: @js($getSplitKeys()),
                    })"
            {{ $getExtraAlpineAttributeBag() }}
        >
            <x-filament::input
                autocomplete="off"
                :autofocus="$isAutofocused()"
                :disabled="$isDisabled"
                :id="$id"
                :inline-prefix="$isPrefixInline && (count($prefixActions) || $prefixIcon || filled($prefixLabel))"
                :inline-suffix="$isSuffixInline && (count($suffixActions) || $suffixIcon || filled($suffixLabel))"
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
                        x-if="! (state?.includes(@js($suggestion)) ?? true)"
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
                            class="fi-fo-tags-input-tags-ctn flex w-full flex-wrap gap-1.5 border-t border-t-gray-200 p-2 dark:border-t-white/10"
                        >
                            <template
                                x-for="(tag, index) in state"
                                x-bind:key="`${tag}-${index}`"
                                class="hidden"
                            >
                                <x-filament::badge
                                    :color="$color"
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
                                        x-on:click.stop="deleteTag(tag)"
                                    ></x-slot>
                                </x-filament::badge>
                            </template>
                        </div>
                    </template>
                </div>
            </div>
        </div>
    </x-filament::input.wrapper>
</x-dynamic-component>
