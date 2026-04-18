<?php

use Filament\Schemas\Components\Fieldset;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Schemas\Schema;
use Filament\Tests\TestCase;
use Livewire\Component;

use function Filament\Tests\livewire;

uses(TestCase::class);

it('can be constructed with a label', function (): void {
    $fieldset = Fieldset::make('Details');

    expect($fieldset->getLabel())->toBe('Details');
});

it('can be constructed with a `Closure` label', function (): void {
    $fieldset = Fieldset::make(static fn (): string => 'Dynamic');

    expect($fieldset->getLabel())->toBe('Dynamic');
});

it('can be constructed without a label', function (): void {
    $fieldset = Fieldset::make();

    expect($fieldset->getLabel())->toBeNull();
});

it('returns `false` from `isRequired()`', function (): void {
    $fieldset = Fieldset::make('Details');

    expect($fieldset->isRequired())->toBeFalse();
});

it('defaults `isContained()` to `true`', function (): void {
    $fieldset = Fieldset::make('Details');

    expect($fieldset->isContained())->toBeTrue();
});

it('can set `contained()` to `false`', function (): void {
    $fieldset = Fieldset::make('Details')->contained(false);

    expect($fieldset->isContained())->toBeFalse();
});

it('can set `contained()` with a `Closure`', function (): void {
    $fieldset = Fieldset::make('Details')
        ->contained(static fn (): bool => false);

    expect($fieldset->isContained())->toBeFalse();
});

describe('rendering', function (): void {
    it('can render with a label', function (): void {
        livewire(RenderFieldset::class)->assertSuccessful()->assertSee('Details');
    });

    it('can render with a `Closure` label', function (): void {
        livewire(RenderFieldsetWithClosureLabel::class)->assertSuccessful()->assertSee('Dynamic');
    });

    it('can render with `contained(false)`', function (): void {
        livewire(RenderFieldsetWithContainedFalse::class)->assertSuccessful();
    });

    it('can render with `contained()` set via `Closure`', function (): void {
        livewire(RenderFieldsetWithClosureContained::class)->assertSuccessful();
    });
});

class RenderFieldset extends Component implements HasSchemas
{
    use InteractsWithSchemas;

    public function infolist(Schema $schema): Schema
    {
        return $schema->state([])->components([Fieldset::make('Details')]);
    }

    public function render(): string
    {
        return '<div>{{ $this->infolist }}</div>';
    }
}

class RenderFieldsetWithClosureLabel extends Component implements HasSchemas
{
    use InteractsWithSchemas;

    public function infolist(Schema $schema): Schema
    {
        return $schema->state([])->components([Fieldset::make(static fn (): string => 'Dynamic')]);
    }

    public function render(): string
    {
        return '<div>{{ $this->infolist }}</div>';
    }
}

class RenderFieldsetWithContainedFalse extends Component implements HasSchemas
{
    use InteractsWithSchemas;

    public function infolist(Schema $schema): Schema
    {
        return $schema->state([])->components([Fieldset::make('Details')->contained(false)]);
    }

    public function render(): string
    {
        return '<div>{{ $this->infolist }}</div>';
    }
}

class RenderFieldsetWithClosureContained extends Component implements HasSchemas
{
    use InteractsWithSchemas;

    public function infolist(Schema $schema): Schema
    {
        return $schema->state([])->components([Fieldset::make('Details')->contained(static fn (): bool => false)]);
    }

    public function render(): string
    {
        return '<div>{{ $this->infolist }}</div>';
    }
}
