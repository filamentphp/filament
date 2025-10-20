<?php

namespace Filament\Tables\Columns\Layout;

use Closure;
use Filament\Support\Components\Contracts\HasEmbeddedView;
use Filament\Support\Concerns\HasAlignment;
use Filament\Support\Enums\Alignment;
use Filament\Tables\Columns\Column;
use Filament\Tables\Columns\Concerns\HasSpace;

class Stack extends Component implements HasEmbeddedView
{
    use HasAlignment;
    use HasSpace;

    /**
     * @param  array<Column | Component> | Closure  $schema
     */
    final public function __construct(array | Closure $schema)
    {
        $this->schema($schema);
    }

    /**
     * @param  array<Column | Component> | Closure  $schema
     */
    public static function make(array | Closure $schema): static
    {
        $static = app(static::class, ['schema' => $schema]);
        $static->configure();

        return $static;
    }

    public function toEmbeddedHtml(): string
    {
        $alignment = $this->getAlignment() ?? Alignment::Start;

        if (! $alignment instanceof Alignment) {
            $alignment = filled($alignment) ? (Alignment::tryFrom($alignment) ?? $alignment) : null;
        }

        $attributes = $this->getExtraAttributeBag()
            ->class([
                'fi-ta-stack',
                ($alignment instanceof Alignment) ? "fi-align-{$alignment->value}" : $alignment,
                match ($space = $this->getSpace()) {
                    1 => 'fi-gap-sm',
                    2 => 'fi-gap-md',
                    3 => 'fi-gap-lg',
                    default => $space,
                },
            ]);

        $record = $this->getRecord();
        $recordKey = $this->getRecordKey();
        $rowLoop = $this->getRowLoop();

        ob_start(); ?>

        <div <?= $attributes->toHtml() ?>>
            <?php foreach ($this->getComponents() as $component) { ?>
                <?= $component->record($record)->recordKey($recordKey)->rowLoop($rowLoop)->renderInLayout() ?>
            <?php } ?>
        </div>

        <?php return ob_get_clean();
    }
}
