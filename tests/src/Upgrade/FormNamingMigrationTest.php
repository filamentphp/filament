<?php

namespace Filament\Tests\Upgrade;

use Closure;
use Filament\Auth\MultiFactor\MultiFactorChallenge;
use Filament\Forms\Components\Field;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Schema;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Filament\Tests\Fixtures\Livewire\Livewire as SchemaLivewire;
use Filament\Tests\Fixtures\Models\User;
use Filament\Tests\TestCase;
use Filament\Widgets\ChartWidget\Concerns\HasFiltersForm;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Filesystem\Filesystem;
use Livewire\Livewire;
use Mockery;
use Symfony\Component\Process\Process;

class FormNamingMigrationTest extends TestCase
{
    public function test_full_set_preserves_filters_chart_hooks_and_provider_contracts_in_fresh_processes(): void
    {
        $directory = sys_get_temp_dir() . '/filament-form-naming-' . bin2hex(random_bytes(8));
        mkdir($directory . '/app', recursive: true);
        $root = dirname(__DIR__, 3);
        $filenames = ['Filters.php', 'ChartBase.php', 'ChartHooks.php', 'MultiFactor.php'];

        try {
            foreach ($filenames as $filename) {
                $source = file_get_contents(__DIR__ . '/Fixtures/FormNaming/' . $filename);
                file_put_contents($directory . '/app/' . $filename, $source);
                eval('?>' . str_replace(
                    ['namespace Filament\\Tests\\Upgrade\\Fixtures\\FormNaming;', 'use Filament\\Tables\\Filters\\Filter;', 'use Filament\\Widgets\\ChartWidget\\Concerns\\HasFiltersSchema;', 'use Filament\\Auth\\MultiFactor\\MultiFactorChallenge;'],
                    ['namespace Filament\\Tests\\Upgrade\\LegacyFixtures\\FormNaming;', 'use Filament\\Tests\\Upgrade\\LegacyFormNamingFilter as Filter;', 'use Filament\\Tests\\Upgrade\\LegacyFormNamingChartFilters as HasFiltersSchema;', 'use Filament\\Tests\\Upgrade\\LegacyFormNamingChallenge as MultiFactorChallenge;'],
                    $source,
                ));
            }

            $expectedFilters = [true, ['stored'], ['computed'], ['property'], false, 1, 'global'];
            $this->assertSame($expectedFilters, LegacyFixtures\FormNaming\filters());
            $filtersForm = Schema::make(new SchemaLivewire)->components([Group::make([])->key('accessor')->statePath('accessor')]);
            $livewire = Mockery::mock(HasTable::class);
            $livewire->shouldReceive('getTableFiltersForm')->andReturn($filtersForm);
            $table = Table::make($livewire);
            $this->assertSame('accessor', LegacyFixtures\FormNaming\filterForm($table)->getStatePath());
            Livewire::test(LegacyFixtures\FormNaming\StandardChart::class)
                ->assertSet('filters.year', '2024');
            $legacyWidget = Livewire::test(LegacyFixtures\FormNaming\GetterChart::class)
                ->assertSet('filters.year', '2024')
                ->set('filters.year', '2023')
                ->call('restore')
                ->assertSet('filters.year', '2024');
            $legacyInstance = $legacyWidget->instance();
            $expectedChart = [
                $legacyInstance->inspectFilters()->getColumns('lg'),
                $legacyInstance->inspectAlias()->getColumns('lg'),
                LegacyFixtures\FormNaming\chartSchema($legacyInstance)->getColumns('lg'),
            ];
            $this->assertSame([4, 4, 4], $expectedChart);
            $user = new User;
            $legacyChallenge = new LegacyFormNamingChallenge;
            $this->assertSame([[], null, []], LegacyFixtures\FormNaming\challenge($legacyChallenge, $user));
            $expectedProviderFields = array_map(static fn (Field $field): string => $field->getName(), LegacyFixtures\FormNaming\providerComponents(new LegacyFixtures\FormNaming\Provider, $user));
            $this->assertSame(['code'], $expectedProviderFields);

            file_put_contents($directory . '/rector.php', <<<PHP
                <?php
                return \Rector\Config\RectorConfig::configure()
                    ->withPaths([__DIR__ . '/app'])
                    ->withCache(__DIR__ . '/cache')
                    ->withoutParallel()
                    ->withSets(['{$root}/packages/upgrade/src/rector.php']);
                PHP);

            $migratedSources = [];

            foreach ([false, true, true] as $dryRun) {
                $migration = new Process([
                    PHP_BINARY, $root . '/vendor/bin/rector', 'process', '--config', $directory . '/rector.php',
                    '--clear-cache', '--no-progress-bar', ...($dryRun ? ['--dry-run'] : []),
                ], $root);
                $migration->setTimeout(120)->run();
                $this->assertSame(0, $migration->getExitCode(), $migration->getOutput() . $migration->getErrorOutput());

                foreach ($filenames as $filename) {
                    $source = file_get_contents($directory . '/app/' . $filename);

                    if ($dryRun) {
                        $this->assertSame($migratedSources[$filename], $source);
                    } else {
                        $migratedSources[$filename] = $source;
                    }
                }
            }

            $this->assertStringNotContainsString('getChallengeSchemaComponents', $migratedSources['MultiFactor.php']);
            $this->assertStringContainsString('parent::getChallengeFormComponents($user)', $migratedSources['MultiFactor.php']);
            $this->assertStringNotContainsString('filtersSchema', $migratedSources['ChartBase.php']);
            $this->assertStringNotContainsString('getFiltersSchema', $migratedSources['ChartHooks.php']);
            $this->assertStringNotContainsString('resetFiltersForm', $migratedSources['ChartHooks.php']);
            $this->assertStringNotContainsString('->schema(', $migratedSources['Filters.php']);

            foreach ($filenames as $filename) {
                require $directory . '/app/' . $filename;
            }

            $this->assertSame($expectedFilters, Fixtures\FormNaming\filters());
            $this->assertSame('accessor', Fixtures\FormNaming\filterForm($table)->getStatePath());
            Livewire::test(Fixtures\FormNaming\StandardChart::class)
                ->assertSet('filters.year', '2024');
            $widget = Livewire::test(Fixtures\FormNaming\GetterChart::class)
                ->assertSet('filters.year', '2024')
                ->set('filters.year', '2023')
                ->call('restore')
                ->assertSet('filters.year', '2024');
            $instance = $widget->instance();
            $this->assertSame($expectedChart, [
                $instance->inspectFilters()->getColumns('lg'),
                $instance->inspectAlias()->getColumns('lg'),
                Fixtures\FormNaming\chartSchema($instance)->getColumns('lg'),
            ]);
            $this->assertSame($legacyInstance->configurations, $instance->configurations);
            $this->assertSame([[], null, []], Fixtures\FormNaming\challenge(new LegacyFormNamingChallenge, $user));
            $this->assertSame($expectedProviderFields, array_map(static fn (Field $field): string => $field->getName(), Fixtures\FormNaming\providerComponents(new Fixtures\FormNaming\Provider, $user)));
        } finally {
            (new Filesystem)->deleteDirectory($directory);
        }
    }
}

