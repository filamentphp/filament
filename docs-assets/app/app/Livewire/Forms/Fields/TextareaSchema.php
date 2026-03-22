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

class TextareaSchema
{
    public static function schema(): array
    {
        return [
            Group::make()
                ->id('textarea')
                ->extraAttributes([
                    'class' => 'p-16 max-w-xl',
                ])
                ->schema([
                    Textarea::make('textarea')
                        ->label('Description')
                        ->default('Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nullam euismod, nisl eget aliquam ultricies, quam sapien aliquet nunc, eget aliquam velit nisl quis nunc.'),
                ]),
            Group::make()
                ->id('textareaRows')
                ->extraAttributes([
                    'class' => 'p-16 max-w-xl',
                ])
                ->schema([
                    Textarea::make('textareaRows')
                        ->label('Description')
                        ->rows(10)
                        ->default("Filament is a collection of full-stack components for accelerated Laravel development. They are beautifully designed, intuitive to use, and fully extensible — the perfect starting point for your next Laravel app.\n\nWith Filament, you can build admin panels, customer-facing apps, SaaS platforms, and more — all with a consistent, polished UI.\n\nIt includes a form builder, table builder, notification system, action modals, infolist builder, and a complete admin panel framework."),
                ]),
            Group::make()
                ->id('textareaAutosize')
                ->extraAttributes([
                    'class' => 'p-16 max-w-xl',
                ])
                ->schema([
                    Textarea::make('textareaAutosize')
                        ->label('Description')
                        ->rows(1)
                        ->autosize()
                        ->default('Short note.'),
                ]),
        ];
    }
}
