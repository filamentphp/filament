<?php

use Filament\Forms\Components\TextInput;

TextInput::make('name')
    ->sortable() ->placeholder('Enter name') ->searchable() ->getState(); ?>
----- <?php use Filament\Forms\Components\TextInput; TextInput::make('name')
->placeholder('Enter name') ->searchable() ->sortable() ->getState(); ?>