class LegacyFormNamingFilter extends Filter
{
    public function schema(array | Closure | null $schema): static
    {
        return $this->form($schema);
    }

    public function getSchemaComponents(): array
    {
        return $this->getFormSchema();
    }

    public function hasSchema(): bool
    {
        return $this->hasForm();
    }

    public function getSchema(): Schema
    {
        return $this->getForm();
    }
}

trait LegacyFormNamingChartFilters
{
    use HasFiltersForm {
        getFiltersForm as private getCanonicalFiltersForm;
    }

    public function filtersForm(Schema $schema): Schema
    {
        return $this->filtersSchema($schema);
    }

    public function filtersSchema(Schema $schema): Schema
    {
        return $schema;
    }

    public function getFiltersForm(): Schema
    {
        return $this->getFiltersSchema();
    }

    public function getFiltersSchema(): Schema
    {
        return $this->getCanonicalFiltersForm();
    }

    public function resetFiltersForm(): void
    {
        $this->resetFilters();
    }
}

class LegacyFormNamingChallenge extends MultiFactorChallenge
{
    public function getEnabledProviders(Authenticatable $user): array
    {
        return [];
    }

    public function getSchemaComponents(Authenticatable $user): array
    {
        return $this->getFormComponents($user);
    }

    public function getProviderPickerSchemaComponent(Authenticatable $user): ?Component
    {
        return $this->getProviderPickerFormComponent($user);
    }

    public function getChallengeSchemaComponents(Authenticatable $user): array
    {
        return $this->getChallengeFormComponents($user);
    }
}
