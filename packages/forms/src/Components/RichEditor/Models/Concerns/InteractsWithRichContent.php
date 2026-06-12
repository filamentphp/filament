<?php

namespace Filament\Forms\Components\RichEditor\Models\Concerns;

use Filament\Forms\Components\RichEditor\RichContentAttribute;

trait InteractsWithRichContent /** @phpstan-ignore trait.unused */
{
    /**
     * @var array<string, RichContentAttribute>
     */
    protected array $richContentAttributes;

    /**
     * @return array<string, RichContentAttribute>
     */
    public function getRichContentAttributes(): array
    {
        if (! isset($this->richContentAttributes)) {
            $this->setUpRichContent();
            $this->richContentAttributes ??= [];
        }

        return $this->richContentAttributes;
    }

    protected function setUpRichContent(): void {}

    public function registerRichContent(string $name): RichContentAttribute
    {
        return $this->richContentAttributes[$name] = RichContentAttribute::make($this, $name);
    }

    public function renderRichContent(string $attribute): string
    {
        $attribute = $this->getRichContentAttribute($attribute) ?? RichContentAttribute::make($this, $attribute);

        return $attribute->toHtml();
    }

    public function getRichContentAttribute(string $attribute): ?RichContentAttribute
    {
        return $this->getRichContentAttributes()[$attribute] ?? null;
    }

    public function hasRichContentAttribute(string $attribute): bool
    {
        return array_key_exists($attribute, $this->getRichContentAttributes());
    }
}
