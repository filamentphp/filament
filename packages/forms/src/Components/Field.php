<?php

namespace Filament\Forms\Components;

use Closure;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Components\Concerns\HasLabel;
use Filament\Schemas\Components\Concerns\HasName;
use Filament\Schemas\Components\StateCasts\Contracts\StateCast;
use Filament\Schemas\Components\StateCasts\EnumStateCast;
use Filament\Schemas\Schema;
use Filament\Support\Components\ViewComponent;
use Filament\Support\Enums\Size;
use Filament\Support\Enums\VerticalAlignment;
use Filament\Support\View\ComponentAttributeBag as FilamentComponentAttributeBag;
use Filament\Support\View\Components\InputComponent\WrapperComponent\IconComponent;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Arr;
use Illuminate\Support\MessageBag;
use Illuminate\Support\Str;
use Illuminate\Support\ViewErrorBag;
use Illuminate\View\ComponentAttributeBag;
use Illuminate\View\ComponentSlot;
use InvalidArgumentException;

use function Filament\Support\generate_icon_html;
use function Filament\Support\generate_loading_indicator_html;

class Field extends Component implements Contracts\HasValidationRules
{
    use Concerns\CanBeAutofocused;
    use Concerns\CanBeMarkedAsRequired;
    use Concerns\CanBeValidated;
    use Concerns\HasEnum;
    use Concerns\HasExtraFieldWrapperAttributes;
    use Concerns\HasHelperText;
    use Concerns\HasHint;
    use HasLabel {
        getLabel as getBaseLabel;
    }
    use HasName;

    protected string $viewIdentifier = 'field';

    const ABOVE_LABEL_SCHEMA_KEY = 'above_label';

    const BELOW_LABEL_SCHEMA_KEY = 'below_label';

    const BEFORE_LABEL_SCHEMA_KEY = 'before_label';

    const AFTER_LABEL_SCHEMA_KEY = 'after_label';

    const ABOVE_CONTENT_SCHEMA_KEY = 'above_content';

    const BELOW_CONTENT_SCHEMA_KEY = 'below_content';

    const BEFORE_CONTENT_SCHEMA_KEY = 'before_content';

    const AFTER_CONTENT_SCHEMA_KEY = 'after_content';

    const ABOVE_ERROR_MESSAGE_SCHEMA_KEY = 'above_error_message';

    const BELOW_ERROR_MESSAGE_SCHEMA_KEY = 'below_error_message';

    final public function __construct(string $name)
    {
        $this->name($name);
        $this->statePath($name);
    }

    public static function make(?string $name = null): static
    {
        $fieldClass = static::class;

        $name ??= static::getDefaultName();

        if ($name === null) {
            throw new InvalidArgumentException("Field of class [$fieldClass] must have a unique name, passed to the [make()] method.");
        }

        $static = app($fieldClass, ['name' => $name]);

        $static->configure();

        return $static;
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->setUpHint();
    }

    public static function getDefaultName(): ?string
    {
        return null;
    }

    /**
     * @return array<StateCast>
     */
    public function getDefaultStateCasts(): array
    {
        $casts = parent::getDefaultStateCasts();

        if ($enumStateCast = $this->getEnumDefaultStateCast()) {
            $casts[] = $enumStateCast;
        }

        return $casts;
    }

    public function getEnumDefaultStateCast(): ?StateCast
    {
        $enum = $this->getEnum();

        if (blank($enum)) {
            return null;
        }

        return app(
            EnumStateCast::class,
            ['enum' => $enum],
        );
    }

    /**
     * @param  array<Component | Action | ActionGroup | string | Htmlable> | Schema | Component | Action | ActionGroup | string | Htmlable | Closure | null  $components
     */
    public function aboveLabel(array | Schema | Component | Action | ActionGroup | string | Htmlable | Closure | null $components): static
    {
        $this->childComponents($components, static::ABOVE_LABEL_SCHEMA_KEY);

        return $this;
    }

    /**
     * @param  array<Component | Action | ActionGroup | string | Htmlable> | Schema | Component | Action | ActionGroup | string | Htmlable | Closure | null  $components
     */
    public function belowLabel(array | Schema | Component | Action | ActionGroup | string | Htmlable | Closure | null $components): static
    {
        $this->childComponents($components, static::BELOW_LABEL_SCHEMA_KEY);

        return $this;
    }

