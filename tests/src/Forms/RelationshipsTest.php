<?php

use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Field;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Fieldset;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Schemas\Schema;
use Filament\Tests\Fixtures\Livewire\Livewire;
use Filament\Tests\Fixtures\Models\Company;
use Filament\Tests\Fixtures\Models\Post;
use Filament\Tests\Fixtures\Models\Profile;
use Filament\Tests\Fixtures\Models\User;
use Filament\Tests\TestCase;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Str;
use Livewire\Component;

use function Filament\Tests\livewire;

uses(TestCase::class);

describe('relationship saving', function (): void {
    test('fields can save relationships', function (): void {
        $numberOfRelationshipsSaved = 0;
        $isFieldVisible = true;

        $saveRelationshipsUsing = function () use (&$numberOfRelationshipsSaved): void {
            $numberOfRelationshipsSaved++;
        };

        $schema = Schema::make(Livewire::make())
            ->statePath('data')
            ->components([
                (new Field(Str::random()))
                    ->saveRelationshipsUsing($saveRelationshipsUsing)
                    ->visible(function () use (&$isFieldVisible) {
                        return $isFieldVisible;
                    }),
            ])
            ->model(Post::factory()->create());

        $schema
            ->saveRelationships();

        expect($numberOfRelationshipsSaved)
            ->toBe(1);

        $schema
            ->saveRelationships();

        expect($numberOfRelationshipsSaved)
            ->toBe(2);

        $isFieldVisible = false;

        $schema
            ->saveRelationships();

        expect($numberOfRelationshipsSaved)
            ->toBe(2);
    });

    test('fields with `saved(false)` do not save relationships', function (): void {
        $numberOfRelationshipsSaved = 0;
        $isSaved = true;

        $saveRelationshipsUsing = function () use (&$numberOfRelationshipsSaved): void {
            $numberOfRelationshipsSaved++;
        };

        $schema = Schema::make(Livewire::make())
            ->statePath('data')
            ->components([
                (new Field(Str::random()))
                    ->saveRelationshipsUsing($saveRelationshipsUsing)
                    ->saved(function () use (&$isSaved) {
                        return $isSaved;
                    }),
            ])
            ->model(Post::factory()->create());

        $schema
            ->saveRelationships();

        expect($numberOfRelationshipsSaved)
            ->toBe(1);

        $schema
            ->saveRelationships();

        expect($numberOfRelationshipsSaved)
            ->toBe(2);

        $isSaved = false;

        $schema
            ->saveRelationships();

        expect($numberOfRelationshipsSaved)
            ->toBe(2);
    });

    test('`saved(false)` prevents relationship saving regardless of `dehydrated()` setting', function (): void {
        $numberOfRelationshipsSaved = 0;

        $saveRelationshipsUsing = function () use (&$numberOfRelationshipsSaved): void {
            $numberOfRelationshipsSaved++;
        };

        $schema = Schema::make(Livewire::make())
            ->statePath('data')
            ->components([
                (new Field(Str::random()))
                    ->saveRelationshipsUsing($saveRelationshipsUsing)
                    ->dehydrated(true)
                    ->saved(false),
            ])
            ->model(Post::factory()->create());

        $schema
            ->saveRelationships();

        expect($numberOfRelationshipsSaved)
            ->toBe(0);
    });

    test('hidden fields can save relationships when `saveRelationshipsWhenHidden()` is called', function (): void {
        $numberOfRelationshipsSaved = 0;
        $shouldSaveRelationships = true;

        $saveRelationshipsUsing = function () use (&$numberOfRelationshipsSaved): void {
            $numberOfRelationshipsSaved++;
        };

        $schema = Schema::make(Livewire::make())
            ->statePath('data')
            ->components([
                (new Field(Str::random()))
                    ->saveRelationshipsUsing($saveRelationshipsUsing)
                    ->hidden()
                    ->saveRelationshipsWhenHidden(function () use (&$shouldSaveRelationships) {
                        return $shouldSaveRelationships;
                    }),
            ])
            ->model(Post::factory()->create());

        $schema
            ->saveRelationships();

        expect($numberOfRelationshipsSaved)
            ->toBe(1);

        $schema
            ->saveRelationships();

        expect($numberOfRelationshipsSaved)
            ->toBe(2);

        $shouldSaveRelationships = false;

        $schema
            ->saveRelationships();

        expect($numberOfRelationshipsSaved)
            ->toBe(2);
    });
});

