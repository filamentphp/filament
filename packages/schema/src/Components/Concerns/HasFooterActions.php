<?php

namespace Filament\Schema\Components\Concerns;

use Closure;
use Filament\Actions\Action;
use Filament\Schema\Components\Decorations\Layouts\AlignDecorations;
use Filament\Support\Enums\Alignment;

trait HasFooterActions
{
    use HasFooterActionsAlignment;

    /**
     * @var array<Action | Closure>
     */
    protected array $footerActions = [];

    protected function setUpFooterActions(): void
    {
        $this->footer(function (\Filament\Schema\Components\Contracts\HasFooterActions $component): AlignDecorations {
            $actions = $component->getFooterActions();
            $alignment = $component->getFooterActionsAlignment();

            return match ($alignment) {
                Alignment::End, Alignment::Right => AlignDecorations::end($actions),
                default => AlignDecorations::start($actions),
            };
        });
    }

    /**
     * @param  array<Action | Closure>  $actions
     */
    public function footerActions(array $actions): static
    {
        $this->footerActions = [
            ...$this->footerActions,
            ...$actions,
        ];

        return $this;
    }

    /**
     * @return array<Action>
     */
    public function getFooterActions(): array
    {
        return $this->footerActions;
    }
}