    /**
     * @param  array<Component | Action | ActionGroup | string | Htmlable> | Schema | Component | Action | ActionGroup | string | Htmlable | Closure | null  $components
     */
    public function beforeLabel(array | Schema | Component | Action | ActionGroup | string | Htmlable | Closure | null $components): static
    {
        $this->childComponents($components, static::BEFORE_LABEL_SCHEMA_KEY);

        return $this;
    }

    /**
     * @param  array<Component | Action | ActionGroup | string | Htmlable> | Schema | Component | Action | ActionGroup | string | Htmlable | Closure | null  $components
     */
    public function afterLabel(array | Schema | Component | Action | ActionGroup | string | Htmlable | Closure | null $components): static
    {
        $this->childComponents($components, static::AFTER_LABEL_SCHEMA_KEY);

        return $this;
    }

    /**
     * @param  array<Component | Action | ActionGroup | string | Htmlable> | Schema | Component | Action | ActionGroup | string | Htmlable | Closure | null  $components
     */
    public function aboveContent(array | Schema | Component | Action | ActionGroup | string | Htmlable | Closure | null $components): static
    {
        $this->childComponents($components, static::ABOVE_CONTENT_SCHEMA_KEY);

        return $this;
    }

    /**
     * @param  array<Component | Action | ActionGroup | string | Htmlable> | Schema | Component | Action | ActionGroup | string | Htmlable | Closure | null  $components
     */
    public function belowContent(array | Schema | Component | Action | ActionGroup | string | Htmlable | Closure | null $components): static
    {
        $this->childComponents($components, static::BELOW_CONTENT_SCHEMA_KEY);

        return $this;
    }

    /**
     * @param  array<Component | Action | ActionGroup | string | Htmlable> | Schema | Component | Action | ActionGroup | string | Htmlable | Closure | null  $components
     */
    public function beforeContent(array | Schema | Component | Action | ActionGroup | string | Htmlable | Closure | null $components): static
    {
        $this->childComponents($components, static::BEFORE_CONTENT_SCHEMA_KEY);

        return $this;
    }

    /**
     * @param  array<Component | Action | ActionGroup | string | Htmlable> | Schema | Component | Action | ActionGroup | string | Htmlable | Closure | null  $components
     */
    public function afterContent(array | Schema | Component | Action | ActionGroup | string | Htmlable | Closure | null $components): static
    {
        $this->childComponents($components, static::AFTER_CONTENT_SCHEMA_KEY);

        return $this;
    }

    /**
     * @param  array<Component | Action | ActionGroup | string | Htmlable> | Schema | Component | Action | ActionGroup | string | Htmlable | Closure | null  $components
     */
    public function aboveErrorMessage(array | Schema | Component | Action | ActionGroup | string | Htmlable | Closure | null $components): static
    {
        $this->childComponents($components, static::ABOVE_ERROR_MESSAGE_SCHEMA_KEY);

        return $this;
    }

    /**
     * @param  array<Component | Action | ActionGroup | string | Htmlable> | Schema | Component | Action | ActionGroup | string | Htmlable | Closure | null  $components
     */
    public function belowErrorMessage(array | Schema | Component | Action | ActionGroup | string | Htmlable | Closure | null $components): static
    {
        $this->childComponents($components, static::BELOW_ERROR_MESSAGE_SCHEMA_KEY);

        return $this;
    }

    protected function makeChildSchema(string $key): Schema
    {
        $schema = parent::makeChildSchema($key);

        if (in_array($key, [static::AFTER_LABEL_SCHEMA_KEY, static::AFTER_CONTENT_SCHEMA_KEY])) {
            $schema->alignEnd();
        }

        return $schema;
    }

