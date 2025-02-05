<?php

use Filament\Tables\Table\Concerns\CanPaginateRecords;
use Filament\Tests\Tables\TestCase;
use Illuminate\Support\Facades\Config;

uses(TestCase::class);

beforeEach(function () {
    $this->trait = new class
    {
        use CanPaginateRecords;

        public function evaluate(...$args): mixed
        {
            return null;
        }
    };
});

it('can load default pagination values', function () {
    expect($this->trait->getPaginationPageOptions())->toBe([5, 10, 25, 50, 'all']);
});

it('can load config pagination values', function () {
    Config::set('filament.default_pagination', [5, 10, 25, 50]);

    expect($this->trait->getPaginationPageOptions())->toBe([5, 10, 25, 50]);
});
