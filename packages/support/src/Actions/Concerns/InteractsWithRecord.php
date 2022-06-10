<?php

namespace Filament\Support\Actions\Concerns;

use Closure;
use function Filament\Support\get_model_label;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

trait InteractsWithRecord
{
    protected Model | Closure | null $record = null;

    protected string | Closure | null $model = null;

    protected string | Closure | null $modelLabel = null;

    protected string | Closure | null $pluralModelLabel = null;

    protected string | Closure | null $recordTitle = null;

    protected string | Closure | null $recordTitleAttribute = null;

    public function record(Model | Closure | null $record): static
    {
        $this->record = $record;

        return $this;
    }

    public function model(string | Closure | null $model): static
    {
        $this->model = $model;

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

    public function getRecord(): ?Model
    {
        return $this->evaluate(
            $this->record,
            exceptParameters: ['record'],
        );
    }

    public function getRecordTitle(?Model $record = null): string
    {
        $record ??= $this->getRecord();

        $title = $this->evaluate($this->recordTitle, ['record' => $record]);

        if (filled($title)) {
            return $title;
        }

        if (! $record) {
            return $this->getModelLabel();
        }

        $titleAttribute = $this->getRecordTitleAttribute($record);

        if (filled($titleAttribute)) {
            return $record->getAttributeValue($titleAttribute);
        }

        return $record->getKey();
    }

    public function getModel(): ?string
    {
        $model = $this->evaluate($this->model);

        if (filled($model)) {
            return $model;
        }

        $record = $this->getRecord();

        if (! $record) {
            return null;
        }

        return $record::class;
    }

    public function getModelLabel(): ?string
    {
        $label = $this->evaluate($this->modelLabel);

        if (filled($label)) {
            return $label;
        }

        $model = $this->getModel();

        if (! $model) {
            return null;
        }

        return get_model_label($model);
    }

    public function getPluralModelLabel(): ?string
    {
        $label = $this->evaluate($this->pluralModelLabel);

        if (filled($label)) {
            return $label;
        }

        $singularLabel = $this->getModelLabel();

        return filled($singularLabel) ? Str::plural($singularLabel) : null;
    }

    public function getRecordTitleAttribute(?Model $record = null): ?string
    {
        return $this->evaluate($this->recordTitleAttribute, [
            'record' => $record ?? $this->getRecord(),
        ]);
    }

    protected function parseAuthorizationArguments(array $arguments): array
    {
        if ($record = $this->getRecord()) {
            array_unshift($arguments, $record);
        }

        return $arguments;
    }
}
