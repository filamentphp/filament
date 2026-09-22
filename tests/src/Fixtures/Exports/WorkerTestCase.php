<?php

namespace Filament\Tests\Fixtures\Exports;

use Filament\Tests\Fixtures\Models\User;
use Filament\Tests\TestCase;
use Illuminate\Contracts\Auth\Authenticatable;
use RuntimeException;

class WorkerTestCase extends TestCase
{
    public static function validateWorkerEnvironment(): void
    {
        $phase = getenv('EXPORT_WORKER_PHASE');
        $directory = getenv('EXPORT_WORKER_DIRECTORY');

        if (! in_array($phase, ['enqueue', 'worker', 'verify'], true)) {
            throw new RuntimeException('Rejected export worker phase.');
        }

        if (
            (! is_string($directory)) ||
            (realpath($directory) !== $directory) ||
            (dirname($directory) !== realpath(sys_get_temp_dir())) ||
            (! preg_match('/^filament-export-worker-[a-f0-9]{24}$/D', basename($directory))) ||
            is_link($directory)
        ) {
            throw new RuntimeException('Rejected export worker directory.');
        }

        $database = $directory . '/database.sqlite';

        if (
            (! is_file($database)) ||
            is_link($database) ||
            (realpath($database) !== $database) ||
            (($phase === 'enqueue') ? (filesize($database) !== 0) : (filesize($database) === 0))
        ) {
            throw new RuntimeException('Rejected export worker database.');
        }
    }

    // Each phase must see committed data from the previous PHP process.
    public function refreshDatabase(): void {}

    protected function defineEnvironment($app): void
    {
        parent::defineEnvironment($app);

        $directory = getenv('EXPORT_WORKER_DIRECTORY');
        $app->useStoragePath($directory . '/storage');
        $app['config']->set([
            'app.key' => file_get_contents($directory . '/key'),
            'database.default' => 'testing',
            'database.connections.testing.database' => $directory . '/database.sqlite',
            'filesystems.disks.local' => ['driver' => 'local', 'root' => $directory . '/exports', 'throw' => true],
            'queue.default' => 'database',
            'queue.connections.database' => ['driver' => 'database', 'connection' => 'testing', 'table' => 'jobs', 'queue' => 'exports', 'retry_after' => 90],
            'queue.batching.database' => 'testing',
            'queue.failed' => ['driver' => 'database-uuids', 'database' => 'testing', 'table' => 'failed_jobs'],
            'cache.default' => 'array',
            'session.driver' => 'array',
            'logging.default' => 'single',
            'logging.channels.single.path' => $directory . '/worker.log',
            'view.compiled' => $directory . '/storage/framework/views',
        ]);
        $app->bind(Authenticatable::class, User::class);
    }
}
