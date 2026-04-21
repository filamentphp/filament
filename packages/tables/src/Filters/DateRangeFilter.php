<?php

namespace Filament\Tables\Filters;

use Closure;
use Filament\Forms\Components\DatePicker;
use Filament\Schemas\Components\Grid;
use Illuminate\Database\Eloquent\Builder;

class DateRangeFilter extends BaseFilter
{
    protected string | Closure | null $attribute = null;

    protected string | Closure | null $fromLabel = null;

    protected string | Closure | null $untilLabel = null;

    protected bool | Closure $isInline = false;

    protected bool | Closure $hasUntil = true;

    protected bool | Closure $isNative = true;

    protected ?Closure $modifyFromDatePickerUsing = null;

    protected ?Closure $modifyUntilDatePickerUsing = null;

    protected function setUp(): void
    {
        parent::setUp();

        $this->indicateUsing(function (DateRangeFilter $filter, array $state): array {
            $indicators = [];

            if (filled($state['date_from'] ?? null)) {
                $indicator = $filter->getIndicator();

                if (! $indicator instanceof Indicator) {
                    $indicator = Indicator::make(__('filament-tables::table.filters.date_range.indicator.from', [
                        'date' => $state['date_from'],
                    ]))->removeField('date_from');
                }

                $indicators['date_from'] = $indicator;
            }

            if ($filter->hasUntil() && filled($state['date_until'] ?? null)) {
                $indicator = $filter->getIndicator();

                if (! $indicator instanceof Indicator) {
                    $indicator = Indicator::make(__('filament-tables::table.filters.date_range.indicator.until', [
                        'date' => $state['date_until'],
                    ]))->removeField('date_until');
                }

                $indicators['date_until'] = $indicator;
            }

            return $indicators;
        });
    }

    public function attribute(string | Closure $name): static
    {
        $this->attribute = $name;

        return $this;
    }

    public function fromLabel(string | Closure | null $label): static
    {
        $this->fromLabel = $label;

        return $this;
    }

    public function untilLabel(string | Closure | null $label): static
    {
        $this->untilLabel = $label;

        return $this;
    }

    public function inline(bool | Closure $condition = true): static
    {
        $this->isInline = $condition;

        return $this;
    }

    public function modifyFromDatePickerUsing(?Closure $callback): static
    {
        $this->modifyFromDatePickerUsing = $callback;

        return $this;
    }

    public function modifyUntilDatePickerUsing(?Closure $callback): static
    {
        $this->modifyUntilDatePickerUsing = $callback;

        return $this;
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

    public function withoutUntil(bool | Closure $condition = true): static
    {
        $this->hasUntil = static function () use ($condition): bool {
            return ! (bool) (is_callable($condition) ? $condition() : $condition);
        };

        return $this;
    }

    public function getAttribute(): string
    {
        return $this->evaluate($this->attribute) ?? $this->getName();
    }

    public function getFromLabel(): string
    {
        return $this->evaluate($this->fromLabel)
            ?? __('filament-tables::table.filters.date_range.fields.date_from.label');
    }

    public function getUntilLabel(): string
    {
        return $this->evaluate($this->untilLabel)
            ?? __('filament-tables::table.filters.date_range.fields.date_until.label');
    }

    public function isInline(): bool
    {
        return (bool) $this->evaluate($this->isInline);
    }

    public function hasUntil(): bool
    {
        return (bool) $this->evaluate($this->hasUntil);
    }

    public function apply(Builder $query, array $data = []): Builder
    {
        if ($this->hasQueryModificationCallback()) {
            return parent::apply($query, $data);
        }

        if (filled($data['date_from'] ?? null)) {
            $query->whereDate($this->getAttribute(), '>=', $data['date_from']);
        }

        if ($this->hasUntil() && filled($data['date_until'] ?? null)) {
            $query->whereDate($this->getAttribute(), '<=', $data['date_until']);
        }

        return $query;
    }

    public function getSchemaComponents(): array
    {
        $fromPicker = DatePicker::make('date_from')
            ->label($this->getFromLabel())
            ->native($this->isNative());

        if ($this->modifyFromDatePickerUsing) {
            $fromPicker = $this->evaluate($this->modifyFromDatePickerUsing, [
                'datePicker' => $fromPicker,
            ]) ?? $fromPicker;
        }

        if (! $this->hasUntil()) {
            if ($this->isInline()) {
                return [Grid::make(1)->schema([$fromPicker])];
            }

            return [$fromPicker];
        }

        $untilPicker = DatePicker::make('date_until')
            ->label($this->getUntilLabel())
            ->native($this->isNative());

        if ($this->modifyUntilDatePickerUsing) {
            $untilPicker = $this->evaluate($this->modifyUntilDatePickerUsing, [
                'datePicker' => $untilPicker,
            ]) ?? $untilPicker;
        }

        if ($this->isInline()) {
            return [Grid::make(2)->schema([$fromPicker, $untilPicker])];
        }

        return [$fromPicker, $untilPicker];
    }

    /**
     * @return array<string, mixed>
     */
    public function getResetState(): array
    {
        return [
            'date_from' => null,
            'date_until' => null,
        ];
    }
}
