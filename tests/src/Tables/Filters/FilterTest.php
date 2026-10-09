<?php

use Filament\Actions\Action;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Toggle;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Enums\FiltersResetActionPosition;
use Filament\Tables\Filters\Filter;
use Filament\Tests\Fixtures\Livewire\PostsTable;
use Filament\Tests\Fixtures\Livewire\PostsTableWithCustomFiltersApplyAction;
use Filament\Tests\Fixtures\Livewire\PostsTableWithCustomFiltersRemoveAllAction;
use Filament\Tests\Fixtures\Livewire\PostsTableWithCustomFiltersResetAction;
use Filament\Tests\Fixtures\Livewire\PostsTableWithCustomFiltersTriggerAction;
use Filament\Tests\Fixtures\Models\Post;
use Filament\Tests\Fixtures\Models\User;
use Filament\Tests\Tables\TestCase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Blade;

use function Filament\Tests\livewire;

uses(TestCase::class);

it('can set `toggle()` as the form component', function (): void {
    $filter = Filter::make('is_published')->toggle();

    expect($filter->getFormField())->toBeInstanceOf(Toggle::class);
});

it('can set `checkbox()` as the form component', function (): void {
    $filter = Filter::make('is_published')->toggle()->checkbox();

    expect($filter->getFormField())->toBeInstanceOf(Checkbox::class);
});

it('defaults form component to `Checkbox`', function (): void {
    $filter = Filter::make('is_published');

    expect($filter->getFormField())->toBeInstanceOf(Checkbox::class);
});

it('can set a custom `formComponent()`', function (): void {
    $filter = Filter::make('is_published')->formComponent(Toggle::class);

    expect($filter->getFormField())->toBeInstanceOf(Toggle::class);
});

it('can filter records by boolean column', function (): void {
    $posts = Post::factory()->count(10)->create();

    livewire(PostsTable::class)
        ->assertCanSeeTableRecords($posts)
        ->filterTable('is_published')
        ->assertCanSeeTableRecords($posts->where('is_published', true))
        ->assertCanNotSeeTableRecords($posts->where('is_published', false));
});

it('can filter records by relationship', function (): void {
    $posts = Post::factory()->count(10)->create();

    $author = $posts->first()->author;

    livewire(PostsTable::class)
        ->assertCanSeeTableRecords($posts)
        ->filterTable('author', $author)
        ->assertCanSeeTableRecords($posts->where('author_id', $author->getKey()))
        ->assertCanNotSeeTableRecords($posts->where('author_id', '!=', $author->getKey()));
});

it('can persist filters in the user\'s session', function (): void {
    $posts = Post::factory()->count(10)->create();

    $unpublishedPosts = $posts->where('is_published', false);

    livewire(PostsTable::class)
        ->assertCanSeeTableRecords($posts)
        ->filterTable('is_published')
        ->assertCanNotSeeTableRecords($unpublishedPosts);

    livewire(PostsTable::class)
        ->assertCanNotSeeTableRecords($unpublishedPosts);

    livewire(PostsTable::class)
        ->resetTableFilters()
        ->assertCanSeeTableRecords($unpublishedPosts);

    livewire(PostsTable::class)
        ->assertCanSeeTableRecords($unpublishedPosts);
});

it('can reset filters', function (): void {
    $posts = Post::factory()->count(10)->create();

    $unpublishedPosts = $posts->where('is_published', false);

    livewire(PostsTable::class)
        ->filterTable('is_published')
        ->assertCanNotSeeTableRecords($unpublishedPosts)
        ->resetTableFilters()
        ->assertCanSeeTableRecords($unpublishedPosts);
});

it('can remove a filter', function (): void {
    $posts = Post::factory()->count(10)->create();

    $unpublishedPosts = $posts->where('is_published', false);

    livewire(PostsTable::class)
        ->assertCanSeeTableRecords($posts)
        ->filterTable('is_published')
        ->assertCanNotSeeTableRecords($unpublishedPosts)
        ->removeTableFilter('is_published')
        ->assertCanSeeTableRecords($posts);
});

it('can remove all table filters', function (): void {
    $posts = Post::factory()->count(10)->create();

    $unpublishedPosts = $posts->where('is_published', false);

    livewire(PostsTable::class)
        ->assertCanSeeTableRecords($posts)
        ->filterTable('is_published')
        ->assertDontSee('Clear filters')
        ->assertCanNotSeeTableRecords($unpublishedPosts)
        ->removeTableFilters()
        ->assertCanSeeTableRecords($posts);
});

