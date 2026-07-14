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
use Filament\Support\Components\Contracts\HasEmbeddedView;
use Filament\Support\Concerns\HasColor;
use Filament\Support\Concerns\HasIcon;
use Filament\Support\Concerns\HasIconColor;
use Filament\Support\Concerns\HasIconSize;
use Filament\Support\Enums\Alignment;
use Filament\Support\Enums\IconSize;
use Filament\Support\Enums\Size;
use Filament\Support\Facades\FilamentIcon;
use Filament\Support\Icons\Heroicon;
use Filament\Support\View\ComponentAttributeBag as FilamentComponentAttributeBag;
use Filament\Support\View\Components\CalloutComponent;
use Filament\Support\View\Components\CalloutComponent\IconComponent;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Arr;

use function Filament\Support\generate_icon_html;

class Callout extends Component implements HasEmbeddedView
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

    protected ?string $publishedViewOverrideCheckPath = 'filament-schemas::components.callout';

    public const FOOTER_SCHEMA_KEY = 'footer';

    public const CONTROLS_SCHEMA_KEY = 'controls';

    protected bool $hasColor = false;

    protected string | Closure | null $status = null;

    /**
     * @var array<Action | Closure>
     */
    protected array $controlActions = [];

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

        $this->controls(function (Callout $component): Schema {
            return Schema::start($component->getControlActions());
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

    /**
     * @param  array<Action | Closure>  $actions
     */
    public function controlActions(array $actions): static
    {
        $this->controlActions = [
            ...$this->controlActions,
            ...$actions,
        ];

        return $this;
    }

    /**
     * @return array<Action>
     */
    public function getControlActions(): array
    {
        $actions = [];

        foreach ($this->controlActions as $controlAction) {
            foreach (Arr::wrap($this->evaluate($controlAction)) as $action) {
                $actions[] = $this->prepareAction($action);
            }
        }

        return $actions;
    }

    /**
     * @param  array<Component | Action | ActionGroup | string | Htmlable> | Schema | Component | Action | ActionGroup | string | Htmlable | Closure | null  $components
     */
    public function controls(array | Schema | Component | Action | ActionGroup | string | Htmlable | Closure | null $components): static
    {
        $this->childComponents($components, static::CONTROLS_SCHEMA_KEY);

        return $this;
    }

    public function toEmbeddedHtml(): string
    {
        $controls = $this->getChildSchema(static::CONTROLS_SCHEMA_KEY)?->toHtmlString();
        $footer = $this->getChildSchema(static::FOOTER_SCHEMA_KEY)?->toHtmlString();
        $color = $this->getColor() ?? 'gray';
        $description = $this->getDescription();
        $heading = $this->getHeading();
        $icon = $this->getIcon();
        $iconColor = $this->getIconColor() ?? $color;
        $iconSize = $this->getIconSize() ?? IconSize::Large;

        if (filled($iconSize) && (! $iconSize instanceof IconSize)) {
            $iconSize = IconSize::tryFrom($iconSize) ?? $iconSize;
        }

        $hasDescription = filled((string) $description);
        $hasHeading = filled($heading);
        $hasFooter = filled($footer?->toHtml());
        $hasIcon = filled($icon);
        $hasControls = filled($controls?->toHtml());

        $status = $this->getStatus();
        $statusLabel = filled($status)
            ? __("filament-schemas::components.callout.statuses.{$status}")
            : null;

        // `__()` returns the key itself when there is no translation (e.g. a custom status), so drop it
        // rather than announcing a raw translation key to screen readers.
        if (filled($statusLabel) && str_contains($statusLabel, 'filament-schemas::')) {
            $statusLabel = null;
        }

        $attributes = $this->getExtraAttributeBag()
            ->color(CalloutComponent::class, $color)
            ->class(['fi-sc-callout', 'fi-callout']);

        ob_start(); ?>

        <div <?= $attributes->toHtml() ?>>
            <?php if ($hasIcon) { ?>
                <?= generate_icon_html(
                    $icon,
                    attributes: (new FilamentComponentAttributeBag(['aria-hidden' => 'true']))
                        ->color(IconComponent::class, $iconColor)
                        ->class(['fi-callout-icon']),
                    size: $iconSize,
                )?->toHtml() ?>
            <?php } ?>

            <?php if ($hasHeading || $hasDescription || $hasFooter) { ?>
                <div class="fi-callout-main">
                    <?php if ($hasHeading || $hasDescription) { ?>
                        <div class="fi-callout-text">
                            <?php if ($hasHeading) { ?>
                                <h4 class="fi-callout-heading"><?php if (filled($statusLabel)) { ?><span class="fi-sr-only"><?= e($statusLabel) ?> </span><?php } ?><?= e($heading) ?></h4>
                            <?php } ?>

                            <?php if ($hasDescription) { ?>
                                <p class="fi-callout-description"><?php if ((! $hasHeading) && filled($statusLabel)) { ?><span class="fi-sr-only"><?= e($statusLabel) ?> </span><?php } ?><?= e($description) ?></p>
                            <?php } ?>
                        </div>
                    <?php } ?>

                    <?php if ($hasFooter) { ?>
                        <div class="fi-callout-footer"><?= $footer ?></div>
                    <?php } ?>
                </div>
            <?php } ?>

            <?php if ($hasControls) { ?>
                <div class="fi-callout-controls"><?= $controls ?></div>
            <?php } ?>
        </div>

        <?php return ob_get_clean();
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

        if ($key === static::CONTROLS_SCHEMA_KEY) {
            $schema
                ->inline()
                ->embeddedInParentComponent()
                ->modifyActionsUsing(fn (Action $action) => $action
                    ->defaultSize(Size::Small))
                ->modifyActionGroupsUsing(fn (ActionGroup $actionGroup) => $actionGroup->defaultSize(Size::Small));
        }

        return $schema;
    }
}
