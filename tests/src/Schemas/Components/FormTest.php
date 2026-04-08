<?php

use Filament\Actions\Action;
use Filament\Schemas\Components\Form;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Schemas\Schema;
use Filament\Tests\TestCase;
use Livewire\Component;

use function Filament\Tests\livewire;

uses(TestCase::class);

it('can be constructed with empty schema', function (): void {
    $form = Form::make();

    expect($form)->toBeInstanceOf(Form::class);
});

it('can be constructed with schema array', function (): void {
    $form = Form::make([]);

    expect($form)->toBeInstanceOf(Form::class);
});

describe('`action()` logic', function (): void {
    it('can set `action()` with an `Action`', function (): void {
        $action = Action::make('submit')->label('Save');
        $form = Form::make()->action($action);

        expect($form)->toBeInstanceOf(Form::class);
    });

    it('converts `Closure` to an `Action` named `submit` in `action()`', function (): void {
        $form = Form::make()->action(static fn () => null);

        expect($form)->toBeInstanceOf(Form::class);
    });

    it('can clear `action()` with `null`', function (): void {
        $form = Form::make()
            ->action(Action::make('submit'))
            ->action(null);

        expect($form)->toBeInstanceOf(Form::class);
    });
});

describe('Livewire submit handler', function (): void {
    it('returns `null` for `getLivewireSubmitHandler()` by default', function (): void {
        $form = Form::make();

        expect($form->getLivewireSubmitHandler())->toBeNull();
    });

    it('can set `livewireSubmitHandler()`', function (): void {
        $form = Form::make()
            ->livewireSubmitHandler('save');

        expect($form->getLivewireSubmitHandler())->toBe('save');
    });

    it('can set `livewireSubmitHandler()` with a `Closure`', function (): void {
        $form = Form::make()
            ->livewireSubmitHandler(static fn (): string => 'submitForm');

        expect($form->getLivewireSubmitHandler())->toBe('submitForm');
    });
});

describe('slot methods', function (): void {
    it('returns fluent `$this` from `header()`', function (): void {
        $form = Form::make();

        expect($form->header([]))->toBe($form);
    });

    it('returns fluent `$this` from `footer()`', function (): void {
        $form = Form::make();

        expect($form->footer([]))->toBe($form);
    });
});

describe('rendering', function (): void {
    it('can render', function (): void {
        livewire(RenderFormComponent::class)->assertSuccessful();
    });

    it('can render with `livewireSubmitHandler()`', function (): void {
        livewire(RenderFormWithSubmitHandler::class)->assertSuccessful();
    });

    it('can render with `livewireSubmitHandler()` set via `Closure`', function (): void {
        livewire(RenderFormWithClosureSubmitHandler::class)->assertSuccessful();
    });

    it('can render with `action()`', function (): void {
        livewire(RenderFormWithAction::class)->assertSuccessful();
    });
});

class RenderFormComponent extends Component implements HasSchemas
{
    use InteractsWithSchemas;

    public function infolist(Schema $schema): Schema
    {
        return $schema->state([])->components([Form::make()]);
    }

    public function render(): string
    {
        return '<div>{{ $this->infolist }}</div>';
    }
}

class RenderFormWithSubmitHandler extends Component implements HasSchemas
{
    use InteractsWithSchemas;

    public function infolist(Schema $schema): Schema
    {
        return $schema->state([])->components([Form::make()->livewireSubmitHandler('save')]);
    }

    public function render(): string
    {
        return '<div>{{ $this->infolist }}</div>';
    }
}

class RenderFormWithClosureSubmitHandler extends Component implements HasSchemas
{
    use InteractsWithSchemas;

    public function infolist(Schema $schema): Schema
    {
        return $schema->state([])->components([Form::make()->livewireSubmitHandler(static fn (): string => 'submitForm')]);
    }

    public function render(): string
    {
        return '<div>{{ $this->infolist }}</div>';
    }
}

class RenderFormWithAction extends Component implements HasSchemas
{
    use InteractsWithSchemas;

    public function infolist(Schema $schema): Schema
    {
        return $schema->state([])->components([Form::make()->action(Action::make('submit')->label('Save'))]);
    }

    public function render(): string
    {
        return '<div>{{ $this->infolist }}</div>';
    }
}
