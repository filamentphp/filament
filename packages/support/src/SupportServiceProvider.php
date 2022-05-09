<?php

namespace Filament\Support;

use Illuminate\Support\Facades\Blade;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class SupportServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('filament-support')
            ->hasTranslations()
            ->hasViews();
    }

    public function packageBooted()
    {
        Blade::directive('captureSlots', function (string $expression) {
            return "<?php \$slotContents = get_defined_vars(); \$slots = collect({$expression})->mapWithKeys(fn (string \$slot): array => [\$slot => \$slotContents[\$slot] ?? null])->toArray(); unset(\$slotContents) ?>";
        });
    }
}
