<?php

namespace Filament\Forms\Components;

use Closure;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use League\Flysystem\AwsS3v3\AwsS3Adapter;
use SplFileInfo;

class BaseFileUpload extends Field
{
    protected array | Arrayable | Closure | null $acceptedFileTypes = null;

    protected string | Closure | null $directory = null;

    protected string | Closure | null $diskName = null;

    protected bool | Closure $isMultiple = false;

    protected int | Closure | null $maxSize = null;

    protected int | Closure | null $minSize = null;

    protected string | Closure $visibility = 'public';

    protected ?Closure $deleteUploadedFileUsing = null;

    protected ?Closure $getUploadedFileUrlUsing = null;

    protected ?Closure $removeUploadedFileUsing = null;

    protected ?Closure $saveUploadedFileUsing = null;

    protected function setUp(): void
    {
        parent::setUp();

        $this->afterStateHydrated(function (BaseFileUpload $component, string | array | null $state): void {
            if (blank($state)) {
                $component->state([]);

                return;
            }

            $files = collect(Arr::wrap($state))
                ->mapWithKeys(fn (string $file): array => [(string) Str::uuid() => $file])
                ->toArray();

            $component->state($files);
        });

        $this->beforeStateDehydrated(function (BaseFileUpload $component): void {
            $component->saveUploadedFiles();
        });

        $this->dehydrateStateUsing(function (BaseFileUpload $component, array $state): string | array | null {
            $files = array_values($state);

            if ($component->isMultiple()) {
                return $files;
            }

            return $files[0] ?? null;
        });
    }

    public function acceptedFileTypes(array | Arrayable | Closure $types): static
    {
        $this->acceptedFileTypes = $types;

        $this->rule(function () {
            $types = implode(',', ($this->getAcceptedFileTypes() ?? []));

            return "mimetypes:{$types}";
        }, function () {
            return $this->hasFileObjectState() && count($this->getAcceptedFileTypes() ?? []);
        });

        return $this;
    }

    public function directory(string | Closure | null $directory): static
    {
        $this->directory = $directory;

        return $this;
    }

    public function disk($name): static
    {
        $this->diskName = $name;

        return $this;
    }

    public function maxSize(int | Closure | null $size): static
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

    public function minSize(int | Closure | null $size): static
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

    public function multiple(bool | Closure $condition = true): static
    {
        $this->isMultiple = $condition;

        return $this;
    }

    public function visibility(string | Closure | null $visibility): static
    {
        $this->visibility = $visibility;

        return $this;
    }

    public function deleteUploadedFileUsing(?Closure $callback): static
    {
        $this->deleteUploadedFileUsing = $callback;

        return $this;
    }

    public function getUploadedFileUrlUsing(?Closure $callback): static
    {
        $this->getUploadedFileUrlUsing = $callback;

        return $this;
    }

    public function removeUploadedFileUsing(?Closure $callback): static
    {
        $this->removeUploadedFileUsing = $callback;

        return $this;
    }

    public function saveUploadedFileUsing(?Closure $callback): static
    {
        $this->saveUploadedFileUsing = $callback;

        return $this;
    }

    public function getAcceptedFileTypes(): ?array
    {
        $types = $this->evaluate($this->acceptedFileTypes);

        if ($types instanceof Arrayable) {
            $types = $types->toArray();
        }

        return $types;
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

    public function deleteUploadedFile(string $fileKey): static
    {
        $file = $this->getState()[$fileKey] ?? null;

        if (! $file) {
            return $this;
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

    public function removeUploadedFile(string $fileKey): static
    {
        $files = $this->getState();
        $file = $files[$fileKey] ?? null;

        if (! $file) {
            return $this;
        }

        unset($files[$fileKey]);

        $this->state($files);

        if ($callback = $this->removeUploadedFileUsing) {
            $this->evaluate($callback, [
                'file' => $file,
            ]);
        } else {
            $this->handleUploadedFileRemoval($file);
        }

        return $this;
    }

    public function getUploadedFileUrl(string $fileKey): ?string
    {
        $files = $this->getState();

        $file = $files[$fileKey] ?? null;

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
        /** @var FilesystemAdapter $storage */
        $storage = $this->getDisk();

        /** @var \League\Flysystem\Filesystem $storageDriver */
        $storageDriver = $storage->getDriver();

        if (
            $storageDriver->getAdapter() instanceof AwsS3Adapter &&
            $storage->getVisibility($file) === 'private'
        ) {
            return $storage->temporaryUrl(
                $file,
                now()->addMinutes(5),
            );
        }

        return $storage->url($file);
    }

    public function saveUploadedFiles(): void
    {
        $state = array_map(function (SplFileInfo | string $file) {
            if (! $file instanceof SplFileInfo) {
                return $file;
            }

            if ($callback = $this->saveUploadedFileUsing) {
                return $this->evaluate($callback, [
                    'file' => $file,
                ]);
            }

            return $this->handleUpload($file);
        }, $this->getState());

        $this->state($state);
    }

    protected function handleUpload($file)
    {
        $storeMethod = $this->getVisibility() === 'public' ? 'storePublicly' : 'store';

        return $file->{$storeMethod}($this->getDirectory(), $this->getDiskName());
    }

    public function isMultiple(): bool
    {
        return $this->evaluate($this->isMultiple);
    }

    protected function handleUploadedFileRemoval($file): void
    {
    }

    protected function handleUploadedFileDeletion($file): void
    {
    }
}
