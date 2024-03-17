<?php

namespace Filament\Commands;

use Filament\Facades\Filament;
use Filament\Panel;
use Filament\Support\Commands\Concerns\CanIndentStrings;
use Filament\Support\Commands\Concerns\CanManipulateFiles;
use Illuminate\Console\Command;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

use function Laravel\Prompts\select;

class TenantRegisterCommand extends Command
{
    use CanIndentStrings;
    use CanManipulateFiles;

    protected $description = 'Create a new Filament tenant registration page';

    protected $signature = 'filament:register-tenant {--panel=} {--F|force}';

    public function handle(): int
    {
        $this->components->info('Creating a new tenant registration page');

        $panel = $this->option('panel');

        if ($panel) {
            $panel = Filament::getPanel($panel);
        }

        if (! $panel) {
            $panels = Filament::getPanels();

            /** @var Panel $panel */
            $panel = (count($panels) > 1) ? $panels[select(
                label: 'Which panel would you like to create this in?',
                options: array_map(
                    fn (Panel $panel): string => $panel->getId(),
                    $panels,
                ),
                default: Filament::getDefaultPanel()->getId()
            )] : Arr::first($panels);
        }

        if (! $panel->hasTenancy()) {
            $this->components->error('No tenant model has been defined for this panel.');

            return static::FAILURE;
        }

        if (! $this->option('force') && filled($panel->getTenantRegistrationPage())) {
            $this->components->info('Tenant registration page already exists.');

            return static::INVALID;
        }

        $tenantModel = $panel->getTenantModel();
        $tenantBaseClass = class_basename($tenantModel);
        $page = "Register{$tenantBaseClass}";
        $tenantModelLabel = Str::snake(Str::singular($tenantBaseClass));

        $tenancyDirectories = $panel->getTenancyDirectories();
        $tenancyNamespaces = $panel->getTenancyNamespaces();

        $namespace = (count($tenancyNamespaces) > 1) ?
            select(
                label: 'Which namespace would you like to create this in?',
                options: $tenancyNamespaces,
            ) :
            (Arr::first($tenancyNamespaces) ?: 'App\\Filament\\Pages\\Tenancy');
        $path = (count($tenancyDirectories) > 1) ?
            $tenancyDirectories[array_search($namespace, $tenancyNamespaces)] :
            Arr::first($tenancyDirectories) ?? app_path('Filament/Pages/Tenancy');

        $filePath = (string) str($page)
            ->prepend('/')
            ->prepend($path)
            ->append('.php');

        $files = [$filePath];

        if (! $this->option('force') && $this->checkForCollision($files)) {
            return static::INVALID;
        }

        $this->copyStubToApp('RegisterTenant', $filePath, [
            'class' => $page,
            'namespace' => $namespace,
            'tenantModelFullPath' => $tenantModel,
            'tenantModel' => $tenantBaseClass,
            'tenantModelLabel' => $tenantModelLabel,
        ]);

        $this->components->info("Tenant registration page created: {$filePath}");

        return static::SUCCESS;
    }
}
