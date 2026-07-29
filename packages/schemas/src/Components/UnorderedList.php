<?php

namespace Filament\Schemas\Components;

use Closure;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Support\Components\Contracts\HasEmbeddedView;
use Filament\Support\Enums\GridDirection;
use Filament\Support\Enums\TextSize;
use Illuminate\Contracts\Support\Htmlable;

class UnorderedList extends Component implements HasEmbeddedView
{
    protected ?string $publishedViewOverrideCheckPath = 'filament-schemas::components.unordered-list';

    protected TextSize | string | Closure | null $size = null;

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

    protected function setUp(): void
    {
        parent::setUp();

        $this->columns(['sm' => 2]);
    }

    public function size(TextSize | string | Closure | null $size): static
    {
        $this->size = $size;

        return $this;
    }

    public function getSize(): TextSize | string | null
    {
        $size = $this->evaluate($this->size);

        if (blank($size)) {
            return null;
        }

        if (is_string($size)) {
            $size = TextSize::tryFrom($size) ?? $size;
        }

        return $size;
    }

    public function toEmbeddedHtml(): string
    {
        $size = $this->getSize();

        $attributes = $this->getExtraAttributeBag()
            ->grid($this->getColumns(), GridDirection::Column)
            ->class([
                'fi-sc-unordered-list',
                ($size instanceof TextSize) ? "fi-size-{$size->value}" : $size,
            ]);

        ob_start(); ?>

        <ul <?= $attributes->toHtml() ?>>
            <?php foreach ($this->getChildSchema()->getComponents() as $schemaComponent) { ?>
                <li><?= $schemaComponent->toHtml() ?></li>
            <?php } ?>
        </ul>

        <?php return ob_get_clean();
    }
}