describe('disabled fields', function (): void {
    test('disabled fields do not save relationships', function (): void {
        $numberOfRelationshipsSaved = 0;

        $saveRelationshipsUsing = function () use (&$numberOfRelationshipsSaved): void {
            $numberOfRelationshipsSaved++;
        };

        $schema = Schema::make(Livewire::make())
            ->statePath('data')
            ->components([
                (new Field(Str::random()))
                    ->saveRelationshipsUsing($saveRelationshipsUsing)
                    ->disabled(),
            ])
            ->model(Post::factory()->create());

        $schema
            ->saveRelationships();

        expect($numberOfRelationshipsSaved)
            ->toBe(0);
    });

    test('disabled fields can save relationships when `saveRelationshipsWhenDisabled()` is called', function (): void {
        $numberOfRelationshipsSaved = 0;

        $saveRelationshipsUsing = function () use (&$numberOfRelationshipsSaved): void {
            $numberOfRelationshipsSaved++;
        };

        $schema = Schema::make(Livewire::make())
            ->statePath('data')
            ->components([
                (new Field(Str::random()))
                    ->saveRelationshipsUsing($saveRelationshipsUsing)
                    ->disabled()
                    ->saveRelationshipsWhenDisabled(),
            ])
            ->model(Post::factory()->create());

        $schema
            ->saveRelationships();

        expect($numberOfRelationshipsSaved)
            ->toBe(1);
    });

    test('disabled fields can save relationships when `saved()` is called', function (): void {
        $numberOfRelationshipsSaved = 0;

        $saveRelationshipsUsing = function () use (&$numberOfRelationshipsSaved): void {
            $numberOfRelationshipsSaved++;
        };

        $schema = Schema::make(Livewire::make())
            ->statePath('data')
            ->components([
                (new Field(Str::random()))
                    ->saveRelationshipsUsing($saveRelationshipsUsing)
                    ->disabled()
                    ->saved(),
            ])
            ->model(Post::factory()->create());

        $schema
            ->saveRelationships();

        expect($numberOfRelationshipsSaved)
            ->toBe(1);
    });
});

