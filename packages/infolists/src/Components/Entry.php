<?php

namespace Filament\Infolists\Components;

use Closure;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Components\Concerns\CanOpenUrl;
use Filament\Schemas\Components\Concerns\HasLabel;
use Filament\Schemas\Components\Concerns\HasName;
use Filament\Schemas\Schema;
use Filament\Support\Concerns\HasAlignment;
use Filament\Support\Concerns\HasPlaceholder;
use Filament\Support\Enums\Alignment;
use Filament\Support\Enums\Size;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\View\ComponentAttributeBag;
use Illuminate\View\ComponentSlot;
use LogicException;

use function Filament\Support\generate_href_html;

class Entry extends Component
{
    use CanOpenUrl;
    use Concerns\HasExtraEntryWrapperAttributes;
    use Concerns\HasHelperText;
    use Concerns\HasHint;
    use Concerns\HasTooltip;
    use HasAlignment;
    use HasLabel {
        getLabel as getBaseLabel;
    }
    use HasName;
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
            throw new LogicException("Entry of class [$entryClass] must have a unique name, passed to the [make()] method.");
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
        if (filled($label = $this->getBaseLabel())) {
            return $label;
        }

        $label = (string) str($this->getName())
            ->afterLast('.')
            ->kebab()
            ->replace(['-', '_'], ' ')
            ->ucfirst();

        return $this->shouldTranslateLabel ? __($label) : $label;
    }

    public function state(mixed $state): static
    {
        $this->constantState($state);

        return $this;
    }

    public function getStateUsing(mixed $callback): static
    {
        $this->state($callback);

        return $this;
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
                ->modifyActionsUsing(fn (Action $action) => $action
                    ->defaultSize(Size::Small)
                    ->defaultView(Action::LINK_VIEW))
                ->modifyActionGroupsUsing(fn (ActionGroup $actionGroup) => $actionGroup->defaultSize(Size::Small));
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

        $hasInlineLabel = $this->hasInlineLabel();
        $alignment = $this->getAlignment();
        $label = $this->getLabel();
        $labelSrOnly = $this->isLabelHidden();
        $action = $this->getAction();
        $url = $this->getUrl();

        $wrapperTag = match (true) {
            filled($url) => 'a',
            filled($action) => 'button',
            default => 'div',
        };

        if (! $alignment instanceof Alignment) {
            $alignment = filled($alignment) ? (Alignment::tryFrom($alignment) ?? $alignment) : null;
        }

        $aboveLabelSchema = $this->getChildSchema($this::ABOVE_LABEL_SCHEMA_KEY)?->toHtmlString();
        $belowLabelSchema = $this->getChildSchema($this::BELOW_LABEL_SCHEMA_KEY)?->toHtmlString();
        $beforeLabelSchema = $this->getChildSchema($this::BEFORE_LABEL_SCHEMA_KEY)?->toHtmlString();
        $afterLabelSchema = $this->getChildSchema($this::AFTER_LABEL_SCHEMA_KEY)?->toHtmlString();
        $beforeContentSchema = $this->getChildSchema($this::BEFORE_CONTENT_SCHEMA_KEY)?->toHtmlString();
        $afterContentSchema = $this->getChildSchema($this::AFTER_CONTENT_SCHEMA_KEY)?->toHtmlString();

        $attributes = $this->getExtraEntryWrapperAttributesBag()
            ->class([
                'fi-in-entry',
                'fi-in-entry-has-inline-label' => $hasInlineLabel,
            ]);

        $contentAttributes = (new ComponentAttributeBag)
            ->merge([
                'type' => ($wrapperTag === 'button') ? 'button' : null,
                'wire:click' => $wireClickAction = $action?->getLivewireClickHandler(),
                'wire:loading.attr' => ($wrapperTag === 'button') ? 'disabled' : null,
                'wire:target' => $wireClickAction,
            ], escape: false)
            ->class([
                'fi-in-entry-content',
                (($alignment instanceof Alignment) ? "fi-align-{$alignment->value}" : (is_string($alignment) ? $alignment : '')),
            ]);

        ob_start(); ?>

        <div <?= $attributes->toHtml() ?>>
            <?php if (filled($label) && $labelSrOnly) { ?>
                <dt class="fi-in-entry-label fi-hidden">
                    <?= e($label) ?>
                </dt>
            <?php } ?>

            <?php if ((filled($label) && (! $labelSrOnly)) || $hasInlineLabel || $aboveLabelSchema || $belowLabelSchema || $beforeLabelSchema || $afterLabelSchema) { ?>
                <div class="fi-in-entry-label-col">
                    <?= $aboveLabelSchema?->toHtml() ?>

                    <?php if ((filled($label) && (! $labelSrOnly)) || $beforeLabelSchema || $afterLabelSchema) { ?>
                        <div class="fi-in-entry-label-ctn">
                            <?= $beforeLabelSchema?->toHtml() ?>

                            <?php if (filled($label) && (! $labelSrOnly)) { ?>
                                <dt class="fi-in-entry-label">
                                    <?= e($label) ?>
                                </dt>
                            <?php } ?>

                            <?= $afterLabelSchema?->toHtml() ?>
                        </div>
                    <?php } ?>

                    <?= $belowLabelSchema?->toHtml() ?>
                </div>
            <?php } ?>

            <div class="fi-in-entry-content-col">
                <?= $this->getChildSchema($this::ABOVE_CONTENT_SCHEMA_KEY)?->toHtml() ?>

                <dd class="fi-in-entry-content-ctn">
                    <?= $beforeContentSchema?->toHtml() ?>

                    <<?= $wrapperTag ?> <?php if ($wrapperTag === 'a') {
                        echo generate_href_html($url, $this->shouldOpenUrlInNewTab())->toHtml();
                    } ?> <?= $contentAttributes->toHtml() ?>>
                        <?= $html ?>
                    </<?= $wrapperTag ?>>

                    <?= $afterContentSchema?->toHtml() ?>
                </dd>

                <?= $this->getChildSchema($this::BELOW_CONTENT_SCHEMA_KEY)?->toHtml() ?>
            </div>
        </div>

        <?php return ob_get_clean();
    }

    public function isDehydrated(): bool
    {
        return false;
    }
}
