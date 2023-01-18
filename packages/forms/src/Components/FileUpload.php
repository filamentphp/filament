<?php

namespace Filament\Forms\Components;

use Closure;
use Filament\Support\Concerns\HasExtraAlpineAttributes;

class FileUpload extends BaseFileUpload
{
    use Concerns\HasExtraInputAttributes;
    use Concerns\HasPlaceholder;
    use HasExtraAlpineAttributes;

    protected string $view = 'forms::components.file-upload';

    protected string | Closure | null $imageCropAspectRatio = null;

    protected string | Closure | null $imagePreviewHeight = null;

    protected string | Closure | null $imageResizeTargetHeight = null;

    protected string | Closure | null $imageResizeTargetWidth = null;

    protected string | Closure | null $imageResizeMode = null;

    protected bool | Closure $imageResizeUpscale = true;

    protected bool | Closure $isAvatar = false;

    protected string | Closure $loadingIndicatorPosition = 'right';

    protected string | Closure | null $panelAspectRatio = null;

    protected string | Closure | null $panelLayout = 'compact';

    protected string | Closure $removeUploadedFileButtonPosition = 'left';

    protected bool | Closure $shouldAppendFiles = false;

    protected bool | Closure $shouldOrientImageFromExif = true;

    protected string | Closure $uploadButtonPosition = 'right';

    protected string | Closure $uploadProgressIndicatorPosition = 'right';

    public function appendFiles(bool | Closure $condition = true): static
    {
        $this->shouldAppendFiles = $condition;

        return $this;
    }

    public function avatar(): static
    {
        $this->isAvatar = true;

        $this->image();
        $this->imageCropAspectRatio('1:1');
        $this->imageResizeTargetHeight('500');
        $this->imageResizeTargetWidth('500');
        $this->loadingIndicatorPosition('center bottom');
        $this->panelLayout('compact circle');
        $this->removeUploadedFileButtonPosition('center bottom');
        $this->uploadButtonPosition('center bottom');
        $this->uploadProgressIndicatorPosition('center bottom');

        return $this;
    }

    /**
     * @deprecated Use `placeholder()` instead.
     */
    public function idleLabel(string | Closure | null $label): static
    {
        $this->placeholder($label);

        return $this;
    }

    public function image(): static
    {
        $this->acceptedFileTypes([
            'image/*',
        ]);

        return $this;
    }

    public function imageCropAspectRatio(string | Closure | null $ratio): static
    {
        $this->imageCropAspectRatio = $ratio;

        return $this;
    }

    public function imagePreviewHeight(string | Closure | null $height): static
    {
        $this->imagePreviewHeight = $height;

        return $this;
    }

    public function imageResizeTargetHeight(string | Closure | null $height): static
    {
        $this->imageResizeTargetHeight = $height;

        return $this;
    }

    public function imageResizeTargetWidth(string | Closure | null $width): static
    {
        $this->imageResizeTargetWidth = $width;

        return $this;
    }

    public function imageResizeMode(string | Closure | null $mode): static
    {
        $this->imageResizeMode = $mode;

        return $this;
    }

    public function imageResizeUpscale(bool | Closure $condition = true): static
    {
        $this->imageResizeUpscale = $condition;

        return $this;
    }

    public function loadingIndicatorPosition(string | Closure | null $position): static
    {
        $this->loadingIndicatorPosition = $position;

        return $this;
    }

    public function orientImageFromExif(bool | Closure $condition = true): static
    {
        $this->shouldOrientImageFromExif = $condition;

        return $this;
    }

    public function panelAspectRatio(string | Closure | null $ratio): static
    {
        $this->panelAspectRatio = $ratio;

        return $this;
    }

    public function panelLayout(string | Closure | null $layout): static
    {
        $this->panelLayout = $layout;

        return $this;
    }

    public function removeUploadedFileButtonPosition(string | Closure | null $position): static
    {
        $this->removeUploadedFileButtonPosition = $position;

        return $this;
    }

    public function uploadButtonPosition(string | Closure | null $position): static
    {
        $this->uploadButtonPosition = $position;

        return $this;
    }

    public function uploadProgressIndicatorPosition(string | Closure | null $position): static
    {
        $this->uploadProgressIndicatorPosition = $position;

        return $this;
    }

    public function getImageCropAspectRatio(): ?string
    {
        return $this->evaluate($this->imageCropAspectRatio);
    }

    public function getImagePreviewHeight(): ?string
    {
        return $this->evaluate($this->imagePreviewHeight);
    }

    public function getImageResizeTargetHeight(): ?string
    {
        return $this->evaluate($this->imageResizeTargetHeight);
    }

    public function getImageResizeTargetWidth(): ?string
    {
        return $this->evaluate($this->imageResizeTargetWidth);
    }

    public function getImageResizeMode(): ?string
    {
        return $this->evaluate($this->imageResizeMode);
    }

    public function getImageResizeUpscale(): bool
    {
        return $this->evaluate($this->imageResizeUpscale);
    }

    public function getLoadingIndicatorPosition(): string
    {
        return $this->evaluate($this->loadingIndicatorPosition);
    }

    public function getPanelAspectRatio(): ?string
    {
        return $this->evaluate($this->panelAspectRatio);
    }

    public function getPanelLayout(): ?string
    {
        return $this->evaluate($this->panelLayout);
    }

    public function getRemoveUploadedFileButtonPosition(): string
    {
        return $this->evaluate($this->removeUploadedFileButtonPosition);
    }

    public function getUploadButtonPosition(): string
    {
        return $this->evaluate($this->uploadButtonPosition);
    }

    public function getUploadProgressIndicatorPosition(): string
    {
        return $this->evaluate($this->uploadProgressIndicatorPosition);
    }

    public function isAvatar(): bool
    {
        return (bool) $this->evaluate($this->isAvatar);
    }

    public function shouldAppendFiles(): bool
    {
        return $this->evaluate($this->shouldAppendFiles);
    }

    public function shouldOrientImageFromExif(): bool
    {
        return (bool) $this->evaluate($this->shouldOrientImageFromExif);
    }
}
