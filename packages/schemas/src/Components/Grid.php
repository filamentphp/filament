<?php

namespace Filament\Schemas\Components;

use Filament\Schemas\Components\Concerns\EntanglesStateWithSingularRelationship;
use Filament\Schemas\Components\Contracts\CanEntangleWithSingularRelationships;
use Filament\Support\Components\Contracts\HasEmbeddedView;
use Filament\Support\View\ComponentAttributeBag as FilamentComponentAttributeBag;

class Grid extends Component implements CanEntangleWithSingularRelationships, HasEmbeddedView
{
    use EntanglesStateWithSingularRelationship;

    protected ?string $publishedViewOverrideCheckPath = 'filament-schemas::components.grid';

    /**
     * @param  array<string, ?int> | int | null  $columns
     */
    final public function __construct(array | int | null $columns)
    {
        $this->columns($columns);
    }

    /**
     * @param  array<string, ?int> | int | null  $columns
     */
    public static function make(array | int | null $columns = 2): static
    {
        $static = app(static::class, ['columns' => $columns]);
        $static->configure();

        return $static;
    }

    public function toEmbeddedHtml(): string
    {
        $attributes = (new FilamentComponentAttributeBag)
            ->merge(['id' => $this->getId()], escape: false)
            ->merge($this->getExtraAttributes(), escape: false);

        $childSchema = $this->getChildSchema();

        ob_start(); ?>

        <div <?= $attributes->toHtml() ?>>
            <?= $childSchema->toHtml() ?>
        </div>

        <?php return ob_get_clean();
    }
}
