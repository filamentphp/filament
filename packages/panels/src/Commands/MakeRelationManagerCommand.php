<?php

namespace Filament\Commands;

use Filament\Facades\Filament;
use Filament\Panel;
use Filament\Support\Commands\Concerns\CanIndentStrings;
use Filament\Support\Commands\Concerns\CanManipulateFiles;
use Illuminate\Console\Command;
use Illuminate\Support\Arr;
use Symfony\Component\Console\Attribute\AsCommand;

use function Laravel\Prompts\select;
use function Laravel\Prompts\text;

#[AsCommand(name: 'make:filament-relation-manager')]
class MakeRelationManagerCommand extends Command
{
    use CanIndentStrings;
    use CanManipulateFiles;

    protected $description = 'Create a new Filament relation manager class for a resource';

    protected $signature = 'make:filament-relation-manager {resource?} {relationship?} {recordTitleAttribute?} {--attach} {--associate} {--soft-deletes} {--view} {--panel=} {--F|force}';

    public function handle(): int
    {
        $resource = (string) str(
            $this->argument('resource') ?? text(
                label: 'What is the resource you would like to create this in?',
                placeholder: 'DepartmentResource',
                required: true,
            ),
        )
            ->studly()
            ->trim('/')
            ->trim('\\')
            ->trim(' ')
            ->replace('/', '\\');

        if (! str($resource)->endsWith('Resource')) {
            $resource .= 'Resource';
        }

        $relationship = (string) str($this->argument('relationship') ?? text(
            label: 'What is the relationship?',
            placeholder: 'members',
            required: true,
        ))
            ->trim(' ');
        $managerClass = (string) str($relationship)
            ->studly()
            ->append('RelationManager');

        $recordTitleAttribute = (string) str($this->argument('recordTitleAttribute') ?? text(
            label: 'What is the title attribute?',
            placeholder: 'name',
            required: true,
        ))
            ->trim(' ');

        $panel = $this->option('panel');

        if ($panel) {
            $panel = Filament::getPanel($panel, isStrict: false);
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

        $resourceDirectories = $panel->getResourceDirectories();
        $resourceNamespaces = $panel->getResourceNamespaces();

        foreach ($resourceDirectories as $resourceIndex => $resourceDirectory) {
            if (str($resourceDirectory)->startsWith(base_path('vendor'))) {
                unset($resourceDirectories[$resourceIndex]);
                unset($resourceNamespaces[$resourceIndex]);
            }
        }

        $resourceNamespace = (count($resourceNamespaces) > 1) ?
            select(
                label: 'Which namespace would you like to create this in?',
                options: $resourceNamespaces
            ) :
            (Arr::first($resourceNamespaces) ?? 'App\\Filament\\Resources');
        $resourcePath = (count($resourceDirectories) > 1) ?
            $resourceDirectories[array_search($resourceNamespace, $resourceNamespaces)] :
            (Arr::first($resourceDirectories) ?? app_path('Filament/Resources/'));

        $path = (string) str($managerClass)
            ->prepend("{$resourcePath}/{$resource}/RelationManagers/")
            ->replace('\\', '/')
            ->append('.php');

        if (! $this->option('force') && $this->checkForCollision([
            $path,
        ])) {
            return static::INVALID;
        }

        $tableHeaderActions = [];

        $tableHeaderActions[] = 'Tables\Actions\CreateAction::make(),';

        if ($this->option('associate')) {
            $tableHeaderActions[] = 'Tables\Actions\AssociateAction::make(),';
        }

        if ($this->option('attach')) {
            $tableHeaderActions[] = 'Tables\Actions\AttachAction::make(),';
        }

        $tableHeaderActions = implode(PHP_EOL, $tableHeaderActions);

        $tableActions = [];

        if ($this->option('view')) {
            $tableActions[] = 'Tables\Actions\ViewAction::make(),';
        }

        $tableActions[] = 'Tables\Actions\EditAction::make(),';

        if ($this->option('associate')) {
            $tableActions[] = 'Tables\Actions\DissociateAction::make(),';
        }

        if ($this->option('attach')) {
            $tableActions[] = 'Tables\Actions\DetachAction::make(),';
        }

        $tableActions[] = 'Tables\Actions\DeleteAction::make(),';

        if ($this->option('soft-deletes')) {
            $tableActions[] = 'Tables\Actions\ForceDeleteAction::make(),';
            $tableActions[] = 'Tables\Actions\RestoreAction::make(),';
        }

        $tableActions = implode(PHP_EOL, $tableActions);

        $tableBulkActions = [];

        if ($this->option('associate')) {
            $tableBulkActions[] = 'Tables\Actions\DissociateBulkAction::make(),';
        }

        if ($this->option('attach')) {
            $tableBulkActions[] = 'Tables\Actions\DetachBulkAction::make(),';
        }

        $tableBulkActions[] = 'Tables\Actions\DeleteBulkAction::make(),';

        $modifyQueryUsing = '';

        if ($this->option('soft-deletes')) {
            $modifyQueryUsing .= '->modifyQueryUsing(fn (Builder $query) => $query->withoutGlobalScopes([';
            $modifyQueryUsing .= PHP_EOL . '    SoftDeletingScope::class,';
            $modifyQueryUsing .= PHP_EOL . ']))';

            $tableBulkActions[] = 'Tables\Actions\ForceDeleteBulkAction::make(),';
            $tableBulkActions[] = 'Tables\Actions\RestoreBulkAction::make(),';
        }

        $tableBulkActions = implode(PHP_EOL, $tableBulkActions);

        $this->copyStubToApp('RelationManager', $path, [
            'modifyQueryUsing' => filled($modifyQueryUsing) ? PHP_EOL . $this->indentString($modifyQueryUsing, 3) : $modifyQueryUsing,
            'namespace' => "{$resourceNamespace}\\{$resource}\\RelationManagers",
            'managerClass' => $managerClass,
            'recordTitleAttribute' => $recordTitleAttribute,
            'relationship' => $relationship,
            'tableActions' => $this->indentString($tableActions, 4),
            'tableBulkActions' => $this->indentString($tableBulkActions, 5),
            'tableFilters' => $this->indentString(
                $this->option('soft-deletes') ? 'Tables\Filters\TrashedFilter::make()' : '//',
                4,
            ),
            'tableHeaderActions' => $this->indentString($tableHeaderActions, 4),
        ]);

        $this->components->info("Filament relation manager [{$path}] created successfully.");

        $this->components->info("Make sure to register the relation in `{$resource}::getRelations()`.");

        return static::SUCCESS;
    }
}
