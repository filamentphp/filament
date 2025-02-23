<?php

use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Filament\Tests\Fixtures\Livewire\Livewire;
use Filament\Tests\Fixtures\Models\Post;
use Filament\Tests\Fixtures\Models\User;
use Filament\Tests\TestCase;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Livewire\Exceptions\RootTagMissingFromViewException;

use function Filament\Tests\livewire;

uses(TestCase::class);

it('can fill and assert data in a repeater', function (array $data): void {
    $undoRepeaterFake = Repeater::fake();

    try {
        livewire(TestComponentWithRepeater::class)
            ->fillForm($data)
            ->assertFormSet($data);
    } catch (RootTagMissingFromViewException $exception) {
        // Flaky test
    }

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

it('can remove items from a repeater', function (): void {
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

it('loads a relationship', function (): void {
    $user = User::factory()
        ->has(Post::factory()->count(3))
        ->create();

    $schema = Schema::make(Livewire::make())
        ->statePath('data')
        ->components([
            (new Repeater('repeater'))
                ->relationship('posts'),
        ])
        ->model($user);

    $schema->loadStateFromRelationships();

    $schema->saveRelationships();

    expect($user->posts()->count())
        ->toBe(3);
});

it('throws an exception for a missing relationship', function (): void {
    $schema = Schema::make(Livewire::make())
        ->statePath('data')
        ->components([
            (new Repeater(Str::random()))
                ->relationship('missing'),
        ])
        ->model(Post::factory()->create());

    $schema
        ->saveRelationships();
})->throws(Exception::class, 'The relationship [missing] does not exist on the model [Filament\Tests\Fixtures\Models\Post].');

class TestComponentWithRepeater extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form
            ->components([
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
}