it('can customize the `filtersRemoveAllAction()`', function (): void {
    $posts = Post::factory()->count(10)->create();

    livewire(PostsTableWithCustomFiltersRemoveAllAction::class)
        ->filterTable('is_published')
        ->assertSee('Clear filters')
        ->assertCanNotSeeTableRecords($posts->where('is_published', false));
});

it('can customize the `filtersTriggerAction()`', function (): void {
    livewire(PostsTableWithCustomFiltersTriggerAction::class)
        ->assertSee('Show filters');
});

it('can customize the `filtersApplyAction()`', function (): void {
    livewire(PostsTableWithCustomFiltersApplyAction::class)
        ->assertSee('Apply filters');
});

it('can render the filters component without a reset action', function (FiltersResetActionPosition $position): void {
    $html = Blade::render(<<<'BLADE'
        <x-filament-tables::filters
            :apply-action="$applyAction"
            form=""
            :reset-action-position="$position"
        />
        BLADE, [
        'applyAction' => Action::make('apply')->hidden(),
        'position' => $position,
    ]);

    expect($html)->not->toContain('resetTableFiltersForm');
})->with(FiltersResetActionPosition::cases());

it('can customize the `filtersResetAction()`', function (): void {
    $action = livewire(PostsTableWithCustomFiltersResetAction::class)
        ->instance()
        ->getTable()
        ->getFiltersResetAction();

    expect($action)
        ->getLabel()->toBe('Custom reset filters')
        ->getIcon()->toBe(Heroicon::XMark)
        ->isButton()->toBeTrue();
});

it('can render a customized `filtersResetAction()` in each position', function (string $position, string $view): void {
    $component = livewire(PostsTableWithCustomFiltersResetAction::class, [
        'resetActionPosition' => $position,
        'resetActionView' => $view,
    ]);

    expect($component->html())->toContain('data-testid="filters-reset-action"');
})->with([
    'header' => ['header', 'button'],
    'footer' => ['footer', 'link'],
    'modal' => ['modal', 'link'],
]);

it('does not execute a guarded `filtersResetAction()`', function (string $state): void {
    Post::factory()->create(['is_published' => true]);
    $unpublishedPost = Post::factory()->create(['is_published' => false]);

    livewire(PostsTableWithCustomFiltersResetAction::class, [
        'resetActionState' => $state,
    ])
        ->filterTable('is_published')
        ->assertCanNotSeeTableRecords([$unpublishedPost])
        ->call('resetTableFiltersForm')
        ->assertCanNotSeeTableRecords([$unpublishedPost]);
})->with([
    'hidden',
    'invisible',
    'disabled',
    'unauthorized',
]);

it('does not reset filters when the `filtersResetAction()` is unauthorized', function (): void {
    Post::factory()->create(['is_published' => true]);
    $unpublishedPost = Post::factory()->create(['is_published' => false]);

    livewire(PostsTableWithCustomFiltersResetAction::class, [
        'resetActionState' => 'unauthorizedWithNotification',
    ])
        ->filterTable('is_published')
        ->assertCanNotSeeTableRecords([$unpublishedPost])
        ->call('resetTableFiltersForm')
        ->assertCanNotSeeTableRecords([$unpublishedPost])
        ->assertNotified('You cannot reset filters');
});

it('renders a customized `filtersResetAction()` accessibly', function (): void {
    retry(10, function (): void {
        Artisan::call('filament:assets');

        $this->actingAs(User::factory()->create());

        visit('/filters-reset-action-browser-test')
            ->click('[data-testid="filters-trigger"]')
            ->assertVisible('[data-testid="filters-reset-action"]')
            ->assertAttribute('[data-testid="published-filter"]', 'aria-checked', 'false')
            ->click('[data-testid="published-filter"]')
            ->assertAttribute('[data-testid="published-filter"]', 'aria-checked', 'true')
            ->click('[data-testid="filters-reset-action"]')
            ->assertAttribute('[data-testid="published-filter"]', 'aria-checked', 'false')
            ->assertNoSmoke()
            ->assertNoAccessibilityIssues();

        visit('/filters-reset-action-browser-test')
            ->inDarkMode()
            ->click('[data-testid="filters-trigger"]')
            ->assertVisible('[data-testid="filters-reset-action"]')
            ->assertNoAccessibilityIssues();
    });
});

