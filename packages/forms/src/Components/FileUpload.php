<?php

namespace Filament\Forms\Components;

use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Support\Facades\Storage;
use League\Flysystem\AwsS3v3\AwsS3Adapter;
use SplFileInfo;

class FileUpload extends Field
{
    use Concerns\HasPlaceholder;

    protected string $view = 'forms::components.file-upload';

    protected $acceptedFileTypes = [];

    protected $deleteUploadedFileUsing = null;

    protected $directory = null;

    protected $diskName = null;

    protected $getUploadedFileUrlUsing = null;

    protected $imageCropAspectRatio = null;

    protected $imagePreviewHeight = null;

    protected $imageResizeTargetHeight = null;

    protected $imageResizeTargetWidth = null;

    protected $isAvatar = false;

    protected $loadingIndicatorPosition = 'right';

    protected $maxSize = null;

    protected $minSize = null;

    protected $panelAspectRatio = null;

    protected $panelLayout = null;

    protected $removeUploadedFileButtonPosition = 'left';

    protected $removeUploadedFileUsing = null;

    protected $saveUploadedFileUsing = null;

    protected $uploadButtonPosition = 'right';

    protected $uploadProgressIndicatorPosition = 'right';

    protected $visibility = 'public';

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

            if (! $state) {
                return;
            }

            $component->getContainer()->getParentComponent()->appendNewUploadField();
        });
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

    public function deleteUploadedFileUsing(callable $callback): static
    {
        $this->deleteUploadedFileUsing = $callback;

        return $this;
    }

    public function directory(string | callable $directory): static
    {
        $this->directory = $directory;

        return $this;
    }

    public function disk($name): static
    {
        $this->diskName = $name;

        return $this;
    }

    public function getUploadedFileUrl(): ?string
    {
        $file = $this->getState();

        if (! $file) {
            return null;
        }

        if ($callback = $this->getUploadedFileUrlUsing) {
            return $this->evaluate($callback, [
                'file' => $file,
            ]);
        }

        return $this->handleUploadedFileUrlRetrieval($file);
    }

    public function getUploadedFileUrlUsing(callable $callback): static
    {
        $this->getUploadedFileUrlUsing = $callback;

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

    public function maxSize(int | callable $size): static
    {
        $this->maxSize = $size;

        $this->rule(function (): string {
            $size = $this->getMaxSize();

            return "max:{$size}";
        }, function () {
            return $this->hasFileObjectState();
        });

        return $this;
    }

    public function minSize(int | callable $size): static
    {
        $this->minSize = $size;

        $this->rule(function (): string {
            $size = $this->getMaxSize();

            return "min:{$size}";
        }, function () {
            return $this->hasFileObjectState();
        });

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

    public function removeUploadedFileUsing(callable $callback): static
    {
        $this->removeUploadedFileUsing = $callback;

        return $this;
    }

    public function saveUploadedFile(): void
    {
        if (! $this->hasFileObjectState()) {
            return;
        }

        $file = $this->getState();

        if (! $file) {
            return;
        }

        if ($callback = $this->saveUploadedFileUsing) {
            $file = $this->evaluate($callback, [
                'file' => $file,
            ]);
        } else {
            $file = $this->handleUpload($file);
        }

        $this->state($file);
    }

    public function saveUploadedFileUsing(callable $callback): static
    {
        $this->saveUploadedFileUsing = $callback;

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

    public function visibility(string | callable $visibility): static
    {
        $this->visibility = $visibility;

        return $this;
    }

    public function getAcceptedFileTypes(): array
    {
        return $this->evaluate($this->acceptedFileTypes);
    }

    public function getDirectory(): ?string
    {
        return $this->evaluate($this->directory);
    }

    public function getDisk(): Filesystem
    {
        return Storage::disk($this->getDiskName());
    }

    public function getDiskName(): string
    {
        return $this->evaluate($this->diskName) ?? config('forms.default_filesystem_disk');
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

    public function getMaxSize(): ?int
    {
        return $this->evaluate($this->maxSize);
    }

    public function getMinSize(): ?int
    {
        return $this->evaluate($this->minSize);
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

    public function getVisibility(): string
    {
        return $this->evaluate($this->visibility);
    }

    public function hasFileObjectState(): bool
    {
        return $this->getState() instanceof SplFileInfo;
    }

    public function isAvatar(): bool
    {
        return (bool) $this->evaluate($this->isAvatar);
    }

    public function isMultiple(): bool
    {
        return $this->getContainer()->getParentComponent() instanceof MultipleFileUpload;
    }

    protected function handleUpload($file)
    {
        $storeMethod = $this->getVisibility() === 'public' ? 'storePublicly' : 'store';

        return $file->{$storeMethod}($this->getDirectory(), $this->getDiskName());
    }

    protected function handleUploadedFileDeletion($file): void
    {
    }

    protected function handleUploadedFileRemoval($file): void
    {
        $this->state(null);
    }

    protected function handleUploadedFileUrlRetrieval($file): ?string
    {
        $storage = $this->getDisk();

        if (
            $storage->getDriver()->getAdapter() instanceof AwsS3Adapter &&
            $storage->getVisibility($file) === 'private'
        ) {
            return $storage->temporaryUrl(
                $file,
                now()->addMinutes(5),
            );
        }

        return $storage->url($file);
    }
}
