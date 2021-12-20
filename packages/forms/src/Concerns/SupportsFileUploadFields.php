<?php

namespace Filament\Forms\Concerns;

use Filament\Forms\Components\BaseFileUpload;

trait SupportsFileUploadFields
{
    public function getUploadedFileUrl(string $statePath): ?string
    {
        foreach ($this->getComponents() as $component) {
            if ($component instanceof BaseFileUpload && $component->getStatePath() === $statePath) {
                return $component->getUploadedFileUrl();
            }

            foreach ($component->getChildComponentContainers() as $container) {
                if ($container->isHidden()) {
                    continue;
                }

                if ($url = $container->getUploadedFileUrl($statePath)) {
                    return $url;
                }
            }
        }

        return null;
    }

    public function removeUploadedFile(string $statePath): bool
    {
        foreach ($this->getComponents() as $component) {
            if ($component instanceof BaseFileUpload && $component->getStatePath() === $statePath) {
                $component->removeUploadedFile();

                return true;
            }

            foreach ($component->getChildComponentContainers() as $container) {
                if ($container->isHidden()) {
                    continue;
                }

                if ($container->removeUploadedFile($statePath)) {
                    return true;
                }
            }
        }

        return false;
    }
}
