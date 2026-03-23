<?php

namespace Filament\Schemas\Components;

use Closure;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Forms\Components\Concerns\CanBeMarkedAsRequired;
use Filament\Forms\Components\Concerns\HasExtraFieldWrapperAttributes;
use Filament\Forms\Components\Concerns\HasHelperText;
use Filament\Forms\Components\Contracts\HasNestedRecursiveValidationRules;
use Filament\Forms\Components\Field;
use Filament\Schemas\Components\Concerns\EntanglesStateWithSingularRelationship;
use Filament\Schemas\Components\Concerns\HasLabel;
use Filament\Schemas\Components\Contracts\CanEntangleWithSingularRelationships;
use Filament\Schemas\Schema;
use Filament\Support\Components\Contracts\HasEmbeddedView;
use Filament\Support\Enums\Size;
use Filament\Support\Enums\VerticalAlignment;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Arr;
use Illuminate\Support\ViewErrorBag;
use Illuminate\View\ComponentAttributeBag;

class FusedGroup extends Component implements CanEntangleWithSingularRelationships, HasEmbeddedView
{
    use CanBeMarkedAsRequired;
    use EntanglesStateWithSingularRelationship;
    use HasExtraFieldWrapperAttributes;
    use HasHelperText;
    use HasLabel;

    const ABOVE_LABEL_SCHEMA_KEY = 'above_label';

    const BELOW_LABEL_SCHEMA_KEY = 'below_label';

    const BEFORE_LABEL_SCHEMA_KEY = 'before_label';

    const AFTER_LABEL_SCHEMA_KEY = 'after_label';

    const BEFORE_CONTENT_SCHEMA_KEY = 'before_content';

    const AFTER_CONTENT_SCHEMA_KEY = 'after_content';

    const ABOVE_CONTENT_SCHEMA_KEY = 'above_content';

    const BELOW_CONTENT_SCHEMA_KEY = 'below_content';

    const ABOVE_ERROR_MESSAGE_SCHEMA_KEY = 'above_error_message';

    const BELOW_ERROR_MESSAGE_SCHEMA_KEY = 'below_error_message';

    /**
     * @param  array<Component | Action | ActionGroup | string | Htmlable> | Closure  $schema
     */
    final public function __construct(array | Closure $schema = [])
    {
        $this->schema($schema);
    }

    /**
     * @param  array<Component | Action | ActionGroup | string | Htmlable> | Closure  $schema
     */
    public static function make(array | Closure $schema = []): static
    {
        $static = app(static::class, ['schema' => $schema]);
        $static->configure();

        return $static;
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->gap(false);
    }

    /**
     * @param  array<Component | Action | ActionGroup> | Schema | Component | Action | ActionGroup | string | Htmlable | Closure | null  $components
     */
    public function aboveLabel(array | Schema | Component | Action | ActionGroup | string | Htmlable | Closure | null $components): static
    {
        $this->childComponents($components, static::ABOVE_LABEL_SCHEMA_KEY);

        return $this;
    }

    /**
     * @param  array<Component | Action | ActionGroup> | Schema | Component | Action | ActionGroup | string | Htmlable | Closure | null  $components
     */
    public function belowLabel(array | Schema | Component | Action | ActionGroup | string | Htmlable | Closure | null $components): static
    {
        $this->childComponents($components, static::BELOW_LABEL_SCHEMA_KEY);

        return $this;
    }

    /**
     * @param  array<Component | Action | ActionGroup> | Schema | Component | Action | ActionGroup | string | Htmlable | Closure | null  $components
     */
    public function beforeLabel(array | Schema | Component | Action | ActionGroup | string | Htmlable | Closure | null $components): static
    {
        $this->childComponents($components, static::BEFORE_LABEL_SCHEMA_KEY);

        return $this;
    }

    /**
     * @param  array<Component | Action | ActionGroup> | Schema | Component | Action | ActionGroup | string | Htmlable | Closure | null  $components
     */
    public function afterLabel(array | Schema | Component | Action | ActionGroup | string | Htmlable | Closure | null $components): static
    {
        $this->childComponents($components, static::AFTER_LABEL_SCHEMA_KEY);

        return $this;
    }

