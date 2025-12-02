<?php

namespace Filament\QueryBuilder\Constraints\DateConstraint\Operators;

use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\QueryBuilder\Constraints\DateConstraint\RelativeDateUnit;
use Filament\QueryBuilder\Constraints\Operators\Operator;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Components\FusedGroup;
use Filament\Schemas\Components\Utilities\Get;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

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

        // Check if using relative mode
        if (($settings['mode'] ?? null) === 'relative') {
            $dateDescription = $this->getRelativeDateDescription($settings);

            return __(
                $this->isInverse() ?
                    'filament-query-builder::query-builder.operators.date.is_before.summary.inverse' :
                    'filament-query-builder::query-builder.operators.date.is_before.summary.direct',
                [
                    'attribute' => $this->getConstraint()->getAttributeLabel(),
                    'date' => $dateDescription,
                ],
            );
        }

        // Default to absolute mode (backwards compatible)
        return __(
            $this->isInverse() ?
                'filament-query-builder::query-builder.operators.date.is_before.summary.inverse' :
                'filament-query-builder::query-builder.operators.date.is_before.summary.direct',
            [
                'attribute' => $this->getConstraint()->getAttributeLabel(),
                'date' => Carbon::parse($settings['date'])->toFormattedDateString(),
            ],
        );
    }

    /**
     * @param  array<string, mixed>  $settings
     */
    protected function getRelativeDateDescription(array $settings): string
    {
        $preset = $settings['preset'] ?? 'custom';

        if ($preset !== 'custom') {
            return Str::lower(__("filament-query-builder::query-builder.operators.date.presets.{$preset}"));
        }

        $value = (int) ($settings['relative_value'] ?? 1);
        $unit = $settings['relative_unit'] ?? RelativeDateUnit::Day->value;
        $tense = $settings['tense'] ?? 'past';

        $unitLabel = trans_choice("filament-query-builder::query-builder.operators.date.units.{$unit}", $value);

        if ($tense === 'future') {
            return __('filament-query-builder::query-builder.operators.date.relative_description_future', [
                'value' => $value,
                'unit' => $unitLabel,
            ]);
        }

        return __('filament-query-builder::query-builder.operators.date.relative_description', [
            'value' => $value,
            'unit' => $unitLabel,
        ]);
    }

    /**
     * @return array<Component | Action | ActionGroup>
     */
    public function getFormSchema(): array
    {
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
            DatePicker::make('date')
                ->label(__('filament-query-builder::query-builder.operators.date.form.date.label'))
                ->hidden(fn (Get $get): bool => $get('mode') === 'relative')
                ->required(fn (Get $get): bool => $get('mode') !== 'relative'),
            Select::make('preset')
                ->label(__('filament-query-builder::query-builder.operators.date.form.preset.label'))
                ->selectablePlaceholder(false)
                ->live()
                ->options([
                    'today' => __('filament-query-builder::query-builder.operators.date.presets.today'),
                    'yesterday' => __('filament-query-builder::query-builder.operators.date.presets.yesterday'),
                    'tomorrow' => __('filament-query-builder::query-builder.operators.date.presets.tomorrow'),
                    'this_week' => __('filament-query-builder::query-builder.operators.date.presets.this_week'),
                    'this_month' => __('filament-query-builder::query-builder.operators.date.presets.this_month'),
                    'this_quarter' => __('filament-query-builder::query-builder.operators.date.presets.this_quarter'),
                    'this_year' => __('filament-query-builder::query-builder.operators.date.presets.this_year'),
                    'start_of_week' => __('filament-query-builder::query-builder.operators.date.presets.start_of_week'),
                    'start_of_month' => __('filament-query-builder::query-builder.operators.date.presets.start_of_month'),
                    'start_of_quarter' => __('filament-query-builder::query-builder.operators.date.presets.start_of_quarter'),
                    'start_of_year' => __('filament-query-builder::query-builder.operators.date.presets.start_of_year'),
                    'end_of_week' => __('filament-query-builder::query-builder.operators.date.presets.end_of_week'),
                    'end_of_month' => __('filament-query-builder::query-builder.operators.date.presets.end_of_month'),
                    'end_of_quarter' => __('filament-query-builder::query-builder.operators.date.presets.end_of_quarter'),
                    'end_of_year' => __('filament-query-builder::query-builder.operators.date.presets.end_of_year'),
                    'past_week' => __('filament-query-builder::query-builder.operators.date.presets.past_week'),
                    'past_2_weeks' => __('filament-query-builder::query-builder.operators.date.presets.past_2_weeks'),
                    'past_month' => __('filament-query-builder::query-builder.operators.date.presets.past_month'),
                    'past_quarter' => __('filament-query-builder::query-builder.operators.date.presets.past_quarter'),
                    'past_6_months' => __('filament-query-builder::query-builder.operators.date.presets.past_6_months'),
                    'past_year' => __('filament-query-builder::query-builder.operators.date.presets.past_year'),
                    'past_2_years' => __('filament-query-builder::query-builder.operators.date.presets.past_2_years'),
                    'next_week' => __('filament-query-builder::query-builder.operators.date.presets.next_week'),
                    'next_2_weeks' => __('filament-query-builder::query-builder.operators.date.presets.next_2_weeks'),
                    'next_month' => __('filament-query-builder::query-builder.operators.date.presets.next_month'),
                    'next_quarter' => __('filament-query-builder::query-builder.operators.date.presets.next_quarter'),
                    'next_6_months' => __('filament-query-builder::query-builder.operators.date.presets.next_6_months'),
                    'next_year' => __('filament-query-builder::query-builder.operators.date.presets.next_year'),
                    'next_2_years' => __('filament-query-builder::query-builder.operators.date.presets.next_2_years'),
                    'custom' => __('filament-query-builder::query-builder.operators.date.presets.custom'),
                ])
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
                        collect(RelativeDateUnit::cases())
                            ->mapWithKeys(fn (RelativeDateUnit $unit): array => [$unit->value => $unit->getLabel()])
                            ->all()
                    )
                    ->default(RelativeDateUnit::Day->value)
                    ->required(),
            ])
                ->columns(3)
                ->columnSpanFull()
                ->hidden(fn (Get $get): bool => $get('mode') !== 'relative' || $get('preset') !== 'custom'),
        ];
    }

    public function apply(Builder $query, string $qualifiedColumn): Builder
    {
        $settings = $this->getSettings();

        // Check if using relative mode - only if mode is explicitly set to 'relative'
        if (($settings['mode'] ?? null) === 'relative') {
            $date = $this->resolveRelativeDate($settings);

            return $query->whereDate($qualifiedColumn, $this->isInverse() ? '>' : '<=', $date);
        }

        // Default to absolute mode using the date directly (backwards compatible)
        return $query->whereDate($qualifiedColumn, $this->isInverse() ? '>' : '<=', $settings['date']);
    }

    /**
     * @param  array<string, mixed>  $settings
     */
    protected function resolveRelativeDate(array $settings): string
    {
        $preset = $settings['preset'] ?? 'custom';

        return match ($preset) {
            'today' => Carbon::today()->toDateString(),
            'yesterday' => Carbon::yesterday()->toDateString(),
            'tomorrow' => Carbon::tomorrow()->toDateString(),
            'this_week' => Carbon::now()->startOfWeek()->toDateString(),
            'this_month' => Carbon::now()->startOfMonth()->toDateString(),
            'this_quarter' => Carbon::now()->startOfQuarter()->toDateString(),
            'this_year' => Carbon::now()->startOfYear()->toDateString(),
            'start_of_week' => Carbon::now()->startOfWeek()->toDateString(),
            'start_of_month' => Carbon::now()->startOfMonth()->toDateString(),
            'start_of_quarter' => Carbon::now()->startOfQuarter()->toDateString(),
            'start_of_year' => Carbon::now()->startOfYear()->toDateString(),
            'end_of_week' => Carbon::now()->endOfWeek()->toDateString(),
            'end_of_month' => Carbon::now()->endOfMonth()->toDateString(),
            'end_of_quarter' => Carbon::now()->endOfQuarter()->toDateString(),
            'end_of_year' => Carbon::now()->endOfYear()->toDateString(),
            'past_week' => Carbon::now()->subWeek()->toDateString(),
            'past_2_weeks' => Carbon::now()->subWeeks(2)->toDateString(),
            'past_month' => Carbon::now()->subMonth()->toDateString(),
            'past_quarter' => Carbon::now()->subQuarter()->toDateString(),
            'past_6_months' => Carbon::now()->subMonths(6)->toDateString(),
            'past_year' => Carbon::now()->subYear()->toDateString(),
            'past_2_years' => Carbon::now()->subYears(2)->toDateString(),
            'next_week' => Carbon::now()->addWeek()->toDateString(),
            'next_2_weeks' => Carbon::now()->addWeeks(2)->toDateString(),
            'next_month' => Carbon::now()->addMonth()->toDateString(),
            'next_quarter' => Carbon::now()->addQuarter()->toDateString(),
            'next_6_months' => Carbon::now()->addMonths(6)->toDateString(),
            'next_year' => Carbon::now()->addYear()->toDateString(),
            'next_2_years' => Carbon::now()->addYears(2)->toDateString(),
            'custom' => $this->resolveCustomRelativeDate($settings),
            default => Carbon::today()->toDateString(),
        };
    }

    /**
     * @param  array<string, mixed>  $settings
     */
    protected function resolveCustomRelativeDate(array $settings): string
    {
        $value = (int) ($settings['relative_value'] ?? 1);
        $unit = $settings['relative_unit'] ?? RelativeDateUnit::Day->value;
        $tense = $settings['tense'] ?? 'past';

        $method = $tense === 'future' ? 'add' : 'sub';

        return match ($unit) {
            RelativeDateUnit::Day->value => Carbon::now()->{$method . 'Days'}($value)->toDateString(),
            RelativeDateUnit::Week->value => Carbon::now()->{$method . 'Weeks'}($value)->toDateString(),
            RelativeDateUnit::Month->value => Carbon::now()->{$method . 'Months'}($value)->toDateString(),
            RelativeDateUnit::Quarter->value => Carbon::now()->{$method . 'Quarters'}($value)->toDateString(),
            RelativeDateUnit::Year->value => Carbon::now()->{$method . 'Years'}($value)->toDateString(),
            default => Carbon::today()->toDateString(),
        };
    }
}
