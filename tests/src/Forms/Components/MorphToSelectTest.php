<?php

namespace Filament\Tests\Forms\Components;

use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Components\MorphToSelect;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Schemas\Schema;
use Filament\Tests\Fixtures\Models\Image;
use Filament\Tests\Fixtures\Models\Post;
use Filament\Tests\Fixtures\Models\User;
use Filament\Tests\TestCase;
use Illuminate\Contracts\View\View;
use InvalidArgumentException;
use Livewire\Component;

use function Filament\Tests\livewire;

uses(TestCase::class);

it('can be instantiated', function (): void {
    $component = MorphToSelect::make('commentable');

    expect($component->getName())->toBe('commentable');
});

it('can set `types()`', function (): void {
    $component = MorphToSelect::make('commentable')
        ->types([
            MorphToSelect\Type::make(Post::class)->titleAttribute('title'),
            MorphToSelect\Type::make(User::class)->titleAttribute('name'),
        ]);
    $types = $component->getTypes();
    expect($types)->toHaveCount(2);
    expect(array_keys($types))->toBe([Post::class, User::class]);
});

it('can set `typeSelectToggleButtons()`', function (): void {
    $component = MorphToSelect::make('commentable');
    expect($component->hasTypeSelectToggleButtons())->toBeFalse();
    $component->typeSelectToggleButtons();
    expect($component->hasTypeSelectToggleButtons())->toBeTrue();
});

it('can set `optionsLimit()`', function (): void {
    $component = MorphToSelect::make('commentable')->optionsLimit(25);
    expect($component->getOptionsLimit())->toBe(25);
});

it('has a default `optionsLimit()` of `50`', function (): void {
    $component = MorphToSelect::make('commentable');
    expect($component->getOptionsLimit())->toBe(50);
});

it('can set `required()`', function (): void {
    $component = MorphToSelect::make('commentable');
    expect($component->isRequired())->toBeFalse();
    $component->required();
    expect($component->isRequired())->toBeTrue();
});

it('can set `modifyTypeSelectUsing()` callback', function (): void {
    $callback = static fn () => null;
    $component = MorphToSelect::make('commentable')->modifyTypeSelectUsing($callback);
    expect($component->getModifyTypeSelectUsingCallback())->toBe($callback);
});

it('can set `modifyKeySelectUsing()` callback', function (): void {
    $callback = static fn () => null;
    $component = MorphToSelect::make('commentable')->modifyKeySelectUsing($callback);
    expect($component->getModifyKeySelectUsingCallback())->toBe($callback);
});

it('throws `InvalidArgumentException` when name is blank', function (): void {
    MorphToSelect::make('');
})->throws(InvalidArgumentException::class);

describe('label auto-generation', function (): void {
    it('auto-generates label from kebab name', function (): void {
        $component = MorphToSelect::make('commentable-type');

        expect($component->getLabel())->toBe('Commentable type');
    });

    it('auto-generates label from underscored name', function (): void {
        $component = MorphToSelect::make('taggable_item');

        expect($component->getLabel())->toBe('Taggable item');
    });

    it('uses custom label over auto-generated', function (): void {
        $component = MorphToSelect::make('commentable')
            ->label('Custom Label');

        expect($component->getLabel())->toBe('Custom Label');
    });

    it('uses `Closure` label', function (): void {
        $component = MorphToSelect::make('commentable')
            ->label(static fn (): string => 'Dynamic Label');

        expect($component->getLabel())->toBe('Dynamic Label');
    });
});

describe('Closure support', function (): void {
    it('can set `required()` with a `Closure`', function (): void {
        $component = MorphToSelect::make('commentable')
            ->required(static fn (): bool => true);

        expect($component->isRequired())->toBeTrue();
    });

    it('can set `optionsLimit()` with a `Closure`', function (): void {
        $component = MorphToSelect::make('commentable')
            ->optionsLimit(static fn (): int => 100);

        expect($component->getOptionsLimit())->toBe(100);
    });

    it('can set `typeSelectToggleButtons()` with a `Closure`', function (): void {
        $component = MorphToSelect::make('commentable')
            ->typeSelectToggleButtons(static fn (): bool => true);

        expect($component->hasTypeSelectToggleButtons())->toBeTrue();
    });
});

describe('validation', function (): void {
    it('rejects an existing value excluded by `modifyOptionsQueryUsing` on a `MorphTo` type', function (): void {
        $inScopePost = Post::factory()->create(['title' => 'Alpha Article']);
        $outOfScopePost = Post::factory()->create(['title' => 'Beta Article']);

        livewire(TestComponentWithMorphToSelectAndModifyQuery::class)
            ->fillForm([
                'imageable_type' => Post::class,
                'imageable_id' => (string) $inScopePost->id,
            ])
            ->call('save')
            ->assertHasNoFormErrors();

        livewire(TestComponentWithMorphToSelectAndModifyQuery::class)
            ->fillForm([
                'imageable_type' => Post::class,
                'imageable_id' => (string) $outOfScopePost->id,
            ])
            ->call('save')
            ->assertHasFormErrors(['imageable_id' => ['in']]);
    });
});

describe('modifier callback clearing', function (): void {
    it('can clear `modifyTypeSelectUsing()` with `null`', function (): void {
        $component = MorphToSelect::make('commentable')
            ->modifyTypeSelectUsing(static fn () => null)
            ->modifyTypeSelectUsing(null);

        expect($component->getModifyTypeSelectUsingCallback())->toBeNull();
    });

    it('can clear `modifyKeySelectUsing()` with `null`', function (): void {
        $component = MorphToSelect::make('commentable')
            ->modifyKeySelectUsing(static fn () => null)
            ->modifyKeySelectUsing(null);

        expect($component->getModifyKeySelectUsingCallback())->toBeNull();
    });
});

class TestComponentWithMorphToSelectAndModifyQuery extends Component implements HasActions, HasSchemas
{
    use InteractsWithActions;
    use InteractsWithSchemas;

    public $data = [];

    public function mount(): void
    {
        $this->form->fill();
    }

    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                MorphToSelect::make('imageable')
                    ->types([
                        MorphToSelect\Type::make(Post::class)
                            ->titleAttribute('title')
                            ->modifyOptionsQueryUsing(fn ($query) => $query->where('title', 'like', 'Alpha%')),
                    ])
                    ->preload(),
            ])
            ->model(Image::class)
            ->statePath('data');
    }

    public function save(): void
    {
        $this->form->getState();
    }

    public function render(): View
    {
        return view('livewire.form');
    }
}
