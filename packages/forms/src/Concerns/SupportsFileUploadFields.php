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

    public function getUploadedFileUrl(string $statePath, string $fileKey): ?string
    {
        foreach ($this->getComponents() as $component) {
            if ($component instanceof BaseFileUpload && $component->getStatePath() === $statePath) {
                return $component->getUploadedFileUrl($fileKey);
            }

            foreach ($component->getChildComponentContainers() as $container) {
                if ($container->isHidden()) {
                    continue;
                }

                if ($url = $container->getUploadedFileUrl($statePath, $fileKey)) {
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
}
