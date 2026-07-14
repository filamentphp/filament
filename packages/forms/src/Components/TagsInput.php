<?php

namespace Filament\Forms\Components;

use Closure;
use Filament\Actions\Action;
use Filament\Schemas\Components\Concerns\CanStripCharactersFromState;
use Filament\Schemas\Components\Concerns\CanTrimState;
use Filament\Schemas\Components\StateCasts\StripCharactersStateCast;
use Filament\Support\Components\Contracts\HasEmbeddedView;
use Filament\Support\Concerns\HasColor;
use Filament\Support\Concerns\HasExtraAlpineAttributes;
use Filament\Support\Concerns\HasReorderAnimationDuration;
use Filament\Support\Enums\IconSize;
use Filament\Support\Facades\FilamentAsset;
use Filament\Support\Icons\Heroicon;
use Filament\Support\View\ComponentAttributeBag as FilamentComponentAttributeBag;
use Filament\Support\View\Components\BadgeComponent;
use Filament\Support\View\SupportIconAlias;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Js;

use function Filament\Support\generate_icon_html;

class TagsInput extends Field implements Contracts\HasAffixes, Contracts\HasNestedRecursiveValidationRules, HasEmbeddedView
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

    protected ?string $publishedViewOverrideCheckPath = 'filament-forms::components.tags-input';

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

        $this->afterStateHydrated(static function (TagsInput $component): void {
            $component->hydrateTags();
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

    public function hydrateTags(): void
    {
        $state = $this->getState();

        if (is_array($state)) {
            return;
        }

        if (! ($separator = $this->getSeparator())) {
            $this->state([]);

            return;
        }

        $state = explode($separator, $state ?? '');

        if (count($state) === 1 && blank($state[0])) {
            $state = [];
        }

        $this->state($state);
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

        $inputAttributes = $extraInputAttributeBag
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
            ]);

        // Filter visible prefix/suffix actions
        $prefixActions = array_filter(
            $prefixActions,
            static fn (Action $action): bool => $action->isVisible(),
        );
        $suffixActions = array_filter(
            $suffixActions,
            static fn (Action $action): bool => $action->isVisible(),
        );

        $wrapperAttributes = $this->getExtraAttributeBag()
            ->merge([
                'x-on:focus-input.stop' => "\$el.querySelector('input')?.focus()",
            ], escape: false)
            ->class([
                'fi-fo-tags-input',
                'fi-disabled' => $isDisabled,
            ]);

        $deleteLabel = __('filament-forms::components.tags_input.actions.delete.label');

        $badgeAttributes = (new FilamentComponentAttributeBag)
            ->class([
                'fi-badge',
                'fi-size-md',
            ])
            ->color(BadgeComponent::class, $color);

        $deleteIconHtml = generate_icon_html(
            Heroicon::XMark,
            alias: SupportIconAlias::BADGE_DELETE_BUTTON,
            size: IconSize::ExtraSmall,
        )?->toHtml();

        ob_start(); ?>

        <div
            x-load
            x-load-src="<?= e(FilamentAsset::getAlpineComponentSrc('tags-input', 'filament/forms')) ?>"
            x-data="tagsInputFormComponent({
                        state: $wire.<?= $this->applyStateBindingModifiers("\$entangle('{$statePath}')") ?>,
                        splitKeys: <?= Js::from($this->getSplitKeys()) ?>,
                        tagAddedMessage: <?= Js::from(__('filament-forms::components.tags_input.tag_added')) ?>,
                        tagRemovedMessage: <?= Js::from(__('filament-forms::components.tags_input.tag_removed')) ?>,
                    })"
            <?= $this->getExtraAlpineAttributeBag()->toHtml() ?>
        >
            <input <?= $inputAttributes->toHtml() ?> />

            <div x-ref="liveRegion" aria-live="polite" class="fi-sr-only"></div>

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
                                <?= $badgeAttributes->class([
                                    'fi-reorderable' => $isReorderable,
                                ])->toHtml() ?>
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

        <?php $slotHtml = ob_get_clean();

        return $this->wrapEmbeddedHtml(
            $this->wrapInputHtml(
                $slotHtml,
                attributes: $wrapperAttributes,
            ),
            extraWrapperAttributes: ['class' => 'fi-fo-tags-input-wrp'],
        );
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
