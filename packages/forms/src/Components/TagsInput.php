<?php

namespace Filament\Forms\Components;

use Closure;
use Filament\Schemas\Components\Concerns\CanStripCharactersFromState;
use Filament\Schemas\Components\Concerns\CanTrimState;
use Filament\Schemas\Components\Contracts\HasAffixActions;
use Filament\Schemas\Components\StateCasts\StripCharactersStateCast;
use Filament\Support\Components\Contracts\HasEmbeddedView;
use Filament\Support\Concerns\HasColor;
use Filament\Support\Concerns\HasExtraAlpineAttributes;
use Filament\Support\Concerns\HasReorderAnimationDuration;
use Filament\Support\Facades\FilamentAsset;
use Filament\Support\Icons\Heroicon;
use Filament\Support\View\Components\BadgeComponent;
use Filament\Support\View\Components\InputComponent\WrapperComponent\IconComponent;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Js;
use Illuminate\View\ComponentAttributeBag;

use function Filament\Support\generate_icon_html;

class TagsInput extends Field implements Contracts\HasNestedRecursiveValidationRules, HasAffixActions, HasEmbeddedView
{
    use CanStripCharactersFromState;
    use CanTrimState;
    use Concerns\HasAffixes;
    use Concerns\HasExtraInputAttributes;
    use Concerns\HasNestedRecursiveValidationRules;
    use Concerns\HasPlaceholder;
    use HasColor;
    use HasExtraAlpineAttributes;
    use HasReorderAnimationDuration;

    protected bool | Closure $isReorderable = false;

    protected string | Closure | null $separator = null;

    /**
     * @var array<string> | Closure
     */
    protected array | Closure $splitKeys = [];

    /**
     * @var array<string> | Arrayable | Closure | null
     */
    protected array | Arrayable | Closure | null $suggestions = null;

    protected string | Closure | null $tagPrefix = null;

    protected string | Closure | null $tagSuffix = null;

    protected function setUp(): void
    {
        parent::setUp();

        $this->default([]);

        $this->afterStateHydrated(static function (TagsInput $component, $state): void {
            if (is_array($state)) {
                return;
            }

            if (! ($separator = $component->getSeparator())) {
                $component->state([]);

                return;
            }

            $state = explode($separator, $state ?? '');

            if (count($state) === 1 && blank($state[0])) {
                $state = [];
            }

            $component->state($state);
        });

        $this->dehydrateStateUsing(static function (TagsInput $component, $state) {
            if ($separator = $component->getSeparator()) {
                return implode($separator, $state);
            }

            return $state;
        });

        $this->placeholder(__('filament-forms::components.tags_input.placeholder'));

        $this->reorderAnimationDuration(100);
    }

    public function tagPrefix(string | Closure | null $prefix): static
    {
        $this->tagPrefix = $prefix;

        return $this;
    }

    public function tagSuffix(string | Closure | null $suffix): static
    {
        $this->tagSuffix = $suffix;

        return $this;
    }

    public function reorderable(bool | Closure $condition = true): static
    {
        $this->isReorderable = $condition;

        return $this;
    }

    public function separator(string | Closure | null $separator = ','): static
    {
        $this->separator = $separator;

        return $this;
    }

    /**
     * @param  array<string> | Closure  $keys
     */
    public function splitKeys(array | Closure $keys): static
    {
        $this->splitKeys = $keys;

        return $this;
    }

    /**
     * @param  array<string> | Arrayable | Closure  $suggestions
     */
    public function suggestions(array | Arrayable | Closure $suggestions): static
    {
        $this->suggestions = $suggestions;

        return $this;
    }

    public function getTagPrefix(): ?string
    {
        return $this->evaluate($this->tagPrefix);
    }

    public function getTagSuffix(): ?string
    {
        return $this->evaluate($this->tagSuffix);
    }

    public function getSeparator(): ?string
    {
        return $this->evaluate($this->separator);
    }

    /**
     * @return array<string>
     */
    public function getSplitKeys(): array
    {
        return $this->evaluate($this->splitKeys) ?? [];
    }

    /**
     * @return array<string>
     */
    public function getSuggestions(): array
    {
        $suggestions = $this->evaluate($this->suggestions ?? []);

        if ($suggestions instanceof Arrayable) {
            $suggestions = $suggestions->toArray();
        }

        return $suggestions;
    }

    public function isReorderable(): bool
    {
        return (bool) $this->evaluate($this->isReorderable);
    }

