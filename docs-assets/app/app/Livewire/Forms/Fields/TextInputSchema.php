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

class TextInputSchema
{
    public static function schema(): array
    {
        return [
            Group::make()
                ->id('textInput')
                ->extraAttributes([
                    'class' => 'p-16 max-w-xl',
                ])
                ->schema([
                    TextInput::make('textInput')
                        ->label('Name')
                        ->default('Dan Harrin'),
                ]),
            Group::make()
                ->id('textInputAffix')
                ->extraAttributes([
                    'class' => 'p-16 max-w-xl',
                ])
                ->schema([
                    TextInput::make('textInputAffix')
                        ->label('Domain')
                        ->default('filamentphp')
                        ->prefix('https://')
                        ->suffix('.com'),
                ]),
            Group::make()
                ->id('textInputSuffixIcon')
                ->extraAttributes([
                    'class' => 'p-16 max-w-xl',
                ])
                ->schema([
                    TextInput::make('textInputSuffixIcon')
                        ->label('Domain')
                        ->default('https://filamentphp.com')
                        ->suffixIcon(Heroicon::GlobeAlt),
                ]),
            Group::make()
                ->id('textInputSuffixIconColor')
                ->extraAttributes([
                    'class' => 'p-16 max-w-xl',
                ])
                ->schema([
                    TextInput::make('textInputSuffixIconColor')
                        ->label('Domain')
                        ->default('https://filamentphp.com')
                        ->suffixIcon(Heroicon::CheckCircle)
                        ->suffixIconColor('success'),
                ]),
            Group::make()
                ->id('textInputRevealablePassword')
                ->extraAttributes([
                    'class' => 'p-16 max-w-xl',
                ])
                ->schema([
                    TextInput::make('textInputRevealablePassword')
                        ->label('Password')
                        ->default('filament123')
                        ->password()
                        ->revealable(),
                    TextInput::make('textInputRevealedPassword')
                        ->label('Password')
                        ->default('filament123')
                        ->suffixActions([
                            TextInput\Actions\HidePasswordAction::make()
                                ->extraAttributes([]),
                        ]),
                ]),
            Group::make()
                ->id('textInputMask')
                ->extraAttributes([
                    'class' => 'p-16 max-w-xl',
                ])
                ->schema([
                    TextInput::make('textInputMask')
                        ->label('Phone number')
                        ->mask('(999) 999-9999')
                        ->placeholder('(555) 555-5555')
                        ->tel(),
                ]),
            Group::make()
                ->id('textInputCopyable')
                ->extraAttributes([
                    'class' => 'p-16 max-w-xl',
                ])
                ->schema([
                    TextInput::make('textInputCopyable')
                        ->label('API key')
                        ->default('flm_sk_1a2b3c4d5e6f7g8h9i0j')
                        ->copyable(copyMessage: 'Copied!'),
                ]),
            Group::make()
                ->id('textInputColor')
                ->extraAttributes([
                    'class' => 'p-16 max-w-xl',
                ])
                ->schema([
                    TextInput::make('textInputColor')
                        ->label('Background color')
                        ->type('color')
                        ->default('#6366f1'),
                ]),
        ];
    }
}
