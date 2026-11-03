<?php

namespace Filament\Tests\Upgrade;

use Error;
use Filament\Actions\Action;
use Filament\Actions\ExportAction;
use Filament\Actions\Exports\Models\Export;
use Filament\Facades\Filament;
use Filament\FilamentManager;
use Filament\Forms\Components\Field;
use Filament\Forms\Components\FileUpload;
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
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\HtmlString;
use LogicException;
use ReflectionClass;
use ReflectionMethod;
use ReflectionProperty;
use Symfony\Component\Process\Process;
use TraitHookConsumer\OtherResource;
use TraitHookConsumer\PropertyResource;
use TypeError;

class PersistentUpgradeSetTest extends TestCase
{
    public function test_convenience_factories_preserve_styles_and_forced_filter_mode(): void
    {
        $directory = sys_get_temp_dir() . '/filament-convenience-' . bin2hex(random_bytes(8));
        mkdir($directory);
        $root = dirname(__DIR__, 3);
        $source = <<<'PHP'
            <?php

            use Filament\Actions\ButtonAction;
            use Filament\Actions\IconButtonAction;
            use Filament\Tables\Filters\MultiSelectFilter;
            use Filament\Tables\Filters\SelectFilter;

            $events = [];
            $components = [
                ButtonAction::make(name: 'save')->label('Save'),
                IconButtonAction::make('edit')->label('Edit')->icon('heroicon-m-pencil'),
                ButtonAction::make('view')->link()->label('View'),
                IconButtonAction::make('open')->button()->label('Open'),
                MultiSelectFilter::make('status')->options(['draft' => 'Draft', 'published' => 'Published'])->default(['published']),
                MultiSelectFilter::make('forced')->multiple(static function () use (&$events): bool {
                    $events[] = 'evaluated';
                    return false;
                }),
                MultiSelectFilter::make('side-effect')->multiple((static function () use (&$events): bool {
                    $events[] = 'constructed';
                    return false;
                })())->tap(static function (SelectFilter $filter) use (&$events): void {
                    $events[] = $filter->isMultiple();
                }),
            ];

            return [$components, &$events];
            PHP;

        try {
            $original = str_replace(
                ['use Filament\\Actions\\ButtonAction;', 'use Filament\\Actions\\IconButtonAction;', 'use Filament\\Tables\\Filters\\MultiSelectFilter;'],
                ['use Filament\\Tests\\Upgrade\\LegacyButtonAction as ButtonAction;', 'use Filament\\Tests\\Upgrade\\LegacyIconButtonAction as IconButtonAction;', 'use Filament\\Tests\\Upgrade\\LegacyMultipleFilter as MultiSelectFilter;'],
                $source,
            );
            [$legacyComponents, $legacyEvents] = eval('?>' . $original);
            $snapshot = static fn (array $components): array => array_map(
                static fn (Action | SelectFilter $component): array => $component instanceof Action
                    ? [$component->getName(), $component->getLabel(), $component->isButton(), $component->isIconButton(), $component->isLink()]
                    : [$component->getName(), $component->isMultiple(), $component->getDefaultState(), $component->getOptions()],
                $components,
            );
            $expected = [
                ['save', 'Save', true, false, false],
                ['edit', 'Edit', false, true, false],
                ['view', 'View', false, false, true],
                ['open', 'Open', true, false, false],
                ['status', true, ['published'], ['draft' => 'Draft', 'published' => 'Published']],
                ['forced', true, null, []],
                ['side-effect', true, null, []],
            ];
            $this->assertSame($expected, $snapshot($legacyComponents));
            $this->assertSame(['constructed', true], $legacyEvents);
            file_put_contents($directory . '/components.php', $source);

            foreach ([false, true] as $dryRun) {
                (new Process([
                    PHP_BINARY, $root . '/vendor/bin/rector', 'process', $directory . '/components.php',
                    '--config', $root . '/packages/upgrade/src/rector.php', '--clear-cache', '--no-progress-bar',
                    ...($dryRun ? ['--dry-run'] : []),
                ], $root, ['TMPDIR' => $directory]))->setTimeout(120)->mustRun();
            }

            [$components, $events] = require $directory . '/components.php';
            $this->assertSame($expected, $snapshot($components));
            $this->assertSame(['constructed', true], $events);
        } finally {
            (new Filesystem)->deleteDirectory($directory);
        }
    }

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

