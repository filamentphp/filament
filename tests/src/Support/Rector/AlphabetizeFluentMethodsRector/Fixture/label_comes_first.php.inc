<?php

use Filament\Forms\Components\TextInput;

TextInput::make('name')
    ->sortable() ->searchable() ->label('Name'); ?> ----- <?php use
Filament\Forms\Components\TextInput; TextInput::make('name') ->label('Name')
->searchable() ->sortable(); ?>