    protected function configureChildSchema(Schema $schema, string $key): Schema
    {
        $schema = parent::configureChildSchema($schema, $key);

        if (in_array($key, [
            static::ABOVE_LABEL_SCHEMA_KEY,
            static::BELOW_LABEL_SCHEMA_KEY,
            static::BEFORE_LABEL_SCHEMA_KEY,
            static::AFTER_LABEL_SCHEMA_KEY,
            static::ABOVE_CONTENT_SCHEMA_KEY,
            static::BELOW_CONTENT_SCHEMA_KEY,
            static::BEFORE_CONTENT_SCHEMA_KEY,
            static::AFTER_CONTENT_SCHEMA_KEY,
            static::ABOVE_ERROR_MESSAGE_SCHEMA_KEY,
            static::BELOW_ERROR_MESSAGE_SCHEMA_KEY,
        ])) {
            $schema
                ->inline()
                ->embeddedInParentComponent()
                ->modifyActionsUsing(fn (Action $action) => $action
                    ->defaultSize(Size::Small)
                    ->defaultView(Action::LINK_VIEW))
                ->modifyActionGroupsUsing(fn (ActionGroup $actionGroup) => $actionGroup->defaultSize(Size::Small));
        }

        return $schema;
    }

    public function hasErrorForPath(?string $statePath): bool
    {
        if (blank($statePath)) {
            return false;
        }

        $errors = view()->shared('errors');

        if (! $errors instanceof ViewErrorBag) {
            return false;
        }

        return $errors->has($statePath);
    }

