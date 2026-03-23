<?php

namespace App\Livewire\Forms\Fields;

use Filament\Forms\Components\Slider;
use Filament\Forms\Components\Slider\Enums\PipsMode;
use Filament\Schemas\Components\Group;
use Filament\Support\RawJs;

class SliderSchema
{
    public static function schema(): array
    {
        return [
            Group::make()
                ->id('slider')
                ->extraAttributes([
                    'class' => 'p-16 max-w-xl',
                ])
                ->schema([
                    Slider::make('slider')
                        ->label('Slider')
                        ->default(50),
                ]),
            Group::make()
                ->id('sliderRange')
                ->extraAttributes([
                    'class' => 'p-16 max-w-xl',
                ])
                ->schema([
                    Slider::make('sliderRange')
                        ->label('Slider')
                        ->range(minValue: 40, maxValue: 80)
                        ->default(50),
                ]),
            Group::make()
                ->id('sliderMultiple')
                ->extraAttributes([
                    'class' => 'p-16 max-w-xl',
                ])
                ->schema([
                    Slider::make('sliderMultiple')
                        ->label('Slider')
                        ->default([20, 70]),
                ]),
            Group::make()
                ->id('sliderVertical')
                ->extraAttributes([
                    'class' => 'p-16 max-w-xl',
                ])
                ->schema([
                    Slider::make('sliderVertical')
                        ->label('Slider')
                        ->vertical()
                        ->default(50),
                ]),
            Group::make()
                ->id('sliderTopToBottom')
                ->extraAttributes([
                    'class' => 'p-16 max-w-xl',
                ])
                ->schema([
                    Slider::make('sliderTopToBottom')
                        ->label('Slider')
                        ->range(minValue: 0, maxValue: 100)
                        ->vertical()
                        ->rtl(false)
                        ->pips()
                        ->default(30),
                ]),
            Group::make()
                ->id('sliderTooltips')
                ->extraAttributes([
                    'class' => 'p-16 max-w-xl',
                ])
                ->schema([
                    Slider::make('sliderTooltips')
                        ->label('Slider')
                        ->tooltips()
                        ->default(50),
                ]),
            Group::make()
                ->id('sliderTooltipsMultiple')
                ->extraAttributes([
                    'class' => 'p-16 max-w-xl',
                ])
                ->schema([
                    Slider::make('sliderTooltipsMultiple')
                        ->label('Slider')
                        ->tooltips([true, false])
                        ->default([20, 70]),
                ]),
            Group::make()
                ->id('sliderTooltipsVertical')
                ->extraAttributes([
                    'class' => 'p-16 max-w-xl',
                ])
                ->schema([
                    Slider::make('sliderTooltipsVertical')
                        ->label('Slider')
                        ->tooltips()
                        ->vertical()
                        ->default(50),
                ]),
            Group::make()
                ->id('sliderTooltipsFormatting')
                ->extraAttributes([
                    'class' => 'p-16 max-w-xl',
                ])
                ->schema([
                    Slider::make('sliderTooltipsFormatting')
                        ->label('Slider')
                        ->tooltips(RawJs::make('`$${$value.toFixed(2)}`'))
                        ->default(64.99),
                ]),
            Group::make()
                ->id('sliderFill')
                ->extraAttributes([
                    'class' => 'p-16 max-w-xl',
                ])
                ->schema([
                    Slider::make('sliderFill')
                        ->label('Slider')
                        ->fillTrack()
                        ->default(50),
                ]),
            Group::make()
                ->id('sliderFillMultiple')
                ->extraAttributes([
                    'class' => 'p-16 max-w-xl',
                ])
                ->schema([
                    Slider::make('sliderFillMultiple')
                        ->label('Slider')
                        ->fillTrack([false, true, false])
                        ->default([20, 70]),
                ]),
            Group::make()
                ->id('sliderFillVertical')
                ->extraAttributes([
                    'class' => 'p-16 max-w-xl',
                ])
                ->schema([
                    Slider::make('sliderFillVertical')
                        ->label('Slider')
                        ->fillTrack()
                        ->vertical()
                        ->default(50),
                ]),
            Group::make()
                ->id('sliderPips')
                ->extraAttributes([
                    'class' => 'p-16 max-w-xl',
                ])
                ->schema([
                    Slider::make('sliderPips')
                        ->label('Slider')
                        ->pips()
                        ->default(50),
                ]),
            Group::make()
                ->id('sliderPipsMultiple')
                ->extraAttributes([
                    'class' => 'p-16 max-w-xl',
                ])
                ->schema([
                    Slider::make('sliderPipsMultiple')
                        ->label('Slider')
                        ->pips()
                        ->default([20, 70]),
                ]),
            Group::make()
                ->id('sliderPipsVertical')
                ->extraAttributes([
                    'class' => 'p-16 max-w-xl',
                ])
                ->schema([
                    Slider::make('sliderPipsVertical')
                        ->label('Slider')
                        ->pips()
                        ->vertical()
                        ->default(50),
                ]),
            Group::make()
                ->id('sliderPipsDensity')
                ->extraAttributes([
                    'class' => 'p-16 max-w-xl',
                ])
                ->schema([
                    Slider::make('sliderPipsDensity')
                        ->label('Slider')
                        ->pips(density: 5)
                        ->default(50),
                ]),
            Group::make()
                ->id('sliderPipsFormatting')
                ->extraAttributes([
                    'class' => 'p-16 max-w-xl',
                ])
                ->schema([
                    Slider::make('sliderPipsFormatting')
                        ->label('Slider')
                        ->pips()
                        ->pipsFormatter(RawJs::make('`$${$value.toFixed(2)}`'))
                        ->default(50),
                ]),
            Group::make()
                ->id('sliderPipsSteps')
                ->extraAttributes([
                    'class' => 'p-16 max-w-xl',
                ])
                ->schema([
                    Slider::make('sliderPipsSteps')
                        ->label('Slider')
                        ->step(10)
                        ->pips(PipsMode::Steps)
                        ->default(50),
                ]),
            Group::make()
                ->id('sliderPipsStepsDensity')
                ->extraAttributes([
                    'class' => 'p-16 max-w-xl',
                ])
                ->schema([
                    Slider::make('sliderPipsStepsDensity')
                        ->label('Slider')
                        ->step(10)
                        ->pips(PipsMode::Steps, density: 5)
                        ->default(50),
                ]),
            Group::make()
                ->id('sliderPipsPositions')
                ->extraAttributes([
                    'class' => 'p-16 max-w-xl',
                ])
                ->schema([
                    Slider::make('sliderPipsPositions')
                        ->label('Slider')
                        ->pips(PipsMode::Positions)
                        ->pipsValues([0, 25, 50, 75, 100])
                        ->default(50),
                ]),
            Group::make()
                ->id('sliderPipsCount')
                ->extraAttributes([
                    'class' => 'p-16 max-w-xl',
                ])
                ->schema([
                    Slider::make('sliderPipsCount')
                        ->label('Slider')
                        ->pips(PipsMode::Count)
                        ->pipsValues(5)
                        ->default(50),
                ]),
            Group::make()
                ->id('sliderPipsValues')
                ->extraAttributes([
                    'class' => 'p-16 max-w-xl',
                ])
                ->schema([
                    Slider::make('sliderPipsValues')
                        ->label('Slider')
                        ->pips(PipsMode::Values)
                        ->pipsValues([5, 15, 25, 35, 45, 55, 65, 75, 85, 95])
                        ->default(50),
                ]),
            Group::make()
                ->id('sliderPipsValuesDensity')
                ->extraAttributes([
                    'class' => 'p-16 max-w-xl',
                ])
                ->schema([
                    Slider::make('sliderPipsValuesDensity')
                        ->label('Slider')
                        ->pips(PipsMode::Values, density: 5)
                        ->pipsValues([5, 15, 25, 35, 45, 55, 65, 75, 85, 95])
                        ->default(50),
                ]),
            Group::make()
                ->id('sliderPipsFilter')
                ->extraAttributes([
                    'class' => 'p-16 max-w-xl',
                ])
                ->schema([
                    Slider::make('sliderPipsFilter')
                        ->label('Slider')
                        ->pips(density: 5)
                        ->pipsFilter(RawJs::make(<<<'JS'
                            ($value % 50) === 0
                                ? 1
                                : ($value % 10) === 0
                                    ? 2
                                    : ($value % 25) === 0
                                        ? 0
                                        : -1
                            JS))
                        ->default(50),
                ]),
            Group::make()
                ->id('sliderNonLinear')
                ->extraAttributes([
                    'class' => 'p-16 max-w-xl',
                ])
                ->schema([
                    Slider::make('sliderNonLinear')
                        ->label('Slider')
                        ->nonLinearPoints(['20%' => 50, '50%' => 75])
                        ->pips()
                        ->default(50),
                ]),
            Group::make()
                ->id('sliderRangePadding')
                ->extraAttributes([
                    'class' => 'p-16 max-w-xl',
                ])
                ->schema([
                    Slider::make('sliderRangePadding')
                        ->label('Slider')
                        ->range(minValue: 0, maxValue: 100)
                        ->rangePadding(15)
                        ->pips()
                        ->default(50),
                ]),
            Group::make()
                ->id('sliderRtl')
                ->extraAttributes([
                    'class' => 'p-16 max-w-xl',
                ])
                ->schema([
                    Slider::make('sliderRtl')
                        ->label('Slider')
                        ->range(minValue: 0, maxValue: 100)
                        ->rtl()
                        ->pips()
                        ->default(30),
                ]),
        ];
    }
}
