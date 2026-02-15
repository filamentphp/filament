<?php

namespace Filament\Schemas\Components;

use BackedEnum;
use Closure;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Schemas\Components\Concerns\HasDescription;
use Filament\Schemas\Components\Concerns\HasFooterActions;
use Filament\Schemas\Components\Concerns\HasHeading;
use Filament\Schemas\Schema;
use Filament\Schemas\View\SchemaIconAlias;
use Filament\Support\Concerns\HasColor;
use Filament\Support\Concerns\HasIcon;
use Filament\Support\Concerns\HasIconColor;
use Filament\Support\Concerns\HasIconSize;
use Filament\Support\Enums\Alignment;
use Filament\Support\Enums\Size;
use Filament\Support\Facades\FilamentIcon;
use Filament\Support\Icons\Heroicon;
use Illuminate\Contracts\Support\Htmlable;

class Callout extends Component
{
    use HasColor {
        color as baseColor;
        getColor as getBaseColor;
    }
    use HasDescription;
    use HasFooterActions;
    use HasHeading;
    use HasIcon {
        getIcon as getBaseIcon;
    }
    use HasIconColor {
        getIconColor as getBaseIconColor;
    }
    use HasIconSize;

    /**
     * @var view-string
     */
    protected string $view = 'filament-schemas::components.callout';

    public const FOOTER_SCHEMA_KEY = 'footer';

    protected bool $hasColor = false;

    protected string | Closure | null $status = null;

    final public function __construct(string | Htmlable | Closure | null $heading = null)
    {
        $this->heading($heading);
    }

    public static function make(string | Htmlable | Closure | null $heading = null): static
    {
        $static = app(static::class, ['heading' => $heading]);
        $static->configure();

        return $static;
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->footer(function (Callout $component): Schema {
            return match ($component->getFooterActionsAlignment()) {
                Alignment::End, Alignment::Right => Schema::end($component->getFooterActions()),
                Alignment::Center => Schema::center($component->getFooterActions()),
                Alignment::Between, Alignment::Justify => Schema::between($component->getFooterActions()),
                default => Schema::start($component->getFooterActions()),
            };
        });
    }

    /**
     * @param  array<Action | Closure>  $actions
     */
    public function actions(array $actions): static
    {
        $this->footerActions($actions);

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
     * @param  string | array<string> | Closure | null  $color
     */
    public function color(string | array | Closure | null $color): static
    {
        $this->hasColor = true;

        $this->baseColor($color);

        return $this;
    }

    public function status(string | Closure | null $status): static
    {
        $this->status = $status;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->evaluate($this->status);
    }

    public function danger(): static
    {
        return $this->status('danger');
    }

    public function info(): static
    {
        return $this->status('info');
    }

    public function success(): static
    {
        return $this->status('success');
    }

    public function warning(): static
    {
        return $this->status('warning');
    }

    public function getIcon(): string | BackedEnum | Htmlable | null
    {
        return $this->getBaseIcon() ?? match ($this->getStatus()) {
            'danger' => FilamentIcon::resolve(SchemaIconAlias::COMPONENTS_CALLOUT_DANGER) ?? Heroicon::OutlinedXCircle,
            'info' => FilamentIcon::resolve(SchemaIconAlias::COMPONENTS_CALLOUT_INFO) ?? Heroicon::OutlinedInformationCircle,
            'success' => FilamentIcon::resolve(SchemaIconAlias::COMPONENTS_CALLOUT_SUCCESS) ?? Heroicon::OutlinedCheckCircle,
            'warning' => FilamentIcon::resolve(SchemaIconAlias::COMPONENTS_CALLOUT_WARNING) ?? Heroicon::OutlinedExclamationCircle,
            default => null,
        };
    }

    /**
     * @return string | array<string> | null
     */
    public function getIconColor(): string | array | null
    {
        return $this->getBaseIconColor() ?? $this->getStatus();
    }

    /**
     * @return string | array<string> | null
     */
    public function getColor(): string | array | null
    {
        if ($this->hasColor) {
            return $this->getBaseColor();
        }

        return $this->getBaseColor() ?? $this->getStatus();
    }

    /**
     * @return array<Action>
     */
    public function getDefaultActions(): array
    {
        return $this->getFooterActions();
    }

    protected function configureChildSchema(Schema $schema, string $key): Schema
    {
        $schema = parent::configureChildSchema($schema, $key);

        if ($key === static::FOOTER_SCHEMA_KEY) {
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
}
