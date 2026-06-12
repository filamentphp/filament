<?php

use Dotenv\Dotenv;

require_once __DIR__ . '/../vendor/autoload.php';

// Load .env.testing file if it exists
// Using safeLoad() to avoid overriding environment variables that are already set
if (file_exists(__DIR__ . '/../.env.testing')) {
    $dotenv = Dotenv::createImmutable(__DIR__ . '/..', '.env.testing');
    $dotenv->safeLoad();
}

/*
|--------------------------------------------------------------------------
| Pre-generate Real-Time Facade Cache Files
|--------------------------------------------------------------------------
|
| Real-time facades are generated on-demand by Laravel's AliasLoader and
| cached as PHP files in storage/framework/cache/. When running tests in
| parallel, multiple workers may try to generate the same facade file
| simultaneously, causing a race condition where one worker reads an
| incomplete file written by another.
|
| We pre-generate these facade files here to avoid the race condition.
| This runs in each worker, but file_put_contents with LOCK_EX ensures
| atomic writes, and checking file existence first minimizes contention.
|
*/

$facadesToPregenerate = [
    'Facades\\Livewire\\Features\\SupportFileUploads\\GenerateSignedUploadUrl',
];

$storagePath = __DIR__ . '/../vendor/orchestra/testbench-core/laravel/storage/framework/cache';

if (! is_dir($storagePath)) {
    @mkdir($storagePath, 0755, true);
}

foreach ($facadesToPregenerate as $alias) {
    $path = $storagePath . '/facade-' . sha1($alias) . '.php';

    if (is_file($path)) {
        continue;
    }

    $namespace = str_replace('/', '\\', dirname(str_replace('\\', '/', $alias)));
    $class = class_basename($alias);
    $target = substr($alias, strlen('Facades\\'));

    $stub = <<<PHP
<?php

namespace {$namespace};

use Illuminate\Support\Facades\Facade;

/**
 * @mixin \\{$target}
 */
class {$class} extends Facade
{
    /**
     * Get the registered name of the component.
     */
    protected static function getFacadeAccessor(): string
    {
        return '{$target}';
    }
}

PHP;

    file_put_contents($path, $stub, LOCK_EX);
}
