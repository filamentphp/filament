<?php

use Filament\Infolists;
use Filament\Tests\Infolists\Fixtures\Livewire;
use Filament\Tests\Models\Team;
use Filament\Tests\Models\User;
use Filament\Tests\Models\UserWithHiddenTeamPivot;
use Filament\Tests\TestCase;

use function Filament\Tests\livewire;

uses(TestCase::class);

it('can evaluate livewire closure dependency by name', function () {
    livewire(TestComponentWithInfolist::class)
        ->assertOk()
        ->assertSee('First Entry Label')
        ->assertSee('First Entry State')
        ->assertSee('Second Entry Label')
        ->assertSee('Second Entry State')
        ->assertSee('Third Entry Label')
        ->assertSee('Third Entry State (dynamic)');
});

it('omits hidden pivot attributes from nested repeatable infolist state', function (): void {
    $user = User::factory()->create();
    $team = Team::factory()->create();
    $user->teams()->attach($team, ['role' => 'editor', 'created_at' => '2001-02-03 04:05:06']);
    $record = UserWithHiddenTeamPivot::findOrFail($user->getKey())->load('teams');
    Infolists\Infolist::make(Livewire::make())->record($record)->schema([
        $repeatable = Infolists\Components\RepeatableEntry::make('teams')->schema([
            Infolists\Components\TextEntry::make('created_at'),
            Infolists\Components\TextEntry::make('membership.created_at'),
            Infolists\Components\TextEntry::make('membership.role'),
            Infolists\Components\TextEntry::make('explicit')->state(static fn (Team $record) => $record->membership->created_at),
        ]),
    ])->getComponents();
    $entries = $repeatable->getChildComponentContainers()[0]->getComponents();

    expect($entries[0]->getState())->toBeNull()
        ->and($entries[1]->getState())->toBeNull()
        ->and($entries[2]->getState())->toBe('editor')
        ->and($entries[3]->getState())->not->toBeNull();
});

it('preserves existing explicit infolist access to hidden non-pivot attributes', function (): void {
    $record = Team::factory()->create()->makeHidden('created_at');
    Infolists\Infolist::make(Livewire::make())->record($record)->schema([
        $entry = Infolists\Components\TextEntry::make('created_at'),
    ])->getComponents();

    expect($entry->getState())->toEqual($record->created_at);
});

class TestComponentWithInfolist extends Livewire
{
    public function mount(): void
    {
        $this->data([
            'first_entry' => 'First Entry State',
            'second_entry' => 'Second Entry State',
        ]);
    }

    public function infolist(Infolists\Infolist $infolist): Infolists\Infolist
    {
        return $infolist
            ->state($this->data)
            ->schema(function (self $livewire) {
                expect($livewire)->toBe($this);

                return [
                    Infolists\Components\TextEntry::make('first_entry')
                        ->label('First Entry Label'),
                ];
            });
    }

    public function infolistWithCustomName(Infolists\Infolist $infolist): Infolists\Infolist
    {
        return $infolist
            ->state($this->data)
            ->schema(function (self $livewire) {
                expect($livewire)->toBe($this);

                return [
                    Infolists\Components\TextEntry::make('second_entry')
                        ->label('Second Entry Label'),
                    Infolists\Components\TextEntry::make('third_entry')
                        ->label('Third Entry Label')
                        ->getStateUsing(function (self $livewire) {
                            expect($livewire)->toBe($this);

                            return 'Third Entry State (dynamic)';
                        }),
                ];
            });
    }

    public function render(): string
    {
        return <<<'BLADE'
		{{ $this->infolist }}
		{{ $this->infolistWithCustomName }}
		BLADE;
    }
}
