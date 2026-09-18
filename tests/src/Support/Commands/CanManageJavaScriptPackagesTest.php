<?php

use Filament\Support\Commands\Concerns\CanManageJavaScriptPackages;
use Filament\Support\Commands\Exceptions\FailureCommandOutput;
use Filament\Tests\TestCase;
use Illuminate\Console\Command;
use Illuminate\Console\OutputStyle;
use Illuminate\Console\View\Components\Factory as ComponentsFactory;
use Illuminate\Process\PendingProcess;
use Illuminate\Support\Facades\Process;
use Laravel\Prompts\ConfirmPrompt;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;

uses(TestCase::class);
$createJavaScriptPackageCommand = function (?string $packageManager = null, bool $skipInstall = false, bool $skipBuild = false): Command {
    $command = new class($packageManager, $skipInstall, $skipBuild) extends Command
    {
        use CanManageJavaScriptPackages;

        public function __construct(
            protected ?string $packageManagerOption,
            protected bool $skipInstall,
            protected bool $skipBuild,
        ) {
            parent::__construct();
        }

        public function option($key = null): mixed
        {
            return match ($key) {
                'pm' => $this->packageManagerOption,
                'skip-install' => $this->skipInstall,
                'skip-build' => $this->skipBuild,
                default => null,
            };
        }

        public function configurePackages(): void
        {
            $this->configurePackageManager();
        }

        public function installPackages(array $dependencies): void
        {
            $this->installJavaScriptDependencies($dependencies);
        }

        public function buildAssets(string $subject): bool
        {
            return $this->buildJavaScriptAssets($subject);
        }
    };
    $output = new OutputStyle(new ArrayInput([]), new BufferedOutput);
    $command->setOutput($output);
    $reflection = new ReflectionClass($command);
    $reflection->getProperty('components')->setValue($command, new ComponentsFactory($output));

    return $command;
};

beforeEach(function (): void {
    $this->withoutMockingConsoleOutput();
});

it('uses npm arguments and the application working directory', function () use ($createJavaScriptPackageCommand): void {
    Process::fake([
        '*' => Process::result(output: '10.0.0'),
    ]);
    Process::preventStrayProcesses();

    $command = $createJavaScriptPackageCommand();
    $command->configurePackages();
    $command->installPackages(['example-package']);

    Process::assertRan(static fn (PendingProcess $process): bool => ($process->command === ['npm', '--version']) && ($process->path === base_path()));
    Process::assertRan(static fn (PendingProcess $process): bool => ($process->command === ['npm', 'install', 'example-package', '--save-dev']) && ($process->path === base_path()));
});

it('uses yarn arguments and the application working directory', function () use ($createJavaScriptPackageCommand): void {
    Process::fake([
        '*' => Process::result(output: '1.22.0'),
    ]);
    Process::preventStrayProcesses();

    $command = $createJavaScriptPackageCommand('yarn');
    $command->configurePackages();
    $command->installPackages(['first-package', 'second-package']);

    Process::assertRan(static fn (PendingProcess $process): bool => ($process->command === ['yarn', '--version']) && ($process->path === base_path()));
    Process::assertRan(static fn (PendingProcess $process): bool => ($process->command === ['yarn', 'add', 'first-package', 'second-package', '--dev']) && ($process->path === base_path()));
});

it('throws `FailureCommandOutput` when the package manager is unavailable', function () use ($createJavaScriptPackageCommand): void {
    Process::fake([
        '*' => Process::result(exitCode: 1),
    ]);
    Process::preventStrayProcesses();

    $createJavaScriptPackageCommand('yarn')->configurePackages();
})->throws(FailureCommandOutput::class);

it('throws `FailureCommandOutput` when dependency installation fails', function () use ($createJavaScriptPackageCommand): void {
    Process::fake([
        '*' => Process::result(exitCode: 1),
    ]);
    Process::preventStrayProcesses();

    $command = $createJavaScriptPackageCommand();

    $reflection = new ReflectionClass($command);
    $reflection->getProperty('packageManager')->setValue($command, 'npm');

    $command->installPackages(['example-package']);
})->throws(FailureCommandOutput::class);

