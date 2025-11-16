<?php

use Filament\Forms\Components\TextInput;

TextInput::make('name')
    ->searchable()
    ->nullable()
    ->required()
    ->label('Name')
    ->alpha();

?>
-----
<?php

use Filament\Forms\Components\TextInput;

TextInput::make('name')
    ->label('Name')
    ->searchable()
    ->required()
    ->alpha()
    ->nullable();

?>
