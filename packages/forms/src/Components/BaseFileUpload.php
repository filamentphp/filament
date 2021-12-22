<?php

namespace Filament\Forms\Components;

use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Support\Facades\Storage;
use League\Flysystem\AwsS3v3\AwsS3Adapter;
use SplFileInfo;

class BaseFileUpload extends Field
{
    protected $acceptedFileTypes = [];

    protected $directory = null;

    protected $diskName = null;

    protected $maxSize = null;

    protected $minSize = null;

    protected $visibility = 'public';

    protected $deleteUploadedFileUsing = null;

    protected $getUploadedFileUrlUsing = null;

    protected $removeUploadedFileUsing = null;

    protected $saveUploadedFileUsing = null;

    protected function setUp(): void
    {
        parent::setUp();

        $this->beforeStateDehydrated(function (BaseFileUpload $component): void {
            $component->saveUploadedFile();
        });

        $this->afterStateUpdated(function (BaseFileUpload $component, $state): void {
            if (! $component->isMultiple()) {
                return;
            }

            if (blank($state)) {
                return;
            }

            $component->getContainer()->getParentComponent()->appendNewUploadField();
        });

        $this->dehydrated(fn (BaseFileUpload $component): bool => ! $component->isMultiple());
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

    public function visibility(string | callable $visibility): static
    {
        $this->visibility = $visibility;

        return $this;
    }

    public function deleteUploadedFileUsing(callable $callback): static
    {
        $this->deleteUploadedFileUsing = $callback;

        return $this;
    }

    public function getUploadedFileUrlUsing(callable $callback): static
    {
        $this->getUploadedFileUrlUsing = $callback;

        return $this;
    }

    public function removeUploadedFileUsing(callable $callback): static
    {
        $this->removeUploadedFileUsing = $callback;

        return $this;
    }

    public function saveUploadedFileUsing(callable $callback): static
    {
        $this->saveUploadedFileUsing = $callback;

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

    public function getMaxSize(): ?int
    {
        return $this->evaluate($this->maxSize);
    }

    public function getMinSize(): ?int
    {
        return $this->evaluate($this->minSize);
    }

    public function getVisibility(): string
    {
        return $this->evaluate($this->visibility);
    }

    public function hasFileObjectState(): bool
    {
        return $this->getState() instanceof SplFileInfo;
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

    protected function handleUpload($file)
    {
        $storeMethod = $this->getVisibility() === 'public' ? 'storePublicly' : 'store';

        return $file->{$storeMethod}($this->getDirectory(), $this->getDiskName());
    }
}
