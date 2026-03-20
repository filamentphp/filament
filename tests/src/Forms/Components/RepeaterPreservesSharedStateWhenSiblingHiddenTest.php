<?php

use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Schemas\Schema;
use Filament\Tests\Fixtures\Models\Post;
use Filament\Tests\Fixtures\Models\User;
use Filament\Tests\TestCase;
use Illuminate\Contracts\View\View;
use Livewire\Component;

use function Filament\Tests\livewire;

uses(TestCase::class);

it('repeater: dehydration shared statePath when a sibling Group is hidden', function (): void {
    // Scenario: two `Group` components inside a `Repeater` both use `->statePath('config')`.
    // The first group contains an internal-only field (`internal_notes`) and is hidden;
    // the second group is visible and contains the public `seo_title` field.
    //
    // Bug: when `dehydrateState()` runs on the hidden `Group`, it removes the entire
    // `config` key from the repeater item's state rather than only excluding the fields
    // it owns (`internal_notes`). This wipes `seo_title` before the relationship is saved,
    // so the value never reaches the database.

    $user = User::factory()->create();
    $existingPost = Post::factory()->create([
        'author_id' => $user->id,
    ]);

    $component = livewire(RepeaterPreservesSharedStateWhenSiblingHidden::class, ['record' => $user]);

    $existingPostKey = array_key_first($component->get('data.posts'));

    $undoRepeaterFake = Repeater::fake();

    $component
        ->fillForm([
            'posts' => [
                $existingPostKey => [
                    // `seo_title` is the visible field we expect to persist under `config`.
                    // The hidden sibling `internal_notes` must not cause the entire `config`
                    // object to be dropped during dehydration.
                    'config' => [
                        'internal_notes' => 'Do not publish',
                        'seo_title' => 'New SEO Title',
                    ],
                ],
            ],
        ])
        ->call('save');

    $undoRepeaterFake();

    $existingPost->refresh();

    expect($existingPost->config)
        ->not->toHaveKey('internal_notes')
        ->toHaveKey('seo_title', 'New SEO Title');
});

class RepeaterPreservesSharedStateWhenSiblingHidden extends Component implements HasSchemas
{
    use InteractsWithSchemas;

    public array $data = [];

    public User $record;

    public function mount(): void
    {
        $this->form->fill([]);
    }

    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                Repeater::make('posts')
                    ->relationship('posts')
                    ->schema([
                        // Hidden, internal-only group. Shares the same `statePath('config')`
                        // with the visible SEO group below. It should not be able to cause
                        // the entire `config` key to be removed during dehydration.
                        Group::make([
                            TextInput::make('internal_notes'),
                        ])
                            ->hidden()
                            ->statePath('config'),
                        // Visible group containing public SEO fields.
                        Group::make([
                            TextInput::make('seo_title'),
                        ])
                            ->statePath('config'),
                    ]),
            ])
            ->model($this->record)
            ->statePath('data');
    }

    public function save(): void
    {
        $this->form->getState();
        $this->form->saveRelationships();
    }

    public function render(): View
    {
        return view('livewire.form');
    }
}
