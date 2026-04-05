<?php

use Filament\Schemas\Components\EmptyState;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tests\TestCase;
use Livewire\Component;

use function Filament\Tests\livewire;

uses(TestCase::class);

describe('heading', function (): void {
    it('can be constructed with a string heading', function (): void {
        $state = EmptyState::make('No records found');

        expect($state->getHeading())->toBe('No records found');
    });

    it('can set `heading()` with a `Closure`', function (): void {
        $state = EmptyState::make('Initial')
            ->heading(static fn (): string => 'Dynamic');

        expect($state->getHeading())->toBe('Dynamic');
    });
});

describe('description', function (): void {
    it('returns `null` for `getDescription()` by default', function (): void {
        $state = EmptyState::make('No records');

        expect($state->getDescription())->toBeNull();
    });

    it('can set `description()`', function (): void {
        $state = EmptyState::make('No records')
            ->description('Try adjusting your filters.');

        expect($state->getDescription())->toBe('Try adjusting your filters.');
    });

    it('can set `description()` with a `Closure`', function (): void {
        $state = EmptyState::make('No records')
            ->description(static fn (): string => 'Dynamic desc');

        expect($state->getDescription())->toBe('Dynamic desc');
    });
});

describe('icon', function (): void {
    it('returns `null` for `getIcon()` by default', function (): void {
        $state = EmptyState::make('No records');

        expect($state->getIcon())->toBeNull();
    });

    it('can set `icon()`', function (): void {
        $state = EmptyState::make('No records')
            ->icon(Heroicon::DocumentText);

        expect($state->getIcon())->toBe(Heroicon::DocumentText);
    });

    it('can set `icon()` with a `Closure`', function (): void {
        $state = EmptyState::make('No records')
            ->icon(static fn () => Heroicon::FaceFrown);

        expect($state->getIcon())->toBe(Heroicon::FaceFrown);
    });
});

describe('containment', function (): void {
    it('defaults `isContained()` to `true`', function (): void {
        $state = EmptyState::make('No records');

        expect($state->isContained())->toBeTrue();
    });

    it('can set `contained()` to `false`', function (): void {
        $state = EmptyState::make('No records')->contained(false);

        expect($state->isContained())->toBeFalse();
    });
});

describe('compactness', function (): void {
    it('defaults `isCompact()` to `false`', function (): void {
        $state = EmptyState::make('No records');

        expect($state->isCompact())->toBeFalse();
    });

    it('can set `compact()`', function (): void {
        $state = EmptyState::make('No records')->compact();

        expect($state->isCompact())->toBeTrue();
    });

    it('can set `compact()` with a `Closure`', function (): void {
        $state = EmptyState::make('No records')
            ->compact(static fn (): bool => true);

        expect($state->isCompact())->toBeTrue();
    });
});

it('returns fluent `$this` from `footer()`', function (): void {
    $state = EmptyState::make('No records');

    expect($state->footer([]))->toBe($state);
});

describe('rendering', function (): void {
    it('can render', function (): void {
        livewire(RenderEmptyState::class)->assertSuccessful()->assertSee('No records found');
    });

    it('can render with `heading()` set via `Closure`', function (): void {
        livewire(RenderEmptyStateWithClosureHeading::class)->assertSuccessful()->assertSee('Dynamic');
    });

    it('can render with `description()`', function (): void {
        livewire(RenderEmptyStateWithDescription::class)->assertSuccessful()->assertSee('Try adjusting');
    });

    it('can render with `description()` set via `Closure`', function (): void {
        livewire(RenderEmptyStateWithClosureDescription::class)->assertSuccessful()->assertSee('Dynamic desc');
    });

    it('can render with `icon()`', function (): void {
        livewire(RenderEmptyStateWithIcon::class)->assertSuccessful();
    });

    it('can render with `icon()` set via `Closure`', function (): void {
        livewire(RenderEmptyStateWithClosureIcon::class)->assertSuccessful();
    });

    it('can render with `contained(false)`', function (): void {
        livewire(RenderEmptyStateWithContainedFalse::class)->assertSuccessful();
    });

    it('can render with `compact()`', function (): void {
        livewire(RenderEmptyStateWithCompact::class)->assertSuccessful();
    });

    it('can render with `compact()` set via `Closure`', function (): void {
        livewire(RenderEmptyStateWithClosureCompact::class)->assertSuccessful();
    });
});

class RenderEmptyState extends Component implements HasSchemas
{
    use InteractsWithSchemas;

    public function infolist(Schema $schema): Schema
    {
        return $schema->state([])->components([EmptyState::make('No records found')]);
    }

    public function render(): string
    {
        return '<div>{{ $this->infolist }}</div>';
    }
}

class RenderEmptyStateWithClosureHeading extends Component implements HasSchemas
{
    use InteractsWithSchemas;

    public function infolist(Schema $schema): Schema
    {
        return $schema->state([])->components([EmptyState::make(static fn (): string => 'Dynamic')]);
    }

    public function render(): string
    {
        return '<div>{{ $this->infolist }}</div>';
    }
}

class RenderEmptyStateWithDescription extends Component implements HasSchemas
{
    use InteractsWithSchemas;

    public function infolist(Schema $schema): Schema
    {
        return $schema->state([])->components([EmptyState::make('No records')->description('Try adjusting')]);
    }

    public function render(): string
    {
        return '<div>{{ $this->infolist }}</div>';
    }
}

class RenderEmptyStateWithClosureDescription extends Component implements HasSchemas
{
    use InteractsWithSchemas;

    public function infolist(Schema $schema): Schema
    {
        return $schema->state([])->components([EmptyState::make('No records')->description(static fn (): string => 'Dynamic desc')]);
    }

    public function render(): string
    {
        return '<div>{{ $this->infolist }}</div>';
    }
}

class RenderEmptyStateWithIcon extends Component implements HasSchemas
{
    use InteractsWithSchemas;

    public function infolist(Schema $schema): Schema
    {
        return $schema->state([])->components([EmptyState::make('No records')->icon(Heroicon::DocumentText)]);
    }

    public function render(): string
    {
        return '<div>{{ $this->infolist }}</div>';
    }
}

class RenderEmptyStateWithClosureIcon extends Component implements HasSchemas
{
    use InteractsWithSchemas;

    public function infolist(Schema $schema): Schema
    {
        return $schema->state([])->components([EmptyState::make('No records')->icon(static fn () => Heroicon::FaceFrown)]);
    }

    public function render(): string
    {
        return '<div>{{ $this->infolist }}</div>';
    }
}

class RenderEmptyStateWithContainedFalse extends Component implements HasSchemas
{
    use InteractsWithSchemas;

    public function infolist(Schema $schema): Schema
    {
        return $schema->state([])->components([EmptyState::make('No records')->contained(false)]);
    }

    public function render(): string
    {
        return '<div>{{ $this->infolist }}</div>';
    }
}

class RenderEmptyStateWithCompact extends Component implements HasSchemas
{
    use InteractsWithSchemas;

    public function infolist(Schema $schema): Schema
    {
        return $schema->state([])->components([EmptyState::make('No records')->compact()]);
    }

    public function render(): string
    {
        return '<div>{{ $this->infolist }}</div>';
    }
}

class RenderEmptyStateWithClosureCompact extends Component implements HasSchemas
{
    use InteractsWithSchemas;

    public function infolist(Schema $schema): Schema
    {
        return $schema->state([])->components([EmptyState::make('No records')->compact(static fn (): bool => true)]);
    }

    public function render(): string
    {
        return '<div>{{ $this->infolist }}</div>';
    }
}
