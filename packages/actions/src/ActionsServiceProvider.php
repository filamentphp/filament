<?php

namespace Filament\Actions;

use Filament\Actions\Testing\TestsActions;
use Illuminate\Support\Facades\Blade;
use Livewire\Features\SupportTesting\Testable;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class ActionsServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('filament-actions')
            ->hasTranslations()
            ->hasViews();
    }

    public function packageBooted(): void
    {
        Blade::directive('action', function ($action): string {
            $action = (string) str($action)->start('\\');
            $actionObject = eval("return {$action};");
            $actionMethodName = "{$actionObject->getName()}Action";

            $result = Blade::render(<<<BLADE
                @volt('test')
                    <?php

                    Livewire\Volt\uses([
                        Filament\Actions\Contracts\HasActions::class,
                        Filament\Forms\Contracts\HasForms::class,
                        Filament\Actions\Concerns\InteractsWithActions::class,
                        Filament\Forms\Concerns\InteractsWithForms::class,
                    ]);

                    \${$actionMethodName} = fn () => {$action};

                    ?>

                    <div>
                        <div>
                            {{ \$this->{$actionMethodName} }}
                        </div>
                    </div>
                @endvolt
            BLADE);

            // dd($result);

            return $result;
        });

        Testable::mixin(new TestsActions());
    }
}
