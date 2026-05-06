<?php

namespace Filament\Forms\Components\Concerns;

use BackedEnum;
use Closure;
use Filament\Actions\Action;
use Filament\Forms\Components\Field;
use Filament\Forms\Components\Placeholder;
use Filament\Schemas\Components\Icon;
use Filament\Schemas\Components\Text;
use Filament\Schemas\Schema;
use Filament\Support\Enums\HintPosition;
use Illuminate\Contracts\Support\Htmlable;

trait HasHint
{
    protected string | Htmlable | Closure | null $hint = null;

    /**
     * @var array<Action | Closure>
     */
    protected array $hintActions = [];

    /**
     * @var string | array<string> | Closure | null
     */
    protected string | array | Closure | null $hintColor = null;

    protected string | BackedEnum | Htmlable | Closure | null $hintIcon = null;

    protected string | Closure | null $hintIconTooltip = null;

    protected HintPosition | string | bool | Closure | null $hintPosition = null;

    protected function setUpHint(): void
    {
        $this->beforeLabel(function (Field | Placeholder $component): array {
            if ($component->getHintPosition() !== HintPosition::BeforeLabel) {
                return [];
            }

            return $this->getHintComponents();
        });

        $this->afterLabel(function (Field | Placeholder $component): array | Schema {
            if ($component->getHintPosition() === HintPosition::BeforeLabel) {
                return [];
            }

            $components = $this->getHintComponents();

            if ($component->getHintPosition() === HintPosition::Inline) {
                return Schema::start($components)
                    ->inline();
            }

            return $components;
        });
    }

    /**
     * @return array<Text | Icon | Action>
     */
    protected function getHintComponents(): array
    {
        $components = [];

        if ($this->hasHint()) {
            $components[] = Text::make(static function (Text $component): string | Htmlable | null {
                /** @var self $parentComponent */
                $parentComponent = $component->getContainer()->getParentComponent();

                return $parentComponent->getHint();
            })
                ->color(static function (Text $component): string | array | null {
                    /** @var self $parentComponent */
                    $parentComponent = $component->getContainer()->getParentComponent();

                    return $parentComponent->getHintColor();
                })
                ->visible(static function (Text $component): bool {
                    /** @var self $parentComponent */
                    $parentComponent = $component->getContainer()->getParentComponent();

                    return filled($parentComponent->getHint());
                });
        }

        if ($this->hasHintIcon()) {
            $components[] = Icon::make(static function (Icon $component): string | BackedEnum | Htmlable | null {
                /** @var self $parentComponent */
                $parentComponent = $component->getContainer()->getParentComponent();

                return $parentComponent->getHintIcon();
            })
                ->tooltip(static function (Icon $component): ?string {
                    /** @var self $parentComponent */
                    $parentComponent = $component->getContainer()->getParentComponent();

                    return $parentComponent->getHintIconTooltip();
                })
                ->visible(static function (Icon $component): bool {
                    /** @var self $parentComponent */
                    $parentComponent = $component->getContainer()->getParentComponent();

                    return filled($parentComponent->getHintIcon());
                })
                ->color(static function (Icon $component): string | array | null {
                    /** @var self $parentComponent */
                    $parentComponent = $component->getContainer()->getParentComponent();

                    return $parentComponent->getHintColor();
                });
        }

        return [
            ...$components,
            ...$this->getHintActions(),
        ];
    }

    public function hint(string | Htmlable | Closure | null $hint): static
    {
        $this->hint = $hint;

        return $this;
    }

    /**
     * @param  string | array<string> | Closure | null  $color
     */
    public function hintColor(string | array | Closure | null $color): static
    {
        $this->hintColor = $color;

        return $this;
    }

    public function hintIcon(string | BackedEnum | Htmlable | Closure | null $icon, string | Closure | null $tooltip = null): static
    {
        $this->hintIcon = $icon;

        if (func_num_args() >= 2) {
            $this->hintIconTooltip($tooltip);
        }

        return $this;
    }

    public function hintPosition(HintPosition | string | Closure | null $position): static
    {
        $this->hintPosition = $position;

        return $this;
    }

    public function hintInline(bool | Closure $condition = true): static
    {
        $this->hintPosition = $condition;

        return $this;
    }

    public function hintIconTooltip(string | Closure | null $tooltip): static
    {
        $this->hintIconTooltip = $tooltip;

        return $this;
    }

    public function hintAction(Action | Closure $action): static
    {
        $this->hintActions([$action]);

        return $this;
    }

    /**
     * @param  array<Action | Closure>  $actions
     */
    public function hintActions(array $actions): static
    {
        $this->hintActions = [
            ...$this->hintActions,
            ...$actions,
        ];

        return $this;
    }

    public function hasHint(): bool
    {
        return filled($this->hint);
    }

    public function getHint(): string | Htmlable | null
    {
        return $this->evaluate($this->hint);
    }

    /**
     * @return string | array<string> | null
     */
    public function getHintColor(): string | array | null
    {
        return $this->evaluate($this->hintColor);
    }

    public function hasHintIcon(): bool
    {
        return filled($this->hintIcon);
    }

    public function getHintIcon(): string | BackedEnum | Htmlable | null
    {
        return $this->evaluate($this->hintIcon);
    }

    public function getHintIconTooltip(): ?string
    {
        return $this->evaluate($this->hintIconTooltip);
    }

    public function getHintPosition(): HintPosition
    {
        $position = $this->evaluate($this->hintPosition);

        if (is_bool($position)) {
            return $position ? HintPosition::Inline : HintPosition::AfterLabel;
        }

        if ($position instanceof HintPosition) {
            return $position;
        }

        return filled($position)
            ? (HintPosition::tryFrom($position) ?? HintPosition::AfterLabel)
            : HintPosition::AfterLabel;
    }

    /**
     * @return array<Action>
     */
    public function getHintActions(): array
    {
        return array_filter(array_map(
            fn (Action | Closure $hintAction): ?Action => $this->evaluate($hintAction),
            $this->hintActions,
        ));
    }
}
