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
use Filament\Support\View\ComponentAttributeBag as FilamentComponentAttributeBag;
use Filament\Support\View\Components\IconButtonComponent;
use Filament\Support\View\Components\SectionComponent\IconComponent;
use Filament\Support\View\SupportIconAlias;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Js;
use Illuminate\Support\Str;

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

    protected ?string $publishedViewOverrideCheckPath = 'filament-schemas::components.section';

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
        $iconColor = $this->getIconColor() ?? 'gray';
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
        $hasAfterHeader = ! is_slot_empty($afterHeader);
        $hasHeader = $hasIcon || $hasHeading || $hasDescription || ($isCollapsible && (! $isAside)) || $hasAfterHeader;

        // Outer wrapper attributes (from schema section view)
        $outerAttributes = (new FilamentComponentAttributeBag)
            ->merge(['id' => $id], escape: false)
            ->merge($this->getExtraAttributes(), escape: false)
            ->merge($this->getExtraAlpineAttributes(), escape: false)
            ->class(['fi-sc-section']);

        // Inner section attributes
        $sectionAttributes = (new FilamentComponentAttributeBag)
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
        $hasContent = ! is_slot_empty(filled($contentHtml) ? new HtmlString($contentHtml) : null);
        $hasFooter = ! is_slot_empty($footer);

        // The disclosure button uses this to reference the collapsible region via `aria-controls`. Only set
        // when there is both an `$id` and a content region to point at, so the reference never dangles.
        $contentId = (filled($id) && ($hasContent || $hasFooter)) ? "{$id}-content" : null;

        // Label schemas
        $label = $this->getLabel();
        $beforeLabelSchema = $this->getChildSchema(static::BEFORE_LABEL_SCHEMA_KEY)?->toHtmlString();
        $afterLabelSchema = $this->getChildSchema(static::AFTER_LABEL_SCHEMA_KEY)?->toHtmlString();
        $aboveContentSchema = $this->getChildSchema(static::ABOVE_CONTENT_SCHEMA_KEY)?->toHtmlString();
        $belowContentSchema = $this->getChildSchema(static::BELOW_CONTENT_SCHEMA_KEY)?->toHtmlString();

        // Name the `<section>` as a landmark region by referencing its heading (or, failing that, its `label()`),
        // and associate its description. An unnamed `<section>` is not exposed as a `region` by assistive tech, so
        // this promotes it to a navigable landmark. Every id is guarded so a reference is never left dangling. The
        // `-heading`/`-description`/`-label` suffixes are disjoint from the disclosure `-content` id above.
        $headingId = (filled($id) && $hasHeading) ? "{$id}-heading" : null;
        $descriptionId = (filled($id) && $hasDescription) ? "{$id}-description" : null;
        $labelId = (filled($id) && filled($label) && (! $hasHeading)) ? "{$id}-label" : null;

        ob_start(); ?>

        <div <?= $outerAttributes->toHtml() ?>>
            <?php if (filled($label)) { ?>
                <div class="fi-sc-section-label-ctn">
                    <?= $beforeLabelSchema?->toHtml() ?>

                    <div
                        <?php if (filled($labelId)) { ?>id="<?= e($labelId) ?>"<?php } ?>
                        class="fi-sc-section-label"
                    >
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
                    x-on:collapse-section.window="if ($event.detail.id == (<?= Js::from($collapseId) ?> ?? $el.id)) isCollapsed = true"
                    x-on:expand="isCollapsed = false"
                    x-on:expand-section.window="if ($event.detail.id == (<?= Js::from($collapseId) ?> ?? $el.id)) isCollapsed = false"
                    x-on:open-section.window="if ($event.detail.id == (<?= Js::from($collapseId) ?> ?? $el.id)) isCollapsed = false"
                    x-on:toggle-section.window="if ($event.detail.id == (<?= Js::from($collapseId) ?> ?? $el.id)) isCollapsed = ! isCollapsed"
                    <?php if (! $shouldPersistCollapsed) { ?>
                        x-on:reset-schema-component-state.window="if (($event.detail.livewireId === <?= Js::from($this->getLivewire()->getId()) ?>) && ($event.detail.schemaKey === <?= Js::from($this->getRootContainer()->getKey()) ?>)) $nextTick(() => isCollapsed = <?= Js::from($isCollapsed) ?>)"
                    <?php } ?>
                    x-bind:class="isCollapsed && 'fi-collapsed'"
                <?php } ?>
                <?php if (filled($labelledById = $headingId ?? $labelId)) { ?>
                    aria-labelledby="<?= e($labelledById) ?>"
                <?php } ?>
                <?php if (filled($descriptionId)) { ?>
                    aria-describedby="<?= e($descriptionId) ?>"
                <?php } ?>
                <?= $sectionAttributes->toHtml() ?>
            >
                <?php if ($hasHeader) { ?>
                    <header
                        <?php if ($collapsible) { ?>
                            x-on:click="if (! $event.target.closest('.fi-section-header-after-ctn')) isCollapsed = ! isCollapsed"
                        <?php } ?>
                        class="fi-section-header"
                    >
                        <?= generate_icon_html($icon, attributes: (new FilamentComponentAttributeBag)
                            ->color(IconComponent::class, $iconColor), size: $iconSize ?? IconSize::Large)?->toHtml() ?>

                        <?php if ($hasHeading || $hasDescription) { ?>
                            <div class="fi-section-header-text-ctn">
                                <?php if ($hasHeading) { ?>
                                    <<?= $headingTag ?>
                                        <?php if (filled($headingId)) { ?>id="<?= e($headingId) ?>"<?php } ?>
                                        class="fi-section-header-heading"
                                    >
                                        <?= e($heading) ?>
                                    </<?= $headingTag ?>>
                                <?php } ?>

                                <?php if ($hasDescription) { ?>
                                    <p
                                        <?php if (filled($descriptionId)) { ?>id="<?= e($descriptionId) ?>"<?php } ?>
                                        class="fi-section-header-description"
                                    >
                                        <?= e($description) ?>
                                    </p>
                                <?php } ?>
                            </div>
                        <?php } ?>

                        <?php if ($hasAfterHeader) { ?>
                            <div class="fi-section-header-after-ctn">
                                <?= $afterHeader ?>
                            </div>
                        <?php } ?>

                        <?php if ($collapsible) { ?>
                            <?php
                                $collapseButtonAttributes = (new FilamentComponentAttributeBag)
                                    ->merge([
                                        'type' => 'button',
                                        'wire:loading.attr' => 'disabled',
                                        'x-on:click.stop' => 'isCollapsed = ! isCollapsed',
                                        // The button only contains a decorative chevron, so give it an accessible
                                        // name. Static values cover the pre-Alpine/no-JS render; `x-bind` keeps the
                                        // name and expanded state correct as the section is toggled. `aria-expanded`
                                        // belongs on the control, not the region, and `aria-controls` points at it.
                                        'aria-label' => __($isCollapsed ? 'filament-schemas::components.section.actions.expand.label' : 'filament-schemas::components.section.actions.collapse.label'),
                                        'x-bind:aria-label' => 'isCollapsed ? ' . Js::from(__('filament-schemas::components.section.actions.expand.label')) . ' : ' . Js::from(__('filament-schemas::components.section.actions.collapse.label')),
                                        'aria-expanded' => $isCollapsed ? 'false' : 'true',
                                        'x-bind:aria-expanded' => '(! isCollapsed).toString()',
                                        'aria-controls' => $contentId,
                                    ], escape: false)
                                    ->class([
                                        'fi-icon-btn',
                                        'fi-size-md',
                                        'fi-section-collapse-btn',
                                    ])
                                    ->color(IconButtonComponent::class, 'gray');
                            ?>

                            <button <?= $collapseButtonAttributes->toHtml() ?>>
                                <?= generate_icon_html(Heroicon::ChevronUp, alias: SupportIconAlias::SECTION_COLLAPSE_BUTTON)?->toHtml() ?>
                            </button>
                        <?php } ?>
                    </header>
                <?php } ?>

                <?php if ($hasContent || $hasFooter) { ?>
                    <div
                        <?php if (filled($contentId)) { ?>
                            id="<?= e($contentId) ?>"
                        <?php } ?>
                        <?php if ($collapsible && ($isCollapsed || $shouldPersistCollapsed)) { ?>
                            x-cloak
                        <?php } ?>
                        class="fi-section-content-ctn"
                    >
                        <?= $contentHtml ?>

                        <?php if ($hasFooter) { ?>
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
