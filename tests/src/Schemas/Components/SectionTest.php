<?php

use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Schemas\Schema;
use Filament\Tests\Fixtures\Models\Profile;
use Filament\Tests\Fixtures\Models\User;
use Filament\Tests\TestCase;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

use function Filament\Tests\livewire;

uses(TestCase::class);

it('can load state from a `HasOne` relationship using eager loaded data without additional queries', function (): void {
    $user = User::factory()->create();
    $profile = Profile::factory()->create(['user_id' => $user->id]);

    $freshUser = $user->fresh();
    expect($freshUser->relationLoaded('profile'))->toBeFalse();

    DB::enableQueryLog();
    DB::flushQueryLog();

    livewire(SectionWithHasOneRelationship::class, ['record' => $freshUser])
        ->assertSchemaStateSet(function (array $state) use ($profile) {
            expect($state['profile']['bio'])->toBe($profile->bio);

            return [];
        });

    $queriesWithoutEagerLoading = count(DB::getQueryLog());

    $eagerUser = $user->fresh();
    $eagerUser->load('profile');
    expect($eagerUser->relationLoaded('profile'))->toBeTrue();

    DB::flushQueryLog();

    livewire(SectionWithHasOneRelationship::class, ['record' => $eagerUser])
        ->assertSchemaStateSet(function (array $state) use ($profile) {
            expect($state['profile']['bio'])->toBe($profile->bio);

            return [];
        });

    $queriesWithEagerLoading = count(DB::getQueryLog());
    DB::disableQueryLog();

    $queriesSaved = $queriesWithoutEagerLoading - $queriesWithEagerLoading;
    expect($queriesSaved)->toBe(1, "Expected to save 1 query with eager loading, but saved {$queriesSaved}");
});

class SectionWithHasOneRelationship extends Component implements HasActions, HasSchemas
{
    use InteractsWithActions;
    use InteractsWithSchemas;

    public $data = [];

    public User $record;

    public function mount(): void
    {
        $this->form->fill([]);
    }

    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                Section::make('Profile')
                    ->relationship('profile')
                    ->schema([
                        TextInput::make('bio'),
                    ]),
            ])
            ->model($this->record)
            ->statePath('data');
    }

    public function render(): View
    {
        return view('livewire.form');
    }
}
