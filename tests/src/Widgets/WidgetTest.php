<?php

namespace Filament\Tests\Widgets;

use Filament\Facades\Filament;
use Filament\Pages\Dashboard;
use Filament\Tests\Fixtures\Models\Post;
use Filament\Tests\Fixtures\Models\User;
use Filament\Tests\Fixtures\Pages\TableRenderHooksBrowserTest;
use Filament\Tests\Fixtures\Resources\Posts\Pages\ListPosts;
use Filament\Tests\TestCase;
use Filament\Widgets\Concerns\InteractsWithPageTable;
use Filament\Widgets\Widget;
use Filament\Widgets\WidgetConfiguration;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Route;
use Livewire\Attributes\Locked;

use function Filament\Tests\livewire;

uses(TestCase::class);

it('returns `true` from `canView()` by default', function (): void {
    expect(TestWidget::canView())->toBeTrue();
});

it('returns `-1` from `getSort()` when `$sort` is not set', function (): void {
    expect(TestWidget::getSort())->toBe(-1);
});

it('returns the configured sort order from `getSort()` when `$sort` is overridden', function (): void {
    expect(TestWidgetWithSort::getSort())->toBe(5);
});

it('returns `true` from `isDiscovered()` by default', function (): void {
    expect(TestWidget::isDiscovered())->toBeTrue();
});

it('returns `false` from `isDiscovered()` when `$isDiscovered` is overridden', function (): void {
    expect(TestWidgetNotDiscovered::isDiscovered())->toBeFalse();
});

it('returns default column span of `1` from `getColumnSpan()`', function (): void {
    $widget = app(TestWidget::class);

    expect($widget->getColumnSpan())->toBe(1);
});

it('returns overridden column span from `getColumnSpan()`', function (): void {
    $widget = app(TestWidgetWithColumnSpan::class);

    expect($widget->getColumnSpan())->toBe('full');
});

it('returns empty column start from `getColumnStart()` by default', function (): void {
    $widget = app(TestWidget::class);

    expect($widget->getColumnStart())->toBe([]);
});

it('returns overridden column start from `getColumnStart()`', function (): void {
    $widget = app(TestWidgetWithColumnStart::class);

    expect($widget->getColumnStart())->toBe(2);
});

it('returns `WidgetConfiguration` from `make()`', function (): void {
    $configuration = TestWidget::make();

    expect($configuration)->toBeInstanceOf(WidgetConfiguration::class);
    expect($configuration->widget)->toBe(TestWidget::class);
});

it('passes properties to `WidgetConfiguration` via `make()`', function (): void {
    $configuration = TestWidget::make(['foo' => 'bar']);

    expect($configuration->getProperties())->toBe(['foo' => 'bar']);
});

it('returns placeholder data from `getPlaceholderData()`', function (): void {
    $widget = app(TestWidget::class);

    expect($widget->getPlaceholderData())
        ->toBe([
            'columnSpan' => 1,
            'columnStart' => [],
        ]);
});

it('returns `[\'lazy\' => true]` from `getDefaultProperties()` when `$isLazy` is `true`', function (): void {
    expect(TestWidget::getDefaultProperties())->toBe(['lazy' => true]);
});

it('returns empty array from `getDefaultProperties()` when `$isLazy` is `false`', function (): void {
    expect(TestWidgetNotLazy::getDefaultProperties())->toBe([]);
});

