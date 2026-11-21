<?php

use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Tests\Forms\Fixtures\Livewire;
use Filament\Tests\Models\Post;
use Filament\Tests\Models\User;
use Filament\Tests\TestCase;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

use function Filament\Tests\livewire;

uses(TestCase::class);

it('can fill and assert data in a repeater', function (array $data) {
    $undoRepeaterFake = Repeater::fake();

    livewire(TestComponentWithRepeater::class)
        ->fillForm($data)
        ->assertFormSet($data);

    $undoRepeaterFake();
})->with([
    'normal' => fn (): array => ['normal' => [
        [
            'title' => Str::random(),
            'category' => Str::random(),
        ],
        [
            'title' => Str::random(),
            'category' => Str::random(),
        ],
        [
            'title' => Str::random(),
            'category' => Str::random(),
        ],
    ]],
    'simple' => fn (): array => ['simple' => [
        Str::random(),
        Str::random(),
        Str::random(),
    ]],
    'nested' => fn (): array => ['parent' => [
        [
            'title' => Str::random(),
            'category' => Str::random(),
            'nested' => [
                [
                    'name' => Str::random(),
                ],
                [
                    'name' => Str::random(),
                ],
                [
                    'name' => Str::random(),
                ],
            ],
            'nestedSimple' => [
                Str::random(),
                Str::random(),
                Str::random(),
            ],
        ],
        [
            'title' => Str::random(),
            'category' => Str::random(),
            'nested' => [
                [
                    'name' => Str::random(),
                ],
                [
                    'name' => Str::random(),
                ],
                [
                    'name' => Str::random(),
                ],
            ],
            'nestedSimple' => [
                Str::random(),
                Str::random(),
                Str::random(),
            ],
        ],
        [
            'title' => Str::random(),
            'category' => Str::random(),
            'nested' => [
                [
                    'name' => Str::random(),
                ],
                [
                    'name' => Str::random(),
                ],
                [
                    'name' => Str::random(),
                ],
            ],
            'nestedSimple' => [
                Str::random(),
                Str::random(),
                Str::random(),
            ],
        ],
    ]],
]);

it('can remove items from a repeater', function () {
    $undoRepeaterFake = Repeater::fake();

    livewire(TestComponentWithRepeater::class)
        ->fillForm($data = [
            'normal' => [
                [
                    'title' => Str::random(),
                    'category' => Str::random(),
                ],
                [
                    'title' => Str::random(),
                    'category' => Str::random(),
                ],
            ],
        ])
        ->assertFormSet($data)
        ->fillForm([
            'normal' => [
                Arr::first($data['normal']),
            ],
        ])
        ->assertFormSet(function (array $data) {
            expect($data['normal'])->toHaveCount(1);

            return [
                'normal' => [
                    Arr::first($data['normal']),
                ],
            ];
        });

    $undoRepeaterFake();
});

it('can use select options from an enum with `disableOptionsWhenSelectedInSiblingRepeaterItems()`', function () {
    $undoRepeaterFake = Repeater::fake();

    livewire(TestComponentWithEnumSelectRepeater::class)
        ->fillForm([
            'alternatives' => [
                ['letter' => TestLetterEnum::A],
                ['letter' => TestLetterEnum::B],
            ],
        ])
        ->assertFormSet([
            'alternatives' => [
                ['letter' => TestLetterEnum::A],
                ['letter' => TestLetterEnum::B],
            ],
        ]);

    $undoRepeaterFake();
});

it('validates unsafe repeater item keys and removes disabled values', function (string $itemKey): void {
    $livewire = Livewire::make()->data(['items' => [
        'safe' => ['url' => 'https://filamentphp.com', 'is_verified' => true],
        $itemKey => ['url' => 'javascript:alert(1)', 'is_verified' => true],
    ]]);
    $form = Form::make($livewire)->statePath('data')->schema([
        $repeater = Repeater::make('items')->schema([
            TextInput::make('url')->url(),
            Toggle::make('is_verified')->disabled(),
        ]),
    ]);

    expect(fn () => $form->getState())->toThrow(ValidationException::class);

    $state = $repeater->getState();
    $state[array_key_last($state)]['url'] = 'https://example.com';
    $repeater->state($state);

    expect($form->getState())->toBe(['items' => [
        ['url' => 'https://filamentphp.com'],
        ['url' => 'https://example.com'],
    ]]);
})->with(['a.b', '', '*', '\\*', '{first}', '\\{last}', 'part\\', 'a"']);

