<?php

namespace App\Livewire\Schemas\Layout;

use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Components\Callout;
use Filament\Schemas\Components\EmptyState;
use Filament\Schemas\Components\Fieldset;
use Filament\Schemas\Components\Flex;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Components\Text;
use Filament\Schemas\Components\Wizard;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Schemas\Schema;
use Filament\Support\Enums\Alignment;
use Filament\Support\Enums\IconPosition;
use Filament\Support\Enums\IconSize;
use Filament\Support\Enums\VerticalAlignment;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\HtmlString;
use Livewire\Component;

class FieldsetSchema
{
    public static function schema(): array
    {
        return [
            Group::make()
                ->id('fieldset')
                ->extraAttributes([
                    'class' => 'p-16 max-w-2xl',
                ])
                ->schema([
                    Fieldset::make('Rate limiting')
                        ->statePath('fieldset')
                        ->schema([
                            TextInput::make('hits')
                                ->default(30),
                            Select::make('period')
                                ->default('hour')
                                ->options([
                                    'hour' => 'Hour',
                                ]),
                            TextInput::make('maximum')
                                ->default(100),
                        ])
                        ->columns(3),
                ]),
            Group::make()
                ->id('fieldsetNotContained')
                ->extraAttributes([
                    'class' => 'p-16 max-w-2xl',
                ])
                ->schema([
                    Fieldset::make('Rate limiting')
                        ->statePath('fieldsetNotContained')
                        ->contained(false)
                        ->schema([
                            TextInput::make('hits')
                                ->default(30),
                            Select::make('period')
                                ->default('hour')
                                ->options([
                                    'hour' => 'Hour',
                                ]),
                            TextInput::make('maximum')
                                ->default(100),
                        ])
                        ->columns(3),
                ]),
        ];
    }
}