it('manages focus for the filters dropdown', function (bool $isDarkMode): void {
    Artisan::call('filament:assets');

    $this->actingAs(User::factory()->create());

    $filtersTrigger = '[data-testid="filters-trigger"]';
    $publishedFilter = '[data-testid="published-filter"]';
    $selectFilter = '[data-testid="status-filter"] .fi-select-input-btn';
    $selectSearch = '[data-testid="status-filter"] .fi-select-input-search-ctn input';
    $dateFilter = '.fi-fo-date-time-picker-trigger';
    $datePanel = '.fi-fo-date-time-picker-panel';
    $colorFilter = '[data-testid="color-filter"] input';
    $colorPanel = '.fi-fo-color-picker-panel';

    $page = visit('/filters-reset-action-browser-test?focus=1');

    if ($isDarkMode) {
        $page = $page->inDarkMode();
    } else {
        $page = $page->resize(375, 812);
    }

    $page->script('window.enclosingEscapeCount = 0; window.addEventListener(\'keydown\', (event) => { if (event.key === \'Escape\') window.enclosingEscapeCount++ })');

    $page
        ->keys($filtersTrigger, 'Enter')
        ->assertScript('document.activeElement.closest(\'[data-testid="published-filter"]\') !== null', true)
        ->keys($publishedFilter, 'Escape')
        ->assertMissing($publishedFilter)
        ->assertScript('document.activeElement.closest(\'[data-testid="filters-trigger"]\') !== null', true)
        ->assertScript('window.enclosingEscapeCount', 0)
        ->click($filtersTrigger)
        ->assertVisible($publishedFilter)
        ->assertScript('document.activeElement.closest(\'[data-testid="filters-trigger"]\') !== null', true);

    $page
        ->click($selectFilter)
        ->type($selectSearch, 'Draft')
        ->keys($selectSearch, 'Escape')
        ->assertAttribute($selectFilter, 'aria-expanded', 'false')
        ->assertVisible($publishedFilter)
        ->assertScript("document.activeElement.matches('{$selectFilter}')", true)
        ->keys($selectFilter, 'Escape')
        ->assertMissing($publishedFilter)
        ->keys($filtersTrigger, 'Enter')
        ->assertAttribute($selectFilter, 'aria-expanded', 'false')
        ->click($dateFilter)
        ->assertVisible($datePanel)
        ->click("{$datePanel} [role=\"gridcell\"][tabindex=\"0\"][aria-disabled=\"false\"]")
        ->assertScript("document.querySelector('{$dateFilter}').value !== ''", true)
        ->assertVisible($datePanel)
        ->click($dateFilter)
        ->assertMissing($datePanel)
        ->click($dateFilter)
        ->assertVisible($datePanel)
        ->assertScript("document.querySelector('{$selectFilter}').focus(); document.querySelector('{$datePanel}').style.display === 'none'", true)
        ->click($dateFilter)
        ->assertVisible($datePanel)
        ->keys($dateFilter, 'Escape')
        ->assertMissing($datePanel)
        ->assertVisible($publishedFilter)
        ->assertScript("document.activeElement.matches('{$dateFilter}')", true)
        ->keys($dateFilter, 'Escape')
        ->assertMissing($publishedFilter)
        ->keys($filtersTrigger, 'Enter')
        ->assertMissing($datePanel)
        ->click($colorFilter)
        ->assertVisible($colorPanel);

    $page->script(<<<JS
            const colorPicker = document.querySelector('{$colorPanel}').firstElementChild
            const hueSlider = colorPicker.shadowRoot.querySelector('[part="hue"]')

            hueSlider.focus()
            hueSlider.dispatchEvent(new KeyboardEvent('keydown', {
                bubbles: true,
                composed: true,
                key: 'Escape',
            }))
            JS);

    $page
        ->assertMissing($colorPanel)
        ->assertVisible($publishedFilter)
        ->assertScript("document.activeElement.matches('{$colorFilter}')", true)
        ->keys($colorFilter, 'Escape')
        ->assertMissing($publishedFilter)
        ->keys($filtersTrigger, 'Enter')
        ->assertVisible($publishedFilter)
        ->assertMissing($colorPanel);

    $page->script('document.querySelector(\'[data-testid="filters-trigger"]\').focus()');

    $page
        ->keys($filtersTrigger, 'Escape')
        ->assertMissing($publishedFilter)
        ->assertScript('window.enclosingEscapeCount', 0)
        ->keys($filtersTrigger, 'Enter')
        ->click('.fi-topbar')
        ->assertMissing($publishedFilter)
        ->assertNoSmoke()
        ->assertNoAccessibilityIssues();
})->with(['mobile light' => false, 'desktop dark' => true]);