            $legacyPath = $directory . '/legacy_api_parameters.php';
            file_put_contents($legacyPath, str_replace(
                'namespace Filament\\Tests\\Upgrade\\Fixtures\\',
                'namespace Filament\\Tests\\Upgrade\\LegacyFixtures\\',
                $originalFiles[$directory . '/app/api_parameters.php'],
            ));
            require_once $legacyPath;

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

                require_once $directory . '/app/deprecated_column_and_support_methods.php';
                $this->assertSame(['42', '3.5'], Fixtures\DeprecatedColumnAndSupportMethods\numericIndicators(42, 3.5));

                require_once $directory . '/app/api_parameters.php';
                $this->assertSame('TITLE!', Fixtures\ApiParameters\render());
                $this->assertSame('TITLE!', LegacyFixtures\ApiParameters\render());

                require_once $directory . '/app/api_parameter_overrides.php';
                $this->assertSame(['Trait', 11, 7, 0, false, null], Fixtures\ApiParameterOverrides\result());

                require_once $directory . '/app/panel_integrations.php';
                $this->assertPanelIntegrations();

                require_once $directory . '/app/behavioral_configuration_expressions.php';
                require_once $directory . '/app/stored_behavioral_configuration.php';
                require_once $directory . '/app/configuration_hooks.php';
                require_once $directory . '/app/trait_configuration_hooks.php';
                require_once $directory . '/app/forwarding_configuration_hooks.php';
                require_once $directory . '/app/panel_registration.php';
                require_once $directory . '/app/menu_items.php';
                $this->assertConfigurationMigrations();
                $this->assertForwardingConfigurationHooks();

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

    public function test_trait_hooks_preserve_cross_file_names_and_lexical_boundaries(): void
    {
        $directory = sys_get_temp_dir() . '/filament-trait-hooks-' . bin2hex(random_bytes(8));
        mkdir($directory);
        mkdir($directory . '/app');
        $root = dirname(__DIR__, 3);

        try {
            $sources = [
                'Source.php' => <<<'PHP'
                    <?php
                    namespace TraitHookSource;
                    use Attribute;
                    use Filament\Actions\Action as SharedAction;
                    const PREFIX = 'Trait:';
                    function formatLabel(int $number): string { return (string) $number; }
                    trait HasLabel {
                        public static function getLabel(): ?string { return PREFIX . formatLabel('17'); }
                    }
                    trait HasActions {
                        protected function getActions(): array { return [SharedAction::make('source')->action(fn () => null)]; }
                    }
                    #[Attribute(Attribute::TARGET_PROPERTY)]
                    class Metadata { public function __construct(public string $value) {} }
                    trait HasProperty {
                        #[Metadata(123)]
                        protected static ?string $label = null;
                    }
                    PHP,
                'OtherSource.php' => <<<'PHP'
                    <?php
                    namespace OtherTraitHookSource;
                    const PREFIX = 'Other:';
                    function formatLabel(int $number): string { return (string) $number; }
                    trait HasLabel {
                        public static function getLabel(): ?string { return PREFIX . formatLabel('19'); }
                    }
                    PHP,
                'Existing.php' => <<<'PHP'
                    <?php
                    namespace ExistingTraitHookImports;
                    const filamentUpgradePREFIXValue = 'Wrong imported alias:';
                    function filamentUpgradeFormatLabelValue(int $number): string { return 'wrong imported alias'; }
                    PHP,
                'Consumer.php' => <<<'PHP'
                    <?php
                    namespace TraitHookConsumer;
                    use function ExistingTraitHookImports\filamentUpgradeFormatLabelValue;
                    use const ExistingTraitHookImports\{filamentUpgradePREFIXValue};
                    use TraitHookSource\HasLabel;
                    use TraitHookSource\HasActions;
                    use TraitHookSource\HasProperty;
                    use Filament\Forms\Components\TextInput as SharedAction;
                    const PREFIX = 'Wrong:';
                    const filamentUpgradePREFIX = 'Wrong alias:';
                    function formatLabel(int $number): string { return 'wrong'; }
                    function filamentUpgradeFormatLabel(int $number): string { return 'wrong alias'; }
                    class Resource extends \Filament\Resources\Resource { use HasLabel; }
                    use function TraitHookSource\formatLabel as laterFormatLabel;
                    class OtherResource extends \Filament\Resources\Resource { use \OtherTraitHookSource\HasLabel; }
                    class PropertyResource extends \Filament\Resources\Resource { use HasProperty; }
                    class Page extends \Filament\Pages\Page { use HasActions; }
                    PHP,
                'Strict.php' => <<<'PHP'
                    <?php
                    declare(STRICT_TYPES=1);
                    namespace TraitHookStrict;
                    use Filament\Resources\Resource as BaseResource;
                    use TraitHookSource\HasProperty;
                    use TraitHookSource\HasLabel;
                    class Resource extends BaseResource { use HasProperty; }
                    class GetterResource extends BaseResource { use HasLabel; }
                    PHP,
                'Ticks.php' => <<<'PHP'
                    <?php
                    declare(TICKS=1);
                    namespace TraitHookTicks;
                    use Filament\Pages\Page as BasePage;
                    trait HasActions {
                        protected function getActions(): array { $actions = []; return $actions; }
                    }
                    class Page extends BasePage { use HasActions; }
                    PHP,
            ];

            foreach ($sources as $name => $source) {
                file_put_contents($directory . '/app/' . $name, $source);
            }

            foreach ([false, true] as $dryRun) {
                (new Process([
                    PHP_BINARY, $root . '/vendor/bin/rector', 'process', $directory . '/app',
                    '--config', $root . '/packages/upgrade/src/rector.php', '--clear-cache', '--no-progress-bar',
                    ...($dryRun ? ['--dry-run'] : []),
                ], $root, ['TMPDIR' => $directory]))->setTimeout(120)->mustRun();
            }

            foreach (['Source.php', 'OtherSource.php', 'Existing.php', 'Strict.php', 'Ticks.php'] as $name) {
                $this->assertSame($sources[$name], file_get_contents($directory . '/app/' . $name));
            }

            require $directory . '/app/Source.php';
            require $directory . '/app/OtherSource.php';
            require $directory . '/app/Existing.php';
            require $directory . '/app/Consumer.php';
            $this->assertSame('Trait:17', \TraitHookConsumer\Resource::getModelLabel());
            $this->assertSame('Other:19', OtherResource::getModelLabel());
            $property = new ReflectionProperty(PropertyResource::class, 'modelLabel');
            $this->assertSame('123', $property->getAttributes()[0]->newInstance()->value);
            $page = new \TraitHookConsumer\Page;
            $page->cacheInteractsWithHeaderActions();
            $this->assertSame(['source'], array_map(static fn (Action $action): string => $action->getName(), $page->getCachedHeaderActions()));
        } finally {
            (new Filesystem)->deleteDirectory($directory);
        }
    }

