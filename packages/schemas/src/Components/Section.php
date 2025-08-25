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
use Filament\Support\Concerns\CanBeContained;
use Filament\Support\Concerns\HasExtraAlpineAttributes;
use Filament\Support\Concerns\HasIcon;
use Filament\Support\Concerns\HasIconColor;
use Filament\Support\Concerns\HasIconSize;
use Filament\Support\Enums\Alignment;
use Filament\Support\Enums\Size;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Str;

class Section extends Component implements CanConcealComponents, CanEntangleWithSingularRelationships
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

    /**
     * @var view-string
     */
    protected string $view = 'filament-schemas::components.section';

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
            $heading = $this->getHeading();

            if (blank($heading)) {
                return null;
            }

            $statePath = $component->getStatePath();

            return Str::slug(Str::transliterate($heading, strict: true)) . '::' . (filled($statePath) ? "{$statePath}::section" : 'section');
        });

        $this->afterHeader(fn (Section $component): array => $component->getHeaderActions());
        $this->footer(function (Section $component): Schema {
            return match ($component->getFooterActionsAlignment()) {
                Alignment::End, Alignment::Right => Schema::end($component->getFooterActions()),
                Alignment::Center, => Schema::center($component->getFooterActions()),
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

    public function getHeadingsCount(): int
    {
        if (blank($this->getHeading())) {
            return 0;
        }

        return 1;
    }
}
