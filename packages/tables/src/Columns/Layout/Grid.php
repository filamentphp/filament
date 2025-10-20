<?php

namespace Filament\Tables\Columns\Layout;

use Filament\Support\Components\Contracts\HasEmbeddedView;

class Grid extends Component implements HasEmbeddedView
{
    /**
     * @var array<string, int | null> | null
     */
    protected ?array $columns = null;

    /**
     * @param  array<string, ?int> | int | null  $columns
     */
    final public function __construct(array | int | null $columns = 2)
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

    /**
     * @param  array<string, ?int> | int | null  $columns
     */
    public function columns(array | int | null $columns = 2): static
    {
        if (! is_array($columns)) {
            $columns = [
                'lg' => $columns,
            ];
        }

        $this->columns = [
            ...($this->columns ?? []),
            ...$columns,
        ];

        return $this;
    }

    /**
     * @return array<string, int | null> | null
     */
    public function getGridColumns(): ?array
    {
        return $this->columns;
    }

    public function toEmbeddedHtml(): string
    {
        $columns = $this->getGridColumns();

        $attributes = $this->getExtraAttributeBag()
            ->grid($columns)
            ->class([
                'fi-ta-grid',
                (($columns['default'] ?? 1) === 1) ? 'fi-gap-sm' : 'fi-gap-lg',
                ($columns['sm'] ?? null) ? (($columns['sm'] === 1) ? 'sm:fi-gap-sm' : 'sm:fi-gap-lg') : null,
                ($columns['md'] ?? null) ? (($columns['md'] === 1) ? 'md:fi-gap-sm' : 'md:fi-gap-lg') : null,
                ($columns['lg'] ?? null) ? (($columns['lg'] === 1) ? 'lg:fi-gap-sm' : 'lg:fi-gap-lg') : null,
                ($columns['xl'] ?? null) ? (($columns['xl'] === 1) ? 'xl:fi-gap-sm' : 'xl:fi-gap-lg') : null,
                ($columns['2xl'] ?? null) ? (($columns['2xl'] === 1) ? '2xl:fi-gap-sm' : '2xl:fi-gap-lg') : null,
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
