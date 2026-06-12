<?php

namespace Filament\QueryBuilder\Constraints;

use Closure;
use Filament\QueryBuilder\Constraints\Operators\IsFilledOperator;
use Filament\QueryBuilder\Constraints\SelectConstraint\Operators\IsOperator;
use Filament\QueryBuilder\View\QueryBuilderIconAlias;
use Filament\Support\Contracts\HasLabel as LabelInterface;
use Filament\Support\Facades\FilamentIcon;
use Filament\Support\Icons\Heroicon;
use Illuminate\Contracts\Support\Arrayable;
use UnitEnum;

class SelectConstraint extends Constraint
{
    use Concerns\CanBeNullable;

    protected bool | Closure $isMultiple = false;

    protected bool | Closure $isNative = true;

    protected bool | Closure $isSearchable = false;

    protected int | Closure $optionsLimit = 50;

    protected ?Closure $getOptionLabelFromRecordUsing = null;

    /**
     * @var array<string | array<string>> | Arrayable | class-string | Closure | null
     */
    protected array | Arrayable | string | Closure | null $options = null;

    protected ?Closure $getOptionLabelUsing = null;

    protected ?Closure $getOptionLabelsUsing = null;

    protected ?Closure $getSearchResultsUsing = null;

    protected function setUp(): void
    {
        parent::setUp();

        $this->icon(FilamentIcon::resolve(QueryBuilderIconAlias::CONSTRAINTS_SELECT) ?? Heroicon::ChevronUpDown);

        $this->operators([
            IsOperator::class,
            IsFilledOperator::make()
                ->visible(fn (): bool => $this->isNullable()),
        ]);
    }

    public function multiple(bool | Closure $condition = true): static
    {
        $this->isMultiple = $condition;

        return $this;
    }

    public function searchable(bool | Closure $condition = true): static
    {
        $this->isSearchable = $condition;

        return $this;
    }

    public function isMultiple(): bool
    {
        return (bool) $this->evaluate($this->isMultiple);
    }

    public function isSearchable(): bool
    {
        return (bool) $this->evaluate($this->isSearchable);
    }

    public function optionsLimit(int | Closure $limit): static
    {
        $this->optionsLimit = $limit;

        return $this;
    }

    public function getOptionsLimit(): int
    {
        return $this->evaluate($this->optionsLimit);
    }

    public function native(bool | Closure $condition = true): static
    {
        $this->isNative = $condition;

        return $this;
    }

    public function isNative(): bool
    {
        return (bool) $this->evaluate($this->isNative);
    }

    public function getOptionLabelFromRecordUsing(?Closure $callback): static
    {
        $this->getOptionLabelFromRecordUsing = $callback;

        return $this;
    }

    public function getOptionLabelFromRecordUsingCallback(): ?Closure
    {
        return $this->getOptionLabelFromRecordUsing;
    }

    /**
     * @param  array<string | array<string>> | Arrayable | class-string | Closure | null  $options
     */
    public function options(array | Arrayable | string | Closure | null $options): static
    {
        $this->options = $options;

        return $this;
    }

    /**
     * @return array<string | array<string>>
     */
    public function getOptions(): array
    {
        $options = $this->evaluate($this->options);

        if (
            is_string($options) &&
            enum_exists($enum = $options)
        ) {
            if (is_a($enum, LabelInterface::class, allow_string: true)) {
                return array_reduce($enum::cases(), function (array $carry, LabelInterface & UnitEnum $case): array {
                    $carry[$case->value ?? $case->name] = $case->getLabel() ?? $case->name;

                    return $carry;
                }, []);
            }

            return array_reduce($enum::cases(), function (array $carry, UnitEnum $case): array {
                $carry[$case->value ?? $case->name] = $case->name;

                return $carry;
            }, []);
        }

        if ($options instanceof Arrayable) {
            $options = $options->toArray();
        }

        return $options ?? [];
    }

    public function getOptionLabelUsing(?Closure $callback): static
    {
        $this->getOptionLabelUsing = $callback;

        return $this;
    }

    public function getOptionLabelsUsing(?Closure $callback): static
    {
        $this->getOptionLabelsUsing = $callback;

        return $this;
    }

    public function getSearchResultsUsing(?Closure $callback): static
    {
        $this->getSearchResultsUsing = $callback;

        return $this;
    }

    public function getOptionLabelUsingCallback(): ?Closure
    {
        return $this->getOptionLabelUsing;
    }

    public function getOptionLabelsUsingCallback(): ?Closure
    {
        return $this->getOptionLabelsUsing;
    }

    public function getSearchResultsUsingCallback(): ?Closure
    {
        return $this->getSearchResultsUsing;
    }
}
