<?php

namespace Filament\Forms\Components\RichEditor;

use Closure;
use Filament\Forms\Components\RichEditor\FileAttachmentProviders\Contracts\FileAttachmentProvider;
use Filament\Forms\Components\RichEditor\Plugins\Contracts\RichContentPlugin;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Model;

class RichContentAttribute implements Htmlable
{
    protected ?string $fileAttachmentsDiskName = null;

    protected ?string $fileAttachmentsVisibility = null;

    /**
     * @var array<RichContentPlugin>
     */
    protected array $plugins = [];

    protected ?FileAttachmentProvider $fileAttachmentProvider = null;

    /**
     * @var ?array<string, mixed>
     */
    protected ?array $mergeTags = null;

    /**
     * @var ?array<class-string<RichContentCustomBlock> | array<string, mixed> | Closure>
     */
    protected ?array $customBlocks = null;

    protected bool $isJson = false;

    public function __construct(protected Model $model, protected string $name) {}

    public static function make(Model $model, string $name): static
    {
        return app(static::class, ['model' => $model, 'name' => $name]);
    }

    public function fileAttachmentsDisk(?string $name): static
    {
        $this->fileAttachmentsDiskName = $name;

        return $this;
    }

    public function fileAttachmentsVisibility(?string $visibility): static
    {
        $this->fileAttachmentsVisibility = $visibility;

        return $this;
    }

    public function getFileAttachmentsDiskName(): ?string
    {
        return $this->fileAttachmentsDiskName;
    }

    public function getFileAttachmentsVisibility(): ?string
    {
        return $this->fileAttachmentsVisibility ?? $this->getFileAttachmentProvider()?->getDefaultFileAttachmentVisibility();
    }

    /**
     * @param  array<RichContentPlugin>  $plugins
     */
    public function plugins(array $plugins): static
    {
        $this->plugins = [
            ...$this->plugins,
            ...$plugins,
        ];

        return $this;
    }

    /**
     * @return array<RichContentPlugin>
     */
    public function getPlugins(): array
    {
        return $this->plugins;
    }

    public function fileAttachmentProvider(?FileAttachmentProvider $provider): static
    {
        $this->fileAttachmentProvider = $provider?->attribute($this);

        return $this;
    }

    public function getFileAttachmentProvider(): ?FileAttachmentProvider
    {
        return $this->fileAttachmentProvider;
    }

    public function getModel(): Model
    {
        return $this->model;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function toHtml(): string
    {
        $content = $this->model->getAttribute($this->name);

        if (blank($content)) {
            return '';
        }

        return RichContentRenderer::make($content)
            ->plugins($this->getPlugins())
            ->customBlocks($this->customBlocks)
            ->mergeTags($this->mergeTags)
            ->fileAttachmentsDisk($this->getFileAttachmentsDiskName())
            ->fileAttachmentsVisibility($this->getFileAttachmentsVisibility())
            ->fileAttachmentProvider($this->getFileAttachmentProvider())
            ->toHtml();
    }

    /**
     * @param  ?array<string, mixed>  $tags
     */
    public function mergeTags(?array $tags): static
    {
        $this->mergeTags = $tags;

        return $this;
    }

    /**
     * @return ?array<string>
     */
    public function getMergeTags(): ?array
    {
        if (blank($this->mergeTags)) {
            return null;
        }

        return array_keys($this->mergeTags);
    }

    /**
     * @param  ?array<class-string<RichContentCustomBlock> | array<string, mixed> | Closure>  $blocks
     */
    public function customBlocks(?array $blocks): static
    {
        $this->customBlocks = $blocks;

        return $this;
    }

    /**
     * @return ?array<class-string<RichContentCustomBlock>>
     */
    public function getCustomBlocks(): ?array
    {
        if (blank($this->customBlocks)) {
            return null;
        }

        $blocks = [];

        foreach ($this->customBlocks as $key => $block) {
            $blocks[] = is_string($key) ? $key : $block;
        }

        return $blocks;
    }

    public function json(bool $condition = true): static
    {
        $this->isJson = $condition;

        return $this;
    }

    public function isJson(): bool
    {
        return $this->isJson;
    }
}
