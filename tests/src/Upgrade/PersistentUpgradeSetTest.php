<?php

namespace Filament\Tests\Upgrade;

use Closure;
use Filament\Actions\Action;
use Filament\Facades\Filament;
use Filament\FilamentManager;
use Filament\Forms\Components\Builder;
use Filament\Forms\Components\Field;
use Filament\Forms\Components\Repeater;
use Filament\Forms\View\FormsIconAlias;
use Filament\Pages\Page;
use Filament\Panel;
use Filament\PanelRegistry;
use Filament\Schemas\Schema;
use Filament\Schemas\View\SchemasIconAlias;
use Filament\Support\Assets\AssetManager;
use Filament\Support\Enums\Alignment;
use Filament\Support\Facades\FilamentAsset;
use Filament\Support\Facades\FilamentIcon;
use Filament\Support\Facades\FilamentView;
use Filament\Support\Icons\Heroicon;
use Filament\Support\View\ViewManager;
use Filament\Tables\Enums\HeaderActionsPosition;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Filament\Tests\Fixtures\Livewire\Livewire;
use Filament\Tests\Fixtures\Livewire\PostsTable;
use Filament\Tests\Fixtures\Models\Post;
use Filament\Tests\Fixtures\Models\Team;
use Filament\Tests\Fixtures\Models\User;
use Filament\Tests\TestCase;
use Filament\View\PanelsRenderHook;
use Filament\Widgets\View\Components\StatsOverviewWidgetComponent\StatComponent\ChartComponent;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\HtmlString;
use LogicException;
use ReflectionClass;
use ReflectionMethod;
use Symfony\Component\Process\Process;

