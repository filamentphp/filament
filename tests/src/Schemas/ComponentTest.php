<?php

use Filament\Forms\Components\Repeater;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Schema;
use Filament\Support\Enums\Width;
use Filament\Tests\Fixtures\Livewire\Livewire;
use Filament\Tests\TestCase;

uses(TestCase::class);

it('belongs to container', function (): void {
    $component = (new Component)
        ->container($schema = Schema::make(Livewire::make()));

    expect($component)
        ->getContainer()->toBe($schema);
});

it('invalidates hierarchy caches when attached to a different container', function (): void {
    $firstSchema = Schema::make(Livewire::make())
        ->key('first')
        ->statePath('firstData');
    $secondSchema = Schema::make(Livewire::make())
        ->key('second')
        ->statePath('secondData');
    $component = (new Component)
        ->key('field')
        ->statePath('name')
        ->container($firstSchema);

    expect($component->getKey())
        ->toBe('first.field')
        ->and($component->getInheritanceKey())
        ->toBe('first.field')
        ->and($component->getStatePath())
        ->toBe('firstData.name')
        ->and($component->getRootContainer())
        ->toBe($firstSchema);

    $component->container($secondSchema);

    expect($component->getKey())
        ->toBe('second.field')
        ->and($component->getInheritanceKey())
        ->toBe('second.field')
        ->and($component->getStatePath())
        ->toBe('secondData.name')
        ->and($component->getRootContainer())
        ->toBe($secondSchema);
});

it('invalidates cached repeater ancestry when an ancestor is attached to a different container', function (): void {
    $livewire = Livewire::make();
    $rootSchema = Schema::make($livewire);
    $firstRepeater = Repeater::make('first')
        ->container($rootSchema);
    $secondRepeater = Repeater::make('second')
        ->container($rootSchema);
    $firstRepeaterItem = Schema::make($livewire)
        ->parentComponent($firstRepeater);
    $secondRepeaterItem = Schema::make($livewire)
        ->parentComponent($secondRepeater);
    $childComponent = new Component;
    $parentComponent = (new Component)
        ->container($firstRepeaterItem)
        ->childComponents([$childComponent]);

    $parentComponent->getChildSchemas()['default']->getComponents();

    expect($childComponent->getParentRepeaterItem())
        ->toBe($firstRepeaterItem)
        ->and($childComponent->getParentRepeater())
        ->toBe($firstRepeater);

    $parentComponent->container($secondRepeaterItem);

    expect($childComponent->getParentRepeaterItem())
        ->toBe($secondRepeaterItem)
        ->and($childComponent->getParentRepeater())
        ->toBe($secondRepeater);
});

it('can access container\'s Livewire component', function (): void {
    $component = (new Component)
        ->container(Schema::make($livewire = Livewire::make()));

    expect($component)
        ->getLivewire()->toBe($livewire);
});

it('has child components', function (): void {
    $components = [];

    foreach (range(1, $count = rand(2, 10)) as $i) {
        $components[] = new Component;
    }

    $componentsBoundToContainer = ($parentComponent = new Component)
        ->container(Schema::make(Livewire::make()))
        ->childComponents($components)
        ->getChildSchema()
        ->getComponents();

    expect($componentsBoundToContainer)
        ->toHaveCount($count)
        ->each(
            fn ($component) => $component
                ->toBeInstanceOf(Component::class)
                ->getContainer()->getParentComponent()->toBe($parentComponent),
        );
});

it('can have meta', function (): void {
    $component = (new Component)
        ->meta('foo', 'bar')
        ->meta('bob', 'baz');

    expect($component)
        ->hasMeta('foo')->toBeTrue()
        ->getMeta('foo')->toBe('bar')
        ->getMeta(['foo', 'bob'])->toEqual([
            'foo' => 'bar',
            'bob' => 'baz',
        ]);
});

