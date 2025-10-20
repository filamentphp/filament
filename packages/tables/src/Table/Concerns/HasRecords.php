<?php

namespace Filament\Tables\Table\Concerns;

use BackedEnum;
use Closure;
use Filament\Support\Contracts\HasLabel;
use Illuminate\Contracts\Pagination\CursorPaginator;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

use function Filament\Support\get_model_label;
use function Filament\Support\locale_has_pluralization;

trait HasRecords
{
    protected bool | Closure $allowsDuplicates = false;

    protected string | Closure | null $modelLabel = null;

    protected string | Closure | null $pluralModelLabel = null;

    protected string | Closure | null $recordTitle = null;

    protected string | Closure | null $recordTitleAttribute = null;

    protected ?Closure $dataSource = null;

    protected ?Closure $resolveSelectedRecordsUsing = null;

    public function records(?Closure $dataSource): static
    {
        $this->dataSource = $dataSource;

        return $this;
    }

    public function resolveSelectedRecordsUsing(?Closure $callback): static
    {
        $this->resolveSelectedRecordsUsing = $callback;

        return $this;
    }

    public function allowDuplicates(bool | Closure $condition = true): static
    {
        $this->allowsDuplicates = $condition;

        return $this;
    }

    public function modelLabel(string | Closure | null $label): static
    {
        $this->modelLabel = $label;

        return $this;
    }

    public function pluralModelLabel(string | Closure | null $label): static
    {
        $this->pluralModelLabel = $label;

        return $this;
    }

    public function recordTitle(string | Closure | null $title): static
    {
        $this->recordTitle = $title;

        return $this;
    }

    public function recordTitleAttribute(string | Closure | null $attribute): static
    {
        $this->recordTitleAttribute = $attribute;

        return $this;
    }

    public function getRecords(): Collection | Paginator | CursorPaginator
    {
        return $this->getLivewire()->getTableRecords();
    }

    public function hasQuery(): bool
    {
        return ! $this->dataSource;
    }

    public function getDataSource(): ?Closure
    {
        return $this->dataSource;
    }

    public function getResolveSelectedRecordsCallback(): ?Closure
    {
        return $this->resolveSelectedRecordsUsing;
    }

    /**
     * @param  Model | array<string, mixed>  $record
     */
    public function getRecordKey(Model | array $record): string
    {
        return $this->getLivewire()->getTableRecordKey($record);
    }

    /**
     * @return class-string<Model>|null
     */
    public function getModel(): ?string
    {
        $query = $this->getQuery();

        $model = $query?->getModel();

        if (blank($model)) {
            return null;
        }

        return $model::class;
    }

    public function allowsDuplicates(): bool
    {
        return (bool) $this->evaluate($this->allowsDuplicates);
    }

    public function getModelLabel(): string
    {
        $label = $this->evaluate($this->modelLabel);

        if (filled($label)) {
            return $label;
        }

        $model = $this->getModel();

        if (filled($model)) {
            return get_model_label($model);
        }

        return __('filament-tables::table.default_model_label');
    }

    public function getPluralModelLabel(): string
    {
        $label = $this->evaluate($this->pluralModelLabel);

        if (filled($label)) {
            return $label;
        }

        if (locale_has_pluralization()) {
            return Str::plural($this->getModelLabel());
        }

        return $this->getModelLabel();
    }

    public function getRecordTitle(Model $record): string
    {
        $title = $this->evaluate(
            $this->recordTitle,
            namedInjections: [
                'record' => $record,
            ],
            typedInjections: [
                Model::class => $record,
                $record::class => $record,
            ],
        );

        if (filled($titleAttribute = $this->getRecordTitleAttribute())) {
            $title ??= $record->getAttributeValue($titleAttribute);
        }

        $title ??= $this->getModelLabel();

        if ($title instanceof HasLabel) {
            return $title->getLabel();
        }

        if ($title instanceof BackedEnum) {
            return $title->value;
        }

        return $title;
    }

    public function hasCustomRecordTitle(): bool
    {
        return $this->recordTitle !== null;
    }

    public function getRecordTitleAttribute(): ?string
    {
        return $this->evaluate($this->recordTitleAttribute);
    }
}
