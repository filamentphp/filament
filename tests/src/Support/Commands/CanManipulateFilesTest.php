<?php

use Filament\Support\Commands\Concerns\CanManipulateFiles;
use Filament\Tests\TestCase;
use Illuminate\Console\Command;
use Illuminate\Console\OutputStyle;
use Illuminate\Console\View\Components\Factory;
use Illuminate\Support\Facades\File;
use Laravel\Prompts\ConfirmPrompt;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;

uses(TestCase::class);

it('preserves colliding files until replacements are written', function (bool $confirmLast): void {
    $directory = sys_get_temp_dir() . '/filament-collisions-' . uniqid();
    File::makeDirectory($directory);
    File::put("{$directory}/entry.js", 'original entry');
    File::put("{$directory}/component.vue", 'original component');
    $environment = app()['env'];
    $confirmations = 0;

    $command = new class extends Command
    {
        use CanManipulateFiles {
            checkForCollision as public;
            writeFile as public;
        }

        public function __construct()
        {
            parent::__construct();
            $this->components = new Factory(new OutputStyle(new ArrayInput([]), new BufferedOutput));
        }
    };

    ConfirmPrompt::fallbackUsing(function () use (&$confirmations, $confirmLast, $directory): bool {
        expect(File::get("{$directory}/entry.js"))->toBe('original entry')
            ->and(File::get("{$directory}/component.vue"))->toBe('original component');

        return (++$confirmations === 1) || $confirmLast;
    });

    try {
        app()['env'] = 'local';

        expect($command->checkForCollision(["{$directory}/entry.js", "{$directory}/missing.js", "{$directory}/component.vue"]))->toBe(! $confirmLast)
            ->and($confirmations)->toBe(2);

        expect(File::get("{$directory}/entry.js"))->toBe('original entry')
            ->and(File::get("{$directory}/component.vue"))->toBe('original component');

        if ($confirmLast) {
            $command->writeFile("{$directory}/entry.js", 'replacement entry');

            expect(File::get("{$directory}/entry.js"))->toBe('replacement entry')
                ->and(File::get("{$directory}/component.vue"))->toBe('original component');
        }
    } finally {
        app()['env'] = $environment;
        File::deleteDirectory($directory);
    }
})->with([false, true]);
