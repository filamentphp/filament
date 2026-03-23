<?php

namespace App\Livewire\Forms\Fields;

use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Group;

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
