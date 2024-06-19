<?php

namespace Filament\Schema\Components\Decorations\Layouts;

use Filament\Actions\Action;
use Filament\Schema\Components\Decorations\Decoration;
use Filament\Support\Concerns\HasAlignment;
use Filament\Support\Enums\Alignment;

class AlignDecorations extends DecorationsLayout
{
    use HasAlignment;

    protected string $view = 'filament-schema::components.decorations.layouts.align-decorations';

    /**
     * @param  array<Decoration | Action | array<Decoration | Action>>  $decorations
     */
    public function __construct(
        protected array $decorations,
        Alignment $alignment,
    ) {
        $this->alignment($alignment);
    }

    /**
     * @param  array<Decoration | Action | array<Decoration | Action>>  $decorations
     */
    public static function make(
        Alignment $alignment,
        array $decorations = [],
    ): static {
        $static = app(static::class, [
            'alignment' => $alignment,
            'decorations' => $decorations,
        ]);
        $static->configure();

        return $static;
    }

    /**
     * @param  array<Decoration | Action | array<Decoration | Action>>  $decorations
     */
    public static function start(array $decorations): static
    {
        return static::make(Alignment::Start, $decorations);
    }

    /**
     * @param  array<Decoration | Action | array<Decoration | Action>>  $decorations
     */
    public static function end(array $decorations): static
    {
        return static::make(Alignment::End, $decorations);
    }

    /**
     * @param  array<Decoration | Action | array<Decoration | Action>>  $decorations
     */
    public static function between(array $decorations): static
    {
        return static::make(Alignment::Between, $decorations);
    }

    public function hasDecorations(): bool
    {
        return count($this->getDecorations()) > 0;
    }

    /**
     * @return array<Decoration | Action | array<Decoration | Action>>
     */
    public function getDecorations(): array
    {
        return $this->prepareDecorations($this->decorations);
    }

    /**
     * @return array<string, Action>
     */
    public function getActions(): array
    {
        return $this->extractActionsFromDecorations($this->getDecorations());
    }
}
