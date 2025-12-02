<?php

namespace Filament\QueryBuilder\Constraints\DateConstraint\Operators;

use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\QueryBuilder\Constraints\DateConstraint;
use Filament\QueryBuilder\Constraints\DateConstraint\DateUnit;
use Filament\QueryBuilder\Constraints\Operators\Operator;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Components\FusedGroup;
use Filament\Schemas\Components\Utilities\Get;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;

class IsBeforeOperator extends Operator
{
    public function getName(): string
    {
        return 'isBefore';
    }

    public function getLabel(): string
    {
        return __(
            $this->isInverse() ?
                'filament-query-builder::query-builder.operators.date.is_before.label.inverse' :
                'filament-query-builder::query-builder.operators.date.is_before.label.direct',
        );
    }

    public function getSummary(): string
    {
        $settings = $this->getSettings();
        $constraint = $this->getConstraint();
        $hasTime = $constraint instanceof DateConstraint && $constraint->hasTime();

        // Check if using relative mode
        if (($settings['mode'] ?? null) === 'relative') {
            $resolvedDate = $this->resolveRelativeDate($settings, $hasTime);
            $parsedDate = Carbon::parse($resolvedDate);
            $isTimeBasedFilter = $this->isTimeBasedFilter($settings);

            return __(
                $this->isInverse() ?
                    'filament-query-builder::query-builder.operators.date.is_before.summary.inverse' :
                    'filament-query-builder::query-builder.operators.date.is_before.summary.direct',
                [
                    'attribute' => $constraint->getAttributeLabel(),
                    'date' => $hasTime && $isTimeBasedFilter ? $parsedDate->toFormattedDayDateString() . ' ' . $parsedDate->format('H:i:s') : $parsedDate->toFormattedDateString(),
                ],
            );
        }

        $parsedDate = Carbon::parse($settings['date']);

        return __(
            $this->isInverse() ?
                'filament-query-builder::query-builder.operators.date.is_before.summary.inverse' :
                'filament-query-builder::query-builder.operators.date.is_before.summary.direct',
            [
                'attribute' => $constraint->getAttributeLabel(),
                'date' => $hasTime ? $parsedDate->toFormattedDayDateString() . ' ' . $parsedDate->format('H:i:s') : $parsedDate->toFormattedDateString(),
            ],
        );
    }

    /**
     * @return array<Component | Action | ActionGroup>
     */
    public function getFormSchema(): array
    {
        $constraint = $this->getConstraint();
        $hasTime = $constraint instanceof DateConstraint && $constraint->hasTime();

        return [
            Select::make('mode')
                ->label(__('filament-query-builder::query-builder.operators.date.form.mode.label'))
                ->selectablePlaceholder(false)
                ->live()
                ->options([
                    'absolute' => __('filament-query-builder::query-builder.operators.date.form.mode.options.absolute'),
                    'relative' => __('filament-query-builder::query-builder.operators.date.form.mode.options.relative'),
                ])
                ->default('absolute'),
            DateTimePicker::make('date')
                ->label(__('filament-query-builder::query-builder.operators.date.form.date.label'))
                ->time($hasTime)
                ->hidden(fn (Get $get): bool => $get('mode') === 'relative')
                ->required(fn (Get $get): bool => $get('mode') !== 'relative'),
            Select::make('preset')
                ->label(__('filament-query-builder::query-builder.operators.date.form.preset.label'))
                ->selectablePlaceholder(false)
                ->live()
                ->options($this->getPresetOptions($hasTime))
                ->default('past_month')
                ->hidden(fn (Get $get): bool => $get('mode') !== 'relative')
                ->required(fn (Get $get): bool => $get('mode') === 'relative'),
            FusedGroup::make([
                Select::make('tense')
                    ->label(__('filament-query-builder::query-builder.operators.date.form.tense.label'))
                    ->selectablePlaceholder(false)
                    ->options([
                        'past' => __('filament-query-builder::query-builder.operators.date.form.tense.options.past'),
                        'future' => __('filament-query-builder::query-builder.operators.date.form.tense.options.future'),
                    ])
                    ->default('past')
                    ->required(),
                TextInput::make('relative_value')
                    ->label(__('filament-query-builder::query-builder.operators.date.form.relative_value.label'))
                    ->numeric()
                    ->minValue(1)
                    ->default(1)
                    ->required(),
                Select::make('relative_unit')
                    ->label(__('filament-query-builder::query-builder.operators.date.form.relative_unit.label'))
                    ->options(
                        collect(DateUnit::cases())
                            ->reject(fn (DateUnit $unit): bool => (! $hasTime) && $unit->isTimeUnit())
                            ->mapWithKeys(fn (DateUnit $unit): array => [$unit->value => $unit->getLabel()])
                            ->all()
                    )
                    ->default(DateUnit::Day->value)
                    ->required(),
            ])
                ->columns(3)
                ->columnSpanFull()
                ->hidden(fn (Get $get): bool => $get('mode') !== 'relative' || $get('preset') !== 'custom'),
        ];
    }

