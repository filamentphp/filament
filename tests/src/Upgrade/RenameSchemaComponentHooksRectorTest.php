<?php

namespace Filament\Tests\Upgrade;

use Filament\Forms\Components\TextInput;
use Illuminate\Filesystem\Filesystem;
use Rector\Testing\PHPUnit\AbstractRectorTestCase;
use Symfony\Component\Process\Process;

class RenameSchemaComponentHooksRectorTest extends AbstractRectorTestCase
{
    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();

        // Rector must refresh its source locator between fixtures when run through Pest.
        if (! defined('PHPUNIT_COMPOSER_INSTALL')) {
            define('PHPUNIT_COMPOSER_INSTALL', dirname(__DIR__, 3) . '/vendor/autoload.php');
        }
    }

    public function test_migrates_component_hooks_and_their_calls_together(): void
    {
        $this->doTestFile(__DIR__ . '/Fixtures/schema_component_hooks.php.inc');
    }

    public function test_migrates_configuration_calls(): void
    {
        $this->doTestFile(__DIR__ . '/Fixtures/schema_configuration_calls.php.inc');
    }

    public function test_preserves_unrelated_configuration_contracts(): void
    {
        $this->doTestFile(__DIR__ . '/Fixtures/schema_configuration_boundaries.php.inc');
    }

    public function test_preserves_phpdoc_union_boundaries(): void
    {
        $this->doTestFile(__DIR__ . '/Fixtures/schema_phpdoc_boundaries.php.inc');
    }

    public function test_preserves_class_string_union_boundaries(): void
    {
        $this->doTestFile(__DIR__ . '/Fixtures/schema_class_string_union_boundary.php.inc');
    }

    public function test_migrates_a_child_without_renaming_an_unprocessed_parent_call(): void
    {
        $this->doTestFile(__DIR__ . '/Fixtures/schema_cross_file_hook.php.inc');
    }

    public function test_bridges_trait_hooks_without_changing_shared_traits_or_dual_name_dispatch(): void
    {
        $this->doTestFile(__DIR__ . '/Fixtures/schema_component_hook_boundaries.php.inc');
    }

    public function test_preserves_overrides_hidden_by_union_normalization(): void
    {
        $this->doTestFile(__DIR__ . '/Fixtures/schema_hook_unions.php.inc');
    }

    public function test_combines_legacy_operator_class_and_hook_renames(): void
    {
        $this->doTestFile(__DIR__ . '/Fixtures/schema_legacy_operator_hook.php.inc');
    }

    public function test_migrates_only_supported_schema_lookup_arguments(): void
    {
        $this->doTestFile(__DIR__ . '/Fixtures/schema_lookup_arguments.php.inc');
    }

    public function test_preserves_custom_lookup_arguments_hidden_by_union_normalization(): void
    {
        $this->doTestFile(__DIR__ . '/Fixtures/schema_lookup_union_boundary.php.inc');
    }

    public function test_migrates_trait_consumers_and_cross_file_parents_in_a_fresh_process(): void
    {
        $directory = sys_get_temp_dir() . '/filament-schema-hooks-' . bin2hex(random_bytes(8));
        mkdir($directory . '/app', recursive: true);

        try {
            foreach ([
                'parent.php' => __DIR__ . '/Fixtures/SchemaHooks/ParentExporter.php',
                'child.php' => __DIR__ . '/Fixtures/schema_cross_file_hook.php.inc',
                'traits.php' => __DIR__ . '/Fixtures/schema_component_hook_boundaries.php.inc',
                'operator.php' => __DIR__ . '/Fixtures/schema_component_hooks.php.inc',
            ] as $filename => $fixture) {
                $source = explode("-----\n", file_get_contents($fixture), 2)[0];
                $source = str_replace('Filament\\Tests\\Upgrade\\Fixtures\\', 'Filament\\Tests\\Upgrade\\MigratedSchemaHooks\\', $source);
                file_put_contents($directory . '/app/' . $filename, $source);
            }

            $root = dirname(__DIR__, 3);
            $configuration = $directory . '/rector.php';
            file_put_contents($configuration, <<<'PHP'
                <?php

                use Filament\Upgrade\Rector\RenameSchemaComponentHooksRector;
                use Rector\Config\RectorConfig;

                return RectorConfig::configure()
                    ->withPaths([__DIR__ . '/app'])
                    ->withCache(__DIR__ . '/cache')
                    ->withoutParallel()
                    ->withRules([RenameSchemaComponentHooksRector::class]);
                PHP);

            foreach ([false, true] as $dryRun) {
                $process = new Process([
                    PHP_BINARY, '-d', 'zend.assertions=1', '-d', 'assert.exception=1',
                    $root . '/vendor/bin/rector', 'process',
                    '--config', $configuration, '--clear-cache', '--no-progress-bar',
                    ...($dryRun ? ['--dry-run'] : []),
                ], $root);
                $process->setTimeout(60);
                $process->run();

                $this->assertSame(0, $process->getExitCode(), $process->getOutput() . $process->getErrorOutput());
            }

            require $directory . '/app/parent.php';
            require $directory . '/app/child.php';
            require $directory . '/app/traits.php';
            require $directory . '/app/operator.php';

            $fieldNames = static fn (array $components): array => array_map(static fn (TextInput $component): string => $component->getName(), $components);

            $this->assertSame(['parent_option', 'child_option'], $fieldNames(MigratedSchemaHooks\SchemaCrossFileHook\ChildExporter::getOptionsSchemaComponents()));
            $this->assertSame(['trait_option'], $fieldNames(MigratedSchemaHooks\SchemaComponentHookBoundaries\TraitImporter::getOptionsSchemaComponents()));
            $this->assertSame(['trait_option'], $fieldNames(MigratedSchemaHooks\SchemaComponentHookBoundaries\TraitImporter::options()));
            $this->assertSame([], (MigratedSchemaHooks\SchemaComponentHookBoundaries\TraitImporter::parentOptions())());
            $this->assertSame([], (MigratedSchemaHooks\SchemaComponentHookBoundaries\TraitImporter::parentOptionsAlias())());
            $this->assertSame([], (MigratedSchemaHooks\SchemaComponentHookBoundaries\ChildTraitImporter::parentOptionsAlias())());
            $this->assertSame(['parent_option', 'trait_option'], $fieldNames(MigratedSchemaHooks\SchemaComponentHookBoundaries\ParentTraitExporter::getOptionsSchemaComponents()));
            $this->assertSame(['parent_option', 'trait_option'], $fieldNames(MigratedSchemaHooks\SchemaComponentHookBoundaries\ParentTraitExporter::options()));
            $this->assertSame(['parent_option'], $fieldNames((MigratedSchemaHooks\SchemaComponentHookBoundaries\ParentTraitExporter::parentOptions())()));
            $this->assertSame(['canonical_parent_option'], $fieldNames(MigratedSchemaHooks\SchemaComponentHookBoundaries\DualParentTraitExporter::getOptionsSchemaComponents()));
            $this->assertSame(['legacy_parent_option', 'trait_option'], $fieldNames(MigratedSchemaHooks\SchemaComponentHookBoundaries\DualParentTraitExporter::options()));
            $this->assertSame(['legacy_parent_option'], $fieldNames((MigratedSchemaHooks\SchemaComponentHookBoundaries\DualParentTraitExporter::parentOptions())()));
            $this->assertSame(['trait_option', 'child_trait_option'], $fieldNames(MigratedSchemaHooks\SchemaComponentHookBoundaries\ChildTraitImporter::getOptionsSchemaComponents()));
            $this->assertSame(['dual_child_option'], $fieldNames(MigratedSchemaHooks\SchemaComponentHookBoundaries\DualChildTraitImporter::getOptionsSchemaComponents()));
            $this->assertSame(['trait_option'], $fieldNames(MigratedSchemaHooks\SchemaComponentHookBoundaries\DualChildTraitImporter::getOptionsFormComponents()));
            $this->assertSame(['unrelated_parent_option', 'trait_option'], $fieldNames(MigratedSchemaHooks\SchemaComponentHookBoundaries\UnrelatedTraitConsumer::options()));
            $this->assertSame(['unrelated_parent_option', 'trait_option'], $fieldNames(MigratedSchemaHooks\SchemaComponentHookBoundaries\UnrelatedGrandchild::options()));
            $this->assertSame(['unrelated_parent_option'], $fieldNames((MigratedSchemaHooks\SchemaComponentHookBoundaries\UnrelatedTraitConsumer::parentOptions())()));
            $this->assertSame(['unrelated_parent_option'], $fieldNames((MigratedSchemaHooks\SchemaComponentHookBoundaries\UnrelatedTraitConsumer::parentOptionsAlias())()));
            $this->assertSame(['unrelated_parent_option'], $fieldNames((MigratedSchemaHooks\SchemaComponentHookBoundaries\UnrelatedGrandchild::parentOptionsAlias())()));
            $this->assertSame(['canonical_option'], $fieldNames(MigratedSchemaHooks\SchemaComponentHookBoundaries\CanonicalTraitImporter::getOptionsSchemaComponents()));
            $this->assertSame(['trait_option'], $fieldNames(MigratedSchemaHooks\SchemaComponentHookBoundaries\AliasedTraitImporter::getOptionsSchemaComponents()));
            $this->assertSame([], MigratedSchemaHooks\SchemaComponentHookBoundaries\ForwardingTraitImporter::getOptionsSchemaComponents());
            $this->assertSame([], MigratedSchemaHooks\SchemaComponentHookBoundaries\ForwardingParentTraitImporter::getOptionsSchemaComponents());
            $this->assertSame(['trait_option'], $fieldNames(MigratedSchemaHooks\SchemaComponentHookBoundaries\ForwardingParentTraitImporter::options()));
            $this->assertSame([], MigratedSchemaHooks\SchemaComponentHookBoundaries\DualExporter::getOptionsSchemaComponents());
            $operator = MigratedSchemaHooks\SchemaComponentHooks\CustomTraitOperator::make();
            $this->assertSame(['operator_option'], $fieldNames($operator->getSchemaComponents()));
            $this->assertSame(['operator_option'], $fieldNames($operator->fields()));
            $unrelatedOperator = new MigratedSchemaHooks\SchemaComponentHooks\UnrelatedChildOperator;
            $this->assertSame(['unrelated_operator_option', 'operator_option'], $fieldNames($unrelatedOperator->fields()));
        } finally {
            (new Filesystem)->deleteDirectory($directory);
        }
    }

    public function provideConfigFilePath(): string
    {
        return dirname(__DIR__, 3) . '/packages/upgrade/src/rector.php';
    }
}