    /**
     * @internal This method is not part of the public API and should not be used. Its parameters may change at any time without notice.
     *
     * @param  array<string, mixed>  $extraWrapperAttributes
     */
    public function wrapEmbeddedHtml(
        string $html,
        array $extraWrapperAttributes = [],
        ?VerticalAlignment $inlineLabelVerticalAlignment = null,
        string | Htmlable | null $labelPrefix = null,
        string | Htmlable | null $labelSuffix = null,
        string $labelTag = 'label',
    ): string {
        $statePath = $this->getStatePath();

        $hasNestedRecursiveValidationRules = $this instanceof Contracts\HasNestedRecursiveValidationRules;

        /** @var MessageBag $errors */
        $errors = view()->shared('errors') instanceof ViewErrorBag
            ? view()->shared('errors')->getBag('default')
            : new MessageBag;

        $hasError = filled($statePath) && ($errors->has($statePath) || ($hasNestedRecursiveValidationRules && $errors->has("{$statePath}.*")));

        $errorMessage = null;
        $errorMessages = null;

        if ($hasError) {
            if ($this->shouldShowAllValidationMessages()) {
                $errorMessages = $errors->has($statePath)
                    ? $errors->get($statePath)
                    : ($hasNestedRecursiveValidationRules ? $errors->get("{$statePath}.*") : []);

                if (count($errorMessages) === 1) {
                    $errorMessage = Arr::first($errorMessages);
                    $errorMessages = [];
                }
            } else {
                $errorMessage = $errors->has($statePath)
                    ? $errors->first($statePath)
                    : ($hasNestedRecursiveValidationRules ? $errors->first("{$statePath}.*") : null);
            }
        }

        $fieldWrapperView = $this->getFieldWrapperView();
        $isDefaultFieldWrapperView = $fieldWrapperView === 'filament-forms::field-wrapper';

        if (
            (! $isDefaultFieldWrapperView)
            || ViewComponent::hasPublishedEmbeddedViewOverride('filament-forms::components.field-wrapper')
        ) {
            $absoluteView = $isDefaultFieldWrapperView
                ? 'filament-forms::components.field-wrapper'
                : (string) (str($fieldWrapperView)->contains('::')
                    ? str($fieldWrapperView)->replaceFirst('::', '::components.')
                    : str("components.{$fieldWrapperView}"));

            if (view()->exists($absoluteView)) {
                return view($absoluteView, [
                    'field' => $this,
                    'slot' => new ComponentSlot($html),
                    'labelPrefix' => $labelPrefix,
                    'labelSuffix' => $labelSuffix,
                    'inlineLabelVerticalAlignment' => $inlineLabelVerticalAlignment ?? VerticalAlignment::Start,
                    'labelTag' => $labelTag,
                    'attributes' => (new FilamentComponentAttributeBag)->merge($extraWrapperAttributes, escape: false),
                    'hasErrors' => $hasError,
                    'errorMessage' => $errorMessage,
                    'errorMessages' => $errorMessages,
                ])->toHtml();
            }

            return $this->renderWrapperBladeComponent(
                $fieldWrapperView,
                new ComponentSlot($html),
                (new FilamentComponentAttributeBag([
                    'field' => $this,
                    'label-prefix' => $labelPrefix,
                    'label-suffix' => $labelSuffix,
                    'inline-label-vertical-alignment' => $inlineLabelVerticalAlignment ?? VerticalAlignment::Start,
                    'label-tag' => $labelTag,
                    'has-errors' => $hasError,
                    'error-message' => $errorMessage,
                    'error-messages' => $errorMessages,
                ]))->merge($extraWrapperAttributes, escape: false),
            );
        }

        $hasInlineLabel = $this->hasInlineLabel();
        $id = $this->getId();
        $isDisabled = $this->isDisabled();
        $label = $this->getLabel();
        $labelSrOnly = $this->isLabelHidden();
        $required = $this->isMarkedAsRequired();

        $aboveLabelSchema = $this->getChildSchema(static::ABOVE_LABEL_SCHEMA_KEY)?->toHtmlString();
        $belowLabelSchema = $this->getChildSchema(static::BELOW_LABEL_SCHEMA_KEY)?->toHtmlString();
        $beforeLabelSchema = $this->getChildSchema(static::BEFORE_LABEL_SCHEMA_KEY)?->toHtmlString();
        $afterLabelSchema = $this->getChildSchema(static::AFTER_LABEL_SCHEMA_KEY)?->toHtmlString();
        $aboveContentSchema = $this->getChildSchema(static::ABOVE_CONTENT_SCHEMA_KEY)?->toHtmlString();
        $belowContentSchema = $this->getChildSchema(static::BELOW_CONTENT_SCHEMA_KEY)?->toHtmlString();
        $beforeContentSchema = $this->getChildSchema(static::BEFORE_CONTENT_SCHEMA_KEY)?->toHtmlString();
        $afterContentSchema = $this->getChildSchema(static::AFTER_CONTENT_SCHEMA_KEY)?->toHtmlString();
        $aboveErrorMessageSchema = $this->getChildSchema(static::ABOVE_ERROR_MESSAGE_SCHEMA_KEY)?->toHtmlString();
        $belowErrorMessageSchema = $this->getChildSchema(static::BELOW_ERROR_MESSAGE_SCHEMA_KEY)?->toHtmlString();

        $areHtmlErrorMessagesAllowed = $this->areHtmlValidationMessagesAllowed();

        $wrapperAttributes = (new FilamentComponentAttributeBag)
            ->merge($extraWrapperAttributes, escape: false)
            ->merge($this->getExtraFieldWrapperAttributes(), escape: false)
            ->class([
                'fi-fo-field',
                'fi-fo-field-has-inline-label' => $hasInlineLabel,
            ]);

        $inlineLabelVerticalAlignment ??= VerticalAlignment::Start;

        ob_start(); ?>

        <div data-field-wrapper <?= $wrapperAttributes->toHtml() ?>>
            <?php if (filled($label) && $labelSrOnly) { ?>
                <<?= $labelTag ?>
                    <?php if ($labelTag === 'label') { ?>
                        for="<?= e($id) ?>"
                    <?php } else { ?>
                        id="<?= e($id) ?>-label"
                    <?php } ?>
                    class="fi-fo-field-label fi-sr-only"
                >
                    <?= e($label) ?>
                </<?= $labelTag ?>>
            <?php } ?>

            <?php if ((filled($label) && (! $labelSrOnly)) || $hasInlineLabel || $aboveLabelSchema || $belowLabelSchema || $beforeLabelSchema || $afterLabelSchema || $labelPrefix || $labelSuffix) { ?>
                <div
                    <?= (new FilamentComponentAttributeBag)->class([
                        'fi-fo-field-label-col',
                        "fi-vertical-align-{$inlineLabelVerticalAlignment->value}" => $hasInlineLabel,
                    ])->toHtml() ?>
                >
                    <?= $aboveLabelSchema?->toHtml() ?>

                    <div
                        <?= (new FilamentComponentAttributeBag)->class([
                            'fi-fo-field-label-ctn',
                            ($label instanceof ComponentSlot) ? $label->attributes->get('class') : null,
                        ])->toHtml() ?>
                    >
                        <?= $beforeLabelSchema?->toHtml() ?>

                        <?php if ((filled($label) && (! $labelSrOnly)) || $labelPrefix || $labelSuffix) { ?>
                            <<?= $labelTag ?>
                                <?php if ($labelTag === 'label') { ?>
                                    for="<?= e($id) ?>"
                                <?php } else { ?>
                                    id="<?= e($id) ?>-label"
                                <?php } ?>
                                class="fi-fo-field-label"
                            >
                                <?= e($labelPrefix) ?>

                                <?php if (filled($label) && (! $labelSrOnly)) { ?>
                                    <span class="fi-fo-field-label-content">
                                        <?= e($label) ?><?php if ($required && (! $isDisabled)) { ?><sup class="fi-fo-field-label-required-mark">*</sup>
                                        <?php } ?>
                                    </span>
                                <?php } ?>

                                <?= e($labelSuffix) ?>
                            </<?= $labelTag ?>>
                        <?php } ?>

                        <?= $afterLabelSchema?->toHtml() ?>
                    </div>

                    <?= $belowLabelSchema?->toHtml() ?>
                </div>
            <?php } ?>

            <?php if (filled($html) || $hasError || $aboveContentSchema || $belowContentSchema || $beforeContentSchema || $afterContentSchema || $aboveErrorMessageSchema || $belowErrorMessageSchema) { ?>
                <div class="fi-fo-field-content-col">
                    <?= $aboveContentSchema?->toHtml() ?>

                    <?php if ($beforeContentSchema || $afterContentSchema) { ?>
                        <div class="fi-fo-field-content-ctn">
                            <?= $beforeContentSchema?->toHtml() ?>

                            <div class="fi-fo-field-content">
                                <?= $html ?>
                            </div>

                            <?= $afterContentSchema?->toHtml() ?>
                        </div>
                    <?php } else { ?>
                        <?= $html ?>
                    <?php } ?>

                    <?= $belowContentSchema?->toHtml() ?>

                    <?php if ($hasError) { ?>
                        <?= $aboveErrorMessageSchema?->toHtml() ?>

                        <?php if (filled($errorMessages)) { ?>
                            <ul data-validation-error class="fi-fo-field-wrp-error-list">
                                <?php foreach ($errorMessages as $errorMsg) { ?>
                                    <li class="fi-fo-field-wrp-error-message">
                                        <?php if ($areHtmlErrorMessagesAllowed) { ?>
                                            <?= $errorMsg ?>
                                        <?php } else { ?>
                                            <?= e($errorMsg) ?>
                                        <?php } ?>
                                    </li>
                                <?php } ?>
                            </ul>
                        <?php } elseif ($areHtmlErrorMessagesAllowed) { ?>
                            <div data-validation-error class="fi-fo-field-wrp-error-message">
                                <?= $errorMessage ?>
                            </div>
                        <?php } else { ?>
                            <p data-validation-error class="fi-fo-field-wrp-error-message">
                                <?= e($errorMessage) ?>
                            </p>
                        <?php } ?>

                        <?= $belowErrorMessageSchema?->toHtml() ?>
                    <?php } ?>
                </div>
            <?php } ?>
        </div>

        <?php return ob_get_clean();
    }

