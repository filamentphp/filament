<?php

namespace Filament\Forms\Components\Concerns;

use Closure;
use Filament\Support\Components\Attributes\ExposedLivewireMethod;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use League\Flysystem\UnableToCheckFileExistence;
use Livewire\Attributes\Renderless;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Throwable;

trait HasFileAttachments
{
    protected string | Closure | null $fileAttachmentsDirectory = null;

    protected string | Closure | null $fileAttachmentsDiskName = null;

    protected ?Closure $getFileAttachmentUrlUsing = null;

    protected ?Closure $saveUploadedFileAttachmentUsing = null;

    protected string | Closure | null $fileAttachmentsVisibility = null;

    protected bool | Closure | null $hasFileAttachments = null;

    /**
     * @var array<string> | Arrayable | Closure | null
     */
    protected array | Arrayable | Closure | null $fileAttachmentsAcceptedFileTypes = ['image/png', 'image/jpeg', 'image/gif', 'image/webp'];

    protected int | Closure | null $fileAttachmentsMaxSize = 12288;

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
    #[Renderless]
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

        if ($attachment instanceof TemporaryUploadedFile) {
            $maxSize = $this->getFileAttachmentsMaxSize();
            $acceptedFileTypes = $this->getFileAttachmentsAcceptedFileTypes();

            try {
                Validator::validate(
                    ['file' => $attachment],
                    rules: [
                        'file' => [
                            'file',
                            ...($maxSize ? ["max:{$maxSize}"] : []),
                            ...($acceptedFileTypes ? ['mimetypes:' . implode(',', $acceptedFileTypes)] : []),
                        ],
                    ],
                );
            } catch (ValidationException $exception) {
                return null;
            }
        }

        return $attachment;
    }

    public function saveUploadedFileAttachment(TemporaryUploadedFile $file): mixed
    {
        if (! $this->hasFileAttachments()) {
            return null;
        }

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
    #[Renderless]
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

    /**
     * @param  array<string> | Arrayable | Closure  $types
     */
    public function fileAttachmentsAcceptedFileTypes(array | Arrayable | Closure $types): static
    {
        $this->fileAttachmentsAcceptedFileTypes = $types;

        return $this;
    }

    /**
     * @return array<string> | null
     */
    public function getFileAttachmentsAcceptedFileTypes(): ?array
    {
        $types = $this->evaluate($this->fileAttachmentsAcceptedFileTypes);

        if ($types instanceof Arrayable) {
            $types = $types->toArray();
        }

        return $types;
    }

    public function fileAttachmentsMaxSize(int | Closure | null $size): static
    {
        $this->fileAttachmentsMaxSize = $size;

        return $this;
    }

    public function getFileAttachmentsMaxSize(): ?int
    {
        return $this->evaluate($this->fileAttachmentsMaxSize);
    }

    public function fileAttachments(bool | Closure | null $condition): static
    {
        $this->hasFileAttachments = $condition;

        return $this;
    }

    public function hasFileAttachments(?bool $default = null): bool
    {
        return $this->evaluate($this->hasFileAttachments) ?? ($default ?? $this->hasFileAttachmentsByDefault());
    }

    public function hasFileAttachmentsByDefault(): bool
    {
        return true;
    }
}