describe('authorization', function (): void {
    beforeEach(function (): void {
        AuthorizableTestWidget::$canViewFlag = true;
        AuthorizableTestWidget::$calls = [];
    });

    it('authorizes an allowed first mount before subclass `mount()` and rendering', function (bool $inPanel): void {
        Filament::setCurrentPanel($inPanel ? Filament::getPanel('admin') : null);

        livewire(AuthorizableTestWidget::class)
            ->assertSuccessful()
            ->assertSee('Private widget data');

        expect(AuthorizableTestWidget::$calls)->toBe(['boot', 'authorize', 'mount', 'render']);
    })->with([true, false]);

    it('rejects a denied first mount without subclass `mount()` or rendering', function (bool $inPanel): void {
        Filament::setCurrentPanel($inPanel ? Filament::getPanel('admin') : null);
        AuthorizableTestWidget::$canViewFlag = false;

        $component = livewire(AuthorizableTestWidget::class);

        $component->assertDontSee('Private widget data');

        expect(AuthorizableTestWidget::$calls)->toBe(['boot', 'authorize']);

        $component->assertForbidden();
    })->with([true, false]);

    it('authorizes the real lazy mount without running sensitive work in the placeholder', function (bool $canView, bool $inPanel): void {
        Filament::setCurrentPanel($inPanel ? Filament::getPanel('admin') : null);

        $component = livewire(AuthorizableTestWidget::class, ['lazy' => true])
            ->assertSuccessful()
            ->assertDontSee('Private widget data');

        expect(AuthorizableTestWidget::$calls)->toBe([]);

        preg_match('/__lazyLoad\(\'([^\']+)\'\)/', html_entity_decode($component->html()), $matches);

        expect($matches)->toHaveKey(1);

        // Access can change after the placeholder has been returned.
        AuthorizableTestWidget::$canViewFlag = $canView;

        $component->call('__lazyLoad', $matches[1]);

        expect(AuthorizableTestWidget::$calls)->toBe($canView
            ? ['authorize', 'boot', 'mount', 'render']
            : ['authorize']);

        if (! $canView) {
            $component->assertForbidden()->assertDontSee('Private widget data');

            return;
        }

        $component->assertSuccessful()->assertSee('Private widget data');

        AuthorizableTestWidget::$calls = [];
        AuthorizableTestWidget::$canViewFlag = false;

        $component->call('sensitiveAction')->assertForbidden()->assertDontSee('Private widget data');

        expect(AuthorizableTestWidget::$calls)->toBe(['boot', 'authorize']);
    })->with([true, false])->with([true, false]);

    it('rejects denied requests before a lazy widget has mounted', function (array $calls, array $updates, bool $withLazyLoad): void {
        $component = livewire(AuthorizableTestWidget::class, ['lazy' => true]);

        if ($withLazyLoad) {
            preg_match('/__lazyLoad\(\'([^\']+)\'\)/', html_entity_decode($component->html()), $matches);

            expect($matches)->toHaveKey(1);

            $calls[] = ['method' => '__lazyLoad', 'params' => [$matches[1]]];
        }

        AuthorizableTestWidget::$canViewFlag = false;

        $component->update(calls: $calls, updates: $updates);

        expect(AuthorizableTestWidget::$calls)->toBe(['authorize']);

        $component->assertForbidden()->assertDontSee('Private widget data');
    })->with([
        'action' => [[['method' => 'sensitiveAction', 'params' => []]], [], false],
        'property update' => [[], ['name' => 'changed'], false],
        'empty request' => [[], [], false],
        'property update before lazy mount' => [[], ['name' => 'changed'], true],
        'action before lazy mount' => [[['method' => 'sensitiveAction', 'params' => []]], [], true],
    ]);

    it('re-authorizes hydration once before subclass `hydrate()`, property updates, and actions', function (bool $canView, bool $updateProperty): void {
        $component = livewire(AuthorizableTestWidget::class)->assertSee('Private widget data');

        AuthorizableTestWidget::$calls = [];
        AuthorizableTestWidget::$canViewFlag = $canView;

        if ($updateProperty) {
            $component->set('name', 'changed');
        } else {
            $component->call('sensitiveAction');
        }

        expect(AuthorizableTestWidget::$calls)->toBe($canView
            ? ['boot', 'authorize', 'hydrate', ...($updateProperty ? ['updating', 'updated'] : ['action']), 'render']
            : ['boot', 'authorize']);

        if ($canView) {
            $component->assertSuccessful()->assertSee('Private widget data');
        } else {
            $component->assertForbidden()->assertDontSee('Private widget data');
        }
    })->with([true, false])->with([true, false]);

    it('enforces `canView()` when a standalone Blade view mounts a widget', function (string $template, bool $canView): void {
        Filament::setCurrentPanel(null);
        AuthorizableTestWidget::$canViewFlag = $canView;

        Route::get('/widget-authorization-test', static fn (): string => Blade::render(
            $template,
            ['widget' => AuthorizableTestWidget::class],
        ));

        $response = $this->get('/widget-authorization-test');

        expect(AuthorizableTestWidget::$calls)->toBe($canView
            ? ['boot', 'authorize', 'mount', 'render']
            : ['boot', 'authorize']);

        if ($canView) {
            $response->assertSuccessful()->assertSee('Private widget data');
        } else {
            $response->assertForbidden()->assertDontSee('Private widget data');
        }
    })->with([
        'direct Livewire' => ['@livewire($widget)'],
        'legacy widget grid' => ['<x-filament-widgets::widgets :widgets="[$widget]" />'],
        'configured legacy widget grid' => ['<x-filament-widgets::widgets :widgets="[$widget::make()]" />'],
    ])->with([true, false]);

    it('filters denied widgets before mounting them on dashboard and resource pages', function (string $page, bool $canView): void {
        $panel = Filament::getPanel('admin');
        Filament::setCurrentPanel($panel);
        $panel->widgets([AuthorizableTestWidget::make()]);
        $this->actingAs(User::factory()->create());
        AuthorizableTestWidget::$canViewFlag = $canView;

        $component = livewire($page)->assertSuccessful();

        if ($canView) {
            $component->assertSee('Private widget data');

            expect(AuthorizableTestWidget::$calls)->toContain('mount', 'render');
        } else {
            $component->assertDontSee('Private widget data');

            expect(AuthorizableTestWidget::$calls)->not->toBeEmpty()
                ->each->toBe('authorize');
        }
    })->with([
        'dashboard' => [Dashboard::class],
        'resource header and footer' => [WidgetAuthorizationResourcePage::class],
    ])->with([true, false]);
});

