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

class KeyValueSchema
{
    public static function schema(): array
    {
        return [
            Group::make()
                ->id('keyValue')
                ->extraAttributes([
                    'class' => 'p-16 max-w-5xl',
                ])
                ->schema([
                    KeyValue::make('keyValue')
                        ->label('Meta')
                        ->default([
                            'description' => 'Filament is a collection of Laravel packages',
                            'og:type' => 'website',
                            'og:site_name' => 'Filament',
                        ]),
                ]),
            Group::make()
                ->id('reorderableKeyValue')
                ->extraAttributes([
                    'class' => 'p-16 max-w-5xl',
                ])
                ->schema([
                    KeyValue::make('reorderableKeyValue')
                        ->label('Meta')
                        ->default([
                            'description' => 'Filament is a collection of Laravel packages',
                            'og:type' => 'website',
                            'og:site_name' => 'Filament',
                        ])
                        ->reorderable(),
                ]),
            Group::make()
                ->id('keyValueCustomLabels')
                ->extraAttributes([
                    'class' => 'p-16 max-w-5xl',
                ])
                ->schema([
                    KeyValue::make('keyValueCustomLabels')
                        ->label('Environment Variables')
                        ->keyLabel('Variable')
                        ->valueLabel('Value')
                        ->keyPlaceholder('e.g. APP_NAME')
                        ->valuePlaceholder('e.g. My Application')
                        ->default([
                            'APP_NAME' => 'Filament',
                            'APP_ENV' => 'production',
                            'APP_DEBUG' => 'false',
                        ]),
                ]),
        ];
    }
}
