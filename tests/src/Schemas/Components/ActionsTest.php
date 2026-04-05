<?php

use Filament\Actions\Action;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Schemas\Schema;
use Filament\Tests\TestCase;
use Livewire\Component;

use function Filament\Tests\livewire;

uses(TestCase::class);

describe('construction', function (): void {
    it('can be constructed with an array of actions', function (): void {
        $actions = Actions::make([
            Action::make('save'),
            Action::make('cancel'),
        ]);

        expect($actions)->toBeInstanceOf(Actions::class);
    });
});

describe('full width', function (): void {
    it('defaults `isFullWidth()` to `false`', function (): void {
        $actions = Actions::make([Action::make('save')]);

        expect($actions->isFullWidth())->toBeFalse();
    });

    it('can set `fullWidth()`', function (): void {
        $actions = Actions::make([Action::make('save')])->fullWidth();

        expect($actions->isFullWidth())->toBeTrue();
    });

    it('can set `fullWidth()` with a `Closure`', function (): void {
        $actions = Actions::make([Action::make('save')])
            ->fullWidth(static fn (): bool => true);

        expect($actions->isFullWidth())->toBeTrue();
    });

    it('can undo `fullWidth()` with `false`', function (): void {
        $actions = Actions::make([Action::make('save')])
            ->fullWidth()
            ->fullWidth(false);

        expect($actions->isFullWidth())->toBeFalse();
    });
});

describe('sticky', function (): void {
    it('defaults `isSticky()` to `false`', function (): void {
        $actions = Actions::make([Action::make('save')]);

        expect($actions->isSticky())->toBeFalse();
    });

    it('can set `sticky()`', function (): void {
        $actions = Actions::make([Action::make('save')])->sticky();

        expect($actions->isSticky())->toBeTrue();
    });

    it('can set `sticky()` with a `Closure`', function (): void {
        $actions = Actions::make([Action::make('save')])
            ->sticky(static fn (): bool => true);

        expect($actions->isSticky())->toBeTrue();
    });
});

describe('slot methods', function (): void {
    it('returns fluent `$this` from slot methods', function (): void {
        $actions = Actions::make([Action::make('save')]);

        expect($actions->beforeLabel([]))->toBe($actions);
        expect($actions->afterLabel([]))->toBe($actions);
        expect($actions->aboveContent([]))->toBe($actions);
        expect($actions->belowContent([]))->toBe($actions);
    });
});

describe('rendering', function (): void {
    it('can render', function (): void {
        livewire(RenderActionsComponent::class)->assertSuccessful();
    });

    it('can render with `fullWidth()`', function (): void {
        livewire(RenderActionsWithFullWidth::class)->assertSuccessful();
    });

    it('can render with `fullWidth()` set via `Closure`', function (): void {
        livewire(RenderActionsWithClosureFullWidth::class)->assertSuccessful();
    });

    it('can render with `sticky()`', function (): void {
        livewire(RenderActionsWithSticky::class)->assertSuccessful();
    });

    it('can render with `sticky()` set via `Closure`', function (): void {
        livewire(RenderActionsWithClosureSticky::class)->assertSuccessful();
    });
});

class RenderActionsComponent extends Component implements HasSchemas
{
    use InteractsWithSchemas;

    public function infolist(Schema $schema): Schema
    {
        return $schema->state([])->components([
            Actions::make([Action::make('save')]),
        ]);
    }

    public function render(): string
    {
        return '<div>{{ $this->infolist }}</div>';
    }
}

class RenderActionsWithFullWidth extends Component implements HasSchemas
{
    use InteractsWithSchemas;

    public function infolist(Schema $schema): Schema
    {
        return $schema->state([])->components([
            Actions::make([Action::make('save')])->fullWidth(),
        ]);
    }

    public function render(): string
    {
        return '<div>{{ $this->infolist }}</div>';
    }
}

class RenderActionsWithClosureFullWidth extends Component implements HasSchemas
{
    use InteractsWithSchemas;

    public function infolist(Schema $schema): Schema
    {
        return $schema->state([])->components([
            Actions::make([Action::make('save')])->fullWidth(static fn (): bool => true),
        ]);
    }

    public function render(): string
    {
        return '<div>{{ $this->infolist }}</div>';
    }
}

class RenderActionsWithSticky extends Component implements HasSchemas
{
    use InteractsWithSchemas;

    public function infolist(Schema $schema): Schema
    {
        return $schema->state([])->components([
            Actions::make([Action::make('save')])->sticky(),
        ]);
    }

    public function render(): string
    {
        return '<div>{{ $this->infolist }}</div>';
    }
}

class RenderActionsWithClosureSticky extends Component implements HasSchemas
{
    use InteractsWithSchemas;

    public function infolist(Schema $schema): Schema
    {
        return $schema->state([])->components([
            Actions::make([Action::make('save')])->sticky(static fn (): bool => true),
        ]);
    }

    public function render(): string
    {
        return '<div>{{ $this->infolist }}</div>';
    }
}
