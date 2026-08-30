<?php

use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\FusedGroup;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Schemas\Schema;
use Filament\Tests\Fixtures\Livewire\Livewire;
use Filament\Tests\TestCase;
use Illuminate\Support\MessageBag;
use Illuminate\Support\ViewErrorBag;
use Livewire\Component;

use function Filament\Tests\livewire;

uses(TestCase::class);

it('can be constructed with empty schema', function (): void {
    $group = FusedGroup::make();

    expect($group)->toBeInstanceOf(FusedGroup::class);
});

describe('label', function (): void {
    it('returns `null` for `getLabel()` by default', function (): void {
        $group = FusedGroup::make();

        expect($group->getLabel())->toBeNull();
    });

    it('can set `label()`', function (): void {
        $group = FusedGroup::make()->label('Contact Info');

        expect($group->getLabel())->toBe('Contact Info');
    });

    it('can set `label()` with a `Closure`', function (): void {
        $group = FusedGroup::make()
            ->label(static fn (): string => 'Dynamic');

        expect($group->getLabel())->toBe('Dynamic');
    });

    it('defaults `isLabelHidden()` to `false`', function (): void {
        $group = FusedGroup::make();

        expect($group->isLabelHidden())->toBeFalse();
    });

    it('can set `hiddenLabel()`', function (): void {
        $group = FusedGroup::make()->hiddenLabel();

        expect($group->isLabelHidden())->toBeTrue();
    });
});

describe('slot methods', function (): void {
    it('returns fluent `$this` from all slot methods', function (): void {
        $group = FusedGroup::make();

        expect($group->aboveLabel([]))->toBe($group);
        expect($group->belowLabel([]))->toBe($group);
        expect($group->beforeLabel([]))->toBe($group);
        expect($group->afterLabel([]))->toBe($group);
        expect($group->aboveContent([]))->toBe($group);
        expect($group->belowContent([]))->toBe($group);
        expect($group->beforeContent([]))->toBe($group);
        expect($group->afterContent([]))->toBe($group);
        expect($group->aboveErrorMessage([]))->toBe($group);
        expect($group->belowErrorMessage([]))->toBe($group);
    });
});

describe('required detection', function (): void {
    it('returns `false` from `isRequired()` when no child fields', function (): void {
        $group = FusedGroup::make();

        expect($group->isRequired())->toBeFalse();
    });
});

describe('rendering', function (): void {
    it('can render', function (): void {
        livewire(RenderFusedGroup::class)->assertSuccessful();
    });

    it('can render with `label()`', function (): void {
        livewire(RenderFusedGroupWithLabel::class)->assertSuccessful()->assertSee('Contact Info');
    });

    it('can render with `label()` set via `Closure`', function (): void {
        livewire(RenderFusedGroupWithClosureLabel::class)->assertSuccessful()->assertSee('Dynamic');
    });

    it('can render with `hiddenLabel()`', function (): void {
        livewire(RenderFusedGroupWithHiddenLabel::class)->assertSuccessful();
    });

    it('renders a single error as `<p>` even when child uses `showAllValidationMessages()`', function (): void {
        $errors = new ViewErrorBag;
        $errors->put('default', new MessageBag([
            'data.email' => ['The email field is required.'],
        ]));

        view()->share('errors', $errors);

        try {
            $livewire = Livewire::make();

            Schema::make($livewire)
                ->statePath('data')
                ->components([
                    $group = FusedGroup::make()
                        ->components([
                            TextInput::make('email')->showAllValidationMessages(),
                        ]),
                ])
                ->fill();

            $html = $group->toHtml();

            expect($html)->toContain('fi-fo-field-wrp-error-message');
            expect($html)->not->toContain('fi-fo-field-wrp-error-list');
        } finally {
            view()->share('errors', new ViewErrorBag);
        }
    });

    it('renders every per-element error from a child using `showAllValidationMessages()`', function (): void {
        $errors = new ViewErrorBag;
        $errors->put('default', new MessageBag([
            'data.choices.0' => ['The first choice is invalid.'],
            'data.choices.1' => ['The second choice is invalid.'],
        ]));

        view()->share('errors', $errors);

        try {
            $livewire = Livewire::make();

            Schema::make($livewire)
                ->statePath('data')
                ->components([
                    $group = FusedGroup::make()
                        ->components([
                            CheckboxList::make('choices')
                                ->options(['alpha' => 'Alpha'])
                                ->showAllValidationMessages(),
                        ]),
                ])
                ->fill();

            expect($group->toHtml())
                ->toContain('The first choice is invalid.')
                ->toContain('The second choice is invalid.');
        } finally {
            view()->share('errors', new ViewErrorBag);
        }
    });
});

class RenderFusedGroup extends Component implements HasSchemas
{
    use InteractsWithSchemas;

    public function infolist(Schema $schema): Schema
    {
        return $schema->state([])->components([FusedGroup::make()]);
    }

    public function render(): string
    {
        return '<div>{{ $this->infolist }}</div>';
    }
}

class RenderFusedGroupWithLabel extends Component implements HasSchemas
{
    use InteractsWithSchemas;

    public function infolist(Schema $schema): Schema
    {
        return $schema->state([])->components([FusedGroup::make()->label('Contact Info')]);
    }

    public function render(): string
    {
        return '<div>{{ $this->infolist }}</div>';
    }
}

class RenderFusedGroupWithClosureLabel extends Component implements HasSchemas
{
    use InteractsWithSchemas;

    public function infolist(Schema $schema): Schema
    {
        return $schema->state([])->components([FusedGroup::make()->label(static fn (): string => 'Dynamic')]);
    }

    public function render(): string
    {
        return '<div>{{ $this->infolist }}</div>';
    }
}

class RenderFusedGroupWithHiddenLabel extends Component implements HasSchemas
{
    use InteractsWithSchemas;

    public function infolist(Schema $schema): Schema
    {
        return $schema->state([])->components([FusedGroup::make()->label('Hidden')->hiddenLabel()]);
    }

    public function render(): string
    {
        return '<div>{{ $this->infolist }}</div>';
    }
}
