<?php

namespace Filament\Tests\Fixtures\Pages;

use Filament\Pages\PageConfiguration;

class ConfigurableSettingsConfiguration extends PageConfiguration
{
    protected ?string $navigationLabel = null;

    protected ?string $navigationGroup = null;

    protected ?int $navigationSort = null;

    protected ?string $settingsCategory = null;

    public function navigationLabel(string $label): static
    {
        $this->navigationLabel = $label;

        return $this;
    }

    public function getNavigationLabel(): ?string
    {
        return $this->navigationLabel;
    }

    public function navigationGroup(string $group): static
    {
        $this->navigationGroup = $group;

        return $this;
    }

    public function getNavigationGroup(): ?string
    {
        return $this->navigationGroup;
    }

    public function navigationSort(int $sort): static
    {
        $this->navigationSort = $sort;

        return $this;
    }

    public function getNavigationSort(): ?int
    {
        return $this->navigationSort;
    }

    public function settingsCategory(string $category): static
    {
        $this->settingsCategory = $category;

        return $this;
    }

    public function getSettingsCategory(): ?string
    {
        return $this->settingsCategory;
    }
}
