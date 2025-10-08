<?php

use Filament\Forms\Components\DateTimePicker;

it('returns full datetime format by default (native with date, time and seconds)', function (): void {
    $picker = DateTimePicker::make('dt');

    expect($picker->getInternalFormat())->toBe('Y-m-d H:i:s');
});

it('returns date-only format when native and time is disabled', function (): void {
    $picker = DateTimePicker::make('dt')
        ->time(false);

    expect($picker->getInternalFormat())->toBe('Y-m-d');
});

it('returns time-only format without seconds when native, date disabled and seconds disabled', function (): void {
    $picker = DateTimePicker::make('dt')
        ->date(false)
        ->seconds(false);

    expect($picker->getInternalFormat())->toBe('H:i');
});

it('returns time-only format with seconds when native and date disabled', function (): void {
    $picker = DateTimePicker::make('dt')
        ->date(false); // seconds enabled by default

    expect($picker->getInternalFormat())->toBe('H:i:s');
});

it('returns datetime format without seconds when native and seconds are disabled', function (): void {
    $picker = DateTimePicker::make('dt')
        ->seconds(false);

    expect($picker->getInternalFormat())->toBe('Y-m-d H:i');
});

it('returns full datetime format for non-native pickers regardless of other flags', function (): void {
    $picker = DateTimePicker::make('dt')
        ->time(false)
        ->date(false)
        ->seconds(false)
        ->native(false);

    expect($picker->getInternalFormat())->toBe('Y-m-d H:i:s');
});