    /**
     * @internal This method is not part of the public API and should not be used. Its parameters may change at any time without notice.
     */
    protected function wrapInputHtml(
        string $html,
        ?string $alpineDisabled = null,
        ?string $alpineValid = null,
        ?ComponentAttributeBag $attributes = null,
    ): string {
        $attributes ??= new FilamentComponentAttributeBag;

        $hasAffixes = $this instanceof Contracts\HasAffixes;

        $prefix = $hasAffixes ? $this->getPrefixLabel() : null;
        $prefixActions = $hasAffixes ? array_filter(
            $this->getPrefixActions(),
            static fn (Action $prefixAction): bool => $prefixAction->isVisible(),
        ) : [];
        $prefixIcon = $hasAffixes ? $this->getPrefixIcon() : null;
        $prefixIconColor = ($hasAffixes ? $this->getPrefixIconColor() : null) ?? 'gray';
        $suffix = $hasAffixes ? $this->getSuffixLabel() : null;
        $suffixActions = $hasAffixes ? array_filter(
            $this->getSuffixActions(),
            static fn (Action $suffixAction): bool => $suffixAction->isVisible(),
        ) : [];
        $suffixIcon = $hasAffixes ? $this->getSuffixIcon() : null;
        $suffixIconColor = ($hasAffixes ? $this->getSuffixIconColor() : null) ?? 'gray';

        $hasPrefix = count($prefixActions) || $prefixIcon || filled($prefix);
        $hasSuffix = count($suffixActions) || $suffixIcon || filled($suffix);

        $hasInlinePrefix = $hasAffixes && $hasPrefix && $this->isPrefixInline();
        $hasInlineSuffix = $hasAffixes && $hasSuffix && $this->isSuffixInline();

        $isDisabled = $this->isDisabled();
        $isValid = ! $this->hasErrorForPath($this->getStatePath());

        $hasAlpineDisabledClasses = filled($alpineDisabled);
        $hasAlpineValidClasses = filled($alpineValid);
        $hasAlpineClasses = $hasAlpineDisabledClasses || $hasAlpineValidClasses;

        $wireTarget = $attributes->whereStartsWith(['wire:target'])->first();
        $hasLoadingIndicator = filled($wireTarget);
        $loadingIndicatorTarget = $hasLoadingIndicator ? html_entity_decode((string) $wireTarget, ENT_QUOTES) : null;
        $loadingDelay = config('filament.livewire_loading_delay', 'default');

        $hasFocusInputListener = $attributes->has('x-on:focus-input.stop');
        $canClickPrefixAffix = $hasFocusInputListener && ($prefixIcon || filled($prefix));
        $canClickSuffixAffix = $hasFocusInputListener && ($suffixIcon || filled($suffix));

        $wrapperAttributes = $attributes
            ->except(['wire:target', 'tabindex'])
            ->class([
                'fi-input-wrp',
                'fi-disabled' => (! $hasAlpineClasses) && $isDisabled,
                'fi-invalid' => (! $hasAlpineClasses) && (! $isValid),
            ]);

        if ($hasAlpineClasses) {
            $alpineClassParts = [];

            if ($hasAlpineDisabledClasses) {
                $alpineClassParts[] = "'fi-disabled': {$alpineDisabled}";
            }

            if ($hasAlpineValidClasses) {
                $alpineClassParts[] = "'fi-invalid': ! ({$alpineValid})";
            }

            $wrapperAttributes = $wrapperAttributes->merge([
                'x-bind:class' => '{ ' . implode(', ', $alpineClassParts) . ' }',
            ], escape: false);
        }

        ob_start(); ?>

        <div <?= $wrapperAttributes->toHtml() ?>>
            <?php if ($hasPrefix || $hasLoadingIndicator) {
                $prefixDivAttributes = (new FilamentComponentAttributeBag)->class([
                    'fi-input-wrp-prefix',
                    'fi-input-wrp-prefix-has-content' => $hasPrefix,
                    'fi-inline' => $hasInlinePrefix,
                    'fi-input-wrp-prefix-has-label' => filled($prefix),
                ]);

                if (! $hasPrefix) {
                    $prefixDivAttributes = $prefixDivAttributes->merge([
                        'wire:loading.delay.' . $loadingDelay . '.flex' => true,
                        'wire:target' => $loadingIndicatorTarget,
                        // Forces the loading indicator to hide once the request completes.
                        'wire:key' => Str::random(),
                    ], escape: false);
                }

                if ($canClickPrefixAffix) {
                    $prefixDivAttributes = $prefixDivAttributes->merge([
                        'x-on:click' => '$dispatch(\'focus-input\')',
                    ], escape: false);
                }
                ?>
                <div <?= $prefixDivAttributes->toHtml() ?>>
                    <?php if (count($prefixActions)) { ?>
                        <div
                            class="fi-input-wrp-actions"
                            <?php if ($canClickPrefixAffix) { ?>x-on:click.stop<?php } ?>
                        >
                            <?php foreach ($prefixActions as $prefixAction) { ?>
                                <?= $prefixAction->toHtml() ?>
                            <?php } ?>
                        </div>
                    <?php } ?>

                    <?= generate_icon_html(
                        $prefixIcon,
                        attributes: (new FilamentComponentAttributeBag)
                            ->merge([
                                'wire:loading.remove.delay.' . $loadingDelay => $hasLoadingIndicator,
                                'wire:target' => $hasLoadingIndicator ? $loadingIndicatorTarget : false,
                            ], escape: false)
                            ->color(IconComponent::class, $prefixIconColor),
                    )?->toHtml() ?>

                    <?php if ($hasLoadingIndicator) { ?>
                        <?= generate_loading_indicator_html((new FilamentComponentAttributeBag([
                            'wire:loading.delay.' . $loadingDelay => $hasPrefix,
                            'wire:target' => $hasPrefix ? $loadingIndicatorTarget : null,
                        ]))->color(IconComponent::class, 'gray'))->toHtml() ?>
                    <?php } ?>

                    <?php if (filled($prefix)) { ?>
                        <span class="fi-input-wrp-label">
                            <?= e($prefix) ?>
                        </span>
                    <?php } ?>
                </div>
            <?php } ?>

            <?php
                $contentCtnAttributes = (new FilamentComponentAttributeBag)->class([
                    'fi-input-wrp-content-ctn',
                    'fi-input-wrp-content-ctn-ps' => $hasLoadingIndicator && (! $hasPrefix) && $hasInlinePrefix,
                ]);

        if ($hasLoadingIndicator && (! $hasPrefix)) {
            $contentCtnAttributes = $contentCtnAttributes->merge([
                'wire:target' => $loadingIndicatorTarget,
                'wire:loading.delay.' . $loadingDelay . '.class.remove' => $hasInlinePrefix ? 'ps-3' : false,
            ], escape: false);
        }
        ?>
            <div <?= $contentCtnAttributes->toHtml() ?>>
                <?= $html ?>
            </div>

            <?php if ($hasSuffix) {
                $suffixDivAttributes = (new FilamentComponentAttributeBag)->class([
                    'fi-input-wrp-suffix',
                    'fi-inline' => $hasInlineSuffix,
                    'fi-input-wrp-suffix-has-label' => filled($suffix),
                ]);

                if ($canClickSuffixAffix) {
                    $suffixDivAttributes = $suffixDivAttributes->merge([
                        'x-on:click' => '$dispatch(\'focus-input\')',
                    ], escape: false);
                }
                ?>
                <div <?= $suffixDivAttributes->toHtml() ?>>
                    <?php if (filled($suffix)) { ?>
                        <span class="fi-input-wrp-label">
                            <?= e($suffix) ?>
                        </span>
                    <?php } ?>

                    <?= generate_icon_html(
                        $suffixIcon,
                        attributes: (new FilamentComponentAttributeBag)
                            ->merge([
                                'wire:loading.remove.delay.' . $loadingDelay => $hasLoadingIndicator,
                                'wire:target' => $hasLoadingIndicator ? $loadingIndicatorTarget : false,
                            ], escape: false)
                            ->color(IconComponent::class, $suffixIconColor),
                    )?->toHtml() ?>

                    <?php if (count($suffixActions)) { ?>
                        <div
                            class="fi-input-wrp-actions"
                            <?php if ($canClickSuffixAffix) { ?>x-on:click.stop<?php } ?>
                        >
                            <?php foreach ($suffixActions as $suffixAction) { ?>
                                <?= $suffixAction->toHtml() ?>
                            <?php } ?>
                        </div>
                    <?php } ?>
                </div>
            <?php } ?>
        </div>

        <?php return ob_get_clean();
    }

    public function hasNullableBooleanState(): bool
    {
        return false;
    }

    public function getLabel(): string | Htmlable | null
    {
        if (filled($label = $this->getBaseLabel())) {
            return $label;
        }

        return $this->getDefaultLabel();
    }

    public function getDefaultLabel(): string
    {
        $label = (string) str($this->getName())
            ->afterLast('.')
            ->kebab()
            ->replace(['-', '_'], ' ')
            ->ucfirst();

        return $this->shouldTranslateLabel ? __($label) : $label;
    }
}
