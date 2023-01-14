<?php

namespace Filament\Infolists\Concerns;

use Filament\Forms\Components\BaseFileUpload;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Arr;

trait HasState
{
    protected ?string $statePath = null;

    public function statePath(?string $path): static
    {
        $this->statePath = $path;

        return $this;
    }

    public function getStatePath(bool $isAbsolute = true): string
    {
        $pathComponents = [];

        if ($isAbsolute && $parentComponentStatePath = $this->getParentComponent()?->getStatePath()) {
            $pathComponents[] = $parentComponentStatePath;
        }

        if (($statePath = $this->statePath) !== null) {
            $pathComponents[] = $statePath;
        }

        return implode('.', $pathComponents);
    }
}