it('returns `false` when the asset build fails', function () use ($createJavaScriptPackageCommand): void {
    Process::fake([
        '*' => Process::result(exitCode: 1),
    ]);
    Process::preventStrayProcesses();
    ConfirmPrompt::fallbackUsing(static fn (): bool => true);

    $command = $createJavaScriptPackageCommand();
    $reflection = new ReflectionClass($command);
    $reflection->getProperty('packageManager')->setValue($command, 'npm');

    expect($command->buildAssets('theme'))->toBeFalse();

    Process::assertRan(static fn (PendingProcess $process): bool => ($process->command === ['npm', 'run', 'build']) && ($process->path === base_path()));
});

it('prints the manual build instruction when the asset build is declined', function () use ($createJavaScriptPackageCommand): void {
    Process::fake();
    Process::preventStrayProcesses();
    ConfirmPrompt::fallbackUsing(static fn (): bool => false);

    $command = $createJavaScriptPackageCommand('yarn');
    $reflection = new ReflectionClass($command);
    $reflection->getProperty('packageManager')->setValue($command, 'yarn');

    expect($command->buildAssets('field'))->toBeTrue();

    Process::assertNothingRan();
    expect($command->getOutput()->getOutput()->fetch())->toContain('Run `yarn run build` to compile the field.');
});

it('skips all package manager processes when installation and building are skipped', function () use ($createJavaScriptPackageCommand): void {
    Process::fake();
    Process::preventStrayProcesses();
    $promptCount = 0;
    ConfirmPrompt::fallbackUsing(function () use (&$promptCount): bool {
        $promptCount++;

        return true;
    });

    $command = $createJavaScriptPackageCommand('yarn', skipInstall: true, skipBuild: true);
    $command->configurePackages();
    $command->installPackages(['example-package']);

    expect($command->buildAssets('theme'))->toBeTrue()
        ->and($promptCount)->toBe(0);

    Process::assertNothingRan();
    expect($command->getOutput()->getOutput()->fetch())
        ->toContain('Run `yarn add example-package --dev` to install the JavaScript dependencies.')
        ->toContain('Run `yarn run build` to compile the theme.');
});

it('only skips dependency installation when requested', function () use ($createJavaScriptPackageCommand): void {
    Process::fake([
        '*' => Process::result(output: '10.0.0'),
    ]);
    Process::preventStrayProcesses();
    ConfirmPrompt::fallbackUsing(static fn (): bool => true);

    $command = $createJavaScriptPackageCommand(skipInstall: true);
    $command->configurePackages();
    $command->installPackages(['example-package']);

    expect($command->buildAssets('theme'))->toBeTrue();

    Process::assertRan(static fn (PendingProcess $process): bool => $process->command === ['npm', '--version']);
    Process::assertRan(static fn (PendingProcess $process): bool => $process->command === ['npm', 'run', 'build']);
    Process::assertNotRan(static fn (PendingProcess $process): bool => in_array('example-package', $process->command, strict: true));
    expect($command->getOutput()->getOutput()->fetch())->toContain('Run `npm install example-package --save-dev` to install the JavaScript dependencies.');
});

it('only skips building without prompting when requested', function () use ($createJavaScriptPackageCommand): void {
    Process::fake([
        '*' => Process::result(output: '10.0.0'),
    ]);
    Process::preventStrayProcesses();
    $promptCount = 0;
    ConfirmPrompt::fallbackUsing(function () use (&$promptCount): bool {
        $promptCount++;

        return true;
    });

    $command = $createJavaScriptPackageCommand(skipBuild: true);
    $command->configurePackages();
    $command->installPackages(['example-package']);

    expect($command->buildAssets('theme'))->toBeTrue()
        ->and($promptCount)->toBe(0);

    Process::assertRan(static fn (PendingProcess $process): bool => $process->command === ['npm', '--version']);
    Process::assertRan(static fn (PendingProcess $process): bool => $process->command === ['npm', 'install', 'example-package', '--save-dev']);
    Process::assertNotRan(static fn (PendingProcess $process): bool => $process->command === ['npm', 'run', 'build']);
    expect($command->getOutput()->getOutput()->fetch())->toContain('Run `npm run build` to compile the theme.');
});

it('does not run an install command for empty dependencies', function () use ($createJavaScriptPackageCommand): void {
    Process::fake();
    Process::preventStrayProcesses();

    $command = $createJavaScriptPackageCommand();
    $reflection = new ReflectionClass($command);
    $reflection->getProperty('packageManager')->setValue($command, 'npm');

    $command->installPackages([]);

    Process::assertNothingRan();
});
