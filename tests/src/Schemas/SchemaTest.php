<?php

use Filament\Schemas\Components\Component;
use Filament\Schemas\Components\Text;
use Filament\Schemas\Schema;
use Filament\Support\Enums\Alignment;
use Filament\Tests\Fixtures\Livewire\Livewire;
use Filament\Tests\Fixtures\Models\User;
use Filament\Tests\TestCase;

uses(TestCase::class);

it('can be constructed with `make()`', function (): void {
    $schema = Schema::make(Livewire::make());

    expect($schema)->toBeInstanceOf(Schema::class);
});

it('can be constructed without a Livewire component', function (): void {
    $schema = Schema::make();

    expect($schema)->toBeInstanceOf(Schema::class);
});

describe('alignment factory methods', function (): void {
    it('creates a start-aligned schema with `start()`', function (): void {
        $schema = Schema::start([]);

        expect($schema->getAlignment())->toBe(Alignment::Start);
    });

    it('creates an end-aligned schema with `end()`', function (): void {
        $schema = Schema::end([]);

        expect($schema->getAlignment())->toBe(Alignment::End);
    });

    it('creates a center-aligned schema with `center()`', function (): void {
        $schema = Schema::center([]);

        expect($schema->getAlignment())->toBe(Alignment::Center);
    });

    it('creates a between-aligned schema with `between()`', function (): void {
        $schema = Schema::between([]);

        expect($schema->getAlignment())->toBe(Alignment::Between);
    });
});

describe('`toEmbeddedHtml()` rendering', function (): void {
    it('returns empty string when schema is hidden', function (): void {
        $schema = Schema::make(Livewire::make())
            ->components([])
            ->hidden();

        expect($schema->toEmbeddedHtml())->toBe('');
    });

    it('returns empty string when no visible components exist', function (): void {
        $schema = Schema::make(Livewire::make())
            ->components([
                (new Component)->hidden(),
            ]);

        expect($schema->toEmbeddedHtml())->toBe('');
    });

    it('renders HTML with visible components', function (): void {
        $schema = Schema::make(Livewire::make())
            ->statePath('data')
            ->components([
                Text::make('Hello'),
            ]);

        $html = $schema->toEmbeddedHtml();

        expect($html)->toContain('<div');
        expect($html)->toContain('fi-sc');
    });

    it('includes alignment class in rendered HTML', function (): void {
        $schema = Schema::make(Livewire::make())
            ->components([
                Text::make('Hello'),
            ])
            ->alignStart();

        $html = $schema->toEmbeddedHtml();

        expect($html)->toContain('fi-align-start');
    });

    it('includes inline class when schema is inline', function (): void {
        $schema = Schema::make(Livewire::make())
            ->components([
                Text::make('Hello'),
            ])
            ->inline();

        $html = $schema->toEmbeddedHtml();

        expect($html)->toContain('fi-inline');
    });

    it('adds `fi-growable` to components with `grow()` in inline schemas', function (): void {
        $growableSchema = Schema::make(Livewire::make())
            ->components([
                Text::make('Grows')->grow(),
            ])
            ->inline();

        $schema = Schema::make(Livewire::make())
            ->components([
                Text::make('Does not grow'),
            ])
            ->inline();

        expect($growableSchema->toEmbeddedHtml())->toContain('fi-growable')
            ->and($schema->toEmbeddedHtml())->not->toContain('fi-growable');
    });

    it('includes gap class when schema has gap', function (): void {
        $schema = Schema::make(Livewire::make())
            ->components([
                Text::make('Hello'),
            ])
            ->gap();

        $html = $schema->toEmbeddedHtml();

        expect($html)->toContain('fi-sc-has-gap');
    });
});

describe('state path', function (): void {
    it('can set `statePath()`', function (): void {
        $schema = Schema::make(Livewire::make())
            ->statePath('data');

        expect($schema->getStatePath())->toBe('data');
    });

    it('returns empty string for `getStatePath()` by default', function (): void {
        $schema = Schema::make(Livewire::make());

        expect($schema->getStatePath())->toBe('');
    });
});

describe('model', function (): void {
    it('returns `null` for `getModel()` by default', function (): void {
        $schema = Schema::make(Livewire::make());

        expect($schema->getModel())->toBeNull();
    });

    it('can set `model()` with a class string', function (): void {
        $schema = Schema::make(Livewire::make())
            ->model(User::class);

        expect($schema->getModel())->toBe(User::class);
    });
});

