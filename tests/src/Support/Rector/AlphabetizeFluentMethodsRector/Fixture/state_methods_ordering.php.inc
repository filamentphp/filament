<?php

use Filament\Forms\Components\TextInput;

TextInput::make('name')
    ->searchable()
    ->default('test')
    ->label('Name')
    ->dehydrated(false)
    ->afterStateUpdated(fn() => null);

?>
-----
<?php

use Filament\Forms\Components\TextInput;

TextInput::make('name')
    ->label('Name')
    ->searchable()
    ->afterStateUpdated(fn() => null)
    ->default('test')
    ->dehydrated(false);

?>
