<?php

namespace Filament\Actions\Exports;

use Closure;
use Filament\Support\Components\Component;
use Filament\Support\Concerns\HasCellState;
use Illuminate\Database\Eloquent\Model;

class ExportColumn extends Component
{
    use Concerns\CanFormatState;
    use HasCellState;

    protected string $name;

    protected string | Closure | null $label = null;

    protected ?Exporter $exporter = null;

    final public function __construct(string $name)
    {
        $this->name($name);
    }

    public static function make(string $name): static
    {
        $static = app(static::class, ['name' => $name]);
        $static->configure();

        return $static;
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

    public function getName(): string
    {
        return $this->name;
    }

    public function getExporter(): ?Exporter
    {
        return $this->exporter;
    }

    public function getRecord(): ?Model
    {
        return $this->getExporter()->getRecord();
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
        $record = $this->getRecord();

        return match ($parameterType) {
            Exporter::class => [$this->getExporter()],
            Model::class, $record ? $record::class : null => [$record],
            default => parent::resolveDefaultClosureDependencyForEvaluationByType($parameterType),
        };
    }
}
