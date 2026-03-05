<?php

namespace Filament\Tests\Fixtures\Resources\Posts;

use Filament\Resources\ResourceConfiguration;

class PostResourceConfiguration extends ResourceConfiguration
{
    protected ?string $navigationLabel = null;

    protected ?string $navigationGroup = null;

    protected ?int $navigationSort = null;

    protected bool $isFeatured = false;

    protected bool $isArchived = false;

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

    public function featured(bool $condition = true): static
    {
        $this->isFeatured = $condition;

        return $this;
    }

    public function isFeatured(): bool
    {
        return $this->isFeatured;
    }

    public function archived(bool $condition = true): static
    {
        $this->isArchived = $condition;

        return $this;
    }

    public function isArchived(): bool
    {
        return $this->isArchived;
    }
}