it('adds a repeater item with defaults when `generateUuidUsing()` returns an unsafe key', function (): void {
    Form::make(Livewire::make()->data(['items' => []]))->statePath('data')->schema([
        $repeater = Repeater::make('items')->generateUuidUsing(static fn (): string => 'unsafe.key')
            ->schema([TextInput::make('name')->default('Added item')]),
    ])->getComponents();

    $repeater->getAddAction()->component($repeater)->call();

    expect(array_values($repeater->getState()))->toBe([['name' => 'Added item']]);
});

it('does not overwrite future numeric repeater keys with UUID generation disabled', function (): void {
    Form::make(Livewire::make()->data(['items' => [
        'a.b' => ['name' => 'First'],
        0 => ['name' => 'Second'],
        1 => ['name' => 'Third'],
    ]]))->statePath('data')->schema([
        $repeater = Repeater::make('items')->generateUuidUsing(false)->schema([TextInput::make('name')]),
    ])->getComponents();

    $repeater->getChildComponentContainers();

    expect(array_values($repeater->getState()))->toBe([
        ['name' => 'First'], ['name' => 'Second'], ['name' => 'Third'],
    ]);
});

it('preserves safe string relationship identities and rejects unsafe identities before changing records', function (string $recordKey, bool $isSafe): void {
    $user = User::factory()->create();
    $user = RepeaterUserWithStringKeyPosts::findOrFail($user->id);
    $post = Post::factory()->create(['author_id' => $user->id, 'title' => $recordKey, 'content' => 'Original']);
    $form = Form::make(Livewire::make())->statePath('data')->model($user)->schema([
        $repeater = Repeater::make('posts')->relationship('posts')->schema([TextInput::make('content')]),
    ]);

    if (! $isSafe) {
        expect(fn () => $form->loadStateFromRelationships())->toThrow(LogicException::class);
        expect($post->fresh()->content)->toBe('Original')
            ->and($user->posts()->count())->toBe(1);

        return;
    }

    $form->loadStateFromRelationships();
    $state = $repeater->getState();
    $state[array_key_first($state)]['content'] = 'Updated';
    $repeater->state($state);
    $form->saveRelationships();

    expect($post->fresh()->content)->toBe('Updated')
        ->and($user->posts()->count())->toBe(1);
})->with([
    'literal backslash' => ['part\\one', true],
    'dot' => ['part.one', false],
    'quote' => ['part"one', false],
]);

class RepeaterUserWithStringKeyPosts extends User
{
    protected $table = 'users';

    public function posts(): HasMany
    {
        return $this->hasMany(RepeaterStringKeyPost::class, 'author_id');
    }
}

class RepeaterStringKeyPost extends Post
{
    protected $table = 'posts';

    protected $primaryKey = 'title';

    protected $keyType = 'string';

    public $incrementing = false;
}

class TestComponentWithRepeater extends Livewire
{
    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Repeater::make('normal')
                    ->itemLabel(function (array $state) {
                        return $state['title'] . $state['category'];
                    })
                    ->schema([
                        TextInput::make('title'),
                        TextInput::make('category'),
                    ]),
                Repeater::make('simple')
                    ->simple(TextInput::make('title')),
                Repeater::make('parent')
                    ->itemLabel(fn (array $state) => $state['title'] . $state['category'])
                    ->schema([
                        TextInput::make('title'),
                        TextInput::make('category'),
                        Repeater::make('nested')
                            ->itemLabel(fn (array $state) => $state['name'])
                            ->schema([
                                TextInput::make('name'),
                            ]),
                        Repeater::make('nestedSimple')
                            ->simple(TextInput::make('name')),
                    ]),
            ])
            ->statePath('data');
    }

    public function render(): View
    {
        return view('forms.fixtures.form');
    }
}

class TestComponentWithEnumSelectRepeater extends Livewire
{
    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Repeater::make('alternatives')
                    ->schema([
                        Select::make('letter')
                            ->options(TestLetterEnum::class)
                            ->disableOptionsWhenSelectedInSiblingRepeaterItems(),
                    ]),
            ])
            ->statePath('data');
    }

    public function render(): View
    {
        return view('forms.fixtures.form');
    }
}

enum TestLetterEnum: string
{
    case A = 'A';
    case B = 'B';
    case C = 'C';
}
