<?php

namespace Filament\Commands;

use Filament\Clusters\Cluster;
use Filament\Facades\Filament;
use Filament\Panel;
use Filament\Support\Commands\Concerns\CanIndentStrings;
use Filament\Support\Commands\Concerns\CanManipulateFiles;
use Illuminate\Console\Command;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

use function Laravel\Prompts\confirm;
use function Laravel\Prompts\select;
use function Laravel\Prompts\suggest;
use function Laravel\Prompts\text;

class TenantRegisterCommand extends Command
{
    use CanIndentStrings;
    use CanManipulateFiles;

    protected $description = 'Register a new tenant';

    protected $signature = 'filament:register-tenant {--panel=} {--F|force}';

    public function handle(): int
    {
        $this->components->info('Registering a new tenant');

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

            return 1;
        }

        $page = $panel->getTenantRegistrationPage();

        $tenantModel = $panel->getTenantModel();
        $tenantModelLabel = Str::snake(Str::singular(class_basename($tenantModel)));

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

        dd($namespace, $path);
    }
}
