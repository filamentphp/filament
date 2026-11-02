<?php

namespace Filament\Tests\Upgrade;

use Rector\Testing\PHPUnit\AbstractRectorTestCase;
use Symfony\Component\Process\Process;

class RenameDeprecatedColumnAndSupportMethodsRectorTest extends AbstractRectorTestCase
{
    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();

        // Rector must refresh its source locator between fixtures when run through Pest.
        if (! defined('PHPUNIT_COMPOSER_INSTALL')) {
            define('PHPUNIT_COMPOSER_INSTALL', dirname(__DIR__, 3) . '/vendor/autoload.php');
        }
    }

    public function test_renames_calls_only_on_known_owners_without_custom_overrides(): void
    {
        $this->doTestFile(__DIR__ . '/Fixtures/deprecated_column_and_support_methods.php.inc');
        $this->doTestFile(__DIR__ . '/Fixtures/row_loop_arguments.php.inc');
    }

    public function test_preserves_manual_migration_boundaries(): void
    {
        $this->doTestFile(__DIR__ . '/Fixtures/deprecated_column_and_support_manual_boundaries.php.inc');
    }

    public function test_renames_the_copyable_trait_including_adaptations(): void
    {
        $this->doTestFile(__DIR__ . '/Fixtures/deprecated_column_copyable_trait.php.inc');
    }

    public function test_migrated_indicators_preserve_values_lazy_evaluation_and_action_styling(): void
    {
        $this->doTestFile(__DIR__ . '/Fixtures/deprecated_column_and_support_methods.php.inc');

        $probe = <<<'PHP'
            require $argv[1] . '/vendor/autoload.php';
            Illuminate\Container\Container::setInstance(new Illuminate\Container\Container);
            [, $migrated] = explode("-----\n", file_get_contents($argv[2]), 2);
            eval(substr($migrated, 5));
            $action = Filament\Actions\Action::make('edit')->badge('Previous');
            $group = Filament\Actions\ActionGroup::make([])->badge('Previous');
            $tab = Filament\Schemas\Components\Tabs\Tab::make('Status')->badge('Previous');
            $snapshots = [];
            $evaluations = 0;
            foreach ([42, 3.5, 'New', null, static function () use (&$evaluations): string { ++$evaluations; return 'Lazy'; }] as $indicator) {
                $components = Filament\Tests\Upgrade\Fixtures\DeprecatedColumnAndSupportMethods\unionIndicators($action, $group, $tab, $indicator);
                $snapshots[] = [$components === [$action, $group, $tab], $evaluations];
                $snapshots[] = [$action->getBadge(), $group->getBadge(), $tab->getBadge(), $action->isBadge(), $group->isBadge()];
            }
            $snapshots[] = $evaluations;
            Filament\Tests\Upgrade\Fixtures\DeprecatedColumnAndSupportMethods\unpackedIndicators($action, $group);
            $snapshots[] = [$action->getBadge(), $group->getBadge()];
            $snapshots[] = Filament\Tests\Upgrade\Fixtures\DeprecatedColumnAndSupportMethods\nullableIndicator(null, static function (): never { throw new LogicException('Nullsafe arguments were evaluated'); });
            $calls = 0;
            Filament\Tests\Upgrade\Fixtures\DeprecatedColumnAndSupportMethods\nullableIndicator($action, static function () use (&$calls): int { ++$calls; return 17; });
            $snapshots[] = [$action->getBadge(), $calls];
            try {
                Filament\Tests\Upgrade\Fixtures\DeprecatedColumnAndSupportMethods\nullableIndicator($action, static fn (): array => []);
                throw new LogicException('An invalid indicator was accepted');
            } catch (TypeError) {
                $snapshots[] = $action->getBadge();
            }
            $snapshots[] = Filament\Tests\Upgrade\Fixtures\DeprecatedColumnAndSupportMethods\numericIndicators(42, 3.5);
            $action->badge();
            $group->badge();
            $snapshots[] = [$action->isBadge(), $group->isBadge()];
            echo json_encode($snapshots, JSON_THROW_ON_ERROR);
            PHP;

        $root = dirname(__DIR__, 3);
        $process = new Process([PHP_BINARY, '-r', $probe, $root, __DIR__ . '/Fixtures/deprecated_column_and_support_methods.php.inc'], $root);
        $process->mustRun();

        $this->assertSame([
            [true, 0], ['42', '42', '42', false, false],
            [true, 0], ['3.5', '3.5', '3.5', false, false],
            [true, 0], ['New', 'New', 'New', false, false],
            [true, 0], [null, null, null, false, false],
            [true, 0], ['Lazy', 'Lazy', 'Lazy', false, false],
            3, ['42', null], null, ['17', 1], '17', ['42', '3.5'], [true, true],
        ], json_decode($process->getOutput(), associative: true, flags: JSON_THROW_ON_ERROR));
    }

    public function provideConfigFilePath(): string
    {
        return dirname(__DIR__, 3) . '/packages/upgrade/src/rector.php';
    }
}
