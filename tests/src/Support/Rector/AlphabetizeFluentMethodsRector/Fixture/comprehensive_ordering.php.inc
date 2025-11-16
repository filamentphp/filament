<?php

use Filament\Forms\Components\TextInput;

TextInput::make('name')
    ->sortable() ->default('test') ->required() ->placeholder('Enter name')
->alpha() ->searchable() ->afterStateUpdated(fn() => null) ->label('Name'); ?>
----- <?php use Filament\Forms\Components\TextInput; TextInput::make('name')
->label('Name') ->placeholder('Enter name') ->searchable() ->sortable()
->required() ->alpha() ->afterStateUpdated(fn() => null) ->default('test'); ?>