describe('relationship filling', function (): void {
    test('a non-cast field is filled from the related record', function (): void {
        $user = User::factory()->create();
        Profile::factory()->create([
            'user_id' => $user->id,
            'bio' => 'Hello from triage',
        ]);

        livewire(SchemaWithSingleSectionRelationship::class, ['record' => $user->fresh()])
            ->assertSchemaStateSet(function (array $state): array {
                expect($state['profile']['bio'])->toBe('Hello from triage');

                return [];
            });
    });

    test('a `default()` value on a non-cast field is applied when no related record exists', function (): void {
        $user = User::factory()->create();

        livewire(SchemaWithSingleSectionRelationshipAndDefault::class, ['record' => $user->fresh()])
            ->assertSchemaStateSet(function (array $state): array {
                expect($state['profile']['bio'])->toBe('default bio from form');

                return [];
            });
    });

    test('a `timezone()` cast on a field nested in a relationship section is applied exactly once', function (): void {
        config(['app.timezone' => 'UTC']);

        $user = User::factory()->create();
        Profile::factory()->create([
            'user_id' => $user->id,
            'created_at' => '2026-04-08 21:00:00',
            'updated_at' => '2026-04-08 21:00:00',
        ]);

        livewire(SchemaWithSingleSectionRelationshipAndTimezone::class, ['record' => $user->fresh()])
            ->assertSchemaStateSet(function (array $state): array {
                // 21:00 UTC == 17:00 EDT (`America/New_York` is UTC-4 in April).
                expect($state['profile']['created_at'])->toBe('2026-04-08 17:00:00');

                return [];
            });
    });

    test('a `default()` value on a cast field nested in a relationship section is cast exactly once when no related record exists', function (): void {
        config(['app.timezone' => 'UTC']);

        $user = User::factory()->create();

        livewire(SchemaWithSingleSectionRelationshipAndTimezoneAndDefault::class, ['record' => $user->fresh()])
            ->assertSchemaStateSet(function (array $state): array {
                expect($state['profile']['created_at'])->toBe('2026-04-08 17:00:00');

                return [];
            });
    });

    test('the related record value wins over a `default()` value and is cast exactly once', function (): void {
        config(['app.timezone' => 'UTC']);

        $user = User::factory()->create();
        Profile::factory()->create([
            'user_id' => $user->id,
            'created_at' => '2026-04-09 14:00:00',
            'updated_at' => '2026-04-09 14:00:00',
        ]);

        livewire(SchemaWithSingleSectionRelationshipAndTimezoneAndDefault::class, ['record' => $user->fresh()])
            ->assertSchemaStateSet(function (array $state): array {
                expect($state['profile']['created_at'])->toBe('2026-04-09 10:00:00');

                return [];
            });
    });

    test('multiple sections sharing the same `relationship()` each cast a shared cast field exactly once', function (): void {
        config(['app.timezone' => 'UTC']);

        $user = User::factory()->create();
        Profile::factory()->create([
            'user_id' => $user->id,
            'created_at' => '2026-04-08 21:00:00',
            'updated_at' => '2026-04-08 21:00:00',
        ]);

        livewire(SchemaWithTwoSectionsSharingRelationship::class, ['record' => $user->fresh()])
            ->assertSchemaStateSet(function (array $state): array {
                expect($state['profile']['created_at'])->toBe('2026-04-08 17:00:00');

                return [];
            });
    });

    test('multiple sections sharing a `relationship()` cast their distinct cast fields exactly once each', function (): void {
        config(['app.timezone' => 'UTC']);

        $user = User::factory()->create();
        Profile::factory()->create([
            'user_id' => $user->id,
            'created_at' => '2026-04-08 21:00:00',
            'updated_at' => '2026-04-08 22:00:00',
        ]);

        livewire(SchemaWithTwoSectionsSharingRelationshipDistinctFields::class, ['record' => $user->fresh()])
            ->assertSchemaStateSet(function (array $state): array {
                // 21:00 UTC → 17:00 EDT; 22:00 UTC → 18:00 EDT.
                expect($state['profile']['created_at'])->toBe('2026-04-08 17:00:00');
                expect($state['profile']['updated_at'])->toBe('2026-04-08 18:00:00');

                return [];
            });
    });

    test('a `Grid->relationship()` fills a cast field correctly (issue #19665 repro)', function (): void {
        config(['app.timezone' => 'UTC']);

        $user = User::factory()->create();
        Profile::factory()->create([
            'user_id' => $user->id,
            'created_at' => '2026-04-08 21:00:00',
            'updated_at' => '2026-04-08 21:00:00',
        ]);

        livewire(SchemaWithGridRelationshipAndTimezone::class, ['record' => $user->fresh()])
            ->assertSchemaStateSet(function (array $state): array {
                expect($state['profile']['created_at'])->toBe('2026-04-08 17:00:00');

                return [];
            });
    });

    test('a `Fieldset->relationship()` fills a cast field correctly', function (): void {
        config(['app.timezone' => 'UTC']);

        $user = User::factory()->create();
        Profile::factory()->create([
            'user_id' => $user->id,
            'created_at' => '2026-04-08 21:00:00',
            'updated_at' => '2026-04-08 21:00:00',
        ]);

        livewire(SchemaWithFieldsetRelationshipAndTimezone::class, ['record' => $user->fresh()])
            ->assertSchemaStateSet(function (array $state): array {
                expect($state['profile']['created_at'])->toBe('2026-04-08 17:00:00');

                return [];
            });
    });

    test('a `BelongsTo` relationship fills its child schema from the related record', function (): void {
        $author = User::factory()->create(['name' => 'Author McAuthorface']);
        $post = Post::factory()->create(['author_id' => $author->id]);

        livewire(SchemaWithBelongsToRelationship::class, ['record' => $post->fresh()])
            ->assertSchemaStateSet(function (array $state): array {
                expect($state['author']['name'])->toBe('Author McAuthorface');

                return [];
            });
    });

    test('multiple `Group->relationship()` components do not overwrite cast state set by an earlier group (issue #18826)', function (): void {
        config(['app.timezone' => 'UTC']);

        $user = User::factory()->create();
        Profile::factory()->create([
            'user_id' => $user->id,
            'created_at' => '2026-04-08 21:00:00',
            'updated_at' => '2026-04-08 21:00:00',
        ]);

        livewire(SchemaWithTwoGroupsSharingRelationship::class, ['record' => $user->fresh()])
            ->assertSchemaStateSet(function (array $state): array {
                expect($state['profile']['created_at'])->toBe('2026-04-08 17:00:00');

                return [];
            });
    });

    test('nested `->relationship()` sections (relationship inside a relationship) each fill correctly', function (): void {
        // `User` `hasOne` `Profile`; `Profile` `belongsTo` `Company`.
        $user = User::factory()->create();
        $company = Company::factory()->create(['name' => 'Acme, Inc.']);
        Profile::factory()->create([
            'user_id' => $user->id,
            'company_id' => $company->id,
            'bio' => 'Engineer at Acme',
        ]);

        livewire(SchemaWithNestedRelationships::class, ['record' => $user->fresh()])
            ->assertSchemaStateSet(function (array $state): array {
                expect($state['profile']['bio'])->toBe('Engineer at Acme');
                expect($state['profile']['company']['name'])->toBe('Acme, Inc.');

                return [];
            });
    });
});

