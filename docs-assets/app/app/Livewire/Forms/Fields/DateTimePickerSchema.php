<?php

namespace App\Livewire\Forms\Fields;

use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Components\Builder;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\CodeEditor;
use Filament\Forms\Components\CodeEditor\Enums\Language;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\RichEditor\MentionProvider;
use Filament\Forms\Components\RichEditor\TextColor;
use Filament\Forms\Components\RichEditor\ToolbarButtonGroup;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Slider;
use Filament\Forms\Components\Slider\Enums\PipsMode;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\ToggleButtons;
use Filament\Schemas\Components\Flex;
use Filament\Schemas\Components\FusedGroup;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Icon;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Text;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Schemas\Schema;
use Filament\Support\Enums\Alignment;
use Filament\Support\Enums\FontWeight;
use Filament\Support\Icons\Heroicon;
use Filament\Support\RawJs;
use Illuminate\Support\HtmlString;
use Livewire\Component;

class DateTimePickerSchema
{
    public static function schema(): array
    {
        return [
            Group::make()
                ->id('dateTimePickers')
                ->extraAttributes([
                    'class' => 'p-16 max-w-xl',
                ])
                ->schema([
                    DateTimePicker::make('dateTimePicker')
                        ->label('Published at'),
                    DatePicker::make('datePickers')
                        ->label('Date of birth'),
                    TimePicker::make('timePicker')
                        ->label('Alarm at'),
                ]),
            Group::make()
                ->id('dateTimePickerWithoutSeconds')
                ->extraAttributes([
                    'class' => 'p-16 max-w-xl',
                ])
                ->schema([
                    DateTimePicker::make('dateTimePickerWithoutSeconds')
                        ->label('Published at')
                        ->seconds(false),
                ]),
            Group::make()
                ->id('javascriptDateTimePicker')
                ->extraAttributes([
                    'class' => 'px-16 pt-16 pb-96 max-w-xl',
                ])
                ->schema([
                    DatePicker::make('javascriptDateTimePicker')
                        ->label('Date of birth')
                        ->native(false)
                        ->default('2000-01-01'),
                ]),
            Group::make()
                ->id('dateTimePickerDisplayFormat')
                ->extraAttributes([
                    'class' => 'p-16 max-w-xl',
                ])
                ->schema([
                    DatePicker::make('dateTimePickerDisplayFormat')
                        ->label('Date of birth')
                        ->native(false)
                        ->displayFormat('d/m/Y')
                        ->default('2000-01-01'),
                ]),
            Group::make()
                ->id('dateTimePickerWeekStartsOnSunday')
                ->extraAttributes([
                    'class' => 'px-16 pt-16 pb-96 max-w-xl',
                ])
                ->schema([
                    DatePicker::make('dateTimePickerWeekStartsOnSunday')
                        ->label('Published at')
                        ->native(false)
                        ->weekStartsOnSunday()
                        ->default('2000-01-01'),
                ]),
            Group::make()
                ->id('dateTimePickerDisabledDates')
                ->extraAttributes([
                    'class' => 'px-16 pt-16 pb-96 max-w-xl',
                ])
                ->schema([
                    DatePicker::make('dateTimePickerDisabledDates')
                        ->label('Date')
                        ->native(false)
                        ->disabledDates(['2000-01-03', '2000-01-15', '2000-01-20'])
                        ->default('2000-01-01'),
                ]),
            Group::make()
                ->id('dateTimePickerDefaultFocusedDate')
                ->extraAttributes([
                    'class' => 'px-16 pt-16 pb-96 max-w-xl',
                ])
                ->schema([
                    DatePicker::make('dateTimePickerDefaultFocusedDate')
                        ->label('Custom starts at')
                        ->native(false)
                        ->placeholder('Jan 1, 2000')
                        ->defaultFocusedDate(\Carbon\Carbon::parse('2000-01-01')),
                ]),
            Group::make()
                ->id('dateTimePickerAffix')
                ->extraAttributes([
                    'class' => 'p-16 max-w-xl',
                ])
                ->schema([
                    DatePicker::make('dateTimePickerAffix')
                        ->label('Date')
                        ->prefix('Starts')
                        ->suffix('at midnight')
                        ->default('2000-01-01'),
                ]),
            Group::make()
                ->id('dateTimePickerPrefixIcon')
                ->extraAttributes([
                    'class' => 'p-16 max-w-xl',
                ])
                ->schema([
                    TimePicker::make('dateTimePickerPrefixIcon')
                        ->label('At')
                        ->prefixIcon(Heroicon::Play)
                        ->default('14:00:00'),
                ]),
            Group::make()
                ->id('dateTimePickerPrefixIconColor')
                ->extraAttributes([
                    'class' => 'p-16 max-w-xl',
                ])
                ->schema([
                    TimePicker::make('dateTimePickerPrefixIconColor')
                        ->label('At')
                        ->prefixIcon(Heroicon::CheckCircle)
                        ->prefixIconColor('success')
                        ->default('09:30:00'),
                ]),
        ];
    }
}
