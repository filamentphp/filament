<?php

use Filament\Infolists;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Schemas\Schema;
use Filament\Tests\Fixtures\Models\Department;
use Filament\Tests\Fixtures\Models\HiddenDepartmentTicket;
use Filament\Tests\Fixtures\Models\Ticket;
use Filament\Tests\TestCase;
use Livewire\Component;

use function Filament\Tests\livewire;

uses(TestCase::class);

it('can evaluate livewire closure dependency by name', function (): void {
    livewire(LivewireInfolists::class)
        ->assertOk()
        ->assertSee('First Entry Label')
        ->assertSee('First Entry State')
        ->assertSee('Second Entry Label')
        ->assertSee('Second Entry State')
        ->assertSee('Third Entry Label')
        ->assertSee('Third Entry State (dynamic)');
});

it('can render multiple infolists on the same component', function (): void {
    livewire(LivewireInfolists::class)
        ->assertOk()
        ->assertSee('First Entry Label')
        ->assertSee('Second Entry Label');
});

it('omits hidden pivot attributes and collisions from automatic infolist state', function (bool $isNested): void {
    $ticket = Ticket::factory()->create();
    $department = Department::factory()->create();
    $ticket->departments()->attach($department, ['quantity' => 7, 'price' => 70, 'created_at' => '2001-02-03 04:05:06']);
    $departments = $ticket->departments()->using(HiddenDepartmentTicket::class)->as('attachment')->get();
    $record = $departments->first();
    $record->setAttribute('quantity', 7);
    $record->setAttribute('pivot_quantity', 7);
    $fields = [
        Infolists\Components\TextEntry::make('created_at'),
        Infolists\Components\TextEntry::make('quantity'),
        Infolists\Components\TextEntry::make('pivot_quantity'),
        Infolists\Components\TextEntry::make('attachment.created_at'),
        Infolists\Components\TextEntry::make('attachment.price'),
        Infolists\Components\TextEntry::make('explicit')->state(static fn (Department $record) => $record->attachment->quantity),
    ];
    $schema = Schema::make(LivewireInfolists::make());

    if ($isNested) {
        $schema->record($ticket->setRelation('departments', $departments))->components([
            $repeatable = Infolists\Components\RepeatableEntry::make('departments')->schema($fields),
        ])->getComponents();
        $entries = $repeatable->getChildSchemas()[0]->getComponents();
    } else {
        $entries = $schema->record($record)->components($fields)->getComponents();
    }

    expect($entries[0]->getState())->toBeNull()
        ->and($entries[1]->getState())->toBeNull()
        ->and($entries[2]->getState())->toBeNull()
        ->and($entries[3]->getState())->toBeNull()
        ->and($entries[4]->getState())->toBe(70)
        ->and($entries[5]->getState())->toBe(7)
        ->and($record->quantity)->toBe(7)
        ->and($record->pivot_quantity)->toBe(7)
        ->and($record->created_at)->not->toBeNull()
        ->and($record->attachment->created_at)->not->toBeNull();
})->with([false, true]);

it('preserves automatic infolist access to hidden non-pivot attributes', function (): void {
    $record = Department::factory()->create()->makeHidden('created_at');
    Schema::make(LivewireInfolists::make())->record($record)->components([
        $entry = Infolists\Components\TextEntry::make('created_at'),
    ])->getComponents();

    expect($entry->getState())->toEqual($record->created_at);
});

class LivewireInfolists extends Component implements HasSchemas
{
    use InteractsWithSchemas;

    public $data;

    public static function make(): static
    {
        return new static;
    }

    public function mount(): void
    {
        $this->data([
            'first_entry' => 'First Entry State',
            'second_entry' => 'Second Entry State',
        ]);
    }

    public function infolist(Schema $schema): Schema
    {
        return $schema
            ->state($this->data)
            ->components(function (self $livewire) {
                expect($livewire)->toBe($this);

                return [
                    Infolists\Components\TextEntry::make('first_entry')
                        ->label('First Entry Label'),
                ];
            });
    }

    public function infolistWithCustomName(Schema $schema): Schema
    {
        return $schema
            ->state($this->data)
            ->components(function (self $livewire) {
                expect($livewire)->toBe($this);

                return [
                    Infolists\Components\TextEntry::make('second_entry')
                        ->label('Second Entry Label'),
                    Infolists\Components\TextEntry::make('third_entry')
                        ->label('Third Entry Label')
                        ->state(function (self $livewire) {
                            expect($livewire)->toBe($this);

                            return 'Third Entry State (dynamic)';
                        }),
                ];
            });
    }

    public function data($data): static
    {
        $this->data = $data;

        return $this;
    }

    public function getData()
    {
        return $this->data;
    }

    public function render(): string
    {
        return <<<'BLADE'
            <div>
                {{ $this->infolist }}
                {{ $this->infolistWithCustomName }}
            </div>
            BLADE;
    }
}
