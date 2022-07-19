<?php

namespace Filament\Support;

use Composer\InstalledVersions;
use Filament\Support\Testing\TestsActions;
use HtmlSanitizer\Sanitizer;
use HtmlSanitizer\SanitizerInterface;
use Illuminate\Foundation\Console\AboutCommand;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Str;
use Illuminate\Support\Stringable;
use Livewire\Testing\TestableLivewire;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class SupportServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('filament-support')
            ->hasConfigFile()
            ->hasTranslations()
            ->hasViews();
    }

    public function packageRegistered()
    {
        $this->app->scoped(
            SanitizerInterface::class,
            function () {
                return Sanitizer::create([
                    'extensions' => [
                        'basic',
                        'code',
                        'details',
                        'extra',
                        'iframe',
                        'image',
                        'list',
                        'table',
                    ],
                ]);
            },
        );

        TestableLivewire::mixin(new TestsActions());
    }

    public function packageBooted()
    {
        Blade::directive('captureSlots', function (string $expression): string {
            return "<?php \$slotContents = get_defined_vars(); \$slots = collect({$expression})->mapWithKeys(fn (string \$slot): array => [\$slot => \$slotContents[\$slot] ?? null])->toArray(); unset(\$slotContents) ?>";
        });

        Str::macro('sanitizeHtml', function (string $html): string {
            return app(SanitizerInterface::class)->sanitize($html);
        });

        Stringable::macro('sanitizeHtml', function (): Stringable {
            /** @phpstan-ignore-next-line */
            return new Stringable(Str::sanitizeHtml($this->value));
        });

        if (class_exists(AboutCommand::class)) {
            AboutCommand::add('Filament', [
                'Version' => InstalledVersions::getPrettyVersion('filament/support'),
                'Packages' => collect([
                    'admin' => InstalledVersions::isInstalled('filament/filament'),
                    'forms' => InstalledVersions::isInstalled('filament/forms'),
                    'tables' => InstalledVersions::isInstalled('filament/tables'),
                ])->filter()->keys()->join(', '),
                'Views' => is_dir(resource_path('views/vendor/filament')) ? '<fg=red;options=bold>PUBLISHED</>' : '<fg=green;options=bold>NOT PUBLISHED</>',
            ]);
        }
    }
}
