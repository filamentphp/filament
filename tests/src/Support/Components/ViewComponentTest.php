<?php

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Filament\Support\Components\Contracts\HasEmbeddedView;
use Filament\Support\Components\ViewComponent;
use Filament\Tests\Fixtures\Livewire\Livewire;
use Filament\Tests\TestCase;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Contracts\View\View;
use Illuminate\Support\HtmlString;

uses(TestCase::class)->group('serial');

describe('view', function (): void {
    it('returns the view set in the property', function (): void {
        $component = new ConcreteViewComponent;

        expect($component->getView())->toBe('simple-component');
    });

    it('can set `view()` to override the property', function (): void {
        $component = new ConcreteViewComponent;
        $component->view('livewire.table');

        expect($component->getView())->toBe('livewire.table');
    });

    it('returns `$this` from `view()` when `null` is passed', function (): void {
        $component = new ConcreteViewComponent;

        expect($component->view(null))->toBe($component);
        expect($component->getView())->toBe('simple-component');
    });

    it('reports `hasView()` as `true` when view is set', function (): void {
        $component = new ConcreteViewComponent;

        expect($component->hasView())->toBeTrue();
    });

    it('throws `LogicException` from `getView()` when no view is defined', function (): void {
        $component = new ViewComponentWithoutView;

        expect(static fn () => $component->getView())
            ->toThrow(LogicException::class);
    });

    it('reports `hasView()` as `false` when no view or default is set', function (): void {
        $component = new ViewComponentWithoutView;

        expect($component->hasView())->toBeFalse();
    });
});

describe('default view', function (): void {
    it('returns `null` for `getDefaultView()` by default', function (): void {
        $component = new ConcreteViewComponent;

        expect($component->getDefaultView())->toBeNull();
    });

    it('can set `defaultView()`', function (): void {
        $component = new ViewComponentWithoutView;
        $component->defaultView('simple-component');

        expect($component->getDefaultView())->toBe('simple-component');
        expect($component->getView())->toBe('simple-component');
    });

    it('can set `defaultView()` with a `Closure`', function (): void {
        $component = new ViewComponentWithoutView;
        $component->defaultView(static fn (): string => 'livewire.table');

        expect($component->getDefaultView())->toBe('livewire.table');
    });

    it('can clear `defaultView()` with `null`', function (): void {
        $component = new ViewComponentWithoutView;
        $component->defaultView('simple-component');
        $component->defaultView(null);

        expect($component->getDefaultView())->toBeNull();
    });

    it('uses explicit `view()` over `defaultView()`', function (): void {
        $component = new ViewComponentWithoutView;
        $component->defaultView('simple-component');
        $component->view('livewire.table');

        expect($component->getView())->toBe('livewire.table');
    });
});

describe('view data', function (): void {
    it('returns empty array for `getViewData()` by default', function (): void {
        $component = new ConcreteViewComponent;

        expect($component->getViewData())->toBe([]);
    });

    it('can set `viewData()` with an array', function (): void {
        $component = new ConcreteViewComponent;
        $component->viewData(['key' => 'value']);

        expect($component->getViewData())->toBe(['key' => 'value']);
    });

    it('merges multiple `viewData()` calls', function (): void {
        $component = new ConcreteViewComponent;
        $component->viewData(['a' => '1']);
        $component->viewData(['b' => '2']);

        $data = $component->getViewData();

        expect($data)->toHaveKey('a', '1');
        expect($data)->toHaveKey('b', '2');
    });

    it('can set `viewData()` with a `Closure`', function (): void {
        $component = new ConcreteViewComponent;
        $component->viewData(static fn (): array => ['dynamic' => true]);

        expect($component->getViewData())->toBe(['dynamic' => true]);
    });

    it('can set view data through `view()` second parameter', function (): void {
        $component = new ConcreteViewComponent;
        $component->view('simple-component', ['passed' => 'value']);

        expect($component->getViewData())->toBe(['passed' => 'value']);
    });
});

describe('HTML rendering', function (): void {
    it('implements `Htmlable`', function (): void {
        $component = new ConcreteViewComponent;

        expect($component)->toBeInstanceOf(Htmlable::class);
    });

    it('returns an HTML string from `toHtml()`', function (): void {
        $component = new ConcreteViewComponent;

        $html = $component->toHtml();

        expect($html)->toBeString();
    });

    it('returns an `HtmlString` from `toHtmlString()`', function (): void {
        $component = new ConcreteViewComponent;

        $result = $component->toHtmlString();

        expect($result)->toBeInstanceOf(HtmlString::class);
    });

    it('returns `null` from `toHtmlString()` when HTML is blank', function (): void {
        $component = new EmptyViewComponent;

        $result = $component->toHtmlString();

        expect($result)->toBeNull();
    });

    it('returns a `View` from `render()`', function (): void {
        $component = new ConcreteViewComponent;

        $view = $component->render();

        expect($view)->toBeInstanceOf(View::class);
    });

    it('falls back to an overridden `render()` without a configured view', function (): void {
        $component = new class extends ViewComponent
        {
            public function render(): View
            {
                return view('simple-component');
            }
        };

        expect($component->hasView())->toBeFalse()
            ->and($component->toHtml())->toBe(view('simple-component')->render());
    });

    it('preserves the missing-view exception when no renderer is provided', function (): void {
        expect(static fn () => (new ViewComponentWithoutView)->toHtml())
            ->toThrow(LogicException::class, 'does not have a [$view] property defined.');
    });
});