it('focuses lazy and empty filter configurations', function (string $focusScenario, string $focusedSelector): void {
    retry(10, function () use ($focusScenario, $focusedSelector): void {
        Artisan::call('filament:assets');

        $this->actingAs(User::factory()->create());

        visit("/filters-reset-action-browser-test?focusScenario={$focusScenario}")
            ->wait(0.5)
            ->keys('[data-testid="filters-trigger"]', 'Enter')
            ->wait(1)
            ->assertScript("document.activeElement.closest('{$focusedSelector}') !== null", true)
            ->keys($focusedSelector, 'Escape')
            ->assertScript('document.activeElement.closest(\'[data-testid="filters-trigger"]\') !== null', true)
            ->assertNoSmoke();
    });
})->with([
    'lazy select first' => ['selectFirst', '.fi-select-input-btn'],
    'disabled focusable control first' => ['disabledFirst', '[data-testid="published-filter"]'],
    'hidden control first' => ['hiddenFirst', '[data-testid="published-filter"]'],
    'disabled control fallback' => ['disabled', '.fi-ta-filters'],
    'empty schema fallback' => ['empty', '.fi-ta-filters'],
]);

it('cancels pending filter autofocus when the dropdown closes', function (): void {
    retry(10, function (): void {
        Artisan::call('filament:assets');

        $this->actingAs(User::factory()->create());

        $page = visit('/filters-reset-action-browser-test?focus=1');

        $page->script(<<<'JS'
            document.querySelector('.fi-ta-filters').closest('.fi-dropdown-panel').addEventListener('dropdown-opened', () => queueMicrotask(() => {
                document.querySelector('[data-testid="filters-trigger"]').dispatchEvent(new KeyboardEvent('keydown', {
                    bubbles: true,
                    key: 'Escape',
                }))
            }), { once: true })
            JS);

        $page
            ->keys('[data-testid="filters-trigger"]', 'Enter')
            ->wait(0.5)
            ->assertMissing('.fi-ta-filters')
            ->assertScript('document.activeElement.closest(\'[data-testid="filters-trigger"]\') !== null', true)
            ->assertNoSmoke();
    });
});

it('skips a lazy filter control until it is initialized', function (): void {
    retry(10, function (): void {
        Artisan::call('filament:assets');

        $this->actingAs(User::factory()->create());

        $page = visit('/filters-reset-action-browser-test?focusScenario=dateFirst')
            ->wait(0.5);

        $page->script('document.querySelector(\'[data-testid="published-at-filter"] [x-load]\').setAttribute(\'x-ignore\', \'\')');

        $page
            ->keys('[data-testid="filters-trigger"]', 'Enter')
            ->assertScript('document.activeElement.closest(\'[data-testid="published-filter"]\') !== null', true);

        $page->script('document.querySelector(\'[data-testid="published-at-filter"] [x-load]\').removeAttribute(\'x-ignore\')');

        $page
            ->wait(0.5)
            ->assertScript('document.activeElement.closest(\'[data-testid="published-filter"]\') !== null', true)
            ->assertNoSmoke();
    });
});

it('contains filters `Escape` handling inside an enclosing modal', function (bool $isDarkMode): void {
    retry(10, function () use ($isDarkMode): void {
        Artisan::call('filament:assets');

        $this->actingAs(User::factory()->create());

        $filtersTrigger = '[data-testid="filters-trigger"]';
        $publishedFilter = '[data-testid="published-filter"]';
        $tableModal = '[data-testid="table-modal"]';

        $page = visit('/filters-modal-browser-test');

        if ($isDarkMode) {
            $page = $page->inDarkMode();
        }

        $page
            ->click('[data-testid="table-modal-trigger"]')
            ->assertVisible($tableModal)
            ->keys($filtersTrigger, 'Enter')
            ->assertVisible($publishedFilter)
            ->keys($publishedFilter, 'Escape')
            ->assertMissing($publishedFilter)
            ->assertVisible($tableModal)
            ->keys($filtersTrigger, 'Enter')
            ->assertVisible($publishedFilter)
            ->assertNoAccessibilityIssues();

        $page->script(<<<'JS'
            const dropdown = document.querySelector('[data-testid="table-modal"] .fi-ta-col-manager-dropdown')
            const trigger = dropdown.querySelector(':scope > .fi-dropdown-trigger button')

            window.columnManagerPanel = dropdown.querySelector(':scope > .fi-dropdown-panel')
            window.columnManagerPanel.open(trigger)
            JS);

        $page->assertScript('window.columnManagerPanel.style.display === \'block\'', true);

        $page->script('document.querySelector(\'[data-testid="filters-trigger"]\').focus()');

        $page
            ->keys($filtersTrigger, 'Escape')
            ->assertMissing($publishedFilter)
            ->assertVisible($tableModal)
            ->assertScript('window.columnManagerPanel.style.display === \'block\'', true)
            ->assertScript('document.activeElement.closest(\'[data-testid="filters-trigger"]\') !== null', true);

        $page->script('window.columnManagerPanel.close()');

        $page
            ->keys($tableModal, 'Escape')
            ->assertMissing($tableModal)
            ->assertNoSmoke();
    });
})->with(['light' => false, 'dark' => true]);

