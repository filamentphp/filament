<?php

namespace Filament\Forms\Components\Concerns;

use Closure;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Support\Facades\Storage;
use Livewire\TemporaryUploadedFile;
use SplFileInfo;
use Throwable;

trait HasFileAttachments
{
    protected string | Closure | null $fileAttachmentsDirectory = null;

    protected string | Closure | null $fileAttachmentsDiskName = null;

    protected ?Closure $getUploadedAttachmentUrlUsing = null;

    protected ?Closure $saveUploadedFileAttachmentsUsing = null;

    protected string | Closure $fileAttachmentsVisibility = 'public';

    public function fileAttachmentsDirectory(string | Closure | null $directory): static
    {
        $this->fileAttachmentsDirectory = $directory;

        return $this;
    }

    public function fileAttachmentsDisk(string | Closure | null $name): static
    {
        $this->fileAttachmentsDiskName = $name;

        return $this;
    }

    public function saveUploadedFileAttachment(TemporaryUploadedFile $attachment): ?string
    {
        if ($callback = $this->saveUploadedFileAttachmentsUsing) {
            $file = $this->evaluate($callback, [
                'file' => $attachment,
            ]);
        } else {
            $file = $this->handleFileAttachmentUpload($attachment);
        }

        if ($callback = $this->getUploadedAttachmentUrlUsing) {
            return $this->evaluate($callback, [
                'file' => $file,
            ]);
        }

        return $this->handleUploadedAttachmentUrlRetrieval($file);
    }

    public function fileAttachmentsVisibility(string | Closure $visibility): static
    {
        $this->fileAttachmentsVisibility = $visibility;

        return $this;
    }

    public function getUploadedAttachmentUrlUsing(Closure | null $callback): static
    {
        $this->getUploadedAttachmentUrlUsing = $callback;

        return $this;
    }

    public function saveUploadedFileAttachmentsUsing(Closure | null $callback): static
    {
        $this->saveUploadedFileAttachmentsUsing = $callback;

        return $this;
    }

    public function getFileAttachmentsDirectory(): ?string
    {
        return $this->evaluate($this->fileAttachmentsDirectory);
    }

    public function getFileAttachmentsDisk(): Filesystem
    {
        return Storage::disk($this->getFileAttachmentsDiskName());
    }

    public function getFileAttachmentsDiskName(): string
    {
        return $this->evaluate($this->fileAttachmentsDiskName) ?? config('filament.default_filesystem_disk');
    }

    public function getFileAttachmentsVisibility(): string
    {
        return $this->evaluate($this->fileAttachmentsVisibility);
    }

    protected function handleFileAttachmentUpload(SplFileInfo $file): mixed
    {
        $storeMethod = $this->getFileAttachmentsVisibility() === 'public' ? 'storePublicly' : 'store';

        return $file->{$storeMethod}($this->getFileAttachmentsDirectory(), $this->getFileAttachmentsDiskName());
    }

    protected function handleUploadedAttachmentUrlRetrieval(mixed $file): ?string
    {
        /** @var FilesystemAdapter $storage */
        $storage = $this->getFileAttachmentsDisk();

        if (! $storage->exists($file)) {
            return null;
        }

        if ($storage->getVisibility($file) === 'private') {
            try {
                return $storage->temporaryUrl(
                    $file,
                    now()->addMinutes(5),
                );
            } catch (Throwable $exception) {
                // This driver does not support creating temporary URLs.
            }
        }

        return $storage->url($file);
    }
}
