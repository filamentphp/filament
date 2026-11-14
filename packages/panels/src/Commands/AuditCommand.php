<?php

namespace Filament\Commands;

use Filament\Facades\Filament;
use Filament\Panel;
use Filament\Resources\Resource;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Concerns\RestrictsFileUploadsToSchemaComponents;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Gate;
use ReflectionClass;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputOption;
use Throwable;

#[AsCommand(name: 'filament:audit')]
class AuditCommand extends Command
{
    protected $description = 'Report risky panel configurations, such as resources without policies';

    protected $name = 'filament:audit';

    /**
     * @var array<string>
     */
    protected array $policyMethods = ['viewAny', 'view', 'create', 'update', 'delete', 'deleteAny'];

    /**
     * @var array<string, int>
     */
    protected array $severities = [
        'info' => 0,
        'warning' => 1,
    ];

    /**
     * @return array<InputOption>
     */
    protected function getOptions(): array
    {
        return [
            new InputOption(
                name: 'json',
                shortcut: null,
                mode: InputOption::VALUE_NONE,
                description: 'Output the findings as JSON',
            ),
            new InputOption(
                name: 'fail-on',
                shortcut: null,
                mode: InputOption::VALUE_REQUIRED,
                description: 'Return a failure exit code if a finding has at least this severity - either \'info\' or \'warning\'',
            ),
        ];
    }

    public function handle(): int
    {
        $failOn = $this->option('fail-on');

        if (filled($failOn) && (! array_key_exists($failOn, $this->severities))) {
            $this->error('The [--fail-on] option must be either [info] or [warning].');

            return static::INVALID;
        }

        $findings = [];

        foreach (Filament::getPanels() as $panel) {
            $findings = [
                ...$findings,
                ...$this->auditResources($panel),
                ...$this->auditGuestPages($panel),
            ];
        }

        if ($this->option('json')) {
            $this->line(json_encode($findings, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
        } elseif (blank($findings)) {
            $this->components->info('No issues found.');
        } else {
            $this->table(
                ['Panel', 'Severity', 'Component', 'File', 'Message'],
                array_map(fn (array $finding): array => [
                    $finding['panel'],
                    $finding['severity'],
                    $finding['component'],
                    $finding['file'],
                    $finding['message'],
                ], $findings),
            );
        }

        if (blank($failOn)) {
            return static::SUCCESS;
        }

        foreach ($findings as $finding) {
            if ($this->severities[$finding['severity']] >= $this->severities[$failOn]) {
                return static::FAILURE;
            }
        }

        return static::SUCCESS;
    }

    /**
     * @return array<array{panel: string, severity: string, component: string, file: ?string, message: string}>
     */
    protected function auditResources(Panel $panel): array
    {
        $findings = [];

        foreach ($panel->getResources() as $resource) {
            /** @var class-string<Resource> $resource */
            if ($resource::shouldSkipAuthorization()) {
                $findings[] = $this->makeFinding($panel, 'info', $resource, 'Authorization is skipped, so all panel users can perform every operation.');

                continue;
            }

            if ($panel->isAuthorizationStrict() || (! $resource::shouldCheckPolicyExistence())) {
                continue;
            }

            try {
                $model = $resource::getModel();
                $policy = Gate::getPolicyFor($model);
            } catch (Throwable) {
                continue;
            }

            if (blank($policy)) {
                $findings[] = $this->makeFinding($panel, 'warning', $resource, "No policy was found for [{$model}], so all operations are allowed. Add a policy or enable strict authorization.");

                continue;
            }

            $missingMethods = array_values(array_filter(
                $this->policyMethods,
                fn (string $method): bool => ! method_exists($policy, $method),
            ));

            if (filled($missingMethods)) {
                $methods = implode('(), ', $missingMethods) . '()';
                $policyClass = $policy::class;

                $findings[] = $this->makeFinding($panel, 'warning', $resource, "The [{$policyClass}] policy is missing [{$methods}], so those operations are allowed. Add the methods or enable strict authorization.");
            }
        }

        return $findings;
    }

    /**
     * @return array<array{panel: string, severity: string, component: string, file: ?string, message: string}>
     */
    protected function auditGuestPages(Panel $panel): array
    {
        $findings = [];

        $routeActions = [
            $panel->getLoginRouteAction(),
            $panel->getRegistrationRouteAction(),
            $panel->getRequestPasswordResetRouteAction(),
            $panel->getResetPasswordRouteAction(),
        ];

        foreach ($routeActions as $page) {
            if (! (is_string($page) && class_exists($page))) {
                continue;
            }

            $traits = class_uses_recursive($page);

            if (! in_array(InteractsWithSchemas::class, $traits)) {
                continue;
            }

            if (in_array(RestrictsFileUploadsToSchemaComponents::class, $traits)) {
                continue;
            }

            $findings[] = $this->makeFinding($panel, 'warning', $page, 'This guest page accepts Livewire file uploads to any property. Add the [' . RestrictsFileUploadsToSchemaComponents::class . '] trait.');
        }

        return $findings;
    }

    /**
     * @param  class-string  $component
     * @return array{panel: string, severity: string, component: string, file: ?string, message: string}
     */
    protected function makeFinding(Panel $panel, string $severity, string $component, string $message): array
    {
        $file = (new ReflectionClass($component))->getFileName();

        return [
            'panel' => $panel->getId(),
            'severity' => $severity,
            'component' => $component,
            'file' => ($file === false) ? null : str($file)->after(base_path() . DIRECTORY_SEPARATOR)->toString(),
            'message' => $message,
        ];
    }
}
