<?php

namespace Filament\Forms\Components\Concerns;

use Closure;
use Filament\Support\Components\Attributes\ExposedLivewireMethod;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Support\Facades\Storage;
use League\Flysystem\UnableToCheckFileExistence;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Throwable;

trait HasFileAttachments
{
    protected string | Closure | null $fileAttachmentsDirectory = null;

    protected string | Closure | null $fileAttachmentsDiskName = null;

    protected ?Closure $getFileAttachmentUrlUsing = null;

    protected ?Closure $saveUploadedFileAttachmentUsing = null;

    protected string | Closure | null $fileAttachmentsVisibility = null;

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

    #[ExposedLivewireMethod]
    public function getUploadedFileAttachmentTemporaryUrl(TemporaryUploadedFile | string | null $attachment = null): ?string
    {
        return $this->getUploadedFileAttachment($attachment)?->temporaryUrl();
    }

    public function getUploadedFileAttachment(TemporaryUploadedFile | string | null $attachment = null): ?TemporaryUploadedFile
    {
        if (is_string($attachment)) {
            $attachment = data_get($this->getLivewire(), "componentFileAttachments.{$this->getStatePath()}.{$attachment}");
        } elseif (! $attachment) {
            $attachment = data_get($this->getLivewire(), "componentFileAttachments.{$this->getStatePath()}");
        }

        return $attachment;
    }

    public function saveUploadedFileAttachment(TemporaryUploadedFile $file): mixed
    {
        if ($callback = $this->saveUploadedFileAttachmentUsing) {
            return $this->evaluate($callback, [
                'file' => $file,
            ]);
        }

        if (filled($savedFile = $this->defaultSaveUploadedFileAttachment($file))) {
            return $savedFile;
        }

        $storeMethod = $this->getFileAttachmentsVisibility() === 'public' ? 'storePublicly' : 'store';

        return $file->{$storeMethod}($this->getFileAttachmentsDirectory(), $this->getFileAttachmentsDiskName());
    }

    public function defaultSaveUploadedFileAttachment(TemporaryUploadedFile $file): mixed
    {
        return null;
    }

    #[ExposedLivewireMethod]
    public function saveUploadedFileAttachmentAndGetUrl(): ?string
    {
        $attachment = $this->getUploadedFileAttachment();

        if (! $attachment) {
            return null;
        }

        $file = $this->saveUploadedFileAttachment($attachment);

        return $this->getFileAttachmentUrl($file);
    }

    public function fileAttachmentsVisibility(string | Closure | null $visibility): static
    {
        $this->fileAttachmentsVisibility = $visibility;

        return $this;
    }

    public function getFileAttachmentUrlUsing(?Closure $callback): static
    {
        $this->getFileAttachmentUrlUsing = $callback;

        return $this;
    }

    /**
     * @deprecated Use `getFileAttachmentUrlUsing()` instead.
     */
    public function getUploadedAttachmentUrlUsing(?Closure $callback): static
    {
        $this->getFileAttachmentUrlUsing($callback);

        return $this;
    }

    public function saveUploadedFileAttachmentUsing(?Closure $callback): static
    {
        $this->saveUploadedFileAttachmentUsing = $callback;

        return $this;
    }

    /**
     * @deprecated Use `saveUploadedFileAttachmentUsing()` instead.
     */
    public function saveUploadedFileAttachmentsUsing(?Closure $callback): static
    {
        $this->saveUploadedFileAttachmentUsing($callback);

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
        $name = $this->evaluate($this->fileAttachmentsDiskName) ?? $this->getDefaultFileAttachmentsDiskName();

        if (filled($name)) {
            return $name;
        }

        $name = config('filament.default_filesystem_disk');

        if ($name !== 'local') {
            return $name;
        }

        if ($this->getFileAttachmentsVisibility() !== 'public') {
            return $name;
        }

        return 'public';
    }

    public function getDefaultFileAttachmentsDiskName(): ?string
    {
        return null;
    }

    public function getFileAttachmentsVisibility(): string
    {
        return $this->evaluate($this->fileAttachmentsVisibility) ?? $this->getDefaultFileAttachmentsVisibility() ?? 'public';
    }

    public function getDefaultFileAttachmentsVisibility(): ?string
    {
        return null;
    }

    public function getFileAttachmentUrl(mixed $file): ?string
    {
        if ($this->getFileAttachmentUrlUsing) {
            return $this->evaluate($this->getFileAttachmentUrlUsing, [
                'file' => $file,
            ]);
        }

        if (filled($url = $this->getDefaultFileAttachmentUrl($file))) {
            return $url;
        }

        /** @var FilesystemAdapter $storage */
        $storage = $this->getFileAttachmentsDisk();

        try {
            if (! $storage->exists($file)) {
                return null;
            }
        } catch (UnableToCheckFileExistence $exception) {
            return null;
        }

        if ($this->getFileAttachmentsVisibility() === 'private') {
            try {
                return $storage->temporaryUrl(
                    $file,
                    now()->addMinutes(30)->endOfHour(),
                );
            } catch (Throwable $exception) {
                // This driver does not support creating temporary URLs.
            }
        }

        return $storage->url($file);
    }

    public function getDefaultFileAttachmentUrl(mixed $file): ?string
    {
        return null;
    }
}
