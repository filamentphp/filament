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

    public function getUploadedFileUrls(string $statePath): ?array
    {
        foreach ($this->getComponents() as $component) {
            if ($component instanceof BaseFileUpload && $component->getStatePath() === $statePath) {
                return $component->getUploadedFileUrls();
            }

            foreach ($component->getChildComponentContainers() as $container) {
                if ($container->isHidden()) {
                    continue;
                }

                if ($url = $container->getUploadedFileUrls($statePath)) {
                    return $url;
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