    /**
     * @return array<string, string>
     */
    protected function getPresetOptions(bool $hasTime): array
    {
        $options = [
            'past_decade' => __('filament-query-builder::query-builder.operators.date.presets.past_decade'),
            'past_5_years' => __('filament-query-builder::query-builder.operators.date.presets.past_5_years'),
            'past_2_years' => __('filament-query-builder::query-builder.operators.date.presets.past_2_years'),
            'past_year' => __('filament-query-builder::query-builder.operators.date.presets.past_year'),
            'past_6_months' => __('filament-query-builder::query-builder.operators.date.presets.past_6_months'),
            'past_quarter' => __('filament-query-builder::query-builder.operators.date.presets.past_quarter'),
            'past_month' => __('filament-query-builder::query-builder.operators.date.presets.past_month'),
            'past_2_weeks' => __('filament-query-builder::query-builder.operators.date.presets.past_2_weeks'),
            'past_week' => __('filament-query-builder::query-builder.operators.date.presets.past_week'),
        ];

        if ($hasTime) {
            $options['past_hour'] = __('filament-query-builder::query-builder.operators.date.presets.past_hour');
            $options['past_minute'] = __('filament-query-builder::query-builder.operators.date.presets.past_minute');
        }

        $options += [
            'this_decade' => __('filament-query-builder::query-builder.operators.date.presets.this_decade'),
            'this_year' => __('filament-query-builder::query-builder.operators.date.presets.this_year'),
            'this_quarter' => __('filament-query-builder::query-builder.operators.date.presets.this_quarter'),
            'this_month' => __('filament-query-builder::query-builder.operators.date.presets.this_month'),
            'today' => __('filament-query-builder::query-builder.operators.date.presets.today'),
        ];

        if ($hasTime) {
            $options['this_hour'] = __('filament-query-builder::query-builder.operators.date.presets.this_hour');
            $options['this_minute'] = __('filament-query-builder::query-builder.operators.date.presets.this_minute');
            $options['next_minute'] = __('filament-query-builder::query-builder.operators.date.presets.next_minute');
            $options['next_hour'] = __('filament-query-builder::query-builder.operators.date.presets.next_hour');
        }

        $options += [
            'next_week' => __('filament-query-builder::query-builder.operators.date.presets.next_week'),
            'next_2_weeks' => __('filament-query-builder::query-builder.operators.date.presets.next_2_weeks'),
            'next_month' => __('filament-query-builder::query-builder.operators.date.presets.next_month'),
            'next_quarter' => __('filament-query-builder::query-builder.operators.date.presets.next_quarter'),
            'next_6_months' => __('filament-query-builder::query-builder.operators.date.presets.next_6_months'),
            'next_year' => __('filament-query-builder::query-builder.operators.date.presets.next_year'),
            'next_2_years' => __('filament-query-builder::query-builder.operators.date.presets.next_2_years'),
            'next_5_years' => __('filament-query-builder::query-builder.operators.date.presets.next_5_years'),
            'next_decade' => __('filament-query-builder::query-builder.operators.date.presets.next_decade'),
            'custom' => __('filament-query-builder::query-builder.operators.date.presets.custom'),
        ];

        return $options;
    }

    public function apply(Builder $query, string $qualifiedColumn): Builder
    {
        $settings = $this->getSettings();
        $constraint = $this->getConstraint();
        $hasTime = $constraint instanceof DateConstraint && $constraint->hasTime();

        // Check if using relative mode - only if mode is explicitly set to 'relative'
        if (($settings['mode'] ?? null) === 'relative') {
            $dateTime = $this->resolveRelativeDate($settings, $hasTime);

            if ($hasTime && $this->isTimeBasedFilter($settings)) {
                return $query->where($qualifiedColumn, $this->isInverse() ? '>' : '<=', $dateTime);
            }

            return $query->whereDate($qualifiedColumn, $this->isInverse() ? '>' : '<=', $dateTime);
        }

        return $query->whereDate($qualifiedColumn, $this->isInverse() ? '>' : '<=', $settings['date']);
    }

