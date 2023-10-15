<?php

namespace Filament\Tables\Table\Concerns;

use Closure;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
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

    public function getRecords(): Collection | Paginator
    {
        return $this->getLivewire()->getTableRecords();
    }

    public function getRecordKey(Model $record): string
    {
        return $this->getLivewire()->getTableRecordKey($record);
    }

    public function getModel(): string
    {
        return $this->getQuery()->getModel()::class;
    }

    public function allowsDuplicates(): bool
    {
        return (bool) $this->evaluate($this->allowsDuplicates);
    }

    public function getModelLabel(): string
    {
        return $this->evaluate($this->modelLabel) ?? get_model_label($this->getModel());
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

        if (filled($title)) {
            return $title;
        }

        $titleAttribute = $this->getRecordTitleAttribute();

        return $record->getAttributeValue($titleAttribute) ?? $this->getModelLabel();
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