describe('operation', function (): void {
    it('can set `operation()`', function (): void {
        $schema = Schema::make(Livewire::make())
            ->operation('create');

        expect($schema->getOperation())->toBe('create');
    });
});

describe('disabled state', function (): void {
    it('defaults `isDisabled()` to `false`', function (): void {
        $schema = Schema::make(Livewire::make());

        expect($schema->isDisabled())->toBeFalse();
    });

    it('can set `disabled()`', function (): void {
        $schema = Schema::make(Livewire::make())
            ->disabled();

        expect($schema->isDisabled())->toBeTrue();
    });
});

describe('columns', function (): void {
    it('can set `columns()` with an int (maps to `lg` breakpoint)', function (): void {
        $schema = Schema::make(Livewire::make())
            ->columns(3);

        expect($schema->getColumns('lg'))->toBe(3);
    });

    it('can set `columns()` with a responsive array', function (): void {
        $schema = Schema::make(Livewire::make())
            ->columns(['sm' => 1, 'lg' => 3]);

        expect($schema->getColumns('sm'))->toBe(1);
        expect($schema->getColumns('lg'))->toBe(3);
    });

    it('reports `hasCustomColumns()` as `false` by default', function (): void {
        $schema = Schema::make(Livewire::make());

        expect($schema->hasCustomColumns())->toBeFalse();
    });

    it('reports `hasCustomColumns()` as `true` after `columns()` is set', function (): void {
        $schema = Schema::make(Livewire::make())
            ->columns(2);

        expect($schema->hasCustomColumns())->toBeTrue();
    });
});

describe('inline labels', function (): void {
    it('returns `null` for `hasInlineLabel()` by default', function (): void {
        $schema = Schema::make(Livewire::make());

        expect($schema->hasInlineLabel())->toBeNull();
    });

    it('can set `inlineLabel()`', function (): void {
        $schema = Schema::make(Livewire::make())
            ->inlineLabel();

        expect($schema->hasInlineLabel())->toBeTrue();
    });

    it('can set `inlineLabel()` to `false`', function (): void {
        $schema = Schema::make(Livewire::make())
            ->inlineLabel(false);

        expect($schema->hasInlineLabel())->toBeFalse();
    });
});

describe('key', function (): void {
    it('can set `key()`', function (): void {
        $schema = Schema::make(Livewire::make())
            ->key('my-form');

        expect($schema->getKey())->toBe('my-form');
    });
});

describe('extra attributes', function (): void {
    it('returns empty array for `getExtraAttributes()` by default', function (): void {
        $schema = Schema::make(Livewire::make());

        expect($schema->getExtraAttributes())->toBe([]);
    });

    it('can set `extraAttributes()`', function (): void {
        $schema = Schema::make(Livewire::make())
            ->extraAttributes(['data-test' => 'value']);

        expect($schema->getExtraAttributes())->toBe(['data-test' => 'value']);
    });

    it('can merge `extraAttributes()`', function (): void {
        $schema = Schema::make(Livewire::make())
            ->extraAttributes(['data-a' => '1'])
            ->extraAttributes(['data-b' => '2'], merge: true);

        $attributes = $schema->getExtraAttributes();

        expect($attributes)->toHaveKey('data-a', '1');
        expect($attributes)->toHaveKey('data-b', '2');
    });
});

describe('alignment', function (): void {
    it('returns `null` for `getAlignment()` by default', function (): void {
        $schema = Schema::make(Livewire::make());

        expect($schema->getAlignment())->toBeNull();
    });

    it('can set `alignment()`', function (): void {
        $schema = Schema::make(Livewire::make())
            ->alignment(Alignment::Center);

        expect($schema->getAlignment())->toBe(Alignment::Center);
    });
});

describe('gap', function (): void {
    it('defaults `hasGap()` to `true`', function (): void {
        $schema = Schema::make(Livewire::make());

        expect($schema->hasGap())->toBeTrue();
    });

    it('can set `gap()` to `false`', function (): void {
        $schema = Schema::make(Livewire::make())
            ->gap(false);

        expect($schema->hasGap())->toBeFalse();
    });
});

describe('inline', function (): void {
    it('defaults `isInline()` to `false`', function (): void {
        $schema = Schema::make(Livewire::make());

        expect($schema->isInline())->toBeFalse();
    });

    it('can set `inline()`', function (): void {
        $schema = Schema::make(Livewire::make())
            ->inline();

        expect($schema->isInline())->toBeTrue();
    });
});
