<?php

namespace Filament\Schemas\Components;

use Closure;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Schemas\Components\Concerns\CanBeCollapsed;
use Filament\Schemas\Components\Concerns\CanBeCompact;
use Filament\Schemas\Components\Concerns\CanBeDivided;
use Filament\Schemas\Components\Concerns\CanBeSecondary;
use Filament\Schemas\Components\Concerns\EntanglesStateWithSingularRelationship;
use Filament\Schemas\Components\Concerns\HasDescription;
use Filament\Schemas\Components\Concerns\HasFooterActions;
use Filament\Schemas\Components\Concerns\HasHeaderActions;
use Filament\Schemas\Components\Concerns\HasHeading;
use Filament\Schemas\Components\Concerns\HasLabel;
use Filament\Schemas\Components\Contracts\CanConcealComponents;
use Filament\Schemas\Components\Contracts\CanEntangleWithSingularRelationships;
use Filament\Schemas\Schema;
use Filament\Support\Components\Contracts\HasEmbeddedView;
use Filament\Support\Concerns\CanBeContained;
use Filament\Support\Concerns\HasExtraAlpineAttributes;
use Filament\Support\Concerns\HasIcon;
use Filament\Support\Concerns\HasIconColor;
use Filament\Support\Concerns\HasIconSize;
use Filament\Support\Enums\Alignment;
use Filament\Support\Enums\IconSize;
use Filament\Support\Enums\Size;
use Filament\Support\Icons\Heroicon;
use Filament\Support\View\Components\SectionComponent\IconComponent;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Js;
use Illuminate\Support\Str;
use Illuminate\View\ComponentAttributeBag;

use function Filament\Support\generate_icon_html;
use function Filament\Support\is_slot_empty;

class Section extends Component implements CanConcealComponents, CanEntangleWithSingularRelationships, HasEmbeddedView
{
    use CanBeCollapsed;
    use CanBeCompact;
    use CanBeContained;
    use CanBeDivided;
    use CanBeSecondary;
    use EntanglesStateWithSingularRelationship;
    use HasDescription;
    use HasExtraAlpineAttributes;
    use HasFooterActions;
    use HasHeaderActions;
    use HasHeading;
    use HasIcon;
    use HasIconColor;
    use HasIconSize;
    use HasLabel;

    protected bool | Closure | null $isAside = null;

    protected bool | Closure $isFormBefore = false;

    const AFTER_HEADER_SCHEMA_KEY = 'after_header';

    const FOOTER_SCHEMA_KEY = 'footer';

    const BEFORE_LABEL_SCHEMA_KEY = 'before_label';

    const AFTER_LABEL_SCHEMA_KEY = 'after_label';

    const ABOVE_CONTENT_SCHEMA_KEY = 'above_content';

    const BELOW_CONTENT_SCHEMA_KEY = 'below_content';

    /**
     * @param  string | array<Component | Action | ActionGroup> | Htmlable | Closure | null  $heading
     */
    final public function __construct(string | array | Htmlable | Closure | null $heading = null)
    {
        is_array($heading)
            ? $this->components($heading)
            : $this->heading($heading);
    }

    /**
     * @param  string | array<Component | Action | ActionGroup> | Htmlable | Closure | null  $heading
     */
    public static function make(string | array | Htmlable | Closure | null $heading = null): static
    {
        $static = app(static::class, ['heading' => $heading]);
        $static->configure();

        return $static;
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->key(function (Section $component): ?string {
            $heading = $component->getHeading();

            if (blank($heading)) {
                return null;
            }

            $statePath = $component->getStatePath();

            return Str::slug(Str::transliterate($heading, strict: true)) . '::' . (filled($statePath) ? "{$statePath}::section" : 'section');
        }, isInheritable: false);

        $this->afterHeader(fn (Section $component): array => $component->getHeaderActions());
        $this->footer(function (Section $component): Schema {
            return match ($component->getFooterActionsAlignment()) {
                Alignment::End, Alignment::Right => Schema::end($component->getFooterActions()),
                Alignment::Center, => Schema::center($component->getFooterActions()),
                Alignment::Between, Alignment::Justify => Schema::between($component->getFooterActions()),
                default => Schema::start($component->getFooterActions()),
            };
        });
    }

    public function aside(bool | Closure | null $condition = true): static
    {
        $this->isAside = $condition;

        return $this;
    }

    public function canConcealComponents(): bool
    {
        return $this->isCollapsible();
    }

    public function isAside(): bool
    {
        return (bool) ($this->evaluate($this->isAside) ?? false);
    }

    public function formBefore(bool | Closure $condition = true): static
    {
        $this->isFormBefore = $condition;

        return $this;
    }

    public function isFormBefore(): bool
    {
        return (bool) $this->evaluate($this->isFormBefore);
    }