class PersistentUpgradeSetTest extends TestCase
{
    public function test_the_complete_set_migrates_fixtures_and_remains_stable_in_fresh_processes(): void
    {
        $directory = sys_get_temp_dir() . '/filament-upgrade-' . bin2hex(random_bytes(8));
        mkdir($directory);

        foreach (['app', 'config', 'routes', 'tests'] as $sourceDirectory) {
            mkdir($directory . '/' . $sourceDirectory);
        }

        mkdir($directory . '/resources/views', recursive: true);

        $originalFiles = [];
        $expectedFiles = [];

        try {
            foreach ([...glob(__DIR__ . '/Fixtures/*.php.inc'), ...glob(__DIR__ . '/Fixtures/Blade/*.inc')] as $fixture) {
                $parts = explode("-----\n", file_get_contents($fixture), 2);
                $sourceDirectory = match (basename($fixture)) {
                    'legacy_query_builder_classes.php.inc' => 'config',
                    'deprecated_enum_constants.php.inc' => 'routes',
                    'persistent_upload_restriction.php.inc' => 'tests',
                    'legacy_aliases.blade.php.inc', 'unrelated.txt.inc' => 'resources/views',
                    default => 'app',
                };
                $path = $directory . '/' . $sourceDirectory . '/' . basename($fixture, '.inc');
                file_put_contents($path, $parts[0]);
                $originalFiles[$path] = $parts[0];
                $expectedFiles[$path] = trim($parts[1] ?? $parts[0]);
            }

            foreach (['behavioral_configuration', 'behavioral_configuration_expressions', 'deprecated_column_and_support_methods'] as $fixture) {
                $legacyPath = $directory . '/legacy_' . $fixture . '.php';
                file_put_contents($legacyPath, str_replace(
                    ['namespace Filament\\Tests\\Upgrade\\Fixtures\\', 'use Filament\\Forms\\Components\\Builder;', 'use Filament\\Forms\\Components\\Repeater;'],
                    ['namespace Filament\\Tests\\Upgrade\\LegacyFixtures\\', 'use Filament\\Tests\\Upgrade\\LegacyInsetBuilder as Builder;', 'use Filament\\Tests\\Upgrade\\LegacyInsetRepeater as Repeater;'],
                    $originalFiles[$directory . '/app/' . $fixture . '.php'],
                ));
                require_once $legacyPath;
            }

            $root = dirname(__DIR__, 3);
            $configuration = $directory . '/rector.php';
            $set = var_export($root . '/packages/upgrade/src/rector.php', true);
            file_put_contents($configuration, <<<PHP
                <?php

                use Rector\Config\RectorConfig;

                return RectorConfig::configure()
                    ->withPaths([
                        __DIR__ . '/app',
                        __DIR__ . '/config',
                        __DIR__ . '/routes',
                        __DIR__ . '/tests',
                    ])
                    ->withoutParallel()
                    ->withCache(__DIR__ . '/cache')
                    ->withSets([{$set}]);
                PHP);

            $runUpgrade = function (bool $dryRun) use ($root, $configuration, $directory, $expectedFiles): Process {
                $process = new Process([
                    PHP_BINARY, $root . '/vendor/bin/rector', 'process',
                    '--config', $configuration, '--clear-cache', '--no-progress-bar',
                    ...($dryRun ? ['--dry-run'] : []),
                ], $root);
                $process->setTimeout(300);
                $process->run();

                $expectedBladeExitCode = 0;

                foreach ($expectedFiles as $path => $expected) {
                    if ($dryRun && str_ends_with($path, '.blade.php') && (trim(file_get_contents($path)) !== $expected)) {
                        $expectedBladeExitCode = 2;
                    }
                }

                $bladeProcess = new Process([
                    PHP_BINARY, $root . '/packages/upgrade/src/upgrade-blade-templates.php',
                    'resources/views', 'app', 'config', 'routes', 'tests',
                    ...($dryRun ? ['--dry-run'] : []),
                ], $directory);
                $bladeProcess->run();
                $this->assertSame($expectedBladeExitCode, $bladeProcess->getExitCode(), $bladeProcess->getOutput() . $bladeProcess->getErrorOutput());

                return $process->isSuccessful() ? $bladeProcess : $process;
            };

            for ($pass = 1; $pass <= 3; $pass++) {
                $process = $runUpgrade($pass > 1);
                $this->assertSame(0, $process->getExitCode(), "Pass {$pass}:\n" . $process->getOutput() . $process->getErrorOutput());

                foreach ($expectedFiles as $path => $expected) {
                    $this->assertSame($expected, trim(file_get_contents($path)), "Pass {$pass}: " . basename($path));
                }

                require_once $directory . '/app/behavioral_configuration.php';
                $this->assertBehavioralConfiguration();
                $this->assertBehavioralConfiguration(legacy: true);

                require_once $directory . '/app/deprecated_column_and_support_methods.php';
                $this->assertSame(['42', '3.5'], Fixtures\DeprecatedColumnAndSupportMethods\numericIndicators(42, 3.5));
                $this->assertSame(['42', '3.5'], LegacyFixtures\DeprecatedColumnAndSupportMethods\numericIndicators(42, 3.5));

                require_once $directory . '/app/panel_integrations.php';
                $this->assertPanelIntegrations();

                require_once $directory . '/app/behavioral_configuration_expressions.php';
                require_once $directory . '/app/configuration_hooks.php';
                require_once $directory . '/app/panel_registration.php';
                require_once $directory . '/app/menu_items.php';
                $this->assertConfigurationMigrations();

                require_once $directory . '/app/frontend_keys.php';
                require_once $directory . '/app/query_builder_icons.php';
                $this->assertFrontendKeys();

                require_once $directory . '/app/php_type_names.php';
                require_once $directory . '/app/password_reset_icon_constants.php';
                require_once $directory . '/app/row_loop_arguments.php';
                $this->assertPhpTypeNames();

                require_once $directory . '/app/filter_and_alignment_parent_aliases.php';
                $this->assertFilterAndAlignmentParentCalls();

                $runtime = new Process([PHP_BINARY, '-r', <<<'PHP'
                    require $argv[1] . '/vendor/autoload.php';
                    require $argv[2] . '/app/deprecated_number_helpers.php';
                    require $argv[2] . '/app/deprecated_exposed_attribute.php';
                    require $argv[2] . '/app/number_helper_variables.php';
                    $results = \Illuminate\Support\Number::withLocale('en', fn () =>
                        \Illuminate\Support\Number::withCurrency('EUR', fn () =>
                            \Filament\Tests\Upgrade\Fixtures\NumberHelpers\results()));
                    foreach (['increment', 'inherited', 'current'] as $method) {
                        $attribute = (new ReflectionMethod(\Filament\Tests\Upgrade\Fixtures\ExposedAttribute\Counter::class, $method))->getAttributes()[0]->newInstance();
                        if (! $attribute instanceof \Filament\Support\Components\Attributes\Exposed) {
                            throw new RuntimeException('Exposure was lost.');
                        }
                    }
                    echo json_encode([
                        array_slice($results, 0, 9),
                        \Illuminate\Support\Number::withLocale('de', fn () => \Filament\Tests\Upgrade\Fixtures\NumberHelpers\results()[0]),
                        \Illuminate\Support\Number::withLocale('en', fn () => \Filament\Tests\Upgrade\Fixtures\NumberVariables\format(1234.75, 'USD')),
                    ]);
                    PHP, $root, $directory], $root);
                $runtime->mustRun();
                $this->assertSame([
                    ['1,234.5', '-1,234.5', '42', '$1,234.50', '-$42.50', '£125.00', '-€1.25', '$987.65', '€12.50'],
                    '1.234,5',
                    ['1,234.75', '-$12.35', '$1,234.75'],
                ], json_decode($runtime->getOutput(), associative: true), "Runtime pass {$pass}");
            }

            // Reintroduce every migratable fixture, not just a subset of rules.
            $reintroducedFiles = [];

            foreach ($originalFiles as $path => $input) {
                if (trim($input) === $expectedFiles[$path]) {
                    continue;
                }

                file_put_contents($path, $input);
                $reintroducedFiles[$path] = $input;
            }

            $process = $runUpgrade(true);
            $this->assertSame(2, $process->getExitCode(), $process->getOutput() . $process->getErrorOutput());

            foreach ($reintroducedFiles as $path => $input) {
                $this->assertSame($input, file_get_contents($path));
            }

            $process = $runUpgrade(false);
            $this->assertSame(0, $process->getExitCode(), $process->getOutput() . $process->getErrorOutput());

            foreach ($expectedFiles as $path => $expected) {
                $this->assertSame($expected, trim(file_get_contents($path)));
            }

            for ($pass = 1; $pass <= 2; $pass++) {
                $process = $runUpgrade(true);
                $this->assertSame(0, $process->getExitCode(), $process->getOutput() . $process->getErrorOutput());
            }
        } finally {
            (new Filesystem)->deleteDirectory($directory);
        }
    }

