<?php

namespace Filament\Forms\Concerns;

use Filament\Forms\Components\Contracts\HasFileAttachments;

trait SupportsComponentFileAttachments
{
    public function getComponentFileAttachmentUrl(string $statePath): ?string
    {
        foreach ($this->getComponents() as $component) {
            if ($component instanceof HasFileAttachments && $component->getStatePath() === $statePath) {
                $attachment = $this->getLivewire()->getComponentFileAttachment($statePath);

                if (! $attachment) {
                    return null;
                }

                return $component->saveUploadedFileAttachment($attachment);
            }

            foreach ($component->getChildComponentContainers() as $container) {
                if ($container->isHidden()) {
                    continue;
                }

                if ($url = $container->getComponentFileAttachmentUrl($statePath)) {
                    return $url;
                }
            }
        }

        return null;
    }
}