it('preserves native restoration for scope-excluded widget model properties', function (string $property, bool $isLazy): void {
    $post = Post::factory()->create();
    $post->delete();
    $component = livewire(ScopedModelTestWidget::class, [
        $property => $post,
        'lazy' => $isLazy,
    ])->assertSuccessful();

    if ($isLazy) {
        expect(preg_match(
            "/__lazyLoad\\('([^']+)'\\)/",
            html_entity_decode($component->html()),
            $matches,
        ))->toBe(1);

        $component->call('__lazyLoad', $matches[1])->assertSuccessful();
    }

    $component->call('accessRecord')->assertSuccessful();

    expect($component->instance()->{$property}?->is($post))->toBeTrue()
        ->and($component->instance()->{$property}?->trashed())->toBeTrue();

    $globalScopes = Post::getAllGlobalScopes();

    try {
        Post::addGlobalScope(
            'exclude-widget-record',
            static fn (Builder $query): Builder => $query->whereKeyNot($post->getKey()),
        );

        $component->call('accessRecord')->assertSuccessful();

        expect($component->instance()->{$property}?->is($post))->toBeTrue();
    } finally {
        Post::setAllGlobalScopes($globalScopes);
    }
})->with(['record', 'parentRecord', 'otherPost'])->with([false, true]);

it('preserves native restoration for scope-excluded lazy widget mount arguments', function (): void {
    MountedScopedModelTestWidget::$hasMounted = false;
    $post = Post::factory()->create();
    $component = livewire(MountedScopedModelTestWidget::class, [
        'record' => $post,
        'lazy' => true,
    ])->assertSuccessful();

    expect(preg_match(
        "/__lazyLoad\\('([^']+)'\\)/",
        html_entity_decode($component->html()),
        $matches,
    ))->toBe(1);

    $post->delete();

    $component
        ->call('__lazyLoad', $matches[1])
        ->assertSuccessful();

    expect(MountedScopedModelTestWidget::$hasMounted)->toBeTrue()
        ->and($component->instance()->record?->is($post))->toBeTrue()
        ->and($component->instance()->record?->trashed())->toBeTrue();
});