    private function assertFilterAndAlignmentParentCalls(): void
    {
        $table = Table::make(app(PostsTable::class))->query(Post::query());

        foreach ([LegacyRelationshipFilter::class, Fixtures\FilterAndAlignmentParents\InheritedFilter::class] as $filterClass) {
            $filter = $filterClass::make('author')->table($table);
            $this->assertFalse($filter->queriesRelationships());
            $filter->relationship(static fn (): string => 'author', 'name');
            $this->assertTrue($filter->queriesRelationships());
            $this->assertSame('users.id', $filter->getRelationshipKey());
            $query = (new User)->setTable('related_alias')->newQuery();
            $this->assertSame('related_alias.id', $filter->getRelationshipKey(query: $query));
        }

        foreach ([LegacyAlignmentPage::class, Fixtures\FilterAndAlignmentParents\InheritedPage::class] as $pageClass) {
            $page = new $pageClass;
            $pageClass::alignFormActionsLeft();
            $this->assertSame(Alignment::Start, $page->getFormActionsAlignment());
            $pageClass::configureRight();
            $this->assertSame(Alignment::End, $page->getFormActionsAlignment());
            $page->configureLeft();
            $this->assertSame(Alignment::Start, $page->getFormActionsAlignment());
        }
    }

    private function assertPhpTypeNames(): void
    {
        $field = Fixtures\PhpTypeNames\LimitedField::make('items')->minItems(2)->maxItems(7);
        $this->assertSame(2, $field->getMinItems());
        $this->assertSame(7, $field->maximum());

        $rowLoop = (object) ['index' => 3, 'iteration' => 4];
        $column = Fixtures\PhpTypeNames\LoopColumn::make('title')->rowLoop(rowLoop: $rowLoop);
        $this->assertSame($rowLoop, $column->currentRow());
        $this->assertNull($column->rowLoop(null)->getRowLoop());

        $this->assertSame([$rowLoop, null, $rowLoop, $rowLoop, null], Fixtures\RowLoopArguments\configure($column, null, $rowLoop));
        $this->assertSame([$rowLoop, $rowLoop, $rowLoop, $rowLoop, null], Fixtures\RowLoopArguments\configure($column, $column, $rowLoop));

        $table = Fixtures\PhpTypeNames\LoadingTable::make(app(Fixtures\PhpTypeNames\LoadingComponent::class));
        $this->assertFalse($table->isLazyLoaded());
        $this->assertTrue($table->lazyLoaded()->isLazyLoaded());
        $this->assertFalse($table->lazyLoaded(false)->isLazyLoaded());

        Table::configureUsing(
            static fn (Table $table) => $table->lazyLoaded(),
            during: fn () => \Livewire\Livewire::test(Fixtures\PhpTypeNames\LoadingComponent::class)
                ->assertSet('isTableLoaded', false)
                ->call('loadTable')
                ->assertSet('isTableLoaded', true),
        );

        $position = Fixtures\PhpTypeNames\position();
        $this->assertSame(HeaderActionsPosition::Bottom, $position);
        $this->assertSame($position, unserialize(serialize($position)));

        Fixtures\PhpTypeNames\registerChartColors();
        $this->assertSame(['bg' => 300], app(ChartComponent::class)->getColorMap([]));
    }