it('can use a custom attribute for the `SelectFilter`', function (): void {
    $posts = Post::factory()->count(10)->create();

    $unpublishedPosts = $posts->where('is_published', false);

    livewire(PostsTable::class)
        ->assertCanSeeTableRecords($posts)
        ->filterTable('select_filter_attribute', false)
        ->assertCanSeeTableRecords($unpublishedPosts)
        ->filterTable('select_filter_attribute', true)
        ->assertCanNotSeeTableRecords($unpublishedPosts);
});

it('can assert a filter exists with a given configuration', function (): void {
    livewire(PostsTable::class)
        ->assertTableFilterExists('is_published', function (Filter $filter): bool {
            return $filter->getLabel() === 'Is published';
        });
});

it('can check if a filter is visible', function (): void {
    livewire(PostsTable::class)
        ->assertTableFilterVisible('is_published');
});

it('can check if a filter is hidden', function (): void {
    livewire(PostsTable::class)
        ->assertTableFilterHidden('hidden');
});

it('returns `["isActive" => false]` from `getResetState()` by default', function (): void {
    $filter = Filter::make('test');

    expect($filter->getResetState())->toBe(['isActive' => false]);
});

it('returns fluent `$this` from `toggle()`', function (): void {
    $filter = Filter::make('test');

    expect($filter->toggle())->toBe($filter);
});

it('returns fluent `$this` from `checkbox()`', function (): void {
    $filter = Filter::make('test');

    expect($filter->checkbox())->toBe($filter);
});

it('returns fluent `$this` from `formComponent()`', function (): void {
    $filter = Filter::make('test');

    expect($filter->formComponent(Toggle::class))->toBe($filter);
});

it('sets the form field label from the filter label', function (): void {
    $filter = Filter::make('test')
        ->label('My Filter');

    $field = $filter->getFormField();

    expect($field->getLabel())->toBe('My Filter');
});

// BaseFilter tests (tested via Filter, which extends BaseFilter)

describe('construction (BaseFilter)', function (): void {
    it('can be constructed with a name', function (): void {
        $filter = Filter::make('status');

        expect($filter->getName())->toBe('status');
    });

    it('throws `LogicException` when name is blank', function (): void {
        Filter::make('');
    })->throws(LogicException::class);
});

it('returns `null` from `getDefaultName()`', function (): void {
    expect(Filter::getDefaultName())->toBeNull();
});

describe('label (BaseFilter)', function (): void {
    it('auto-generates label from name', function (): void {
        $filter = Filter::make('is-published');

        expect($filter->getLabel())->toBeString()->not->toBeEmpty();
    });

    it('can set label with a `Closure`', function (): void {
        $filter = Filter::make('status')
            ->label(static fn (): string => 'Dynamic');

        expect($filter->getLabel())->toBe('Dynamic');
    });
});

describe('column span (BaseFilter)', function (): void {
    it('defaults column span to `1`', function (): void {
        $filter = Filter::make('status');

        expect($filter->getColumnSpan())->toBe(1);
    });

    it('can set `columnSpan()`', function (): void {
        $filter = Filter::make('status')
            ->columnSpan(2);

        expect($filter->getColumnSpan())->toBe(2);
    });

    it('can set `columnSpanFull()`', function (): void {
        $filter = Filter::make('status')
            ->columnSpanFull();

        expect($filter->getColumnSpan())->toBe(['default' => 'full']);
    });
});
