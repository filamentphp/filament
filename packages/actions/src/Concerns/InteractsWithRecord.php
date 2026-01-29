<?php

namespace Filament\Actions\Concerns;

use Closure;
use Filament\Actions\Action;
use Filament\Support\ArrayRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use LogicException;

use function Filament\Support\get_model_label;
use function Filament\Support\locale_has_pluralization;

trait InteractsWithRecord
{
    /**
     * @var Model | class-string<Model> | array<string, mixed> | Closure | null
     */
    protected Model | string | array | Closure | null $record = null;

    protected ?Closure $resolveRecordUsing = null;

    /**
     * @var class-string<Model>|Closure|null
     */
    protected string | Closure | null $model = null;

    protected string | Closure | null $modelLabel = null;

    protected string | Closure | null $pluralModelLabel = null;

    protected string | Closure | null $recordTitle = null;

    protected string | Closure | null $recordTitleAttribute = null;

    /**
     * @param  Model | string | array<string, mixed> | Closure | null  $record
     */
    public function record(Model | string | array | Closure | null $record): static
    {
        $this->record = $record;

        return $this;
    }

    public function resolveRecordUsing(?Closure $callback): static
    {
        $this->resolveRecordUsing = $callback;

        return $this;
    }

    /**
     * @param  class-string<Model>|Closure|null  $model
     */
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

    /**
     * @return Model | array<string, mixed> | null
     */
    public function getRecord(bool $withDefault = true): Model | array | null
    {
        $record = $this->evaluate($this->record);

        $isRecordKey = filled($record) && (! $record instanceof Model) && (! is_array($record));

        if ($isRecordKey && (! $this->resolveRecordUsing)) {
            throw new LogicException("Could not resolve record from key [{$record}] without a [resolveRecordUsing()] callback.");
        }

        if ($isRecordKey) {
            $record = $this->evaluate($this->resolveRecordUsing, [
                'key' => $record,
            ]);
        }

        if ($isRecordKey && $record && (! $this->record instanceof Closure)) {
            $this->record = $record;
        }

        $customModel = $this->getCustomModel();

        $ensureCorrectRecordType = function (Model | array | null $record) use ($customModel): Model | array | null {
            if (
                ($record instanceof Model)
                && filled($customModel)
                && (! $record instanceof $customModel)
            ) {
                return null;
            }

            return $record;
        };

        if ($record) {
            return $ensureCorrectRecordType($record);
        }

        if ($record = $this->getGroup()?->getRecord($withDefault)) {
            return $ensureCorrectRecordType($record);
        }

        if (($this instanceof Action) && $record = $this->getSchemaContainer()?->getRecord()) {
            return $ensureCorrectRecordType($record);
        }

        if (($this instanceof Action) && $record = $this->getSchemaComponent()?->getRecord()) {
            return $ensureCorrectRecordType($record);
        }

        return ($withDefault && ($this instanceof Action)) ? $ensureCorrectRecordType($this->getHasActionsLivewire()?->getDefaultActionRecord($this)) : null;
    }

    public function getRecordTitle(?Model $record = null): ?string
    {
        $record ??= $this->getRecord();

        if (
            ($record instanceof Model)
            && filled($customModel = $this->getCustomModel())
            && (! $record instanceof $customModel)
        ) {
            $record = null;
        }

        if ($record) {
            if (filled($title = $this->getCustomRecordTitle($record))) {
                return $title;
            }

            if (filled($title = $this->getTable()?->getRecordTitle($record))) {
                return $title;
            }
        }

        if ($this instanceof Action && filled($title = $this->getHasActionsLivewire()?->getDefaultActionRecordTitle($this))) {
            return $title;
        }

        return $this->getModelLabel();
    }

    /**
     * @param  Model | array<string, mixed>  $record
     */
    public function resolveRecordKey(Model | array $record): ?string
    {
        if (is_array($record)) {
            return strval($record[ArrayRecord::getKeyName()] ?? throw new LogicException('Record arrays must have a unique [' . ArrayRecord::getKeyName() . '] entry for identification.'));
        }

        $key = $record->getKey();

        if (blank($key)) {
            return null;
        }

        return strval($key);
    }

    public function getCustomRecordTitle(?Model $record = null): ?string
    {
        $record ??= $this->getRecord();

        $title = $this->evaluate(
            $this->recordTitle,
            namedInjections: [
                'record' => $record,
            ],
            typedInjections: ($record instanceof Model) ? [
                Model::class => $record,
                $record::class => $record,
            ] : [],
        );

        if (filled($title)) {
            return $title;
        }

        $titleAttribute = $this->getCustomRecordTitleAttribute();

        if (blank($titleAttribute)) {
            return null;
        }

        if (str_contains($titleAttribute, '->')) {
            $titleAttribute = str_replace('->', '.', $titleAttribute);
        }

        return data_get($record, $titleAttribute);
    }

    public function getRecordTitleAttribute(): ?string
    {
        return $this->getCustomRecordTitleAttribute() ?? $this->getTable()?->getRecordTitleAttribute();
    }

    public function getCustomRecordTitleAttribute(): ?string
    {
        return $this->evaluate($this->recordTitleAttribute);
    }

    public function hasCustomRecordTitle(): bool
    {
        return filled($this->recordTitle);
    }

    public function hasCustomRecordTitleAttribute(): bool
    {
        return $this->recordTitleAttribute !== null;
    }

    public function hasRecord(): bool
    {
        return filled($this->record);
    }

    /**
     * @return class-string<Model>|null
     */
    public function getModel(bool $withDefault = true): ?string
    {
        $model = $this->getCustomModel();

        if (filled($model)) {
            return $model;
        }

        $model = $this->getTable()?->getModel();

        if (filled($model)) {
            return $model;
        }

        $record = $this->getRecord($withDefault);

        if ($record instanceof Model) {
            return $record::class;
        }

        if ($record = $this->getGroup()?->getModel($withDefault)) {
            return $record;
        }

        if ($this instanceof Action && $model = $this->getSchemaContainer()?->getModel()) {
            return $model;
        }

        if ($this instanceof Action && $model = $this->getSchemaComponent()?->getModel()) {
            return $model;
        }

        return ($withDefault && ($this instanceof Action)) ? $this->getHasActionsLivewire()?->getDefaultActionModel($this) : null;
    }

    /**
     * @return class-string<Model>|null
     */
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

        $label = $this->getTable()?->getModelLabel();

        if (filled($label)) {
            return $label;
        }

        $model = $this->getModel();

        if (! $model) {
            return $this instanceof Action ? $this->getHasActionsLivewire()?->getDefaultActionModelLabel($this) : null;
        }

        $defaultModel = $this instanceof Action ? $this->getHasActionsLivewire()?->getDefaultActionModel($this) : null;

        if (($this instanceof Action) && ($model === $defaultModel)) {
            return $this->getHasActionsLivewire()?->getDefaultActionModelLabel($this);
        }

        return get_model_label($model);
    }

    public function getTitleCaseModelLabel(): ?string
    {
        $modelLabel = $this->getModelLabel();

        if (blank($modelLabel)) {
            return null;
        }

        return Str::ucwords($modelLabel);
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

        $label = $this->getTable()?->getPluralModelLabel();

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

    public function getTitleCasePluralModelLabel(): ?string
    {
        $pluralModelLabel = $this->getPluralModelLabel();

        if (blank($pluralModelLabel)) {
            return null;
        }

        return Str::ucwords($pluralModelLabel);
    }

    public function getCustomPluralModelLabel(): ?string
    {
        return $this->evaluate($this->pluralModelLabel);
    }
}