    /**
     * @param  array<Component | Action | ActionGroup> | Schema | Component | Action | ActionGroup | string | Htmlable | Closure | null  $components
     */
    public function beforeContent(array | Schema | Component | Action | ActionGroup | string | Htmlable | Closure | null $components): static
    {
        $this->childComponents($components, static::BEFORE_CONTENT_SCHEMA_KEY);

        return $this;
    }

    /**
     * @param  array<Component | Action | ActionGroup> | Schema | Component | Action | ActionGroup | string | Htmlable | Closure | null  $components
     */
    public function afterContent(array | Schema | Component | Action | ActionGroup | string | Htmlable | Closure | null $components): static
    {
        $this->childComponents($components, static::AFTER_CONTENT_SCHEMA_KEY);

        return $this;
    }

    /**
     * @param  array<Component | Action | ActionGroup> | Schema | Component | Action | ActionGroup | string | Htmlable | Closure | null  $components
     */
    public function aboveContent(array | Schema | Component | Action | ActionGroup | string | Htmlable | Closure | null $components): static
    {
        $this->childComponents($components, static::ABOVE_CONTENT_SCHEMA_KEY);

        return $this;
    }

    /**
     * @param  array<Component | Action | ActionGroup> | Schema | Component | Action | ActionGroup | string | Htmlable | Closure | null  $components
     */
    public function belowContent(array | Schema | Component | Action | ActionGroup | string | Htmlable | Closure | null $components): static
    {
        $this->childComponents($components, static::BELOW_CONTENT_SCHEMA_KEY);

        return $this;
    }

    /**
     * @param  array<Component | Action | ActionGroup> | Schema | Component | Action | ActionGroup | string | Htmlable | Closure | null  $components
     */
    public function aboveErrorMessage(array | Schema | Component | Action | ActionGroup | string | Htmlable | Closure | null $components): static
    {
        $this->childComponents($components, static::ABOVE_ERROR_MESSAGE_SCHEMA_KEY);

        return $this;
    }

    /**
     * @param  array<Component | Action | ActionGroup> | Schema | Component | Action | ActionGroup | string | Htmlable | Closure | null  $components
     */
    public function belowErrorMessage(array | Schema | Component | Action | ActionGroup | string | Htmlable | Closure | null $components): static
    {
        $this->childComponents($components, static::BELOW_ERROR_MESSAGE_SCHEMA_KEY);

        return $this;
    }

    protected function makeChildSchema(string $key): Schema
    {
        $schema = parent::makeChildSchema($key);

        if ($key === static::AFTER_LABEL_SCHEMA_KEY) {
            $schema->alignEnd();
        }

        $schema->fieldWrapperView('filament-forms::plain-field-wrapper');

        return $schema;
    }

