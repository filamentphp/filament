<?php

namespace Filament\Support\Commands;

use Filament\Support\Commands\Concerns\CanManipulateFiles;
use Illuminate\Console\Command;
use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand(name: 'make:action')]
class MakeActionCommand extends Command
{
    use CanManipulateFiles;

    protected $description = 'Command to make an action instance';

    protected $signature = 'make:action {action} {--action-only} {--contract-only}';

    public function handle(): int
    {
        $action = $this->argument(key: 'action');

        $explode = explode(separator: '/', string: $action);

        $actionClassName = array_pop($explode);
        $actionContractName = $this->getContractClassName($actionClassName);

        $actionClassPath = implode(separator: '/', array: $explode);
        $baseActionPath = app_path(path: '/Actions/' . $actionClassPath);

        $contractCreationStatus = $this->shouldNotCreateContract() ?
            self::SUCCESS :
            $this->createContract(
                baseActionPath: $baseActionPath,
                actionContractName: $actionContractName,
                actionClassPath: $actionClassPath,
            );

        $actionCreationStatus = $this->shouldNotCreateAction() ?
            self::SUCCESS :
            $this->createAction(
                baseActionPath: $baseActionPath,
                actionClassName: $actionClassName,
                actionClassPath: $actionClassPath,
            );

        return $contractCreationStatus === self::SUCCESS && $actionCreationStatus === self::SUCCESS ?
            self::SUCCESS :
            self::FAILURE;
    }

    protected function shouldNotCreateContract(): bool
    {
        return $this->option(key: 'action-only');
    }

    protected function shouldNotCreateAction(): bool
    {
        return $this->option(key: 'contract-only');
    }

    protected function getContractNamespace(string $actionClassPath): string
    {
        return str('App\\Actions\\')
            ->append($actionClassPath . '\\Contracts')
            ->replace(search: '/', replace: '\\')
            ->toString();
    }

    protected function getContractClassName(string $actionClassName): string
    {
        return str($actionClassName)
            ->snake()
            ->explode(delimiter: '_')
            ->map(
                fn (string $word, int $index): string => $index === 0 ?
                    str($word)->plural()->ucfirst()->toString() :
                    str($word)->ucfirst()->toString(),
            )
            ->implode(value: '');
    }

    protected function createContract(
        string $baseActionPath,
        string $actionContractName,
        string $actionClassPath,
    ): int {
        $actionContractPath = $baseActionPath . '/Contracts/' . $actionContractName . '.php';
        $contractNamespace = $this->getContractNamespace($actionClassPath);

        if (file_exists($actionContractPath)) {
            $this->error(string: 'Action contract already exists!');

            return self::FAILURE;
        }

        $this->copyStubToApp('ActionContract', $actionContractPath, [
            'contractNamespace' => $contractNamespace,
            'actionContractName' => $actionContractName,
        ]);

        $this->info(string: 'Action contract [' . $contractNamespace . '\\' . $actionContractName . '] created successfully');

        return self::SUCCESS;
    }

    protected function createAction(
        string $baseActionPath,
        string $actionClassName,
        string $actionClassPath,
    ): int {
        $actionPath = $baseActionPath . '/' . $actionClassName . '.php';
        $actionNamespace = str('App\\Actions\\')
            ->append($actionClassPath)
            ->replace(search: '/', replace: '\\')
            ->toString();

        if (file_exists($actionPath)) {
            $this->error(string: 'Action already exists!');

            return self::FAILURE;
        }

        $this->shouldNotCreateContract() ?
            $this->copyStubToApp('ActionWithoutContract', targetPath: $actionPath, replacements: [
                'namespace' => $actionNamespace,
                'actionClassName' => $actionClassName,
            ]) :
            $this->copyStubToApp('Action', targetPath: $actionPath, replacements: [
                'namespace' => $actionNamespace,
                'contractNamespace' => $this->getContractNamespace($actionClassPath),
                'actionContractName' => $this->getContractClassName($actionClassName),
                'actionClassName' => $actionClassName,
            ]);

        $this->info(string: 'Action [' . $actionNamespace . '\\' . $actionClassName . '] created successfully');

        return self::SUCCESS;
    }
}
