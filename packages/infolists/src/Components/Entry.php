<?php

namespace Filament\Infolists\Components;

use Closure;
use Exception;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Components\Concerns\CanOpenUrl;
use Filament\Schemas\Schema;
use Filament\Support\Concerns\HasAlignment;
use Filament\Support\Concerns\HasPlaceholder;
use Filament\Support\Enums\Alignment;
use Filament\Support\Enums\Size;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Arr;
use Illuminate\View\ComponentSlot;

class Entry extends Component
{
    use CanOpenUrl;
    use Concerns\HasExtraEntryWrapperAttributes;
    use Concerns\HasHelperText;
    use Concerns\HasHint;
    use Concerns\HasName;
    use Concerns\HasTooltip;
    use HasAlignment;
    use HasPlaceholder;

    protected string $viewIdentifier = 'entry';

    const ABOVE_LABEL_SCHEMA_KEY = 'above_label';

    const BELOW_LABEL_SCHEMA_KEY = 'below_label';

    const BEFORE_LABEL_SCHEMA_KEY = 'before_label';

    const AFTER_LABEL_SCHEMA_KEY = 'after_label';

    const ABOVE_CONTENT_SCHEMA_KEY = 'above_content';

    const BELOW_CONTENT_SCHEMA_KEY = 'below_content';

    const BEFORE_CONTENT_SCHEMA_KEY = 'before_content';

    const AFTER_CONTENT_SCHEMA_KEY = 'after_content';

    final public function __construct(string $name)
    {
        $this->name($name);
        $this->statePath($name);
    }

