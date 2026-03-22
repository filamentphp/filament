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

class ColorPickerSchema
{
    public static function schema(): array
    {
        return [
            Group::make()
                ->id('colorPicker')
                ->extraAttributes([
                    'class' => 'p-16 max-w-xl',
                ])
                ->schema([
                    ColorPicker::make('colorPicker')
                        ->label('Color')
                        ->default('#3490dc'),
                ]),
            Group::make()
                ->id('colorPickerOpen')
                ->extraAttributes([
                    'class' => 'px-16 pt-16 pb-96 max-w-xl',
                ])
                ->schema([
                    ColorPicker::make('colorPickerOpen')
                        ->label('Color')
                        ->default('#e3342f'),
                ]),
            Group::make()
                ->id('colorPickerFormats')
                ->extraAttributes([
                    'class' => 'p-16 max-w-2xl',
                ])
                ->schema([
                    ColorPicker::make('hsl_color')
                        ->label('HSL')
                        ->hsl()
                        ->default('hsl(210, 68%, 53%)'),
                    ColorPicker::make('rgb_color')
                        ->label('RGB')
                        ->rgb()
                        ->default('rgb(52, 144, 220)'),
                    ColorPicker::make('rgba_color')
                        ->label('RGBA')
                        ->rgba()
                        ->default('rgba(52, 144, 220, 0.5)'),
                ]),
        ];
    }
}
