<?php

namespace Filament\Forms\Components;

use Closure;

/**
 * @deprecated Use `\Filament\Forms\Components\FileUpload` instead, with the `multiple()` method.
 * @see FileUpload
 */
class MultipleFileUpload extends Field
{
    protected string $view = 'forms::components.multiple-file-upload';

    protected int | Closure | null $maxFiles = null;

    protected int | Closure | null $minFiles = null;

    protected BaseFileUpload | Closure | null $uploadComponent = null;

    protected function setUp(): void
    {
        parent::setUp();

        $this->dehydrateStateUsing(static function (array $state): array {
            return array_values($state);
        });
    }

    public function maxFiles(int | Closure | null $count): static
    {
        $this->maxFiles = $count;

        return $this;
    }

    public function minFiles(int | Closure | null $count): static
    {
        $this->minFiles = $count;

        return $this;
    }

    public function uploadComponent(Component | Closure | null $component): static
    {
        $this->uploadComponent = $component;

        return $this;
    }

    public function getChildComponents(): array
    {
        return [$this->getUploadComponent()];
    }

    public function getUploadComponent(): Component
    {
        $component = $this->evaluate($this->uploadComponent) ?? $this->getDefaultUploadComponent();

        if (filled($this->maxFiles)) {
            $component->maxFiles($this->maxFiles);
        }

        if (filled($this->minFiles)) {
            $component->minFiles($this->minFiles);
        }

        return $component
            ->label($this->getLabel())
            ->multiple()
            ->statePath(null)
            ->validationAttribute($this->getValidationAttribute());
    }

    protected function getDefaultUploadComponent(): BaseFileUpload
    {
        return FileUpload::make('files');
    }
}
