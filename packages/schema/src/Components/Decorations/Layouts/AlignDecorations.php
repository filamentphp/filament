<?php

namespace Filament\Schema\Components\Decorations\Layouts;

use Filament\Actions\Action;
use Filament\Schema\Components\Decorations\Decoration;

class AlignDecorations extends Layout
{
    protected string $view = 'filament-schema::components.decorations.layouts.align-decorations';

    /**
     * @var array<Decoration | Action>
     */
    protected array $startDecorations = [];

    /**
     * @var array<Decoration | Action>
     */
    protected array $endDecorations = [];

    /**
     * @param  array<Decoration | Action>  $decorations
     */
    public static function start(array $decorations): static
    {
        $static = app(static::class);
        $static->configure();
        $static->startDecorations($decorations);

        return $static;
    }

    /**
     * @param  array<Decoration | Action>  $decorations
     */
    public static function end(array $decorations): static
    {
        $static = app(static::class);
        $static->configure();
        $static->endDecorations($decorations);

        return $static;
    }

    /**
     * @param  array<Decoration | Action>  $startDecorations
     * @param  array<Decoration | Action>  $endDecorations
     */
    public static function between(array $startDecorations, array $endDecorations): static
    {
        $static = app(static::class);
        $static->configure();
        $static->startDecorations($startDecorations);
        $static->endDecorations($endDecorations);

        return $static;
    }

    public function hasDecorations(): bool
    {
        if (count($this->getStartDecorations())) {
            return true;
        }

        if (count($this->getEndDecorations())) {
            return true;
        }

        return false;
    }

    /**
     * @param  array<Decoration | Action>  $decorations
     */
    public function startDecorations(array $decorations): static
    {
        $this->startDecorations = $decorations;

        return $this;
    }

    /**
     * @param  array<Decoration | Action>  $decorations
     */
    public function endDecorations(array $decorations): static
    {
        $this->endDecorations = $decorations;

        return $this;
    }

    /**
     * @return array<Decoration | Action>
     */
    public function getStartDecorations(): array
    {
        return $this->filterHiddenActionsFromDecorations($this->startDecorations);
    }

    /**
     * @return array<Decoration | Action>
     */
    public function getEndDecorations(): array
    {
        return $this->filterHiddenActionsFromDecorations($this->endDecorations);
    }

    /**
     * @return array<Action>
     */
    public function getActions(): array
    {
        return [
            ...$this->extractActionsFromDecorations($this->getStartDecorations()),
            ...$this->extractActionsFromDecorations($this->getEndDecorations()),
        ];
    }
}
