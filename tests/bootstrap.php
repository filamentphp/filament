<?php

use Dotenv\Dotenv;

define('TESTBENCH_WORKING_PATH', dirname(__DIR__));

require_once __DIR__ . '/../vendor/autoload.php';

// Load `.env.testing` without overriding environment variables that are already set.
if (file_exists(__DIR__ . '/../.env.testing')) {
    $dotenv = Dotenv::createImmutable(__DIR__ . '/..', '.env.testing');
    $dotenv->safeLoad();
}

// Pre-generate real-time facades before tests can load them through `AliasLoader`.
// Parallel workers share this cache, so writes use `LOCK_EX` to serialize writers.

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