describe('column span', function (): void {
    it('defaults `getColumnSpan()` to array with default `1`', function (): void {
        $component = new Component;

        expect($component->getColumnSpan())->toBeArray();
        expect($component->getColumnSpan('default'))->toBe(1);
    });

    it('can set `columnSpan()` for a breakpoint', function (): void {
        $component = (new Component)->columnSpan(2);

        expect($component->getColumnSpan('lg'))->toBe(2);
    });

    it('can set `columnSpanFull()`', function (): void {
        $component = (new Component)->columnSpanFull();

        expect($component->getColumnSpan())->toBe(['default' => 'full']);
    });
});

describe('max width', function (): void {
    it('returns `null` for `getMaxWidth()` by default', function (): void {
        $component = new Component;

        expect($component->getMaxWidth())->toBeNull();
    });

    it('can set `maxWidth()`', function (): void {
        $component = (new Component)->maxWidth(Width::TwoExtraLarge);

        expect($component->getMaxWidth())->toBe(Width::TwoExtraLarge);
    });
});

describe('ID', function (): void {
    it('returns `null` for `getId()` by default', function (): void {
        $component = new Component;

        expect($component->getId())->toBeNull();
    });

    it('can set `id()`', function (): void {
        $component = (new Component)->id('my-component');

        expect($component->getId())->toBe('my-component');
    });
});

describe('extra attributes', function (): void {
    it('returns empty array for `getExtraAttributes()` by default', function (): void {
        $component = new Component;

        expect($component->getExtraAttributes())->toBe([]);
    });

    it('can set `extraAttributes()`', function (): void {
        $component = (new Component)->extraAttributes(['data-test' => 'value']);

        expect($component->getExtraAttributes())->toBe(['data-test' => 'value']);
    });

    it('can merge `extraAttributes()`', function (): void {
        $component = (new Component)
            ->extraAttributes(['data-a' => '1'])
            ->extraAttributes(['data-b' => '2'], merge: true);

        $attributes = $component->getExtraAttributes();

        expect($attributes)->toHaveKey('data-a', '1');
        expect($attributes)->toHaveKey('data-b', '2');
    });

    it('can set `extraAttributes()` with a `Closure`', function (): void {
        $component = (new Component)
            ->extraAttributes(static fn (): array => ['data-dynamic' => 'yes']);

        expect($component->getExtraAttributes())->toBe(['data-dynamic' => 'yes']);
    });

    it('does not merge the same `Closure` into `extraAttributes()` more than once', function (): void {
        $evaluationCount = 0;
        $attributes = static function () use (&$evaluationCount): array {
            $evaluationCount++;

            return ['class' => 'custom-content'];
        };
        $component = (new Component)
            ->extraAttributes($attributes, merge: true)
            ->extraAttributes($attributes, merge: true);

        expect($component->getExtraAttributes())
            ->toBe(['class' => 'custom-content'])
            ->and($evaluationCount)
            ->toBe(1);
    });
});

describe('visibility', function (): void {
    it('defaults to visible', function (): void {
        $component = (new Component)
            ->container(Schema::make(Livewire::make()));

        expect($component->isVisible())->toBeTrue();
    });

    it('can be hidden with `hidden()`', function (): void {
        $component = (new Component)
            ->container(Schema::make(Livewire::make()))
            ->hidden();

        expect($component->isHidden())->toBeTrue();
    });

    it('can set `visible()` with a `Closure`', function (): void {
        $component = (new Component)
            ->container(Schema::make(Livewire::make()))
            ->visible(static fn (): bool => false);

        expect($component->isVisible())->toBeFalse();
    });
});

it('can be cloned', function (): void {
    $afterClonedCallbackCalledCount = 0;
    $afterClonedCallbackClone = null;
    $afterClonedCallbackOriginal = null;

    $component = (new Component)
        ->afterCloned(function (Component $clone, Component $original) use (&$afterClonedCallbackCalledCount, &$afterClonedCallbackClone, &$afterClonedCallbackOriginal): void {
            $afterClonedCallbackCalledCount++;
            $afterClonedCallbackClone = $clone;
            $afterClonedCallbackOriginal = $original;
        });

    $clone = $component->getClone();

    expect($afterClonedCallbackCalledCount)
        ->toBe(1);

    expect($afterClonedCallbackClone)
        ->not->toBe($component)
        ->toBe($clone);

    expect($afterClonedCallbackOriginal)
        ->toBe($component);
});
