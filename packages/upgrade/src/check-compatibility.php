<?php

use function Laravel\Prompts\confirm;
use function Termwind\render;

render('<p class="text-blue font-bold">Checking PHP version compatibility with v4...</p>');

if (version_compare(PHP_VERSION, '8.2.0', '<')) {
    $detected = PHP_VERSION;

    render('<p class="text-red font-bold">Incompatible PHP version detected</p>');
    render("<p>Detected PHP {$detected}. Filament v4 and Laravel 11 require PHP 8.2+ for language features, performance improvements, and security fixes. Please upgrade your PHP runtime to 8.2 or higher before proceeding.</p>");
    render(<<<'HTML'
        <p class="bg-red-600 text-red-50 mt-1">
            <strong>Upgrade aborted because PHP version is below 8.2</strong>
        </p>
    HTML);

    exit(1);
}

render('<p class="text-blue font-bold">Checking Laravel version compatibility with v4...</p>');

$laravelVersion = null;

try {
    if (file_exists('composer.lock')) {
        $lock = json_decode(file_get_contents('composer.lock'), true);
        $packages = array_merge($lock['packages'] ?? [], $lock['packages-dev'] ?? []);

        foreach ($packages as $package) {
            if (($package['name'] ?? '') === 'laravel/framework') {
                $laravelVersion = ltrim((string) ($package['version'] ?? ''), 'v');

                break;
            }
        }
    }
} catch (Throwable $exception) {
}

if ($laravelVersion !== null && version_compare($laravelVersion, '11.28.0', '<')) {
    render('<p class="text-red font-bold">Incompatible Laravel version detected</p>');
    render("<p>Detected Laravel {$laravelVersion}. Filament v4 targets Laravel v11.28+ to rely on framework changes and fixes introduced in v11.28 and later. Please upgrade Laravel to at least v11.28 before continuing.</p>v");
    render(<<<'HTML'
        <p class="bg-red-600 text-red-50 mt-1">
            <strong>Upgrade aborted because Laravel version is below 11.28</strong>
        </p>
    HTML);

    exit(1);
}

render('<p class="text-blue font-bold">Checking plugin compatibility with v4...</p>');

$composer = json_decode(file_get_contents('composer.json'), true);
$deps = $composer['require'] ?? [];
$allPackages = array_keys($deps);

$plugins = array_filter($allPackages, function ($plugin) {
    if (str_starts_with($plugin, 'filament/')) {
        return false;
    }

    try {
        $composerPath = "vendor/{$plugin}/composer.json";

        if (! file_exists($composerPath)) {
            return false;
        }

        $composerContent = file_get_contents($composerPath);
        $composer = json_decode($composerContent, true);

        if (! $composer || ! is_array($composer)) {
            return false;
        }

        $requires = $composer['require'] ?? [];

        foreach ($requires as $key => $value) {
            if (str_starts_with($key, 'filament/')) {
                return true;
            }
        }

        return false;
    } catch (Exception $exception) {
        render("<p class=\"text-red\">Error checking if {$plugin} requires a filament/* package: " . $exception->getMessage() . '</p>');
    }

    return false;
});

// Initialize a shared global cache for Packagist data to be reused by the bin/filament-v4 script.
$GLOBALS['FILAMENT_UPGRADE_PACKAGIST'] = $GLOBALS['FILAMENT_UPGRADE_PACKAGIST'] ?? [
    'versions' => [], // [plugin => ['stable' => versionsArray, 'dev' => versionsArray]]
    'compatibility' => [], // [plugin => ['version' => string, 'isPrerelease' => bool] | null]
    'plugins' => [], // list of detected third-party plugins
];
$GLOBALS['FILAMENT_UPGRADE_PACKAGIST']['plugins'] = array_values($plugins);

$incompatiblePlugins = [];

