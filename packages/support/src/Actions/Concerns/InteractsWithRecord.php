<?php

namespace Filament\Support\Actions\Concerns;

use Closure;
use function Filament\Support\get_model_label;
use function Filament\Support\locale_has_pluralization;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

trait InteractsWithRecord
{
    protected Model | Closure | null $record = null;

    protected string | Closure | null $model = null;

    protected string | Closure | null $modelLabel = null;

    protected string | Closure | null $pluralModelLabel = null;

    protected string | Closure | null $recordTitle = null;

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

    public function getRecord(): ?Model
    {
        return $this->evaluate(
            $this->record,
            exceptParameters: ['record'],
        );
    }

    public function getRecordTitle(?Model $record = null): ?string
    {
        return $this->getCustomRecordTitle($record) ?? $this->getModelLabel();
    }

    public function getCustomRecordTitle(?Model $record = null): ?string
    {
        return $this->evaluate($this->recordTitle, ['record' => $record ?? $this->getRecord()]);
    }

    public function getModel(): ?string
    {
        $model = $this->getCustomModel();

        if (filled($model)) {
            return $model;
        }

        $record = $this->getRecord();

        if (! $record) {
            return null;
        }

        return $record::class;
    }

    public function getCustomModel(): ?string
    {
        return $this->evaluate($this->model);
    }

    public function getModelLabel(): ?string
    {
        $label = $this->getCustomModelLabel();

        if (filled($label)) {
            return $label;
        }

        $model = $this->getModel();

        if (! $model) {
            return null;
        }

        return get_model_label($model);
    }

    public function getCustomModelLabel(): ?string
    {
        return $this->evaluate($this->modelLabel);
    }

    public function getPluralModelLabel(): ?string
    {
        $label = $this->getCustomPluralModelLabel();

        if (filled($label)) {
            return $label;
        }

        $singularLabel = $this->getModelLabel();

        if (blank($singularLabel)) {
            return null;
        }

        if (locale_has_pluralization()) {
            return Str::plural($singularLabel);
        }

        return $singularLabel;
    }

    public function getCustomPluralModelLabel(): ?string
    {
        return $this->evaluate($this->pluralModelLabel);
    }

    protected function parseAuthorizationArguments(array $arguments): array
    {
        if ($record = $this->getRecord()) {
            array_unshift($arguments, $record);
        }

        return $arguments;
    }
}