    public function getDefaultStateCasts(): array
    {
        return [
            ...parent::getDefaultStateCasts(),
            ...($this->hasStripCharacters() ? [app(StripCharactersStateCast::class, ['characters' => $this->getStripCharacters()])] : []),
        ];
    }

    public function mutateDehydratedState(mixed $state): mixed
    {
        if (is_array($state)) {
            $state = array_map(function (mixed $value): mixed {
                return $this->trimState($value);
            }, $state);
        } else {
            $state = $this->trimState($state);
        }

        return parent::mutateDehydratedState($state);
    }

    public function mutateStateForValidation(mixed $state): mixed
    {
        if (is_array($state)) {
            $state = array_map(function (mixed $value): mixed {
                $value = $this->stripCharactersFromState($value);
                $value = $this->trimState($value);

                return $value;
            }, $state);
        } else {
            $state = $this->stripCharactersFromState($state);
            $state = $this->trimState($state);
        }

        return parent::mutateStateForValidation($state);
    }

    public function toEmbeddedHtml(): string
    {
        $extraAttributes = $this->getExtraAttributes();
        $extraInputAttributeBag = $this->getExtraInputAttributeBag();
        $color = $this->getColor() ?? 'primary';
        $id = $this->getId();
        $isAutofocused = $this->isAutofocused();
        $isDisabled = $this->isDisabled();
        $isPrefixInline = $this->isPrefixInline();
        $isReorderable = (! $isDisabled) && $this->isReorderable();
        $isSuffixInline = $this->isSuffixInline();
        $placeholder = $this->getPlaceholder();
        $prefixActions = $this->getPrefixActions();
        $prefixIcon = $this->getPrefixIcon();
        $prefixIconColor = $this->getPrefixIconColor();
        $prefixLabel = $this->getPrefixLabel();
        $statePath = $this->getStatePath();
        $suffixActions = $this->getSuffixActions();
        $suffixIcon = $this->getSuffixIcon();
        $suffixIconColor = $this->getSuffixIconColor();
        $suffixLabel = $this->getSuffixLabel();

        // Filter visible prefix/suffix actions
        $prefixActions = array_filter(
            $prefixActions,
            static fn (\Filament\Actions\Action $action): bool => $action->isVisible(),
        );
        $suffixActions = array_filter(
            $suffixActions,
            static fn (\Filament\Actions\Action $action): bool => $action->isVisible(),
        );

        $hasPrefix = count($prefixActions) || $prefixIcon || filled($prefixLabel);
        $hasSuffix = count($suffixActions) || $suffixIcon || filled($suffixLabel);

        $wrapperAttributes = \Filament\Support\prepare_inherited_attributes($this->getExtraAttributeBag())
            ->merge($extraAttributes, escape: false)
            ->merge([
                'x-on:focus-input.stop' => "\$el.querySelector('input')?.focus()",
            ], escape: false)
            ->class([
                'fi-input-wrp',
                'fi-fo-tags-input',
                'fi-disabled' => $isDisabled,
                'fi-invalid' => filled($statePath) && view()->shared('errors')?->has($statePath),
            ]);

        $deleteLabel = __('filament-forms::components.tags_input.actions.delete.label');

        // Badge color classes for the tag badges
        $badgeColorClasses = (new ComponentAttributeBag)->color(BadgeComponent::class, $color)->getAttributes()['class'] ?? '';

        $deleteIconHtml = generate_icon_html(
            Heroicon::XMark,
            alias: \Filament\Support\View\SupportIconAlias::BADGE_DELETE_BUTTON,
            size: \Filament\Support\Enums\IconSize::ExtraSmall,
        )?->toHtml();

        ob_start(); ?>

        <div <?= $wrapperAttributes->toHtml() ?>>
            <?php if ($hasPrefix) { ?>
                <div
                    <?= (new ComponentAttributeBag)->class([
                        'fi-input-wrp-prefix',
                        'fi-input-wrp-prefix-has-content' => true,
                        'fi-inline' => $isPrefixInline,
                        'fi-input-wrp-prefix-has-label' => filled($prefixLabel),
                    ])->toHtml() ?>
                >
                    <?php if (count($prefixActions)) { ?>
                        <div class="fi-input-wrp-actions">
                            <?php foreach ($prefixActions as $prefixAction) { ?>
                                <?= $prefixAction->toHtml() ?>
                            <?php } ?>
                        </div>
                    <?php } ?>

                    <?= generate_icon_html($prefixIcon, null, (new ComponentAttributeBag)->color(IconComponent::class, $prefixIconColor))?->toHtml() ?>

                    <?php if (filled($prefixLabel)) { ?>
                        <span class="fi-input-wrp-label"><?= e($prefixLabel) ?></span>
                    <?php } ?>
                </div>
            <?php } ?>

            <div class="fi-input-wrp-content-ctn">
                <div
                    x-load
                    x-load-src="<?= e(FilamentAsset::getAlpineComponentSrc('tags-input', 'filament/forms')) ?>"
                    x-data="tagsInputFormComponent({
                                state: $wire.<?= $this->applyStateBindingModifiers("\$entangle('{$statePath}')") ?>,
                                splitKeys: <?= Js::from($this->getSplitKeys()) ?>,
                            })"
                    <?= $this->getExtraAlpineAttributeBag()->toHtml() ?>
                >
                    <input
                        <?= $extraInputAttributeBag
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
                                'fi-input-has-inline-prefix' => $isPrefixInline && $hasPrefix,
                                'fi-input-has-inline-suffix' => $isSuffixInline && $hasSuffix,
                            ])
                            ->toHtml() ?>
                    />

                    <datalist id="<?= e($id) ?>-suggestions">
                        <?php foreach ($this->getSuggestions() as $suggestion) { ?>
                            <template
                                x-bind:key="<?= Js::from($suggestion) ?>"
                                x-if="! (state?.includes(<?= Js::from($suggestion) ?>) ?? true)"
                            >
                                <option value="<?= e($suggestion) ?>" />
                            </template>
                        <?php } ?>
                    </datalist>

                    <div wire:ignore>
                        <template x-cloak x-if="state?.length">
                            <div
                                <?php if ($isReorderable) { ?>
                                    x-on:end.stop="reorderTags($event)"
                                    x-sortable
                                    data-sortable-animation-duration="<?= e($this->getReorderAnimationDuration()) ?>"
                                <?php } ?>
                                class="fi-fo-tags-input-tags-ctn"
                            >
                                <template
                                    x-for="(tag, index) in state"
                                    x-bind:key="`${tag}-${index}`"
                                >
                                    <span
                                        <?php if ($isReorderable) { ?>
                                            x-bind:x-sortable-item="index"
                                            x-sortable-handle
                                        <?php } ?>
                                        class="fi-badge <?= e($badgeColorClasses) ?> <?php if ($isReorderable) { ?>fi-reorderable<?php } ?>"
                                    >
                                        <span class="fi-badge-label-ctn">
                                            <span class="fi-badge-label">
                                                <?= e($this->getTagPrefix()) ?>
                                                <span x-text="tag"></span>
                                                <?= e($this->getTagSuffix()) ?>
                                            </span>
                                        </span>

                                        <button
                                            type="button"
                                            x-on:click.stop="deleteTag(tag)"
                                            x-bind:aria-label="'<?= e($deleteLabel) ?>: ' + tag"
                                            class="fi-badge-delete-btn"
                                        >
                                            <?= $deleteIconHtml ?>
                                        </button>
                                    </span>
                                </template>
                            </div>
                        </template>
                    </div>
                </div>
            </div>

            <?php if ($hasSuffix) { ?>
                <div
                    <?= (new ComponentAttributeBag)->class([
                        'fi-input-wrp-suffix',
                        'fi-inline' => $isSuffixInline,
                        'fi-input-wrp-suffix-has-label' => filled($suffixLabel),
                    ])->toHtml() ?>
                >
                    <?php if (filled($suffixLabel)) { ?>
                        <span class="fi-input-wrp-label"><?= e($suffixLabel) ?></span>
                    <?php } ?>

                    <?= generate_icon_html($suffixIcon, null, (new ComponentAttributeBag)->color(IconComponent::class, $suffixIconColor))?->toHtml() ?>

                    <?php if (count($suffixActions)) { ?>
                        <div class="fi-input-wrp-actions">
                            <?php foreach ($suffixActions as $suffixAction) { ?>
                                <?= $suffixAction->toHtml() ?>
                            <?php } ?>
                        </div>
                    <?php } ?>
                </div>
            <?php } ?>
        </div>

        <?php return $this->wrapEmbeddedHtml(ob_get_clean(), extraWrapperAttributes: ['class' => 'fi-fo-tags-input-wrp']);
    }

    public function mutatesDehydratedState(): bool
    {
        return parent::mutatesDehydratedState() || $this->isTrimmed();
    }

    public function mutatesStateForValidation(): bool
    {
        return parent::mutatesStateForValidation() || $this->hasStripCharacters() || $this->isTrimmed();
    }
}
