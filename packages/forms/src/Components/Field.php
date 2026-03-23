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
use Filament\Support\Enums\Size;
use Filament\Support\Enums\VerticalAlignment;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Arr;
use Illuminate\Support\ViewErrorBag;
use Illuminate\View\ComponentAttributeBag;
use Illuminate\View\ComponentSlot;
use InvalidArgumentException;

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

    /**
     * @param  array<string, mixed>  $extraWrapperAttributes
     */
    public function wrapEmbeddedHtml(string $html, ?string $labelPrefix = null, ?string $labelSuffix = null, ?VerticalAlignment $inlineLabelVerticalAlignment = null, string $labelTag = 'label', array $extraWrapperAttributes = []): string
    {
        $fieldWrapperView = $this->getFieldWrapperView();

        if ($fieldWrapperView !== 'filament-forms::field-wrapper') {
            $absoluteView = str($fieldWrapperView)->contains('::')
                ? str($fieldWrapperView)->replaceFirst('::', '::components.')
                : "components.{$fieldWrapperView}";

            return view((string) $absoluteView, [
                'field' => $this,
                'slot' => new ComponentSlot($html),
            ])->toHtml();
        }

        $hasInlineLabel = $this->hasInlineLabel();
        $id = $this->getId();
        $isDisabled = $this->isDisabled();
        $label = $this->getLabel();
        $labelSrOnly = $this->isLabelHidden();
        $required = $this->isMarkedAsRequired();
        $statePath = $this->getStatePath();

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

        $hasNestedRecursiveValidationRules = $this instanceof Contracts\HasNestedRecursiveValidationRules;

        /** @var \Illuminate\Support\MessageBag $errors */
        $errors = view()->shared('errors') instanceof ViewErrorBag
            ? view()->shared('errors')->getBag('default')
            : new \Illuminate\Support\MessageBag;

        $hasError = filled($statePath) && ($errors->has($statePath) || ($hasNestedRecursiveValidationRules && $errors->has("{$statePath}.*")));

        $errorMessage = null;
        $errorMessages = [];

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

        $areHtmlErrorMessagesAllowed = $this->areHtmlValidationMessagesAllowed();

        $wrapperAttributes = (new ComponentAttributeBag)
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
                    <?= (new ComponentAttributeBag)->class([
                        'fi-fo-field-label-col',
                        "fi-vertical-align-{$inlineLabelVerticalAlignment->value}" => $hasInlineLabel,
                    ])->toHtml() ?>
                >
                    <?= $aboveLabelSchema?->toHtml() ?>

                    <div
                        <?= (new ComponentAttributeBag)->class([
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
                                <?= $labelPrefix ?>

                                <?php if (filled($label) && (! $labelSrOnly)) { ?>
                                    <span class="fi-fo-field-label-content">
                                        <?= e($label) ?><?php if ($required && (! $isDisabled)) { ?><sup class="fi-fo-field-label-required-mark">*</sup>
                                        <?php } ?>
                                    </span>
                                <?php } ?>

                                <?= $labelSuffix ?>
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