class SchemaWithSingleSectionRelationship extends Component implements HasActions, HasSchemas
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

class SchemaWithSingleSectionRelationshipAndDefault extends Component implements HasActions, HasSchemas
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
                        TextInput::make('bio')
                            ->default('default bio from form'),
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

class SchemaWithSingleSectionRelationshipAndTimezone extends Component implements HasActions, HasSchemas
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
                        DateTimePicker::make('created_at')
                            ->timezone('America/New_York'),
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

class SchemaWithSingleSectionRelationshipAndTimezoneAndDefault extends Component implements HasActions, HasSchemas
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
                        DateTimePicker::make('created_at')
                            ->timezone('America/New_York')
                            ->default('2026-04-08 21:00:00'),
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

class SchemaWithTwoSectionsSharingRelationship extends Component implements HasActions, HasSchemas
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
                Section::make('Profile (left)')
                    ->relationship('profile')
                    ->schema([
                        DateTimePicker::make('created_at')
                            ->timezone('America/New_York'),
                    ]),
                Section::make('Profile (right)')
                    ->relationship('profile')
                    ->schema([
                        DateTimePicker::make('created_at')
                            ->timezone('America/New_York'),
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

class SchemaWithTwoSectionsSharingRelationshipDistinctFields extends Component implements HasActions, HasSchemas
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
                Section::make('Profile (created)')
                    ->relationship('profile')
                    ->schema([
                        DateTimePicker::make('created_at')
                            ->timezone('America/New_York'),
                    ]),
                Section::make('Profile (updated)')
                    ->relationship('profile')
                    ->schema([
                        DateTimePicker::make('updated_at')
                            ->timezone('America/New_York'),
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

class SchemaWithGridRelationshipAndTimezone extends Component implements HasActions, HasSchemas
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
                Grid::make()
                    ->relationship('profile')
                    ->schema([
                        DateTimePicker::make('created_at')
                            ->timezone('America/New_York'),
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

class SchemaWithFieldsetRelationshipAndTimezone extends Component implements HasActions, HasSchemas
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
                Fieldset::make('Profile')
                    ->relationship('profile')
                    ->schema([
                        DateTimePicker::make('created_at')
                            ->timezone('America/New_York'),
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

class SchemaWithBelongsToRelationship extends Component implements HasActions, HasSchemas
{
    use InteractsWithActions;
    use InteractsWithSchemas;

    public $data = [];

    public Post $record;

    public function mount(): void
    {
        $this->form->fill([]);
    }

    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                Section::make('Author')
                    ->relationship('author')
                    ->schema([
                        TextInput::make('name'),
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

class SchemaWithNestedRelationships extends Component implements HasActions, HasSchemas
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
                        Section::make('Company')
                            ->relationship('company')
                            ->schema([
                                TextInput::make('name'),
                            ]),
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

class SchemaWithTwoGroupsSharingRelationship extends Component implements HasActions, HasSchemas
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
                Group::make()
                    ->relationship('profile')
                    ->schema([
                        DateTimePicker::make('created_at')
                            ->timezone('America/New_York'),
                    ]),
                Group::make()
                    ->relationship('profile')
                    ->schema([
                        DateTimePicker::make('created_at')
                            ->timezone('America/New_York'),
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