it('supports a page table from outside a resource', function (): void {
    Post::factory()->create();

    livewire(NonResourcePageTableTestWidget::class, ['lazy' => false])
        ->call('loadPageTable')
        ->assertSet('pageTableRecordsCount', 1)
        ->assertSuccessful();
});

class TestWidget extends Widget
{
    protected string $view = 'filament-widgets::chart-widget';

    protected function getViewData(): array
    {
        return [];
    }
}

class TestWidgetWithSort extends Widget
{
    protected static ?int $sort = 5;

    protected string $view = 'filament-widgets::chart-widget';

    protected function getViewData(): array
    {
        return [];
    }
}

class TestWidgetNotDiscovered extends Widget
{
    protected static bool $isDiscovered = false;

    protected string $view = 'filament-widgets::chart-widget';

    protected function getViewData(): array
    {
        return [];
    }
}

class TestWidgetWithColumnSpan extends Widget
{
    protected int | string | array $columnSpan = 'full';

    protected string $view = 'filament-widgets::chart-widget';

    protected function getViewData(): array
    {
        return [];
    }
}

class TestWidgetWithColumnStart extends Widget
{
    protected int | string | array $columnStart = 2;

    protected string $view = 'filament-widgets::chart-widget';

    protected function getViewData(): array
    {
        return [];
    }
}

class TestWidgetNotLazy extends Widget
{
    protected static bool $isLazy = false;

    protected string $view = 'filament-widgets::chart-widget';

    protected function getViewData(): array
    {
        return [];
    }
}

class AuthorizableTestWidget extends Widget
{
    public static bool $canViewFlag = true;

    /** @var array<string> */
    public static array $calls = [];

    public ?string $name = null;

    protected static bool $isLazy = false;

    protected string $view = 'widgets.authorizable';

    public static function canView(): bool
    {
        static::$calls[] = 'authorize';

        return static::$canViewFlag;
    }

    public function boot(): void
    {
        static::$calls[] = 'boot';
    }

    public function mount(): void
    {
        static::$calls[] = 'mount';
    }

    public function hydrate(): void
    {
        static::$calls[] = 'hydrate';
    }

    public function updatingName(): void
    {
        static::$calls[] = 'updating';
    }

    public function updatedName(): void
    {
        static::$calls[] = 'updated';
    }

    public function sensitiveAction(): void
    {
        static::$calls[] = 'action';
    }

    protected function getViewData(): array
    {
        static::$calls[] = 'render';

        return ['privateWidgetData' => 'Private widget data'];
    }
}

class WidgetAuthorizationResourcePage extends ListPosts
{
    protected function getHeaderWidgets(): array
    {
        return [AuthorizableTestWidget::class];
    }

    protected function getFooterWidgets(): array
    {
        return [AuthorizableTestWidget::make()];
    }
}

class ScopedModelTestWidget extends Widget
{
    public ?Post $otherPost = null;

    public ?Post $record = null;

    #[Locked]
    public ?Post $parentRecord = null;

    protected string $view = 'pages.settings';

    public function accessRecord(): void {}
}

class MountedScopedModelTestWidget extends Widget
{
    public static bool $hasMounted = false;

    public ?Post $record = null;

    protected string $view = 'pages.settings';

    public function mount(Post $record): void
    {
        static::$hasMounted = true;
        $this->record = $record;
    }
}

class NonResourcePageTableTestWidget extends Widget
{
    use InteractsWithPageTable;

    public int $pageTableRecordsCount = 0;

    protected string $view = 'pages.settings';

    public function loadPageTable(): void
    {
        $this->pageTableRecordsCount = $this->getPageTableQuery()->count();
    }

    protected function getTablePage(): string
    {
        return TableRenderHooksBrowserTest::class;
    }
}
