<?php

use Filament\Support\Components\ViewComponent;
use Filament\Tests\TestCase;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Contracts\View\View;
use Illuminate\Support\HtmlString;

uses(TestCase::class);

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
});

class ConcreteViewComponent extends ViewComponent
{
    protected string $view = 'simple-component';
}

class ViewComponentWithoutView extends ViewComponent
{
    // No $view property
}

class EmptyViewComponent extends ViewComponent
{
    protected string $view = 'livewire.empty';
}
