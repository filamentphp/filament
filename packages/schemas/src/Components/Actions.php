<?php

namespace Filament\Schemas\Components;

use Closure;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Schemas\Components\Concerns\HasLabel;
use Filament\Schemas\Schema;
use Filament\Support\Components\Contracts\HasEmbeddedView;
use Filament\Support\Concerns\HasAlignment;
use Filament\Support\Concerns\HasVerticalAlignment;
use Filament\Support\Enums\Alignment;
use Filament\Support\Enums\Size;
use Filament\Support\Enums\VerticalAlignment;
use Filament\Support\View\ComponentAttributeBag as FilamentComponentAttributeBag;
use Illuminate\Contracts\Support\Htmlable;

class Actions extends Component implements HasEmbeddedView
{
    use HasAlignment;
    use HasLabel;
    use HasVerticalAlignment;

    protected ?string $publishedViewOverrideCheckPath = 'filament-schemas::components.actions';

    protected bool | Closure $isSticky = false;

    protected bool | Closure $isFullWidth = false;

    const BEFORE_LABEL_SCHEMA_KEY = 'before_label';

    const AFTER_LABEL_SCHEMA_KEY = 'after_label';

    const ABOVE_CONTENT_SCHEMA_KEY = 'above_content';

    const BELOW_CONTENT_SCHEMA_KEY = 'below_content';

    /**
     * @param  array<Action | ActionGroup> | Closure  $actions
     */
    final public function __construct(array | Closure $actions)
    {
        $this->actions($actions);
    }

    /**
     * @param  array<Action | ActionGroup> | Closure  $actions
     */
    public static function make(array | Closure $actions): static
    {
        $static = app(static::class, ['actions' => $actions]);
        $static->configure();

        return $static;
    }

    /**
     * @param  array<Action | ActionGroup> | Closure  $actions
     */
    public function actions(array | Closure $actions): static
    {
        $this->components($actions);

        return $this;
    }

    public function isHidden(): bool
    {
        if (parent::isHidden()) {
            return true;
        }

        foreach ($this->getChildSchema()->getComponents() as $component) {
            if ($component->isVisible()) {
                return false;
            }
        }

        return true;
    }

    public function fullWidth(bool | Closure $isFullWidth = true): static
    {
        $this->isFullWidth = $isFullWidth;

        return $this;
    }

    public function isFullWidth(): bool
    {
        return (bool) $this->evaluate($this->isFullWidth);
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

    protected function makeChildSchema(string $key): Schema
    {
        $schema = parent::makeChildSchema($key);

        if ($key === static::AFTER_LABEL_SCHEMA_KEY) {
            $schema->alignEnd();
        }

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

    public function sticky(bool | Closure $condition = true): static
    {
        $this->isSticky = $condition;

        return $this;
    }

    public function isSticky(): bool
    {
        return (bool) $this->evaluate($this->isSticky);
    }

    public function toEmbeddedHtml(): string
    {
        $actions = $this->getChildSchema()->getComponents();
        $alignment = $this->getAlignment();
        $isFullWidth = $this->isFullWidth();
        $isSticky = $this->isSticky();
        $verticalAlignment = $this->getVerticalAlignment();

        if (! $verticalAlignment instanceof VerticalAlignment) {
            $verticalAlignment = filled($verticalAlignment) ? (VerticalAlignment::tryFrom($verticalAlignment) ?? $verticalAlignment) : null;
        }

        if (! $alignment instanceof Alignment) {
            $alignment = filled($alignment) ? (Alignment::tryFrom($alignment) ?? $alignment) : null;
        }

        // Filter to visible actions
        $visibleActions = array_filter(
            $actions,
            fn ($action): bool => $action->isVisible(),
        );

        $outerAttributes = (new FilamentComponentAttributeBag)
            ->merge([
                'id' => $this->getId(),
            ], escape: false)
            ->merge($this->getExtraAttributes(), escape: false)
            ->class([
                'fi-sc-actions',
                ($verticalAlignment instanceof VerticalAlignment) ? "fi-vertical-align-{$verticalAlignment->value}" : $verticalAlignment,
            ]);

        $label = $this->getLabel();
        $beforeLabelSchema = $this->getChildSchema(static::BEFORE_LABEL_SCHEMA_KEY)?->toHtmlString();
        $afterLabelSchema = $this->getChildSchema(static::AFTER_LABEL_SCHEMA_KEY)?->toHtmlString();
        $aboveContentSchema = $this->getChildSchema(static::ABOVE_CONTENT_SCHEMA_KEY)?->toHtmlString();
        $belowContentSchema = $this->getChildSchema(static::BELOW_CONTENT_SCHEMA_KEY)?->toHtmlString();

        ob_start(); ?>

        <div
            <?php if ($isSticky) { ?>
                x-data="filamentActionsSchemaComponent()"
                x-intersect:enter.half="disableSticky"
                x-intersect:leave="enableSticky"
                x-bind:class="{ 'fi-sticky': isSticky }"
            <?php } ?>
            <?= $outerAttributes->toHtml() ?>
        >
            <?php if (filled($label)) { ?>
                <div class="fi-sc-actions-label-ctn">
                    <?= $beforeLabelSchema?->toHtml() ?>

                    <div class="fi-sc-actions-label">
                        <?= e($label) ?>
                    </div>

                    <?= $afterLabelSchema?->toHtml() ?>
                </div>
            <?php } ?>

            <?= $aboveContentSchema?->toHtml() ?>

            <?php if (filled($visibleActions)) { ?>
                <div
                    <?= (new FilamentComponentAttributeBag)
                        ->merge([
                            'x-bind:style' => $isSticky ? 'isSticky ? `width: ${width}px;` : \'\'' : null,
                        ], escape: false)
                        ->class([
                            'fi-ac',
                            'fi-width-full' => $isFullWidth,
                            (($alignment instanceof Alignment) ? "fi-align-{$alignment->value}" : (is_string($alignment) ? $alignment : null)) => ! $isFullWidth,
                        ])->toHtml() ?>
                >
                    <?php foreach ($visibleActions as $action) { ?>
                        <?= $action->toHtml() ?>
                    <?php } ?>
                </div>
            <?php } ?>

            <?= $belowContentSchema?->toHtml() ?>
        </div>

        <?php return ob_get_clean();
    }
}
