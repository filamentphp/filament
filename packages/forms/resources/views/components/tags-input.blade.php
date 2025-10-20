@php
    $fieldWrapperView = $getFieldWrapperView();
    $extraAttributes = $getExtraAttributes();
    $extraInputAttributeBag = $getExtraInputAttributeBag();
    $color = $getColor() ?? 'primary';
    $id = $getId();
    $isAutofocused = $isAutofocused();
    $isDisabled = $isDisabled();
    $isPrefixInline = $isPrefixInline();
    $isReorderable = (! $isDisabled) && $isReorderable();
    $isSuffixInline = $isSuffixInline();
    $placeholder = $getPlaceholder();
    $prefixActions = $getPrefixActions();
    $prefixIcon = $getPrefixIcon();
    $prefixIconColor = $getPrefixIconColor();
    $prefixLabel = $getPrefixLabel();
    $statePath = $getStatePath();
    $suffixActions = $getSuffixActions();
    $suffixIcon = $getSuffixIcon();
    $suffixIconColor = $getSuffixIconColor();
    $suffixLabel = $getSuffixLabel();
@endphp

<x-dynamic-component
    :component="$fieldWrapperView"
    :field="$field"
    class="fi-fo-tags-input-wrp"
>
    <x-filament::input.wrapper
        :disabled="$isDisabled"
        :inline-prefix="$isPrefixInline"
        :inline-suffix="$isSuffixInline"
        :prefix="$prefixLabel"
        :prefix-actions="$prefixActions"
        :prefix-icon="$prefixIcon"
        :prefix-icon-color="$prefixIconColor"
        :suffix="$suffixLabel"
        :suffix-actions="$suffixActions"
        :suffix-icon="$suffixIcon"
        :suffix-icon-color="$suffixIconColor"
        :valid="! $errors->has($statePath)"
        :attributes="
            \Filament\Support\prepare_inherited_attributes($attributes)
                ->merge($extraAttributes, escape: false)
                ->class([
                    'fi-fo-tags-input',
                    'fi-disabled' => $isDisabled,
                ])
        "
    >
        <div
            x-load
            x-load-src="{{ \Filament\Support\Facades\FilamentAsset::getAlpineComponentSrc('tags-input', 'filament/forms') }}"
            x-data="tagsInputFormComponent({
                        state: $wire.{{ $applyStateBindingModifiers("\$entangle('{$statePath}')") }},
                        splitKeys: @js($getSplitKeys()),
                    })"
            {{ $getExtraAlpineAttributeBag() }}
        >
            <input
                {{
                    $extraInputAttributeBag
                        ->merge([
                            'autocomplete' => 'off',
                            'autofocus' => $isAutofocused,
                            'disabled' => $isDisabled,
                            'id' => $id,
                            'list' => $id . '-suggestions',
                            'placeholder' => filled($placeholder) ? e($placeholder) : null,
                            'type' => 'text',
                            'x-bind' => 'input',
                        ], escape: false)
                        ->class([
                            'fi-input',
                            'fi-input-has-inline-prefix' => $isPrefixInline && (count($prefixActions) || $prefixIcon || filled($prefixLabel)),
                            'fi-input-has-inline-suffix' => $isSuffixInline && (count($suffixActions) || $suffixIcon || filled($suffixLabel)),
                        ])
                }}
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

            <div wire:ignore>
                <template x-cloak x-if="state?.length">
                    <div
                        @if ($isReorderable)
                            x-on:end.stop="reorderTags($event)"
                            x-sortable
                            data-sortable-animation-duration="{{ $getReorderAnimationDuration() }}"
                        @endif
                        class="fi-fo-tags-input-tags-ctn"
                    >
                        <template
                            x-for="(tag, index) in state"
                            x-bind:key="`${tag}-${index}`"
                        >
                            <x-filament::badge
                                :color="$color"
                                :x-bind:x-sortable-item="$isReorderable ? 'index' : null"
                                :x-sortable-handle="$isReorderable ? '' : null"
                                @class([
                                    'fi-reorderable' => $isReorderable,
                                ])
                            >
                                {{ $getTagPrefix() }}

                                <span x-text="tag"></span>

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
    </x-filament::input.wrapper>
</x-dynamic-component>