    /**
     * @param  array<string, mixed>  $settings
     */
    protected function isTimeBasedFilter(array $settings): bool
    {
        $preset = $settings['preset'] ?? 'custom';

        if (in_array($preset, ['this_minute', 'this_hour', 'past_minute', 'past_hour', 'next_minute', 'next_hour'], true)) {
            return true;
        }

        if ($preset === 'custom') {
            $unit = $settings['relative_unit'] ?? DateUnit::Day->value;

            return in_array($unit, [DateUnit::Second->value, DateUnit::Minute->value, DateUnit::Hour->value], true);
        }

        return false;
    }

    /**
     * @param  array<string, mixed>  $settings
     */
    protected function resolveRelativeDate(array $settings, bool $hasTime = false): string
    {
        $preset = $settings['preset'] ?? 'custom';

        return match ($preset) {
            'past_decade' => Carbon::now()->subYears(10)->toDateString(),
            'past_5_years' => Carbon::now()->subYears(5)->toDateString(),
            'past_2_years' => Carbon::now()->subYears(2)->toDateString(),
            'past_year' => Carbon::now()->subYear()->toDateString(),
            'past_6_months' => Carbon::now()->subMonths(6)->toDateString(),
            'past_quarter' => Carbon::now()->subQuarter()->toDateString(),
            'past_month' => Carbon::now()->subMonth()->toDateString(),
            'past_2_weeks' => Carbon::now()->subWeeks(2)->toDateString(),
            'past_week' => Carbon::now()->subWeek()->toDateString(),
            'past_hour' => Carbon::now()->subHour()->toDateTimeString(),
            'past_minute' => Carbon::now()->subMinute()->toDateTimeString(),
            'this_decade' => Carbon::now()->startOfDecade()->toDateString(),
            'this_year' => Carbon::now()->startOfYear()->toDateString(),
            'this_quarter' => Carbon::now()->startOfQuarter()->toDateString(),
            'this_month' => Carbon::now()->startOfMonth()->toDateString(),
            'today' => Carbon::today()->toDateString(),
            'this_hour' => Carbon::now()->startOfHour()->toDateTimeString(),
            'this_minute' => Carbon::now()->startOfMinute()->toDateTimeString(),
            'next_minute' => Carbon::now()->addMinute()->toDateTimeString(),
            'next_hour' => Carbon::now()->addHour()->toDateTimeString(),
            'next_week' => Carbon::now()->addWeek()->toDateString(),
            'next_2_weeks' => Carbon::now()->addWeeks(2)->toDateString(),
            'next_month' => Carbon::now()->addMonth()->toDateString(),
            'next_quarter' => Carbon::now()->addQuarter()->toDateString(),
            'next_6_months' => Carbon::now()->addMonths(6)->toDateString(),
            'next_year' => Carbon::now()->addYear()->toDateString(),
            'next_2_years' => Carbon::now()->addYears(2)->toDateString(),
            'next_5_years' => Carbon::now()->addYears(5)->toDateString(),
            'next_decade' => Carbon::now()->addYears(10)->toDateString(),
            'custom' => $this->resolveCustomRelativeDate($settings, $hasTime),
            default => Carbon::today()->toDateString(),
        };
    }

    /**
     * @param  array<string, mixed>  $settings
     */
    protected function resolveCustomRelativeDate(array $settings, bool $hasTime = false): string
    {
        $value = (int) ($settings['relative_value'] ?? 1);
        $unit = $settings['relative_unit'] ?? DateUnit::Day->value;
        $tense = $settings['tense'] ?? 'past';

        $method = $tense === 'future' ? 'add' : 'sub';

        return match ($unit) {
            DateUnit::Second->value => Carbon::now()->{$method . 'Seconds'}($value)->toDateTimeString(),
            DateUnit::Minute->value => Carbon::now()->{$method . 'Minutes'}($value)->toDateTimeString(),
            DateUnit::Hour->value => Carbon::now()->{$method . 'Hours'}($value)->toDateTimeString(),
            DateUnit::Day->value => Carbon::now()->{$method . 'Days'}($value)->toDateString(),
            DateUnit::Week->value => Carbon::now()->{$method . 'Weeks'}($value)->toDateString(),
            DateUnit::Month->value => Carbon::now()->{$method . 'Months'}($value)->toDateString(),
            DateUnit::Quarter->value => Carbon::now()->{$method . 'Quarters'}($value)->toDateString(),
            DateUnit::Year->value => Carbon::now()->{$method . 'Years'}($value)->toDateString(),
            default => Carbon::today()->toDateString(),
        };
    }
}