    private function assertFrontendKeys(): void
    {
        foreach (Fixtures\QueryBuilderIcons\aliases() as $alias => $constant) {
            $this->assertSame($alias, $constant);
            FilamentIcon::register([$alias => Heroicon::Star]);
            $this->assertSame(Heroicon::Star, FilamentIcon::resolve(['custom::missing', $constant]));
        }

        $aliases = array_merge(
            (new ReflectionClass(FormsIconAlias::class))->getConstants(),
            (new ReflectionClass(SchemasIconAlias::class))->getConstants(),
        );

        foreach (Fixtures\FrontendKeys\iconAliases() as $index => $alias) {
            $this->assertContains($alias, $aliases);
            $icon = new HtmlString('<svg data-upgraded-icon="' . $index . '"></svg>');
            FilamentIcon::register([$alias => $icon]);
            $this->assertSame($icon, FilamentIcon::resolve(['custom::missing', $alias]));
            $this->assertStringContainsString($icon->toHtml(), Blade::render('<x-filament::icon :alias="$alias" />', ['alias' => $alias]));
        }

        $hooks = [PanelsRenderHook::PAGE_SUB_NAVIGATION_MOBILE_MENU_BEFORE, PanelsRenderHook::PAGE_SUB_NAVIGATION_MOBILE_MENU_AFTER];
        $this->assertSame($hooks, Fixtures\FrontendKeys\renderHooks());
        $view = app()->build(ViewManager::class);

        foreach (Fixtures\FrontendKeys\renderHooks() as $index => $hook) {
            $html = '<span data-upgraded-hook="' . $index . '"></span>';
            $view->registerRenderHook($hook, static fn (): HtmlString => new HtmlString($html), scopes: $hook);
            $this->assertTrue($view->hasRenderHook($hooks[$index], scopes: $hook));
            $this->assertSame($html, $view->renderHook($hooks[$index], scopes: $hook)->toHtml());
        }
    }

