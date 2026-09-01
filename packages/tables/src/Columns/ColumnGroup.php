<?php

namespace Filament\Tables\Columns;

use Closure;
use Filament\Support\Components\Component;
use Filament\Support\Concerns\CanWrapHeader;
use Filament\Support\Concerns\HasAlignment;
use Illuminate\Contracts\Support\Htmlable;

class ColumnGroup extends Component
{
    use CanWrapHeader;
    use Concerns\BelongsToTable;
    use Concerns\CanBeHiddenResponsively;
    use Concerns\HasExtraHeaderAttributes;
    use HasAlignment;

    protected string $evaluationIdentifier = 'group';

    protected string | Htmlable | Closure $label;

    protected bool $shouldTranslateLabel = false;

    /**
     * @var array<Column> | Closure
     */
    protected array | Closure $columns = [];

    /**
     * @param  array<Column>  $columns
     */
    final public function __construct(string | Htmlable | Closure $label, array | Closure $columns = [])
    {
        $this->label($label);
        $this->columns($columns);
    }

    /**
     * @param  array<Column>  $columns
     */
    public static function make(string | Htmlable | Closure $label, array | Closure $columns = []): static
    {
        $static = app(static::class, ['label' => $label, 'columns' => $columns]);
        $static->configure();

        return $static;
    }

    public function label(string | Htmlable | Closure $label): static
    {
        $this->label = $label;

        return $this;
    }

    public function translateLabel(bool $shouldTranslateLabel = true): static
    {
        $this->shouldTranslateLabel = $shouldTranslateLabel;

        return $this;
    }

    public function getLabel(): string | Htmlable
    {
        $label = $this->evaluate($this->label);

        return $this->shouldTranslateLabel ? __($label) : $label;
    }

    /**
     * @param  array<Column> | Closure  $columns
     */
    public function columns(array | Closure $columns): static
    {
        $this->columns = $columns;

        return $this;
    }

    /**
     * @return array<string, Column>
     */
    public function getColumns(): array
    {
        return array_reduce($this->evaluate($this->columns) ?? [], function (array $result, Column $column): array {
            $result[$column->getName()] = $column->group($this);

            return $result;
        }, []);
    }

    /**
     * @return array<string, Column>
     */
    public function getVisibleColumns(): array
    {
        return array_filter(
            $this->getColumns(),
            fn (Column $column): bool => $column->isVisible() && (! $column->isToggledHidden()),
        );
    }
}
