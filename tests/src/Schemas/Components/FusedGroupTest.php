<?php

use Filament\Schemas\Components\FusedGroup;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Schemas\Schema;
use Filament\Tests\TestCase;
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
