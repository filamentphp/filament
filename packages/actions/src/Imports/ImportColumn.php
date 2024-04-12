<?php

namespace Filament\Actions\Imports;

use Closure;
use Filament\Forms\Components\Select;
use Filament\Support\Components\Component;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class ImportColumn extends Component
{
    protected string $name;

    protected string | Closure | null $label = null;

    protected bool | Closure $isMappingRequired = false;

    protected int | Closure | null $decimalPlaces = null;

    protected bool | Closure $isNumeric = false;

    protected bool | Closure $isBoolean = false;

    protected bool | Closure $isBlankStateIgnored = false;

    protected string | Closure | null $arraySeparator = null;

    /**
     * @var array<string> | Closure
     */
    protected array | Closure $guesses = [];

    protected ?Closure $fillRecordUsing = null;

    protected ?Closure $castStateUsing = null;

    /**
     * @var array<mixed> | Closure
     */
    protected array | Closure $dataValidationRules = [];

    /**
     * @var array<mixed> | Closure
     */
    protected array | Closure $nestedRecursiveDataValidationRules = [];

    protected ?Importer $importer = null;

    protected mixed $example = null;

    protected string | Closure | null $exampleHeader = null;

    protected string | Closure | null $relationship = null;

    /**
     * @var string | array<string> | Closure | null
     */
    protected string | array | Closure | null $resolveRelationshipUsing = null;

    /**
     * @var array<Model>
     */
    protected array $resolvedRelatedRecords = [];

    protected string | Closure | null $validationAttribute = null;

    protected string $evaluationIdentifier = 'column';

    protected string | Htmlable | Closure | null $helperText = null;

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

    public function getSelect(): Select
    {
        return Select::make($this->getName())
            ->label($this->getLabel())
            ->placeholder(__('filament-actions::import.modal.form.columns.placeholder'))
            ->required($this->isMappingRequired())
            ->helperText($this->helperText);
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

    public function example(mixed $example): static
    {
        $this->example = $example;

        return $this;
    }

    public function exampleHeader(string | Closure | null $header): static
    {
        $this->exampleHeader = $header;

        return $this;
    }

    public function requiredMapping(bool | Closure $condition = true): static
    {
        $this->isMappingRequired = $condition;

        return $this;
    }

    public function numeric(bool | Closure $condition = true, int | Closure | null $decimalPlaces = null): static
    {
        $this->isNumeric = $condition;
        $this->decimalPlaces = $decimalPlaces;

        return $this;
    }

    public function helperText(string | Htmlable | Closure | null $text): static
    {
        $this->helperText = $text;

        return $this;
    }

    public function integer(bool | Closure $condition = true): static
    {
        $this->numeric($condition, decimalPlaces: 0);

        return $this;
    }

    public function boolean(bool | Closure $condition = true): static
    {
        $this->isBoolean = $condition;

        return $this;
    }

    public function ignoreBlankState(bool | Closure $condition = true): static
    {
        $this->isBlankStateIgnored = $condition;

        return $this;
    }

    /**
     * @param  array<mixed> | Closure  $rules
     */
    public function rules(array | Closure $rules): static
    {
        $this->dataValidationRules = $rules;

        return $this;
    }

    /**
     * @param  array<mixed> | Closure  $rules
     */
    public function nestedRecursiveRules(array | Closure $rules): static
    {
        $this->nestedRecursiveDataValidationRules = $rules;

        return $this;
    }

    public function array(string | Closure | null $separator = ','): static
    {
        $this->arraySeparator = $separator;

        return $this;
    }

    /**
     * @param  array<string> | Closure  $guesses
     */
    public function guess(array | Closure $guesses): static
    {
        $this->guesses = $guesses;

        return $this;
    }

    public function importer(?Importer $importer): static
    {
        $this->importer = $importer;

        return $this;
    }

    /**
     * @return array<string>
     */
    public function getGuesses(): array
    {
        $guesses = $this->evaluate($this->guesses);

        array_unshift($guesses, $this->getName());

        if (filled($label = $this->getLabel())) {
            array_unshift($guesses, $this->getLabel());
        }

        return array_reduce($guesses, function (array $carry, string $guess): array {
            $carry[] = Str::lower($guess);

            $guess = (string) str($guess)
                ->lower()
                ->replace('-', ' ')
                ->replace('_', ' ');
            $carry[] = $guess;

            if (str($guess)->contains(' ')) {
                $carry[] = (string) str($guess)->replace(' ', '-');
                $carry[] = (string) str($guess)->replace(' ', '_');
            }

            return $carry;
        }, []);
    }

    public function castStateUsing(?Closure $callback): static
    {
        $this->castStateUsing = $callback;

        return $this;
    }

    public function fillRecordUsing(?Closure $callback): static
    {
        $this->fillRecordUsing = $callback;

        return $this;
    }

    /**
     * @param  array<string, mixed>  $options
     */
    public function castState(mixed $state, array $options): mixed
    {
        $originalState = $state;

        if (filled($arraySeparator = $this->getArraySeparator())) {
            $state = collect(explode($arraySeparator, strval($state)))
                ->map(fn (mixed $stateItem): mixed => $this->castStateItem($stateItem))
                ->filter(fn (mixed $stateItem): bool => filled($stateItem))
                ->all();
        } else {
            $state = $this->castStateItem($state);
        }

        if ($this->castStateUsing) {
            return $this->evaluate($this->castStateUsing, [
                'originalState' => $originalState,
                'state' => $state,
                'options' => $options,
            ]);
        }

        return $state;
    }

    public function fillRecord(mixed $state): void
    {
        if ($this->fillRecordUsing) {
            $this->evaluate($this->fillRecordUsing, [
                'state' => $state,
            ]);

            return;
        }

        $relationship = $this->getRelationship();

        if ($relationship) {
            $relationship->associate($this->resolveRelatedRecord($state));

            return;
        }

        $this->getRecord()->{$this->getName()} = $state;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getExampleHeader(): string
    {
        return $this->evaluate($this->exampleHeader) ?? $this->getName();
    }

    /**
     * @return array<mixed>
     */
    public function getDataValidationRules(): array
    {
        $rules = $this->evaluate($this->dataValidationRules);

        if ($this->hasRelationship()) {
            $rules[] = function (string $attribute, mixed $state, Closure $fail) {
                if (blank($state)) {
                    return;
                }

                $record = $this->resolveRelatedRecord($state);

                if ($record) {
                    return;
                }

                $fail(__('validation.exists', ['attribute' => $attribute]));
            };
        }

        return $rules;
    }

    public function resolveRelatedRecord(mixed $state): ?Model
    {
        if (array_key_exists($state, $this->resolvedRelatedRecords)) {
            return $this->resolvedRelatedRecords[$state];
        }

        /** @var BelongsTo $relationship */
        $relationship = Relation::noConstraints(fn () => $this->getRelationship());
        $relationshipQuery = $relationship->getQuery();

        if (blank($this->resolveRelationshipUsing)) {
            return $this->resolvedRelatedRecords[$state] = $relationshipQuery
                ->where($relationship->getQualifiedOwnerKeyName(), $state)
                ->first();
        }

        $resolveUsing = $this->evaluate($this->resolveRelationshipUsing, [
            'state' => $state,
        ]);

        if (blank($resolveUsing)) {
            return $this->resolvedRelatedRecords[$state] = null;
        }

        if ($resolveUsing instanceof Model) {
            return $this->resolvedRelatedRecords[$state] = $resolveUsing;
        }

        if (! (is_array($resolveUsing) || is_string($resolveUsing))) {
            return null;
        }

        $resolveUsing = Arr::wrap($resolveUsing);

        $isFirst = true;

        foreach ($resolveUsing as $columnToResolve) {
            $whereClause = $isFirst ? 'where' : 'orWhere';

            $relationshipQuery->{$whereClause}(
                $columnToResolve,
                $state,
            );

            $isFirst = false;
        }

        return $this->resolvedRelatedRecords[$state] = $relationshipQuery->first();
    }

    /**
     * @return array<mixed>
     */
    public function getNestedRecursiveDataValidationRules(): array
    {
        return $this->evaluate($this->nestedRecursiveDataValidationRules);
    }

    public function isNumeric(): bool
    {
        return (bool) $this->evaluate($this->isNumeric);
    }

    public function isBoolean(): bool
    {
        return (bool) $this->evaluate($this->isBoolean);
    }

    public function isBlankStateIgnored(): bool
    {
        return (bool) $this->evaluate($this->isBlankStateIgnored);
    }

    public function getDecimalPlaces(): ?int
    {
        return $this->evaluate($this->decimalPlaces);
    }

    public function getArraySeparator(): ?string
    {
        return $this->evaluate($this->arraySeparator);
    }

    public function isArray(): bool
    {
        return filled($this->getArraySeparator());
    }

    public function getImporter(): ?Importer
    {
        return $this->importer;
    }

    public function getExample(): mixed
    {
        return $this->evaluate($this->example);
    }

    /**
     * @param  string | array<string> | Closure | null  $resolveUsing
     */
    public function relationship(string | Closure | null $name = null, string | array | Closure | null $resolveUsing = null): static
    {
        $this->relationship = $name ?? $this->getName();
        $this->resolveRelationshipUsing = $resolveUsing;

        return $this;
    }

    public function getRelationship(): ?BelongsTo
    {
        $name = $this->getRelationshipName();

        if (blank($name)) {
            return null;
        }

        return $this->getRecord()->{$name}();
    }

    public function getRelationshipName(): ?string
    {
        return $this->evaluate($this->relationship);
    }

    public function getRecord(): ?Model
    {
        return $this->getImporter()?->getRecord();
    }

    public function isMappingRequired(): bool
    {
        return (bool) $this->evaluate($this->isMappingRequired);
    }

    public function hasRelationship(): bool
    {
        return filled($this->getRelationshipName());
    }

    public function validationAttribute(string | Closure | null $label): static
    {
        $this->validationAttribute = $label;

        return $this;
    }

    public function getValidationAttribute(): string
    {
        return $this->evaluate($this->validationAttribute) ?? Str::lcfirst($this->getLabel());
    }

    public function getLabel(): ?string
    {
        return $this->evaluate($this->label);
    }

    protected function castStateItem(mixed $state): mixed
    {
        if (is_string($state)) {
            $state = trim($state);
        }

        if (blank($state)) {
            return null;
        }

        if ($this->isBoolean()) {
            return $this->castBooleanStateItem($state);
        }

        if ($this->isNumeric()) {
            return $this->castNumericStateItem($state);
        }

        return $state;
    }

    protected function castBooleanStateItem(mixed $state): bool
    {
        // Narrow down the possible values of the state to make comparison easier.
        $state = strtolower(strval($state));

        return match ($state) {
            '1', 'true', 'yes', 'y', 'on' => true,
            '0', 'false', 'no', 'n', 'off' => false,
            default => (bool) $state,
        };
    }

    protected function castNumericStateItem(mixed $state): int | float
    {
        $state = floatval(preg_replace('/[^0-9.-]/', '', $state));

        $decimalPlaces = $this->getDecimalPlaces();

        if ($decimalPlaces === null) {
            return $state;
        }

        return round($state, $decimalPlaces);
    }

    protected function resolveDefaultClosureDependencyForEvaluationByName(string $parameterName): array
    {
        return match ($parameterName) {
            'data' => [$this->getImporter()->getData()],
            'importer' => [$this->getImporter()],
            'options' => [$this->getImporter()->getOptions()],
            'originalData' => [$this->getImporter()->getOriginalData()],
            'record' => [$this->getRecord()],
            default => parent::resolveDefaultClosureDependencyForEvaluationByName($parameterName),
        };
    }

    protected function resolveDefaultClosureDependencyForEvaluationByType(string $parameterType): array
    {
        $record = $this->getRecord();

        return match ($parameterType) {
            Importer::class => [$this->getImporter()],
            Model::class, $record ? $record::class : null => [$record],
            default => parent::resolveDefaultClosureDependencyForEvaluationByType($parameterType),
        };
    }
}