    private function assertConfigurationMigrations(): void
    {
        foreach (['Fixtures', 'LegacyFixtures'] as $fixtureNamespace) {
            $functionNamespace = __NAMESPACE__ . '\\' . $fixtureNamespace . '\\BehavioralExpressions\\';
            $events = [];
            $condition = true;
            $components = ($functionNamespace . 'components')($events, $condition);
            Schema::make(Livewire::make())->components(array_filter($components, static fn ($component): bool => $component instanceof Field))->getComponents();

            $this->assertSame(['eager'], $events);
            $this->assertFalse($components[0]->isPreviewable());
            $condition = false;
            $this->assertTrue($components[0]->isPreviewable());
            $this->assertFalse($components[1]->isPreviewable());
            $this->assertSame('16:9', $components[2]->getImageAspectRatio());
            $this->assertTrue($components[2]->shouldAutomaticallyCropImagesToAspectRatio());
            $this->assertSame(['eager', 'preview', 'preview', 'ratio', 'ratio'], $events);
            $this->assertSame('off', $components[3]->getAutocomplete());
            $this->assertFalse((clone $components[4])->label('clone')->isAddable());
            $this->assertFalse((clone $components[5])->label('clone')->canSelectPlaceholder());
            $this->assertSame(Alignment::End, $components[6]->getModalAlignment());
            $this->assertFalse($components[7]->canCreateAnother());
            $this->assertSame(Heroicon::Clock, $components[8]->getSuffixIcon());
            $this->assertTrue($components[8]->isSuffixInline());
            ($functionNamespace . 'configureTyped')($components[0], $components[6]);
            $this->assertTrue($components[0]->isPreviewable());
            $this->assertSame(Alignment::Center, $components[6]->getModalAlignment());
        }

        $originalLocale = app()->getLocale();

        try {
            app()->setLocale('en');
            $this->assertSame('Person', Fixtures\ConfigurationHooks\Labels::getModelLabel());
            $this->assertSame('People', Fixtures\ConfigurationHooks\Labels::getPluralModelLabel());
            $this->assertSame('Mouse', Fixtures\ConfigurationHooks\LabelGetters::getModelLabel());
            $this->assertSame('Mice', Fixtures\ConfigurationHooks\LabelGetters::getPluralModelLabel());
            app()->setLocale('ja');
            $this->assertSame('Mouse', Fixtures\ConfigurationHooks\LabelGetters::getPluralModelLabel());
            app()->setLocale('en');

            $create = new Fixtures\ConfigurationHooks\Create;
            $notification = new ReflectionMethod($create, 'getCreatedNotification');
            $this->assertSame('Created', $notification->invoke($create)->getTitle());
            $create->message = '';
            $this->assertNull($notification->invoke($create));
            $create->message = 'Custom created';
            $this->assertSame('Custom created', $notification->invoke($create)->getTitle());
            $this->assertSame('Settings saved', (new Fixtures\ConfigurationHooks\Settings)->getSavedNotificationTitle());

            $page = new Fixtures\ConfigurationHooks\Actions;
            $page->cacheInteractsWithHeaderActions();
            $this->assertSame(['export'], array_map(static fn ($action): string => $action->getName(), $page->getCachedHeaderActions()));
            $stats = new Fixtures\ConfigurationHooks\Stats;
            $this->assertSame(31, (new ReflectionMethod($stats, 'getStats'))->invoke($stats)[0]->getValue());
            $widget = new Fixtures\ConfigurationHooks\Filters;
            $widget->pageFilters = ['period' => 'quarter'];
            $this->assertSame('quarter', $widget->selectedPeriod());
        } finally {
            app()->setLocale($originalLocale);
        }

        $originalRegistry = app(PanelRegistry::class);
        $originalManager = Filament::getFacadeRoot();
        $originalPanel = $originalManager->getCurrentOrDefaultPanel();
        app()->instance(PanelRegistry::class, new PanelRegistry);
        Filament::swap(app()->build(FilamentManager::class));

        try {
            $default = Panel::make()->id('upgrade-default')->default();
            $current = Panel::make()->id('upgrade-current');
            app(PanelRegistry::class)->register($default);
            app(PanelRegistry::class)->register($current);
            Filament::setCurrentPanel($current);
            $events = [];
            Fixtures\PanelRegistration\register($events);
            $this->assertSame(['groups'], $events);
            $this->assertSame(['First', 'Second'], $default->getNavigationGroups());
            $this->assertSame([], $current->getNavigationGroups());

            $default->navigationGroups(['Third']);
            Fixtures\PanelRegistration\register($events);
            $this->assertSame(['First', 'Second', 'Third', 'First', 'Second'], $default->getNavigationGroups());

            // Resolve menus against the test panel's real routes and user.
            Filament::setCurrentPanel($originalPanel);
            $this->actingAs(User::factory()->create());
            Fixtures\MenuItems\register($default);
            $groups = $default->getUserMenuItemGroups();
            $this->assertSame(['profile', 'help-support'], array_keys($groups[0]));
            $this->assertSame('My profile', $groups[0]['profile']->getLabel());
            $this->assertNull($groups[0]['profile']->getIcon());
            $this->assertSame(0, $groups[0]['profile']->getSort());
            $this->assertSame('/help', $groups[0]['help-support']->getUrl());
            $this->assertFalse($groups[0]['help-support']->shouldOpenUrlInNewTab());
            $this->assertSame('warning', $groups[0]['help-support']->getColor());
            $this->assertFalse($groups[1]['hidden']->isVisible());
            $this->assertTrue($groups[1]['logout']->shouldPostToUrl());
            $this->assertSame('', $groups[1]['logout']->getUrl());

            $originalTenant = Filament::getTenant();
            Filament::setCurrentPanel('tenant-menu-flat');
            Filament::setTenant(Team::factory()->create(), isQuiet: true);

            try {
                $configuredActions = [];
                $tenantGroups = Action::configureUsing(
                    static function (Action $action) use (&$configuredActions): void {
                        $configuredActions[] = $action;
                    },
                    during: fn (): array => $default->getTenantMenuItemGroups(),
                );
                $this->assertSame(['profile', 'billing', 'register'], array_map(static fn (Action $action): string => $action->getName(), $configuredActions));
                $this->assertSame($configuredActions[0], $tenantGroups[0]['profile']);
                $this->assertSame('Organization', $tenantGroups[0]['profile']->getLabel());
                $this->assertSame(-1, $tenantGroups[0]['profile']->getSort());
            } finally {
                Filament::setTenant($originalTenant, isQuiet: true);
                Filament::setCurrentPanel($originalPanel);
            }

            app()->instance(PanelRegistry::class, new PanelRegistry);
            $events = [];

            try {
                Fixtures\PanelRegistration\register($events);
                $this->fail('Missing default panel should throw.');
            } catch (LogicException $exception) {
                $this->assertSame('Please use the `navigationGroups()` method on the panel configuration to register navigation groups. See the documentation - https://filamentphp.com/docs/panels/navigation#customizing-navigation-groups', $exception->getMessage());
                $this->assertSame(['groups'], $events);
            }
        } finally {
            app()->instance(PanelRegistry::class, $originalRegistry);
            Filament::swap($originalManager);
        }
    }

