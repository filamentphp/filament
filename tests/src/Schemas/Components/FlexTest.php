<?php

use Filament\Schemas\Components\Flex;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Schemas\Schema;
use Filament\Support\Enums\Alignment;
use Filament\Support\Enums\VerticalAlignment;
use Filament\Tests\TestCase;
use Livewire\Component;

use function Filament\Tests\livewire;

uses(TestCase::class);

it('can be constructed with a schema array', function (): void {
    $flex = Flex::make([]);

    expect($flex)->toBeInstanceOf(Flex::class);
});

describe('alignment', function (): void {
    it('returns `null` for `getAlignment()` by default', function (): void {
        $flex = Flex::make([]);

        expect($flex->getAlignment())->toBeNull();
    });

    it('can set `alignment()` with enum', function (): void {
        $flex = Flex::make([])->alignment(Alignment::Center);

        expect($flex->getAlignment())->toBe(Alignment::Center);
    });

    it('can set `alignment()` with a `Closure`', function (): void {
        $flex = Flex::make([])
            ->alignment(static fn (): Alignment => Alignment::End);

        expect($flex->getAlignment())->toBe(Alignment::End);
    });

    it('can use `alignStart()` shortcut', function (): void {
        $flex = Flex::make([])->alignStart();

        expect($flex->getAlignment())->toBe(Alignment::Start);
    });

    it('can use `alignCenter()` shortcut', function (): void {
        $flex = Flex::make([])->alignCenter();

        expect($flex->getAlignment())->toBe(Alignment::Center);
    });

    it('can use `alignEnd()` shortcut', function (): void {
        $flex = Flex::make([])->alignEnd();

        expect($flex->getAlignment())->toBe(Alignment::End);
    });

    it('can use `alignBetween()` shortcut', function (): void {
        $flex = Flex::make([])->alignBetween();

        expect($flex->getAlignment())->toBe(Alignment::Between);
    });
});

describe('vertical alignment', function (): void {
    it('returns `null` for `getVerticalAlignment()` by default', function (): void {
        $flex = Flex::make([]);

        expect($flex->getVerticalAlignment())->toBeNull();
    });

    it('can set `verticalAlignment()`', function (): void {
        $flex = Flex::make([])->verticalAlignment(VerticalAlignment::Center);

        expect($flex->getVerticalAlignment())->toBe(VerticalAlignment::Center);
    });

    it('can set `verticalAlignment()` with a `Closure`', function (): void {
        $flex = Flex::make([])
            ->verticalAlignment(static fn (): VerticalAlignment => VerticalAlignment::End);

        expect($flex->getVerticalAlignment())->toBe(VerticalAlignment::End);
    });

    it('can use `verticallyAlignStart()` shortcut', function (): void {
        $flex = Flex::make([])->verticallyAlignStart();

        expect($flex->getVerticalAlignment())->toBe(VerticalAlignment::Start);
    });

    it('can use `verticallyAlignCenter()` shortcut', function (): void {
        $flex = Flex::make([])->verticallyAlignCenter();

        expect($flex->getVerticalAlignment())->toBe(VerticalAlignment::Center);
    });

    it('can use `verticallyAlignEnd()` shortcut', function (): void {
        $flex = Flex::make([])->verticallyAlignEnd();

        expect($flex->getVerticalAlignment())->toBe(VerticalAlignment::End);
    });
});

describe('breakpoint', function (): void {
    it('returns `null` for `getFromBreakpoint()` by default', function (): void {
        $flex = Flex::make([]);

        expect($flex->getFromBreakpoint())->toBeNull();
    });

    it('can set `from()` breakpoint', function (): void {
        $flex = Flex::make([])->from('md');

        expect($flex->getFromBreakpoint())->toBe('md');
    });

    it('can set `from()` with a `Closure`', function (): void {
        $flex = Flex::make([])
            ->from(static fn (): string => 'lg');

        expect($flex->getFromBreakpoint())->toBe('lg');
    });
});

describe('rendering', function (): void {
    it('can render', function (): void {
        livewire(RenderFlex::class)->assertSuccessful();
    });

    it('can render with `alignment()`', function (): void {
        livewire(RenderFlexWithAlignment::class)->assertSuccessful();
    });

    it('can render with `alignment()` set via `Closure`', function (): void {
        livewire(RenderFlexWithClosureAlignment::class)->assertSuccessful();
    });

    it('can render with `verticalAlignment()`', function (): void {
        livewire(RenderFlexWithVerticalAlignment::class)->assertSuccessful();
    });

    it('can render with `verticalAlignment()` set via `Closure`', function (): void {
        livewire(RenderFlexWithClosureVerticalAlignment::class)->assertSuccessful();
    });

    it('can render with `from()` breakpoint', function (): void {
        livewire(RenderFlexWithFrom::class)->assertSuccessful();
    });

    it('can render with `from()` set via `Closure`', function (): void {
        livewire(RenderFlexWithClosureFrom::class)->assertSuccessful();
    });
});

class RenderFlex extends Component implements HasSchemas
{
    use InteractsWithSchemas;

    public function infolist(Schema $schema): Schema
    {
        return $schema->state([])->components([Flex::make([])]);
    }

    public function render(): string
    {
        return '<div>{{ $this->infolist }}</div>';
    }
}

class RenderFlexWithAlignment extends Component implements HasSchemas
{
    use InteractsWithSchemas;

    public function infolist(Schema $schema): Schema
    {
        return $schema->state([])->components([Flex::make([])->alignment(Alignment::Center)]);
    }

    public function render(): string
    {
        return '<div>{{ $this->infolist }}</div>';
    }
}

class RenderFlexWithClosureAlignment extends Component implements HasSchemas
{
    use InteractsWithSchemas;

    public function infolist(Schema $schema): Schema
    {
        return $schema->state([])->components([Flex::make([])->alignment(static fn (): Alignment => Alignment::End)]);
    }

    public function render(): string
    {
        return '<div>{{ $this->infolist }}</div>';
    }
}

class RenderFlexWithVerticalAlignment extends Component implements HasSchemas
{
    use InteractsWithSchemas;

    public function infolist(Schema $schema): Schema
    {
        return $schema->state([])->components([Flex::make([])->verticalAlignment(VerticalAlignment::Center)]);
    }

    public function render(): string
    {
        return '<div>{{ $this->infolist }}</div>';
    }
}

class RenderFlexWithClosureVerticalAlignment extends Component implements HasSchemas
{
    use InteractsWithSchemas;

    public function infolist(Schema $schema): Schema
    {
        return $schema->state([])->components([Flex::make([])->verticalAlignment(static fn (): VerticalAlignment => VerticalAlignment::End)]);
    }

    public function render(): string
    {
        return '<div>{{ $this->infolist }}</div>';
    }
}

class RenderFlexWithFrom extends Component implements HasSchemas
{
    use InteractsWithSchemas;

    public function infolist(Schema $schema): Schema
    {
        return $schema->state([])->components([Flex::make([])->from('md')]);
    }

    public function render(): string
    {
        return '<div>{{ $this->infolist }}</div>';
    }
}

class RenderFlexWithClosureFrom extends Component implements HasSchemas
{
    use InteractsWithSchemas;

    public function infolist(Schema $schema): Schema
    {
        return $schema->state([])->components([Flex::make([])->from(static fn (): string => 'lg')]);
    }

    public function render(): string
    {
        return '<div>{{ $this->infolist }}</div>';
    }
}