    public function test_manager_integrations_preserve_receivers_arguments_and_overrides(): void
    {
        $directory = sys_get_temp_dir() . '/filament-manager-' . bin2hex(random_bytes(8));
        mkdir($directory);
        $root = dirname(__DIR__, 3);
        $source = explode("-----\n", file_get_contents(__DIR__ . '/Fixtures/panel_integrations.php.inc'), 2)[0];
        $source = str_replace('Fixtures\\PanelIntegrations', 'Fixtures\\ManagerIntegrations', $source);
        $path = $directory . '/manager.php';
        file_put_contents($path, $source);
        $scalarSource = <<<'PHP'
            <?php
            declare(strict_types=1);

            namespace ManagerStrict;

            use Filament\FilamentManager;

            function theme(FilamentManager $manager, mixed $theme): void
            {
                $manager->registerViteTheme($theme);
            }

            function nullableTheme(?FilamentManager $manager, mixed $theme): void
            {
                $manager?->registerViteTheme($theme);
            }

            class CustomManager extends FilamentManager
            {
                public string | array | null $theme = null;

                public function registerViteTheme(string | array $theme, ?string $buildDirectory = null): void
                {
                    $this->theme = $theme;
                }
            }
            PHP;
        file_put_contents($directory . '/strict.php', $scalarSource);
        file_put_contents($directory . '/weak.php', str_replace(['strict_types=1', 'ManagerStrict'], ['strict_types=0', 'ManagerWeak'], $scalarSource));
        $originalRegistry = app(PanelRegistry::class);
        $originalManager = Filament::getFacadeRoot();
        $originalAssets = FilamentAsset::getFacadeRoot();
        $originalView = FilamentView::getFacadeRoot();

        try {
            foreach ([false, true] as $dryRun) {
                (new Process([
                    PHP_BINARY, $root . '/vendor/bin/rector', 'process', $path,
                    $directory . '/strict.php', $directory . '/weak.php',
                    '--config', $root . '/packages/upgrade/src/rector.php', '--clear-cache', '--no-progress-bar',
                    ...($dryRun ? ['--dry-run'] : []),
                ], $root, ['TMPDIR' => $directory]))->setTimeout(120)->mustRun();
            }

            require $path;
            require $directory . '/strict.php';
            require $directory . '/weak.php';
            app()->instance(PanelRegistry::class, new PanelRegistry);
            Filament::swap(app()->build(FilamentManager::class));
            FilamentAsset::swap(app()->build(AssetManager::class));
            FilamentView::swap(app()->build(ViewManager::class));
            $default = Panel::make()->id('manager-default')->default();
            $current = Panel::make()->id('manager-current');
            app(PanelRegistry::class)->register($default);
            app(PanelRegistry::class)->register($current);
            Filament::setCurrentPanel($current);
            $events = [];

            $this->assertSame([null, null, null, null, null, null, null, null, null, 'manager', null, null], Fixtures\ManagerIntegrations\registerManager(filament(), $events));
            $this->assertSame(['receiver', 'build', 'theme', 'receiver', 'hook-created', 'manager.hook', 'hook-rendered'], $events);
            $this->assertSame(['Manager'], $default->getNavigationGroups());
            $this->assertSame([], $current->getNavigationGroups());
            $this->assertSame('theme', $default->getViteTheme());
            $this->assertSame(['manager' => 31, 'helper' => 37], FilamentAsset::getScriptData());

            $events = [];
            $this->assertNull(Fixtures\ManagerIntegrations\nullableManager(null, $events));
            $this->assertSame(['receiver'], $events);
            $this->assertSame('manager', Fixtures\ManagerIntegrations\nullableManager(filament(), $events));
            $this->assertSame(['receiver', 'receiver', 'manager.hook', 'hook-rendered'], $events);

            $events = [];
            $this->assertSame([null, 'original'], Fixtures\ManagerIntegrations\chainedManager(null, $events));
            $this->assertSame([], $events);
            $this->assertSame(['manager', 'original'], Fixtures\ManagerIntegrations\chainedManager(new Fixtures\ManagerIntegrations\ManagerHolder(filament()), $events));
            $this->assertSame(['manager.hook', 'hook-rendered'], $events);
            $events = [];

            try {
                Fixtures\ManagerIntegrations\chainedManager(new Fixtures\ManagerIntegrations\ManagerHolder(null), $events);
                $this->fail('A null manager without nullsafe access should throw.');
            } catch (Error $exception) {
                $this->assertSame('Call to a member function renderHook() on null', $exception->getMessage());
                $this->assertSame([], $events);
            }

            Fixtures\ManagerIntegrations\inheritedManager(new Fixtures\ManagerIntegrations\InheritedManager);
            $this->assertSame(41, FilamentAsset::getScriptData()['inherited']);

            $custom = new Fixtures\ManagerIntegrations\CustomManager;
            Fixtures\ManagerIntegrations\customManager($custom);
            $this->assertSame(['Custom'], $custom->groups);
            $events = [];
            Fixtures\ManagerIntegrations\registerManager($custom, $events);
            $this->assertSame(['Manager'], $custom->groups);
            $this->assertSame(['Manager'], $default->getNavigationGroups());

            $selected = Panel::make()->id('manager-selected');
            $events = [];
            Fixtures\ManagerIntegrations\registerManager(new Fixtures\ManagerIntegrations\PanelManager($selected), $events);
            $this->assertSame(['Manager'], $selected->getNavigationGroups());
            $this->assertSame(['Manager'], $default->getNavigationGroups());

            foreach (['ManagerStrict' => true, 'ManagerWeak' => false] as $namespace => $isStrict) {
                $customClass = $namespace . '\\CustomManager';
                $scalarCustom = new $customClass;

                foreach (['theme', 'nullableTheme'] as $function) {
                    foreach ([filament(), $scalarCustom] as $manager) {
                        try {
                            ($namespace . '\\' . $function)($manager, 42);
                            $this->assertFalse($isStrict, 'Strict scalar arguments must not be coerced.');
                            $this->assertSame('42', $manager === $scalarCustom ? $scalarCustom->theme : $default->getViteTheme());
                        } catch (TypeError $exception) {
                            $this->assertTrue($isStrict);
                        }
                    }
                }
            }

            app()->instance(PanelRegistry::class, new PanelRegistry);
            $events = [];

            try {
                Fixtures\ManagerIntegrations\registerManager(filament(), $events);
                $this->fail('Missing default panel should throw.');
            } catch (LogicException $exception) {
                $this->assertSame('Please use the `navigationGroups()` method on the panel configuration to register navigation groups. See the documentation - https://filamentphp.com/docs/panels/navigation#customizing-navigation-groups', $exception->getMessage());
                $this->assertSame(['receiver'], $events);
            }
        } finally {
            app()->instance(PanelRegistry::class, $originalRegistry);
            Filament::swap($originalManager);
            FilamentAsset::swap($originalAssets);
            FilamentView::swap($originalView);
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

    private function assertForwardingConfigurationHooks(): void
    {
        $originalDisk = config('filament.default_filesystem_disk');

        try {
            foreach (['local', 's3'] as $disk) {
                config(['filament.default_filesystem_disk' => $disk]);

                foreach ([
                    Fixtures\ForwardingConfigurationHooks\DiskExporter::class => 'root:',
                    Fixtures\ForwardingConfigurationHooks\ChildExporter::class => 'child:root:',
                    Fixtures\ForwardingConfigurationHooks\TraitExporter::class => 'trait:',
                    Fixtures\ForwardingConfigurationHooks\ChildTraitExporter::class => 'child:trait:',
                ] as $class => $prefix) {
                    $exporter = new $class(new Export, [], []);
                    $this->assertSame($prefix . $disk, $exporter->getFileDiskName());
                    $this->assertSame($prefix . $disk, Fixtures\ForwardingConfigurationHooks\disk($exporter));
                    $this->assertSame($prefix . $disk, Fixtures\ForwardingConfigurationHooks\nullableDisk($exporter));

                    if ($exporter instanceof Fixtures\ForwardingConfigurationHooks\DiskExporter) {
                        $this->assertSame($prefix . $disk, $exporter->applicationDisk());
                        $this->assertSame($disk, $exporter->defaultDisk());
                        $this->assertSame($prefix . $disk, Fixtures\ForwardingConfigurationHooks\customCallback($exporter)());
                    }
                }
            }

            $this->assertNull(Fixtures\ForwardingConfigurationHooks\nullableDisk(null));
            $this->assertSame('trait:unrelated', (new Fixtures\ForwardingConfigurationHooks\UnrelatedDiskConsumer)->getFileDisk());
        } finally {
            config(['filament.default_filesystem_disk' => $originalDisk]);
        }

        foreach ([
            Fixtures\ForwardingConfigurationHooks\TableGenerator::class,
            Fixtures\ForwardingConfigurationHooks\ChildTableGenerator::class,
            Fixtures\ForwardingConfigurationHooks\ResourceGenerator::class,
            Fixtures\ForwardingConfigurationHooks\RelationGenerator::class,
            Fixtures\ForwardingConfigurationHooks\RelatedRecordsGenerator::class,
        ] as $class) {
            $generator = app($class, [
                'fqn' => 'App\\Generated\\Posts',
                'modelFqn' => Post::class,
                'resourceFqn' => 'App\\Resources\\Posts\\PostResource',
                'pageRoutes' => [],
                'formSchemaFqn' => null,
                'infolistSchemaFqn' => null,
                'tableFqn' => null,
                'clusterFqn' => null,
                'parentResourceFqn' => null,
                'relatedResourceFqn' => null,
                'recordTitleAttribute' => 'title',
                'hasViewOperation' => true,
                'isGenerated' => false,
                'isSoftDeletable' => true,
                'isSimple' => true,
                'relationship' => 'posts',
                'relatedModelFqn' => Post::class,
                'relationshipType' => null,
            ]);
            $output = $generator->generate();
            $this->assertStringContainsString('// record-hook', $output);
            $this->assertStringContainsString('// bulk-hook', $output);
            $this->assertContains(Action::class, $generator->getTableRecordActions());

            if ($generator instanceof Fixtures\ForwardingConfigurationHooks\ChildTableGenerator) {
                $this->assertContains(ExportAction::class, $generator->getTableRecordActions());
                $this->assertStringStartsWith('// child-record' . PHP_EOL . '// record-hook', $generator->outputTableRecordActions());
                $this->assertStringStartsWith('// child-bulk' . PHP_EOL . '// bulk-hook', $generator->outputTableBulkActions());
            }
        }

        foreach ([
            Fixtures\ForwardingConfigurationHooks\DirectGenerator::class => [[Action::class], '// direct-record', '// direct-bulk'],
            Fixtures\ForwardingConfigurationHooks\ChildDirectGenerator::class => [[Action::class, ExportAction::class], '// child' . PHP_EOL . '// direct-record', '// child' . PHP_EOL . '// direct-bulk'],
        ] as $class => $expected) {
            $generator = new $class;
            $generator->generate();
            $this->assertSame($expected, [$generator->getTableRecordActions(), $generator->outputTableRecordActions(), $generator->outputTableBulkActions()]);
        }

        $generator = new Fixtures\ForwardingConfigurationHooks\DirectDefaults;
        $generator->generate();
        $this->assertSame([['Filament\\Actions\\EditAction'], 'EditAction::make(),', 'DeleteBulkAction::make(),'], Fixtures\ForwardingConfigurationHooks\directOutputs($generator));
    }

    private function assertConfigurationMigrations(): void
    {
        $events = [];
        $condition = true;
        $components = Fixtures\BehavioralExpressions\components($events, $condition);
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
        $this->assertFalse($components[4]->isAddable());
        $this->assertFalse($components[5]->canSelectPlaceholder());
        $this->assertSame(Alignment::End, $components[6]->getModalAlignment());
        $this->assertFalse($components[7]->canCreateAnother());
        $this->assertSame(Heroicon::Clock, $components[8]->getSuffixIcon());
        $this->assertTrue($components[8]->isSuffixInline());
        $this->assertFalse((clone $components[4])->label('clone')->isAddable());
        $this->assertFalse((clone $components[5])->label('clone')->canSelectPlaceholder());
        $repeater = (clone $components[10])->label('clone');
        $this->assertSame([false, false, false], [$repeater->isAddable(), $repeater->isDeletable(), $repeater->isReorderable()]);
        $this->assertSame(Alignment::Center, $components[11]->getModalAlignment());
        $this->assertTrue((clone $components[12])->label('clone')->canEditKeys());
        $this->assertFalse(Fixtures\BehavioralExpressions\globallyConfigured(true)->isPreviewable());
        $this->assertTrue(Fixtures\BehavioralExpressions\globallyConfigured(false)->isPreviewable());
        $this->assertFalse(Fixtures\BehavioralExpressions\globallyConfigured(static fn (FileUpload $component): bool => $component->getName() === 'global')->isPreviewable());
        $stored = Fixtures\BehavioralExpressions\stored();
        Schema::make(Livewire::make())->components([$stored])->getComponents();
        $this->assertFalse((clone $stored)->label('clone')->isAddable());
        Fixtures\BehavioralExpressions\configureTyped($components[0], $components[6]);
        $this->assertTrue($components[0]->isPreviewable());
        $this->assertSame(Alignment::Center, $components[6]->getModalAlignment());

        [$storedBuilder, $storedAction, $storedBadge] = Fixtures\StoredBehavioralConfiguration\components();
        Schema::make(Livewire::make())->components([$storedBuilder])->getComponents();
        $this->assertFalse((clone $storedBuilder)->label('clone')->isAddable());
        $this->assertTrue($storedBuilder->isDeletable());
        $this->assertSame(Alignment::Center, $storedAction->getModalAlignment());
        $this->assertFalse($storedAction->canCreateAnother());
        $this->assertSame('Ready', $storedBadge->getBadge());
        $this->assertFalse($storedBadge->isBadge());

        $originalLocale = app()->getLocale();

        try {
            app()->setLocale('en');
            $this->assertSame('Person', Fixtures\ConfigurationHooks\Labels::getModelLabel());
            $this->assertSame('People', Fixtures\ConfigurationHooks\Labels::getPluralModelLabel());
            $this->assertSame('Customer', Fixtures\ConfigurationHooks\ChildLabels::getModelLabel());
            $this->assertSame('Customers', Fixtures\ConfigurationHooks\ChildLabels::getPluralModelLabel());
            $this->assertSame('Mouse', Fixtures\ConfigurationHooks\LabelGetters::getModelLabel());
            $this->assertSame('Mice', Fixtures\ConfigurationHooks\LabelGetters::getPluralModelLabel());
            Fixtures\ConfigurationHooks\ChildLabelGetters::$labelCalls = 0;
            $this->assertSame('Field mouse', Fixtures\ConfigurationHooks\ChildLabelGetters::getModelLabel());
            $this->assertSame(1, Fixtures\ConfigurationHooks\ChildLabelGetters::$labelCalls);
            Fixtures\ConfigurationHooks\ChildLabelGetters::$labelCalls = 0;
            $this->assertSame('Field mice', Fixtures\ConfigurationHooks\ChildLabelGetters::getPluralModelLabel());
            $this->assertSame(1, Fixtures\ConfigurationHooks\ChildLabelGetters::$labelCalls);
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

            $create = new Fixtures\ConfigurationHooks\ChildCreate;
            $this->assertSame('Child created', $notification->invoke($create)->getTitle());
            $create->message = '';
            $this->assertNull($notification->invoke($create));
            $create->message = 'Custom child';
            $this->assertSame('Custom child', $notification->invoke($create)->getTitle());

            $edit = new Fixtures\ConfigurationHooks\Edit;
            $edit->record = null;
            $notification = new ReflectionMethod($edit, 'getSavedNotification');
            $this->assertSame('Saved', $notification->invoke($edit)->getTitle());
            $edit->record = new Post;
            $this->assertNull($notification->invoke($edit));

            $edit = new Fixtures\ConfigurationHooks\ChildEdit;
            $edit->record = null;
            $this->assertSame('Child saved', $notification->invoke($edit)->getTitle());
            $edit->record = new Post;
            $this->assertNull($notification->invoke($edit));

            $this->assertSame('Settings saved', (new Fixtures\ConfigurationHooks\Settings)->getSavedNotificationTitle());
            $this->assertSame('Settings saved again', (new Fixtures\ConfigurationHooks\ChildSettings)->getSavedNotificationTitle());

            $page = new Fixtures\ConfigurationHooks\Actions;
            $page->cacheInteractsWithHeaderActions();
            $this->assertSame(['export'], array_map(static fn ($action): string => $action->getName(), $page->getCachedHeaderActions()));
            $page = new Fixtures\ConfigurationHooks\ChildActions;
            $page->cacheInteractsWithHeaderActions();
            $this->assertSame(['export', 'print'], array_map(static fn ($action): string => $action->getName(), $page->getCachedHeaderActions()));
            $stats = new Fixtures\ConfigurationHooks\Stats;
            $this->assertSame(31, $stats->getSectionContentComponent()->getDefaultChildComponents()[0]->getValue());
            $stats = new Fixtures\ConfigurationHooks\ChildStats;
            $this->assertSame([31, 17], array_map(static fn ($stat) => $stat->getValue(), $stats->getSectionContentComponent()->getDefaultChildComponents()));
            $widget = new Fixtures\ConfigurationHooks\Filters;
            $widget->pageFilters = ['period' => 'quarter'];
            $this->assertSame('quarter', $widget->selectedPeriod());
            $widget = new Fixtures\ConfigurationHooks\ChildFilters;
            $widget->pageFilters = ['period' => 'month'];
            $this->assertSame('month', $widget->selectedPeriod());

            $page = new Fixtures\TraitConfigurationHooks\ChildActions;
            $page->cacheInteractsWithHeaderActions();
            $this->assertSame(['shared', 'child'], array_map(static fn ($action): string => $action->getName(), $page->getCachedHeaderActions()));
            $this->assertSame(1, $page->actionCalls);
            $page = new Fixtures\TraitConfigurationHooks\ChildTraitPage;
            $page->cacheInteractsWithHeaderActions();
            $this->assertSame(['base', 'child'], array_map(static fn ($action): string => $action->getName(), $page->getCachedHeaderActions()));
            $consumer = new Fixtures\TraitConfigurationHooks\PlainConsumer;
            $this->assertSame('unrelated', $consumer->actions()[0]);
            $this->assertSame(1, $consumer->actionCalls);

            $this->assertSame('Mouse', Fixtures\TraitConfigurationHooks\Labels::getModelLabel());
            $this->assertSame('Mice', Fixtures\TraitConfigurationHooks\Labels::getPluralModelLabel());
            Fixtures\TraitConfigurationHooks\ChildLabels::$labelCalls = 0;
            $this->assertSame('Field mouse', Fixtures\TraitConfigurationHooks\ChildLabels::getModelLabel());
            $this->assertSame(1, Fixtures\TraitConfigurationHooks\ChildLabels::$labelCalls);
            $this->assertSame('Field mice', Fixtures\TraitConfigurationHooks\ChildLabels::getPluralModelLabel());
            $this->assertSame('Customer', Fixtures\TraitConfigurationHooks\PropertyLabels::getModelLabel());
            $this->assertSame('Customers', Fixtures\TraitConfigurationHooks\PropertyLabels::getPluralModelLabel());
            $this->assertSame('Partner', Fixtures\TraitConfigurationHooks\ChildPropertyLabels::getModelLabel());
            $this->assertSame('Partners', Fixtures\TraitConfigurationHooks\ChildPropertyLabels::getPluralModelLabel());
            $this->assertSame('Child', Fixtures\TraitConfigurationHooks\ChildDeclaredPropertyLabels::getModelLabel());

            $create = new Fixtures\TraitConfigurationHooks\Create;
            $notification = new ReflectionMethod($create, 'getCreatedNotification');
            $this->assertSame('Created', $notification->invoke($create)->getTitle());
            $create->message = '';
            $this->assertNull($notification->invoke($create));
            $create->message = 'Trait created';
            $this->assertSame('Trait created', $notification->invoke($create)->getTitle());
            $create = new Fixtures\TraitConfigurationHooks\ChildCreate;
            $this->assertSame('Trait child created', $notification->invoke($create)->getTitle());
            $create->message = '';
            $this->assertNull($notification->invoke($create));

            app('translator')->addLines([
                'resources/pages/edit-record.notifications.saved.title' => 'Record saved',
            ], 'trait-testing', 'filament-panels');
            app('translator')->addLines([
                'pages/settings-page.notifications.saved.title' => 'Settings saved',
            ], 'trait-testing', 'filament-spatie-laravel-settings-plugin');
            app()->setLocale('trait-testing');
            $edit = new Fixtures\TraitConfigurationHooks\Edit;
            $notification = new ReflectionMethod($edit, 'getSavedNotification');
            $this->assertSame('Record saved', $notification->invoke($edit)->getTitle());
            $settings = new Fixtures\TraitConfigurationHooks\Settings;
            $this->assertSame('Settings saved', $settings->getSavedNotificationTitle());
            $settings->message = '';
            $this->assertSame('', $settings->getSavedNotificationTitle());
            app()->setLocale('en');

            $stats = new Fixtures\TraitConfigurationHooks\ChildStats;
            $this->assertSame([43, 19], array_map(static fn ($stat) => $stat->getValue(), $stats->getSectionContentComponent()->getDefaultChildComponents()));
            $this->assertSame('Return', (new Fixtures\ConfigurationHooks\Profile)->getCancelFormAction()->getLabel());
            $this->assertSame('Child', (new Fixtures\ConfigurationHooks\ChildProfile)->getCancelFormAction()->getLabel());

            foreach ([Fixtures\TraitConfigurationHooks\Profile::class => 'Trait return', Fixtures\TraitConfigurationHooks\ChildProfile::class => 'Trait return child'] as $profileClass => $label) {
                $profile = new $profileClass;
                $action = $profile->getCancelFormAction();
                $this->assertSame('back', $action->getName());
                $this->assertSame($label, $action->getLabel());
                $this->assertSame('gray', $action->getColor());
                $this->assertSame(1, $profile->backActionCalls);
            }
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

    private function assertBehavioralConfiguration(): void
    {
        $components = Fixtures\BehavioralConfiguration\components();

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

class LegacyButtonAction extends Action
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->button();
    }
}

class LegacyIconButtonAction extends Action
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->iconButton();
    }
}

class LegacyMultipleFilter extends SelectFilter
{
    public function isMultiple(): bool
    {
        return true;
    }
}

// Preserve the original forwarding contracts only on the legacy side of the pair.
class LegacyRelationshipFilter extends SelectFilter
{
    public function queriesRelationships(): bool
    {
        return $this->hasRelationship();
    }

    public function getRelationshipKey(?Builder $query = null): ?string
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