foreach ($plugins as $plugin) {
    $version = $deps[$plugin];
    $compatibility = $GLOBALS['FILAMENT_UPGRADE_PACKAGIST']['compatibility'][$plugin] ?? null;

    $url = "https://repo.packagist.org/p2/{$plugin}.json";

    try {
        $versions = $GLOBALS['FILAMENT_UPGRADE_PACKAGIST']['versions'][$plugin]['stable'] ?? null;
        if ($versions === null) {
            $json = @file_get_contents($url);

            if ($json) {
                $data = json_decode($json, true);
                $versions = $data['packages'][$plugin] ?? [];
                $GLOBALS['FILAMENT_UPGRADE_PACKAGIST']['versions'][$plugin]['stable'] = $versions;
            } else {
                $versions = [];
                $GLOBALS['FILAMENT_UPGRADE_PACKAGIST']['versions'][$plugin]['stable'] = $versions;
            }
        }

        if ($compatibility === null && $versions) {
            foreach ($versions as $checkingVersion) {
                $requires = $checkingVersion['require'] ?? [];

                foreach ($requires as $dep => $constraint) {
                    if (! str_starts_with($dep, 'filament/')) {
                        continue;
                    }

                    if (preg_match("/\^\s*4(?:\.|$)|~\s*4(?:\.|$)|>=\s*4(?:\.|$)/", (string) $constraint)) {
                        $compatibility = [
                            'version' => $checkingVersion['version'],
                            'isPrerelease' => false,
                        ];

                        break;
                    }
                }
            }
        }

        if ($compatibility === null) {
            $devVersions = $GLOBALS['FILAMENT_UPGRADE_PACKAGIST']['versions'][$plugin]['dev'] ?? null;
            if ($devVersions === null) {
                $devUrl = "https://repo.packagist.org/p2/{$plugin}~dev.json";
                $devJson = @file_get_contents($devUrl);

                if ($devJson) {
                    $devData = json_decode($devJson, true);
                    $devVersions = $devData['packages'][$plugin] ?? [];
                    $GLOBALS['FILAMENT_UPGRADE_PACKAGIST']['versions'][$plugin]['dev'] = $devVersions;
                } else {
                    $devVersions = [];
                    $GLOBALS['FILAMENT_UPGRADE_PACKAGIST']['versions'][$plugin]['dev'] = $devVersions;
                }
            }

            foreach ($devVersions as $checkingVersion) {
                $requires = $checkingVersion['require'] ?? [];

                foreach ($requires as $dep => $constraint) {
                    if (! str_starts_with($dep, 'filament/')) {
                        continue;
                    }

                    if (preg_match("/\^\s*4(?:\.|$)|~\s*4(?:\.|$)|>=\s*4(?:\.|$)/", (string) $constraint)) {
                        $compatibility = [
                            'version' => $checkingVersion['version'],
                            'isPrerelease' => true,
                        ];

                        break;
                    }
                }
            }
        }

        // If still unknown and we couldn't fetch any versions from Packagist, but the package exists locally in vendor,
        // assume it's a private/non-Packagist package and do not block the upgrade.
        if ($compatibility === null) {
            $stableEmpty = empty($GLOBALS['FILAMENT_UPGRADE_PACKAGIST']['versions'][$plugin]['stable'] ?? []);
            $devEmpty = empty($GLOBALS['FILAMENT_UPGRADE_PACKAGIST']['versions'][$plugin]['dev'] ?? []);
            $isInstalledLocally = file_exists("vendor/{$plugin}/composer.json");

            if ($stableEmpty && $devEmpty && $isInstalledLocally) {
                // Mark as compatible (unknown) to avoid blocking; we cache this decision.
                $compatibility = [
                    'version' => 'unknown',
                    'isPrerelease' => false,
                ];
            }
        }

        if (! array_key_exists($plugin, $GLOBALS['FILAMENT_UPGRADE_PACKAGIST']['compatibility'])) {
            $GLOBALS['FILAMENT_UPGRADE_PACKAGIST']['compatibility'][$plugin] = $compatibility;
        }

        if ($compatibility === null) {
            $incompatiblePlugins[] = $plugin;
        }
    } catch (Exception $exception) {
        $incompatiblePlugins[] = $plugin;
    }
}

if ($incompatiblePlugins) {
    $pluginList = implode(', ', $incompatiblePlugins);

    render('<p class="text-red font-bold mt-1">Incompatible plugins found!</p>');
    render('<p>The following plugins are incompatible with Filament v4 and need to be removed before upgrading:</p>');
    render("<p class=\"text-red\">{$pluginList}</p>");
    render('<p>You could temporarily remove them from your composer.json file until they\'ve been upgraded, replace them with a similar plugin that is compatible with v4, wait for the plugins to be upgraded before upgrading your app, or even write PRs to help the authors upgrade them.</p>');

    $continue = confirm(
        label: 'Do you want to continue even though there are incompatible plugins?',
        default: false,
        yes: 'Yes - Continue anyway',
        no: 'No - Abort upgrade',
        hint: 'You\'ll need to manually remove / fix the listed plugins.',
    );

    if (! $continue) {
        render(<<<'HTML'
            <p class="bg-red-600 text-red-50 mt-1">
                <strong>Upgrade aborted because of incompatible plugins</strong>
            </p>
        HTML);

        exit(1);
    }
}
