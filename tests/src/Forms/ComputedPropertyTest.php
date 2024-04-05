<?php

use Filament\Forms\Components\TextInput;
use Filament\Schema\Schema;
use Filament\Tests\Forms\Fixtures\Livewire;
use Filament\Tests\TestCase;
use Pest\Expectation;

uses(TestCase::class);

test('computed properties used to generate form schema can be accessed before caching forms', function () {
    expect(ComputedPropertySchema::make())
        ->getSchemaArray()
        ->toBeArray()
        ->sequence(
            fn (Expectation $expect) => $expect->toBeInstanceOf(TextInput::class)
        );
});

class ComputedPropertySchema extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form
            ->schema($this->schema);
    }

    public function getSchemaProperty()
    {
        return [
            TextInput::make(''),
        ];
    }

    public function getSchemaArray()
    {
        return $this->schema;
    }
}
