<?php

namespace Filament\Schemas\Components;

use Closure;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Schemas\Components\Concerns\EntanglesStateWithSingularRelationship;
use Filament\Schemas\Components\Contracts\CanEntangleWithSingularRelationships;
use Filament\Support\Components\Contracts\HasEmbeddedView;
use Filament\Support\View\ComponentAttributeBag as FilamentComponentAttributeBag;
use Illuminate\Contracts\Support\Htmlable;

class Group extends Component implements CanEntangleWithSingularRelationships, HasEmbeddedView
{
    use EntanglesStateWithSingularRelationship;

    protected ?string $publishedViewOverrideCheckPath = 'filament-schemas::components.grid';

    /**
     * @param  array<Component | Action | ActionGroup | string | Htmlable> | Closure  $schema
     */
    final public function __construct(array | Closure $schema = [])
    {
        $this->schema($schema);
    }

    /**
     * @param  array<Component | Action | ActionGroup | string | Htmlable> | Closure  $schema
     */
    public static function make(array | Closure $schema = []): static
    {
        $static = app(static::class, ['schema' => $schema]);
        $static->configure();

        return $static;
    }

    public function toEmbeddedHtml(): string
    {
        $attributes = (new FilamentComponentAttributeBag)
            ->merge(['id' => $this->getId()], escape: false)
            ->merge($this->getExtraAttributes(), escape: false);

        ob_start(); ?>

        <div <?= $attributes->toHtml() ?>>
            <?= $this->getChildSchema()->toHtml() ?>
        </div>

        <?php return ob_get_clean();
    }
}
