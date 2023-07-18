<?php

namespace Filament\Forms\Concerns;

use Filament\Forms\Components\BaseFileUpload;

trait SupportsFileUploadFields
{
    public function deleteUploadedFile(string $statePath, string $fileKey): bool
    {
        foreach ($this->getComponents() as $component) {
            if ($component instanceof BaseFileUpload && $component->getStatePath() === $statePath) {
                $component->deleteUploadedFile($fileKey);

                return true;
            }

            foreach ($component->getChildComponentContainers() as $container) {
                if ($container->isHidden()) {
                    continue;
                }

                if ($container->deleteUploadedFile($statePath, $fileKey)) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * @return array<array{name: string, size: int, type: string, url: string} | null> | null
     */
    public function getUploadedFiles(string $statePath): ?array
    {
        foreach ($this->getComponents() as $component) {
            if ($component instanceof BaseFileUpload && $component->getStatePath() === $statePath) {
                return $component->getUploadedFiles();
            }

            foreach ($component->getChildComponentContainers() as $container) {
                if ($container->isHidden()) {
                    continue;
                }

                if ($files = $container->getUploadedFiles($statePath)) {
                    return $files;
                }
            }
        }

        return null;
    }

    public function removeUploadedFile(string $statePath, string $fileKey): bool
    {
        foreach ($this->getComponents() as $component) {
            if ($component instanceof BaseFileUpload && $component->getStatePath() === $statePath) {
                $component->removeUploadedFile($fileKey);

                return true;
            }

            foreach ($component->getChildComponentContainers() as $container) {
                if ($container->isHidden()) {
                    continue;
                }

                if ($container->removeUploadedFile($statePath, $fileKey)) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * @param  array<array-key>  $fileKeys
     */
    public function reorderUploadedFiles(string $statePath, array $fileKeys): bool
    {
        foreach ($this->getComponents() as $component) {
            if ($component instanceof BaseFileUpload && $component->getStatePath() === $statePath) {
                $component->reorderUploadedFiles($fileKeys);

                return true;
            }

            foreach ($component->getChildComponentContainers() as $container) {
                if ($container->isHidden()) {
                    continue;
                }

                if ($container->reorderUploadedFiles($statePath, $fileKeys)) {
                    return true;
                }
            }
        }

        return false;
    }
}
