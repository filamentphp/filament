<?php

namespace Filament\Forms\Components;

use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Support\Facades\Storage;

class FileUpload extends BaseFileUpload
{
    use Concerns\HasExtraAlpineAttributes;
    use Concerns\HasPlaceholder;

    protected string $view = 'forms::components.file-upload';

    protected $acceptedFileTypes = [];

    protected $imageCropAspectRatio = null;

    protected $imagePreviewHeight = null;

    protected $imageResizeTargetHeight = null;

    protected $imageResizeTargetWidth = null;

    protected $isAvatar = false;

    protected $loadingIndicatorPosition = 'right';

    protected $panelAspectRatio = null;

    protected $panelLayout = null;

    protected $removeUploadedFileButtonPosition = 'left';

    protected $uploadButtonPosition = 'right';

    protected $uploadProgressIndicatorPosition = 'right';

    protected function setUp(): void
    {
        parent::setUp();

        $this->beforeStateDehydrated(function (FileUpload $component): void {
            $component->saveUploadedFile();
        });

        $this->afterStateUpdated(function (FileUpload $component, $state): void {
            if (! $component->isMultiple()) {
                return;
            }

            if (blank($state)) {
                return;
            }

            $component->getContainer()->getParentComponent()->appendNewUploadField();
        });

        $this->dehydrated(fn (FileUpload $component): bool => ! $component->isMultiple());
    }

    public function acceptedFileTypes(array | callable $types): static
    {
        $this->acceptedFileTypes = $types;

        $this->rule(function () {
            $types = implode(',', $this->getAcceptedFileTypes());

            return "mimetypes:{$types}";
        }, function () {
            return $this->hasFileObjectState() && count($this->getAcceptedFileTypes());
        });

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

    public function deleteUploadedFile($file = null): static
    {
        if (! $file) {
            $file = $this->getState();
        }

        if ($callback = $this->deleteUploadedFileUsing) {
            $this->evaluate($callback, [
                'file' => $file,
            ]);
        } else {
            $this->handleUploadedFileDeletion($file);
        }

        return $this;
    }

    public function idleLabel(string | callable $label): static
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

    public function imageCropAspectRatio(string | callable $ratio): static
    {
        $this->imageCropAspectRatio = $ratio;

        return $this;
    }

    public function imagePreviewHeight(string | callable $height): static
    {
        $this->imagePreviewHeight = $height;

        return $this;
    }

    public function imageResizeTargetHeight(string | callable $height): static
    {
        $this->imageResizeTargetHeight = $height;

        return $this;
    }

    public function imageResizeTargetWidth(string | callable $width): static
    {
        $this->imageResizeTargetWidth = $width;

        return $this;
    }

    public function loadingIndicatorPosition(string | callable $position): static
    {
        $this->loadingIndicatorPosition = $position;

        return $this;
    }

    public function panelAspectRatio(string | callable $ratio): static
    {
        $this->panelAspectRatio = $ratio;

        return $this;
    }

    public function panelLayout(string | callable $layout): static
    {
        $this->panelLayout = $layout;

        return $this;
    }

    public function removeUploadedFileButtonPosition(string | callable $position): static
    {
        $this->removeUploadedFileButtonPosition = $position;

        return $this;
    }

    public function uploadButtonPosition(string | callable $position): static
    {
        $this->uploadButtonPosition = $position;

        return $this;
    }

    public function uploadProgressIndicatorPosition(string | callable $position): static
    {
        $this->uploadProgressIndicatorPosition = $position;

        return $this;
    }

    public function getAcceptedFileTypes(): array
    {
        return $this->evaluate($this->acceptedFileTypes);
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

    public function isMultiple(): bool
    {
        return $this->getContainer()->getParentComponent() instanceof MultipleFileUpload;
    }
    
    protected function handleUploadedFileRemoval($file): void
    {
        $this->state(null);
    }

    protected function handleUploadedFileDeletion($file): void
    {
    }

    public function removeUploadedFile(): static
    {
        $file = $this->getState();

        if ($callback = $this->removeUploadedFileUsing) {
            $this->evaluate($callback, [
                'file' => $file,
            ]);
        } else {
            $this->handleUploadedFileRemoval($file);
        }

        if ($this->isMultiple()) {
            $container = $this->getContainer();
            $container->getParentComponent()->removeUploadedFile(
                $container->getStatePath(isAbsolute: false),
            );
        }

        return $this;
    }
}
