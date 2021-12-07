<?php

use Filament\Forms\ComponentContainer;
use Filament\Forms\Components\Component;
use Illuminate\Support\Str;
use Filament\Tests\TestCase;
use Filament\Tests\Forms\Fixtures\Livewire;

uses(TestCase::class);

test('registered event listeners are executed', function () {
    $event = Str::random();

    $this->expectExceptionMessage($event);

    ComponentContainer::make(Livewire::make())
        ->components([
            (new Component())
                ->registerListeners([
                    $event => [
                        fn () => throw new Exception($event),
                    ],
                ]),
        ])
        ->dispatchEvent($event);
});
