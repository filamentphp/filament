<?php

namespace Filament\Schema\Components\Decorations\Layouts;

use Filament\Actions\Action;
use Filament\Schema\Components\Decorations\Decoration;
use Filament\Support\Concerns\HasAlignment;
use Filament\Support\Enums\Alignment;

class AlignDecorations extends Layout
{
    use HasAlignment;

    protected string $view = 'filament-schema::components.decorations.layouts.align-decorations';

    /**
     * @var array<Decoration | Action | array<Decoration | Action>>
     */
    protected array $decorations = [];

    /**
     * @param  array<Decoration | Action | array<Decoration | Action>>  $decorations
     */
    public static function start(array $decorations): static
    {
        $static = app(static::class);
        $static->configure();
        $static->decorations($decorations);
        $static->alignment(Alignment::Start);

        return $static;
    }

    /**
     * @param  array<Decoration | Action | array<Decoration | Action>>  $decorations
     */
    public static function end(array $decorations): static
    {
        $static = app(static::class);
        $static->configure();
        $static->decorations($decorations);
        $static->alignment(Alignment::End);

        return $static;
    }

    /**
     * @param  array<Decoration | Action | array<Decoration | Action>>  $decorations
     */
    public static function between(array $decorations): static
    {
        $static = app(static::class);
        $static->configure();
        $static->decorations($decorations);
        $static->alignment(Alignment::Between);

        return $static;
    }

    public function hasDecorations(): bool
    {
        return count($this->getDecorations()) > 0;
    }

    /**
     * @param  array<Decoration | Action | array<Decoration | Action>>  $decorations
     */
    public function decorations(array $decorations): static
    {
        $this->decorations = $decorations;

        return $this;
    }

    /**
     * @return array<Decoration | Action | array<Decoration | Action>>
     */
    public function getDecorations(): array
    {
        return $this->filterHiddenActionsFromDecorations($this->decorations);
    }

    /**
     * @return array<Action>
     */
    public function getActions(): array
    {
        return $this->extractActionsFromDecorations($this->getDecorations());
    }
}