describe('published view override', function (): void {
    $resetOverrideCache = function (): void {
        $cache = (new ReflectionClass(ViewComponent::class))
            ->getProperty('hasPublishedEmbeddedViewOverrideCache');
        $cache->setValue(null, []);
    };

    beforeEach($resetOverrideCache);

    afterEach(function () use ($resetOverrideCache): void {
        foreach ([
            resource_path('views/vendor/filament-forms/test-override.blade.php'),
            resource_path('views/vendor/filament-forms/components/text-input.blade.php'),
        ] as $path) {
            if (file_exists($path)) {
                unlink($path);
            }
        }

        foreach ([
            resource_path('views/vendor/filament-forms/components'),
            resource_path('views/vendor/filament-forms'),
        ] as $directory) {
            if (is_dir($directory)) {
                @rmdir($directory);
            }
        }

        $resetOverrideCache();
    });

    it('returns the path declared in the property from `getPublishedViewOverrideCheckPath()`', function (): void {
        $component = new EmbeddedViewComponent;

        expect($component->getPublishedViewOverrideCheckPath())->toBe('filament-forms::test-override');
    });

    it('returns `null` from `getPublishedViewOverrideCheckPath()` when no path is declared', function (): void {
        $component = new ConcreteViewComponent;

        expect($component->getPublishedViewOverrideCheckPath())->toBeNull();
    });

    it('renders `toEmbeddedHtml()` when no published override exists', function (string $componentClass): void {
        $component = new $componentClass;

        expect($component->toHtml())->toBe('embedded');
    })->with([[EmbeddedViewComponent::class], [LegacyEmbeddedViewComponent::class]]);

    it('renders the published Blade override instead of `toEmbeddedHtml()` when one exists', function (string $componentClass): void {
        writePublishedOverride('filament-forms/test-override.blade.php', 'PUBLISHED OVERRIDE');

        $component = new $componentClass;

        expect($component->toHtml())->toBe('PUBLISHED OVERRIDE');
    })->with([[EmbeddedViewComponent::class], [LegacyEmbeddedViewComponent::class]]);

    it('prefers a configured view over published and embedded HTML', function (string $componentClass, string $setter): void {
        writePublishedOverride('filament-forms/test-override.blade.php', 'PUBLISHED OVERRIDE');
        $component = (new $componentClass)->{$setter}('simple-component');

        expect($component->toHtml())->toBe(view('simple-component')->render());
    })->with([[EmbeddedViewComponent::class], [LegacyEmbeddedViewComponent::class]])->with(['view', 'defaultView']);

    it('caches the override-detection result by view path', function (): void {
        $path = writePublishedOverride('filament-forms/test-override.blade.php', 'X');

        expect(ViewComponent::hasPublishedEmbeddedViewOverride('filament-forms::test-override'))->toBeTrue();

        unlink($path);

        expect(ViewComponent::hasPublishedEmbeddedViewOverride('filament-forms::test-override'))->toBeTrue();
    });

    it('returns `false` from `hasPublishedEmbeddedViewOverride()` for a path without a namespace', function (): void {
        expect(ViewComponent::hasPublishedEmbeddedViewOverride('no-namespace.view'))->toBeFalse();
    });

    it('honours a published override on a real form component (`TextInput`)', function (): void {
        writePublishedOverride('filament-forms/components/text-input.blade.php', 'PUBLISHED-TEXT-INPUT-SENTINEL');

        Schema::make(Livewire::make())
            ->statePath('data')
            ->components([
                $input = TextInput::make('name'),
            ])
            ->fill();

        expect($input->toHtml())->toContain('PUBLISHED-TEXT-INPUT-SENTINEL');
    });
});

function writePublishedOverride(string $relativePath, string $contents): string
{
    $path = resource_path('views/vendor/' . $relativePath);

    if (! is_dir(dirname($path))) {
        mkdir(dirname($path), recursive: true);
    }

    file_put_contents($path, $contents);

    return $path;
}

class ConcreteViewComponent extends ViewComponent
{
    protected string $view = 'simple-component';
}

class EmbeddedViewComponent extends ViewComponent
{
    protected ?string $publishedViewOverrideCheckPath = 'filament-forms::test-override';

    public function toEmbeddedHtml(): string
    {
        return 'embedded';
    }
}

class LegacyEmbeddedViewComponent extends EmbeddedViewComponent implements HasEmbeddedView {}

class ViewComponentWithoutView extends ViewComponent
{
    // No $view property
}

class EmptyViewComponent extends ViewComponent
{
    protected string $view = 'livewire.empty';
}