    private function assertPanelIntegrations(): void
    {
        $originalAssets = FilamentAsset::getFacadeRoot();
        $originalView = FilamentView::getFacadeRoot();
        FilamentAsset::swap(app()->build(AssetManager::class));
        FilamentView::swap(app()->build(ViewManager::class));

        try {
            $events = [];
            $this->assertSame([null, null, 'firstsecond'], Fixtures\PanelIntegrations\register($events));
            $this->assertSame([[[], []]], $events);
            $this->assertSame('firstsecondscoped', FilamentView::renderHook('upgrade.panel', scopes: 'only', data: ['value' => 13])->toHtml());
            $this->assertSame([[[], []], [['only'], ['value' => 13]]], $events);
            $this->assertSame(['legacy' => 17, 'shared' => 'new', 'package' => 29], FilamentAsset::getScriptData());
            $this->assertSame(['legacy' => 17, 'shared' => 'new'], FilamentAsset::getScriptData(['other-package']));

        } finally {
            FilamentAsset::swap($originalAssets);
            FilamentView::swap($originalView);
        }
    }

    private function assertBehavioralConfiguration(bool $legacy = false): void
    {
        $components = $legacy ? LegacyFixtures\BehavioralConfiguration\components() : Fixtures\BehavioralConfiguration\components();

        Schema::make(Livewire::make())->components(array_filter($components, static fn ($component): bool => $component instanceof Field))->getComponents();

        $this->assertFalse($components[0]->isPreviewable());
        $this->assertTrue($components[1]->isPreviewable());
        $this->assertSame('16:9', $components[1]->getAutomaticallyCropImagesAspectRatio());
        $this->assertFalse($components[2]->shouldAutomaticallyCropImagesToAspectRatio());
        $this->assertTrue($components[3]->shouldAutomaticallyCropImagesToAspectRatio());
        $this->assertFalse($components[4]->shouldAutomaticallyCropImagesToAspectRatio());
        $this->assertNull($components[4]->getImageAspectRatio());
        $this->assertSame([false, true, false], [$components[5]->isAddable(), $components[5]->isDeletable(), $components[5]->isReorderable()]);
        $this->assertSame([true, false, true], [$components[6]->isAddable(), $components[6]->isDeletable(), $components[6]->isReorderable()]);
        $this->assertSame([false, true, true, false], [$components[7]->isAddable(), $components[7]->isDeletable(), $components[7]->canEditKeys(), $components[7]->canEditValues()]);
        $this->assertSame([null, 'off'], [$components[8]->getAutocomplete(), $components[8]->getAutocapitalize()]);
        $this->assertSame(['off', null], [$components[9]->getAutocomplete(), $components[9]->getAutocapitalize()]);
        $this->assertNull($components[10]->getAutocomplete());
        $this->assertFalse($components[11]->canSelectPlaceholder());
        $this->assertTrue($components[12]->canSelectPlaceholder());
        $this->assertSame('H:i', $components[13]->getInternalFormat());
        $this->assertSame(Heroicon::Calendar, $components[13]->getSuffixIcon());
        $this->assertTrue($components[13]->isSuffixInline());
        $this->assertSame(Heroicon::Calendar, $components[14]->getSuffixIcon());
        $this->assertSame('heroicon-o-clock', $components[15]->getSuffixIcon());
        $this->assertSame('heroicon-o-star', $components[16]->getSuffixIcon());
        $this->assertFalse($components[16]->isSuffixInline());
        $this->assertSame(Alignment::Start, $components[17]->getModalAlignment());
        $this->assertSame(Alignment::End, $components[18]->getModalAlignment());
        $this->assertSame(Alignment::Center, $components[19]->getModalAlignment());
        $this->assertFalse($components[20]->canCreateAnother());
        $this->assertTrue($components[21]->canAttachAnother());
        $this->assertFalse($components[22]->canAssociateAnother());
        $this->assertFalse($components[23]->shouldAutomaticallyCropImagesToAspectRatio());
        $this->assertSame('16:9', $components[23]->getImageAspectRatio());
        $this->assertSame(Alignment::Start, $components[24]->getModalAlignment());
        $this->assertSame(['on', 'words'], [$components[25]->getAutocomplete(), $components[25]->getAutocapitalize()]);

    }
}

// Preserve the pinned v5 no-op only for the original side of the runtime pair.
class LegacyInsetBuilder extends Builder
{
    public function inset(bool | Closure $condition = true): static
    {
        return $this;
    }
}

class LegacyInsetRepeater extends Repeater
{
    public function inset(bool | Closure $condition = true): static
    {
        return $this;
    }
}

// Preserve the original forwarding contracts only on the legacy side of the pair.
class LegacyRelationshipFilter extends SelectFilter
{
    public function queriesRelationships(): bool
    {
        return $this->hasRelationship();
    }

    public function getRelationshipKey(?\Illuminate\Database\Eloquent\Builder $query = null): ?string
    {
        return $this->getQualifiedRelatedKeyNameForRelationship($query);
    }
}

class LegacyAlignmentPage extends Page
{
    public static function alignFormActionsLeft(): void
    {
        static::alignFormActionsStart();
    }

    public static function configureRight(): void
    {
        static::alignFormActionsEnd();
    }

    public function configureLeft(): void
    {
        static::alignFormActionsStart();
    }
}
