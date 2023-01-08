<?php

namespace Filament\Concerns;

trait HasHeadTags
{
    /**
     * @var array<string>
     */
    protected array $headTags = [];

    /**
     * @param  array<string>  $tags
     */
    public function headTags(array $tags): static
    {
        $this->headTags = array_merge($this->headTags, $tags);

        return $this;
    }

    /**
     * @return array<string>
     */
    public function getHeadTags(): array
    {
        return $this->headTags;
    }
}
