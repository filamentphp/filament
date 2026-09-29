<?php

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;
use Filament\Tests\Fixtures\Livewire\Livewire;
use Filament\Tests\Fixtures\Models\User;
use Filament\Tests\TestCase;

uses(TestCase::class);

it('decides whether a state relationship is multiple from the state path', function (): void {
    $user = User::factory()->create();

    $toMany = TextEntry::make('posts.title')
        ->container(Schema::make(Livewire::make()));

    $toOne = TextEntry::make('team.name')
        ->container(Schema::make(Livewire::make()));

    expect($toMany->hasMultipleStateRelationship($user))->toBeTrue()
        ->and($toOne->hasMultipleStateRelationship($user))->toBeFalse();
});
