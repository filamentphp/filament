<?php

namespace Filament\Actions\Exports;

use Closure;
use Filament\Support\Components\Component;
use Filament\Support\Concerns\CanAggregateRelatedModels;
use Filament\Support\Concerns\HasCellState;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use InvalidArgumentException;

class ExportColumn extends Component
{
    use CanAggregateRelatedModels;
    use Concerns\CanFormatState;
    use HasCellState;

    protected string $name;

    protected string | Closure | null $label = null;

    protected ?Exporter $exporter = null;

    protected bool | Closure $isEnabledByDefault = true;

    protected string $evaluationIdentifier = 'column';

    final public function __construct(string $name)
    {
        $this->name($name);
    }

    public static function make(?string $name = null): static
    {
        $exportColumnClass = static::class;

        $name ??= static::getDefaultName();

        if (blank($name)) {
            throw new InvalidArgumentException("Export column of class [$exportColumnClass] must have a unique name, passed to the [make()] method.");
        }

        $static = app($exportColumnClass, ['name' => $name]);
        $static->configure();

        return $static;
    }

    public static function getDefaultName(): ?string
    {
        return null;
    }

    public function name(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function label(string | Closure | null $label): static
    {
        $this->label = $label;

        return $this;
    }

    public function exporter(?Exporter $exporter): static
    {
        $this->exporter = $exporter;

        return $this;
    }

    public function enabledByDefault(bool | Closure $condition): static
    {
        $this->isEnabledByDefault = $condition;

        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getExporter(): ?Exporter
    {
        return $this->exporter;
    }

    public function isEnabledByDefault(): bool
    {
        return (bool) $this->evaluate($this->isEnabledByDefault);
    }

    public function getRecord(): ?Model
    {
        return $this->getExporter()?->getRecord();
    }

    public function getLabel(): ?string
    {
        return $this->evaluate($this->label) ?? (string) str($this->getName())
            ->beforeLast('.')
            ->afterLast('.')
            ->kebab()
            ->replace(['-', '_'], ' ')
            ->ucfirst();
    }

    public function applyRelationshipAggregates(EloquentBuilder $query): EloquentBuilder
    {
        return $query->when(
            filled([$this->getRelationshipToAvg(), $this->getColumnToAvg()]),
            fn ($query) => $query->withAvg($this->getRelationshipToAvg(), $this->getColumnToAvg())
        )->when(
            filled($this->getRelationshipsToCount()),
            fn ($query) => $query->withCount(Arr::wrap($this->getRelationshipsToCount()))
        )->when(
            filled($this->getRelationshipsToExistenceCheck()),
            fn ($query) => $query->withExists(Arr::wrap($this->getRelationshipsToExistenceCheck()))
        )->when(
            filled([$this->getRelationshipToMax(), $this->getColumnToMax()]),
            fn ($query) => $query->withMax($this->getRelationshipToMax(), $this->getColumnToMax())
        )->when(
            filled([$this->getRelationshipToMin(), $this->getColumnToMin()]),
            fn ($query) => $query->withMin($this->getRelationshipToMin(), $this->getColumnToMin())
        )->when(
            filled([$this->getRelationshipToSum(), $this->getColumnToSum()]),
            fn ($query) => $query->withSum($this->getRelationshipToSum(), $this->getColumnToSum())
        );
    }

    public function applyEagerLoading(EloquentBuilder $query): EloquentBuilder
    {
        if (! $this->hasRelationship($query->getModel())) {
            return $query;
        }

        $relationshipName = $this->getRelationshipName($query->getModel());

        if (array_key_exists($relationshipName, $query->getEagerLoads())) {
            return $query;
        }

        return $query->with([$relationshipName]);
    }

    protected function resolveDefaultClosureDependencyForEvaluationByName(string $parameterName): array
    {
        return match ($parameterName) {
            'exporter' => [$this->getExporter()],
            'options' => [$this->getExporter()->getOptions()],
            'record' => [$this->getRecord()],
            default => parent::resolveDefaultClosureDependencyForEvaluationByName($parameterName),
        };
    }

    protected function resolveDefaultClosureDependencyForEvaluationByType(string $parameterType): array
    {
        $record = is_a($parameterType, Model::class, allow_string: true) ? $this->getRecord() : null;

        return match ($parameterType) {
            Exporter::class => [$this->getExporter()],
            Model::class, $record ? $record::class : null => [$record],
            default => parent::resolveDefaultClosureDependencyForEvaluationByType($parameterType),
        };
    }
}