    public static function make(?string $name = null): static
    {
        $entryClass = static::class;

        $name ??= static::getDefaultName();

        if (blank($name)) {
            throw new Exception("Entry of class [$entryClass] must have a unique name, passed to the [make()] method.");
        }

        $static = app($entryClass, ['name' => $name]);
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

    public function getState(): mixed
    {
        return $this->getConstantState();
    }

    public function getLabel(): string | Htmlable | null
    {
        $label = parent::getLabel() ?? (string) str($this->getName())
            ->before('.')
            ->kebab()
            ->replace(['-', '_'], ' ')
            ->ucfirst();

        return (is_string($label) && $this->shouldTranslateLabel) ?
            __($label) :
            $label;
    }

    public function state(mixed $state): static
    {
        $this->constantState($state);

        return $this;
    }

    /**
     * @param  array<Component | Action | ActionGroup | string> | Schema | Component | Action | ActionGroup | string | Closure | null  $components
     */
    public function aboveLabel(array | Schema | Component | Action | ActionGroup | string | Closure | null $components): static
    {
        $this->childComponents($components, static::ABOVE_LABEL_SCHEMA_KEY);

        return $this;
    }

    /**
     * @param  array<Component | Action | ActionGroup | string> | Schema | Component | Action | ActionGroup | string | Closure | null  $components
     */
    public function belowLabel(array | Schema | Component | Action | ActionGroup | string | Closure | null $components): static
    {
        $this->childComponents($components, static::BELOW_LABEL_SCHEMA_KEY);

        return $this;
    }

    /**
     * @param  array<Component | Action | ActionGroup | string> | Schema | Component | Action | ActionGroup | string | Closure | null  $components
     */
    public function beforeLabel(array | Schema | Component | Action | ActionGroup | string | Closure | null $components): static
    {
        $this->childComponents($components, static::BEFORE_LABEL_SCHEMA_KEY);

        return $this;
    }

    /**
     * @param  array<Component | Action | ActionGroup | string> | Schema | Component | Action | ActionGroup | string | Closure | null  $components
     */
    public function afterLabel(array | Schema | Component | Action | ActionGroup | string | Closure | null $components): static
    {
        $this->childComponents($components, static::AFTER_LABEL_SCHEMA_KEY);

        return $this;
    }

    /**
     * @param  array<Component | Action | ActionGroup | string> | Schema | Component | Action | ActionGroup | string | Closure | null  $components
     */
    public function aboveContent(array | Schema | Component | Action | ActionGroup | string | Closure | null $components): static
    {
        $this->childComponents($components, static::ABOVE_CONTENT_SCHEMA_KEY);

        return $this;
    }

    /**
     * @param  array<Component | Action | ActionGroup | string> | Schema | Component | Action | ActionGroup | string | Closure | null  $components
     */
    public function belowContent(array | Schema | Component | Action | ActionGroup | string | Closure | null $components): static
    {
        $this->childComponents($components, static::BELOW_CONTENT_SCHEMA_KEY);

        return $this;
    }

    /**
     * @param  array<Component | Action | ActionGroup | string> | Schema | Component | Action | ActionGroup | string | Closure | null  $components
     */
    public function beforeContent(array | Schema | Component | Action | ActionGroup | string | Closure | null $components): static
    {
        $this->childComponents($components, static::BEFORE_CONTENT_SCHEMA_KEY);

        return $this;
    }

    /**
     * @param  array<Component | Action | ActionGroup | string> | Schema | Component | Action | ActionGroup | string | Closure | null  $components
     */
    public function afterContent(array | Schema | Component | Action | ActionGroup | string | Closure | null $components): static
    {
        $this->childComponents($components, static::AFTER_CONTENT_SCHEMA_KEY);

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
        ])) {
            $schema
                ->inline()
                ->embeddedInParentComponent()
                ->configureActionsUsing(fn (Action $action) => $action
                    ->defaultSize(Size::Small)
                    ->defaultView(Action::LINK_VIEW))
                ->configureActionGroupsUsing(fn (ActionGroup $actionGroup) => $actionGroup->defaultSize(Size::Small));
        }

        return $schema;
    }

    public function wrapEmbeddedHtml(string $html): string
    {
        $view = $this->getEntryWrapperAbsoluteView();

        if ($view !== 'filament-infolists::components.entry-wrapper') {
            return view($this->getEntryWrapperAbsoluteView(), [
                'entry' => $this,
                'slot' => new ComponentSlot($html),
            ])->toHtml();
        }

        $alignment = $this->getAlignment();
        $label = $this->getLabel();
        $labelSrOnly = $this->isLabelHidden();

        if (! $alignment instanceof Alignment) {
            $alignment = filled($alignment) ? (Alignment::tryFrom($alignment) ?? $alignment) : null;
        }

        $beforeLabelContainer = $this->getChildSchema($this::BEFORE_LABEL_SCHEMA_KEY)?->toHtmlString();
        $afterLabelContainer = $this->getChildSchema($this::AFTER_LABEL_SCHEMA_KEY)?->toHtmlString();
        $beforeContentContainer = $this->getChildSchema($this::BEFORE_CONTENT_SCHEMA_KEY)?->toHtmlString();
        $afterContentContainer = $this->getChildSchema($this::AFTER_CONTENT_SCHEMA_KEY)?->toHtmlString();

        $attributes = $this->getExtraEntryWrapperAttributesBag()
            ->class([
                'fi-in-entry',
                'fi-in-entry-has-inline-label' => $this->hasInlineLabel(),
            ]);

        ob_start(); ?>

        <div <?= $attributes->toHtml() ?>>
            <?php if ($label && $labelSrOnly) { ?>
                <dt class="fi-in-entry-label fi-hidden">
                    <?= e($label) ?>
                </dt>
            <?php } ?>

            <div class="fi-in-entry-label-col">
                <?= $this->getChildSchema($this::ABOVE_LABEL_SCHEMA_KEY)?->toHtml() ?>

                <?php if (($label && (! $labelSrOnly)) || $beforeLabelContainer || $afterLabelContainer) { ?>
                    <div class="fi-in-entry-label-ctn">
                        <?= $beforeLabelContainer?->toHtml() ?>

                        <?php if ($label && (! $labelSrOnly)) { ?>
                            <dt class="fi-in-entry-label">
                                <?= e($label) ?>
                            </dt>
                        <?php } ?>

                        <?= $afterLabelContainer?->toHtml() ?>
                    </div>
                <?php } ?>

                <?= $this->getChildSchema($this::BELOW_LABEL_SCHEMA_KEY)?->toHtml() ?>
            </div>

            <div class="fi-in-entry-content-col">
                <?= $this->getChildSchema($this::ABOVE_CONTENT_SCHEMA_KEY)?->toHtml() ?>

                <div class="fi-in-entry-content-ctn">
                    <?= $beforeContentContainer?->toHtml() ?>

                    <dd class="<?= Arr::toCssClasses([
                        'fi-in-entry-content',
                        (($alignment instanceof Alignment) ? "fi-align-{$alignment->value}" : (is_string($alignment) ? $alignment : '')),
                    ])?>">
                        <?= $html ?>
                    </dd>

                    <?= $afterContentContainer?->toHtml() ?>
                </div>

                <?= $this->getChildSchema($this::BELOW_CONTENT_SCHEMA_KEY)?->toHtml() ?>
            </div>
        </div>

        <?php return ob_get_clean();
    }
}