    protected function configureChildSchema(Schema $schema, string $key): Schema
    {
        $schema = parent::configureChildSchema($schema, $key);

        if (in_array($key, [
            static::BEFORE_LABEL_SCHEMA_KEY,
            static::AFTER_LABEL_SCHEMA_KEY,
            static::ABOVE_CONTENT_SCHEMA_KEY,
            static::BELOW_CONTENT_SCHEMA_KEY,
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

    public function toEmbeddedHtml(): string
    {
        // Custom error collection from child fields
        /** @var \Illuminate\Support\MessageBag $errors */
        $errors = view()->shared('errors') instanceof ViewErrorBag
            ? view()->shared('errors')->getBag('default')
            : new \Illuminate\Support\MessageBag;

        $errorMessage = null;
        $errorMessages = [];
        $areHtmlErrorMessagesAllowed = false;

        foreach ($this->getChildComponentContainer()->getComponents() as $childComponent) {
            if (! ($childComponent instanceof Field)) {
                continue;
            }

            $statePath = $childComponent->getStatePath();

            if (blank($statePath)) {
                continue;
            }

            if ($errors->has($statePath)) {
                if ($childComponent->shouldShowAllValidationMessages()) {
                    $errorMessages = $errors->get($statePath);
                } else {
                    $errorMessage = $errors->first($statePath);
                }

                $areHtmlErrorMessagesAllowed = $childComponent->areHtmlValidationMessagesAllowed();

                break;
            }

            if (! ($childComponent instanceof HasNestedRecursiveValidationRules)) {
                continue;
            }

            if ($errors->has("{$statePath}.*")) {
                if ($childComponent->shouldShowAllValidationMessages()) {
                    $errorMessages = $errors->get("{$statePath}.*");
                } else {
                    $errorMessage = $errors->first("{$statePath}.*");
                }

                $areHtmlErrorMessagesAllowed = $childComponent->areHtmlValidationMessagesAllowed();

                break;
            }
        }

        $hasError = filled($errorMessage) || filled($errorMessages);

        // Field wrapper rendering (inline, same as Field::wrapEmbeddedHtml but with custom errors)
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

        $wrapperAttributes = (new ComponentAttributeBag)
            ->merge($this->getExtraFieldWrapperAttributes(), escape: false)
            ->class([
                'fi-fo-field',
                'fi-fo-field-has-inline-label' => $hasInlineLabel,
            ]);

        // Inner content
        $innerAttributes = (new ComponentAttributeBag)
            ->merge(['id' => $id], escape: false)
            ->merge($this->getExtraAttributes(), escape: false)
            ->class(['fi-sc-fused-group']);

        $innerHtml = '<div ' . $innerAttributes->toHtml() . '>' . $this->getChildSchema()->toHtml() . '</div>';

        $inlineLabelVerticalAlignment = VerticalAlignment::Start;

        ob_start(); ?>

        <div data-field-wrapper <?= $wrapperAttributes->toHtml() ?>>
            <?php if (filled($label) && $labelSrOnly) { ?>
                <label for="<?= e($id) ?>" class="fi-fo-field-label fi-sr-only"><?= e($label) ?></label>
            <?php } ?>

            <?php if ((filled($label) && (! $labelSrOnly)) || $hasInlineLabel || $aboveLabelSchema || $belowLabelSchema || $beforeLabelSchema || $afterLabelSchema) { ?>
                <div
                    <?= (new ComponentAttributeBag)->class([
                        'fi-fo-field-label-col',
                        "fi-vertical-align-{$inlineLabelVerticalAlignment->value}" => $hasInlineLabel,
                    ])->toHtml() ?>
                >
                    <?= $aboveLabelSchema?->toHtml() ?>
                    <div class="fi-fo-field-label-ctn">
                        <?= $beforeLabelSchema?->toHtml() ?>
                        <?php if (filled($label) && (! $labelSrOnly)) { ?>
                            <label for="<?= e($id) ?>" class="fi-fo-field-label">
                                <span class="fi-fo-field-label-content">
                                    <?= e($label) ?><?php if ($required && (! $isDisabled)) { ?><sup class="fi-fo-field-label-required-mark">*</sup><?php } ?>
                                </span>
                            </label>
                        <?php } ?>
                        <?= $afterLabelSchema?->toHtml() ?>
                    </div>
                    <?= $belowLabelSchema?->toHtml() ?>
                </div>
            <?php } ?>

            <div class="fi-fo-field-content-col">
                <?= $aboveContentSchema?->toHtml() ?>

                <?php if ($beforeContentSchema || $afterContentSchema) { ?>
                    <div class="fi-fo-field-content-ctn">
                        <?= $beforeContentSchema?->toHtml() ?>
                        <div class="fi-fo-field-content"><?= $innerHtml ?></div>
                        <?= $afterContentSchema?->toHtml() ?>
                    </div>
                <?php } else { ?>
                    <?= $innerHtml ?>
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
                        <div data-validation-error class="fi-fo-field-wrp-error-message"><?= $errorMessage ?></div>
                    <?php } else { ?>
                        <p data-validation-error class="fi-fo-field-wrp-error-message"><?= e($errorMessage) ?></p>
                    <?php } ?>

                    <?= $belowErrorMessageSchema?->toHtml() ?>
                <?php } ?>
            </div>
        </div>

        <?php return ob_get_clean();
    }

    public function isRequired(): bool
    {
        foreach ($this->getDefaultChildComponents() as $component) {
            if (! ($component instanceof Field)) {
                continue;
            }

            if ($component->isMarkedAsRequired()) {
                return true;
            }
        }

        return false;
    }
}