    /**
     * @param  array<Component | Action | ActionGroup | string | Htmlable> | Schema | Component | Action | ActionGroup | string | Htmlable | Closure | null  $components
     */
    public function afterHeader(array | Schema | Component | Action | ActionGroup | string | Htmlable | Closure | null $components): static
    {
        $this->childComponents($components, static::AFTER_HEADER_SCHEMA_KEY);

        return $this;
    }

    /**
     * @param  array<Component | Action | ActionGroup | string | Htmlable> | Schema | Component | Action | ActionGroup | string | Htmlable | Closure | null  $components
     */
    public function footer(array | Schema | Component | Action | ActionGroup | string | Htmlable | Closure | null $components): static
    {
        $this->childComponents($components, static::FOOTER_SCHEMA_KEY);

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

    protected function makeChildSchema(string $key): Schema
    {
        $schema = parent::makeChildSchema($key);

        if (in_array($key, [static::AFTER_HEADER_SCHEMA_KEY, static::AFTER_LABEL_SCHEMA_KEY])) {
            $schema->alignEnd();
        }

        return $schema;
    }

    protected function configureChildSchema(Schema $schema, string $key): Schema
    {
        $schema = parent::configureChildSchema($schema, $key);

        if (in_array($key, [
            static::AFTER_HEADER_SCHEMA_KEY,
            static::FOOTER_SCHEMA_KEY,
            static::BEFORE_LABEL_SCHEMA_KEY,
            static::AFTER_LABEL_SCHEMA_KEY,
            static::ABOVE_CONTENT_SCHEMA_KEY,
            static::BELOW_CONTENT_SCHEMA_KEY,
        ])) {
            $schema
                ->inline()
                ->embeddedInParentComponent();
        }

        if (in_array($key, [
            static::BEFORE_LABEL_SCHEMA_KEY,
            static::AFTER_LABEL_SCHEMA_KEY,
            static::ABOVE_CONTENT_SCHEMA_KEY,
            static::BELOW_CONTENT_SCHEMA_KEY,
        ])) {
            $schema
                ->modifyActionsUsing(fn (Action $action) => $action
                    ->defaultSize(Size::Small)
                    ->defaultView(Action::LINK_VIEW))
                ->modifyActionGroupsUsing(fn (ActionGroup $actionGroup) => $actionGroup->defaultSize(Size::Small));
        }

        return $schema;
    }

    public function toEmbeddedHtml(): string
    {
        $afterHeader = $this->getChildSchema(static::AFTER_HEADER_SCHEMA_KEY)?->toHtmlString();
        $isAside = $this->isAside();
        $isCollapsed = $this->isCollapsed();
        $isCollapsible = $this->isCollapsible();
        $isCompact = $this->isCompact();
        $isContained = $this->isContained();
        $isDivided = $this->isDivided();
        $isFormBefore = $this->isFormBefore();
        $description = $this->getDescription();
        $footer = $this->getChildSchema(static::FOOTER_SCHEMA_KEY)?->toHtmlString();
        $heading = $this->getHeading();
        $headingTag = $this->getHeadingTag();
        $icon = $this->getIcon();
        $iconColor = $this->getIconColor();
        $iconSize = $this->getIconSize();
        $shouldPersistCollapsed = $this->shouldPersistCollapsed();
        $isSecondary = $this->isSecondary();
        $id = $this->getId();

        if (filled($iconSize) && (! $iconSize instanceof IconSize)) {
            $iconSize = IconSize::tryFrom($iconSize) ?? $iconSize;
        }

        $hasDescription = filled((string) $description);
        $hasHeading = filled($heading);
        $hasIcon = filled($icon);
        $hasHeader = $hasIcon || $hasHeading || $hasDescription || ($isCollapsible && (! $isAside)) || filled($afterHeader?->toHtml());

        // Outer wrapper attributes (from schema section view)
        $outerAttributes = (new ComponentAttributeBag)
            ->merge(['id' => $id], escape: false)
            ->merge($this->getExtraAttributes(), escape: false)
            ->merge($this->getExtraAlpineAttributes(), escape: false)
            ->class(['fi-sc-section']);

        // Inner section attributes
        $sectionAttributes = (new ComponentAttributeBag)
            ->class([
                'fi-section',
                'fi-section-not-contained' => ! $isContained,
                'fi-section-has-content-before' => $isFormBefore,
                'fi-section-has-header' => $hasHeader,
                'fi-aside' => $isAside,
                'fi-compact' => $isCompact,
                'fi-collapsible' => $isCollapsible && (! $isAside),
                'fi-divided' => $isDivided,
                'fi-secondary' => $isSecondary,
            ]);

        $collapsible = $isCollapsible && (! $isAside);
        $collapseId = $id;

        // Render child schema content
        $contentHtml = $this->getChildSchema()?->extraAttributes(['class' => 'fi-section-content'])->toHtml();

        // Label schemas
        $label = $this->getLabel();
        $beforeLabelSchema = $this->getChildSchema(static::BEFORE_LABEL_SCHEMA_KEY)?->toHtmlString();
        $afterLabelSchema = $this->getChildSchema(static::AFTER_LABEL_SCHEMA_KEY)?->toHtmlString();
        $aboveContentSchema = $this->getChildSchema(static::ABOVE_CONTENT_SCHEMA_KEY)?->toHtmlString();
        $belowContentSchema = $this->getChildSchema(static::BELOW_CONTENT_SCHEMA_KEY)?->toHtmlString();

        ob_start(); ?>

        <div <?= $outerAttributes->toHtml() ?>>
            <?php if (filled($label)) { ?>
                <div class="fi-sc-section-label-ctn">
                    <?= $beforeLabelSchema?->toHtml() ?>

                    <div class="fi-sc-section-label">
                        <?= e($label) ?>
                    </div>

                    <?= $afterLabelSchema?->toHtml() ?>
                </div>
            <?php } ?>

            <?= $aboveContentSchema?->toHtml() ?>

            <section
                x-data="{
                    isCollapsed: <?php if ($shouldPersistCollapsed) { ?>$persist(<?= Js::from($isCollapsed) ?>).as(`section-${<?= Js::from($collapseId) ?> ?? $el.id}-isCollapsed`)<?php } else { ?><?= Js::from($isCollapsed) ?><?php } ?>,
                }"
                <?php if ($collapsible) { ?>
                    x-on:collapse-section.window="if ($event.detail.id == <?= Js::from($collapseId) ?> ?? $el.id) isCollapsed = true"
                    x-on:expand="isCollapsed = false"
                    x-on:expand-section.window="if ($event.detail.id == <?= Js::from($collapseId) ?> ?? $el.id) isCollapsed = false"
                    x-on:open-section.window="if ($event.detail.id == <?= Js::from($collapseId) ?> ?? $el.id) isCollapsed = false"
                    x-on:toggle-section.window="if ($event.detail.id == <?= Js::from($collapseId) ?> ?? $el.id) isCollapsed = ! isCollapsed"
                    x-bind:class="isCollapsed && 'fi-collapsed'"
                <?php } ?>
                <?= $sectionAttributes->toHtml() ?>
            >
                <?php if ($hasHeader) { ?>
                    <header
                        <?php if ($collapsible) { ?>
                            x-on:click="isCollapsed = ! isCollapsed"
                        <?php } ?>
                        class="fi-section-header"
                    >
                        <?= generate_icon_html($icon, attributes: (new ComponentAttributeBag)
                            ->color(IconComponent::class, $iconColor), size: $iconSize ?? IconSize::Large)?->toHtml() ?>

                        <?php if ($hasHeading || $hasDescription) { ?>
                            <div class="fi-section-header-text-ctn">
                                <?php if ($hasHeading) { ?>
                                    <<?= $headingTag ?> class="fi-section-header-heading">
                                        <?= e($heading) ?>
                                    </<?= $headingTag ?>>
                                <?php } ?>

                                <?php if ($hasDescription) { ?>
                                    <p class="fi-section-header-description">
                                        <?= e($description) ?>
                                    </p>
                                <?php } ?>
                            </div>
                        <?php } ?>

                        <?php if (filled($afterHeader?->toHtml())) { ?>
                            <div x-on:click.stop class="fi-section-header-after-ctn">
                                <?= $afterHeader ?>
                            </div>
                        <?php } ?>

                        <?php if ($collapsible) { ?>
                            <?= view('filament::components.icon-button', [
                                'color' => 'gray',
                                'icon' => Heroicon::ChevronUp,
                                'iconAlias' => \Filament\Support\View\SupportIconAlias::SECTION_COLLAPSE_BUTTON,
                                'attributes' => (new ComponentAttributeBag)
                                    ->merge(['x-on:click.stop' => 'isCollapsed = ! isCollapsed'], escape: false)
                                    ->class(['fi-section-collapse-btn']),
                            ])->toHtml() ?>
                        <?php } ?>
                    </header>
                <?php } ?>

                <?php if (filled($contentHtml) || filled($footer?->toHtml())) { ?>
                    <div
                        <?php if ($collapsible) { ?>
                            x-bind:aria-expanded="(! isCollapsed).toString()"
                            <?php if ($isCollapsed || $shouldPersistCollapsed) { ?>
                                x-cloak
                            <?php } ?>
                        <?php } ?>
                        class="fi-section-content-ctn"
                    >
                        <?= $contentHtml ?>

                        <?php if (filled($footer?->toHtml())) { ?>
                            <footer class="fi-section-footer">
                                <?= $footer ?>
                            </footer>
                        <?php } ?>
                    </div>
                <?php } ?>
            </section>

            <?= $belowContentSchema?->toHtml() ?>
        </div>

        <?php return ob_get_clean();
    }

    public function getHeadingsCount(): int
    {
        if (blank($this->getHeading())) {
            return 0;
        }

        return 1;
    }
}
