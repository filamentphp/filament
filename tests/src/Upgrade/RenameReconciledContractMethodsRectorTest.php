<?php

namespace Filament\Tests\Upgrade;

use Illuminate\Filesystem\Filesystem;
use Rector\Testing\PHPUnit\AbstractRectorTestCase;
use Symfony\Component\Process\Process;

class RenameReconciledContractMethodsRectorTest extends AbstractRectorTestCase
{
    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();

        if (! defined('PHPUNIT_COMPOSER_INSTALL')) {
            define('PHPUNIT_COMPOSER_INSTALL', dirname(__DIR__, 3) . '/vendor/autoload.php');
        }
    }

    public function test_migrates_contract_declarations_and_calls(): void
    {
        $this->doTestFile(__DIR__ . '/Fixtures/ReconciledContractMethods/contracts.php.inc');
    }

    public function test_migrates_trait_defined_hooks(): void
    {
        $this->doTestFile(__DIR__ . '/Fixtures/ReconciledContractMethods/traits.php.inc');
    }

    public function test_preserves_unrelated_and_conflicting_methods(): void
    {
        $this->doTestFile(__DIR__ . '/Fixtures/ReconciledContractMethods/boundaries.php.inc');
    }

    public function test_preserves_behavior_when_a_descendant_has_a_competing_canonical_method(): void
    {
        $directory = sys_get_temp_dir() . '/filament-contract-collision-' . bin2hex(random_bytes(8));
        mkdir($directory . '/app', recursive: true);
        $root = dirname(__DIR__, 3);
        $original = file_get_contents(__DIR__ . '/Fixtures/ReconciledContractMethods/inherited_collision.php.inc');

        try {
            file_put_contents($directory . '/app/Users.php', $original);
            file_put_contents($directory . '/verify.php', <<<'PHP'
                <?php

                use Filament\Tests\Upgrade\Fixtures\InheritedContractCollision\ChildUser;
                use Filament\Tests\Upgrade\Fixtures\InheritedContractCollision\CleanUser;
                use Filament\Tests\Upgrade\Fixtures\InheritedContractCollision\SiblingUser;

                use function Filament\Tests\Upgrade\Fixtures\InheritedContractCollision\enableChild;
                use function Filament\Tests\Upgrade\Fixtures\InheritedContractCollision\enableClean;
                use function Filament\Tests\Upgrade\Fixtures\InheritedContractCollision\enableContract;
                use function Filament\Tests\Upgrade\Fixtures\InheritedContractCollision\enableParent;
                use function Filament\Tests\Upgrade\Fixtures\InheritedContractCollision\enableSibling;

                require $argv[1] . '/vendor/autoload.php';

                // Isolate method dispatch from the interface migration, which needs manual reconciliation.
                eval('namespace Filament\Auth\MultiFactor\Email\Contracts; interface HasEmailAuthentication { public function hasEmailAuthentication(): bool; }');
                require __DIR__ . '/app/Users.php';

                echo json_encode([enableChild(new ChildUser), enableParent(new ChildUser), enableContract(new ChildUser), enableSibling(new SiblingUser), enableClean(new CleanUser), enableContract(new CleanUser)]);
                PHP);

            $before = new Process([PHP_BINARY, $directory . '/verify.php', $root], $root);
            $before->mustRun();
            $this->assertSame('[true,true,true,true,true,true]', $before->getOutput());

            file_put_contents($directory . '/rector.php', <<<'PHP'
                <?php

                use Filament\Upgrade\Rector\RenameReconciledContractMethodsRector;
                use Rector\Config\RectorConfig;

                return RectorConfig::configure()
                    ->withPaths([__DIR__ . '/app'])
                    ->withCache(__DIR__ . '/cache')
                    ->withoutParallel()
                    ->withRules([RenameReconciledContractMethodsRector::class]);
                PHP);

            $migration = new Process([PHP_BINARY, $root . '/vendor/bin/rector', 'process', '--config', $directory . '/rector.php', '--clear-cache', '--no-progress-bar'], $root);
            $migration->setTimeout(60)->run();
            $this->assertSame(0, $migration->getExitCode(), $migration->getOutput() . $migration->getErrorOutput());

            $after = new Process([PHP_BINARY, $directory . '/verify.php', $root], $root);
            $after->mustRun();
            $this->assertSame($before->getOutput(), $after->getOutput());
            $this->assertSame($original, file_get_contents($directory . '/app/Users.php'));
        } finally {
            (new Filesystem)->deleteDirectory($directory);
        }
    }

    public function test_migrates_cross_file_traits_and_inheritance_without_changing_behavior(): void
    {
        $directory = sys_get_temp_dir() . '/filament-contract-methods-' . bin2hex(random_bytes(8));
        mkdir($directory . '/app', recursive: true);
        $root = dirname(__DIR__, 3);

        try {
            foreach (['EmailState.php', 'Users.php'] as $filename) {
                copy(__DIR__ . '/Fixtures/ReconciledContractMethods/CrossFile/' . $filename, $directory . '/app/' . $filename);
            }

            [$contractsBefore, $contractsAfter] = explode("-----\n", file_get_contents(__DIR__ . '/Fixtures/ReconciledContractMethods/contracts.php.inc'), 2);
            file_put_contents($directory . '/app/Contracts.php', $contractsBefore);

            file_put_contents($directory . '/verify.php', <<<'PHP'
                <?php

                require $argv[1] . '/vendor/autoload.php';

                if ($argv[2] === 'legacy') {
                    eval('namespace Filament\Auth\MultiFactor\Email\Contracts; interface HasEmailAuthentication { public function hasEmailAuthentication(): bool; public function toggleEmailAuthentication(bool $condition): void; }');
                }

                require __DIR__ . '/app/EmailState.php';
                require __DIR__ . '/app/Users.php';

                $user = new Filament\Tests\Upgrade\Fixtures\ReconciledContractMethods\CrossFile\ChildUser;
                $method = $argv[2] === 'legacy' ? 'toggleEmailAuthentication' : 'saveEmailAuthentication';
                $user->{$method}(condition: true);
                $user->{$method}(condition: true);
                $user->disable();
                echo json_encode([$user->hasEmailAuthentication(), $user->savedStates]);
                PHP);

            $legacy = new Process([PHP_BINARY, $directory . '/verify.php', $root, 'legacy'], $root);
            $legacy->mustRun();
            $this->assertSame('[false,[true,true,false]]', $legacy->getOutput());

            file_put_contents($directory . '/rector.php', <<<'PHP'
                <?php

                use Filament\Upgrade\Rector\RenameReconciledContractMethodsRector;
                use Rector\Config\RectorConfig;

                return RectorConfig::configure()
                    ->withPaths([__DIR__ . '/app'])
                    ->withCache(__DIR__ . '/cache')
                    ->withoutParallel()
                    ->withRules([RenameReconciledContractMethodsRector::class]);
                PHP);

            foreach ([false, true] as $dryRun) {
                $process = new Process([
                    PHP_BINARY, $root . '/vendor/bin/rector', 'process',
                    '--config', $directory . '/rector.php', '--clear-cache', '--no-progress-bar',
                    ...($dryRun ? ['--dry-run'] : []),
                ], $root);
                $process->setTimeout(60)->run();
                $this->assertSame(0, $process->getExitCode(), $process->getOutput() . $process->getErrorOutput());
            }

            foreach (['EmailState.php', 'Users.php'] as $filename) {
                $this->assertStringNotContainsString('toggleEmailAuthentication', file_get_contents($directory . '/app/' . $filename));
            }

            $this->assertSame($contractsAfter, file_get_contents($directory . '/app/Contracts.php'));

            $migrated = new Process([PHP_BINARY, $directory . '/verify.php', $root, 'migrated'], $root);
            $migrated->mustRun();
            $this->assertSame($legacy->getOutput(), $migrated->getOutput());
        } finally {
            (new Filesystem)->deleteDirectory($directory);
        }
    }

    public function provideConfigFilePath(): string
    {
        return __DIR__ . '/Fixtures/ReconciledContractMethods/rector.php';
    }
}
