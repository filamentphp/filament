<?php

namespace Filament\Forms\Components;

use Closure;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use League\Flysystem\UnableToCheckFileExistence;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Throwable;

class BaseFileUpload extends Field implements Contracts\HasNestedRecursiveValidationRules
{
    use Concerns\HasNestedRecursiveValidationRules;
    use Concerns\HasUploadingMessage;

    /**
     * @var array<string> | Arrayable | Closure | null
     */
    protected array | Arrayable | Closure | null $acceptedFileTypes = null;

    protected bool | Closure $isDeletable = true;

    protected bool | Closure $isDownloadable = false;

    protected bool | Closure $isOpenable = false;

    protected bool | Closure $isPreviewable = true;

    protected bool | Closure $isReorderable = false;

    protected string | Closure | null $directory = null;

    protected string | Closure | null $diskName = null;

    protected bool | Closure $isMultiple = false;

    protected int | Closure | null $maxSize = null;

    protected int | Closure | null $minSize = null;

    protected int | Closure | null $maxFiles = null;

    protected int | Closure | null $minFiles = null;

    protected bool | Closure $shouldPreserveFilenames = false;

    protected bool | Closure $shouldMoveFiles = false;

    protected bool | Closure $shouldStoreFiles = true;

    protected bool | Closure $shouldFetchFileInformation = true;

    protected string | Closure | null $fileNamesStatePath = null;

    protected string | Closure $visibility = 'public';

    protected ?Closure $deleteUploadedFileUsing = null;

    protected ?Closure $getUploadedFileNameForStorageUsing = null;

    protected ?Closure $getUploadedFileUsing = null;

    protected ?Closure $reorderUploadedFilesUsing = null;

    protected ?Closure $saveUploadedFileUsing = null;

    protected function setUp(): void
    {
        parent::setUp();

        $this->afterStateHydrated(static function (BaseFileUpload $component, string | array | null $state): void {
            if (blank($state)) {
                $component->state([]);

                return;
            }

            $shouldFetchFileInformation = $component->shouldFetchFileInformation();

            $files = collect(Arr::wrap($state))
                ->filter(static function (string $file) use ($component, $shouldFetchFileInformation): bool {
                    if (blank($file)) {
                        return false;
                    }

                    if (! $shouldFetchFileInformation) {
                        return true;
                    }

                    try {
                        return $component->getDisk()->exists($file);
                    } catch (UnableToCheckFileExistence $exception) {
                        return false;
                    }
                })
                ->mapWithKeys(static fn (string $file): array => [((string) Str::uuid()) => $file])
                ->all();

            $component->state($files);
        });

        $this->afterStateUpdated(static function (BaseFileUpload $component, $state) {
            if ($state instanceof TemporaryUploadedFile) {
                return;
            }

            if (blank($state)) {
                return;
            }

            if (is_array($state)) {
                return;
            }

            $component->state([(string) Str::uuid() => $state]);
        });

        $this->beforeStateDehydrated(static function (BaseFileUpload $component): void {
            $component->saveUploadedFiles();
        });

        $this->dehydrateStateUsing(static function (BaseFileUpload $component, ?array $state): string | array | null | TemporaryUploadedFile {
            $files = array_values($state ?? []);

            if ($component->isMultiple()) {
                return $files;
            }

            return $files[0] ?? null;
        });

        $this->getUploadedFileUsing(static function (BaseFileUpload $component, string $file, string | array | null $storedFileNames): ?array {
            /** @var FilesystemAdapter $storage */
            $storage = $component->getDisk();

            $shouldFetchFileInformation = $component->shouldFetchFileInformation();

            if ($shouldFetchFileInformation) {
                try {
                    if (! $storage->exists($file)) {
                        return null;
                    }
                } catch (UnableToCheckFileExistence $exception) {
                    return null;
                }
            }

            $url = null;

            if ($component->getVisibility() === 'private') {
                try {
                    $url = $storage->temporaryUrl(
                        $file,
                        now()->addMinutes(5),
                    );
                } catch (Throwable $exception) {
                    // This driver does not support creating temporary URLs.
                }
            }

            $url ??= $storage->url($file);

            return [
                'name' => ($component->isMultiple() ? ($storedFileNames[$file] ?? null) : $storedFileNames) ?? basename($file),
                'size' => $shouldFetchFileInformation ? $storage->size($file) : 0,
                'type' => $shouldFetchFileInformation ? $storage->mimeType($file) : null,
                'url' => $url,
            ];
        });

        $this->getUploadedFileNameForStorageUsing(static function (BaseFileUpload $component, TemporaryUploadedFile $file) {
            return $component->shouldPreserveFilenames() ? $file->getClientOriginalName() : (Str::ulid() . '.' . $file->getClientOriginalExtension());
        });

        $this->saveUploadedFileUsing(static function (BaseFileUpload $component, TemporaryUploadedFile $file): ?string {
            try {
                if (! $file->exists()) {
                    return null;
                }
            } catch (UnableToCheckFileExistence $exception) {
                return null;
            }

            if (
                $component->shouldMoveFiles() &&
                ($component->getDiskName() == (fn (): string => $this->disk)->call($file))
            ) {
                $newPath = trim($component->getDirectory() . '/' . $component->getUploadedFileNameForStorage($file), '/');

                $component->getDisk()->move((fn (): string => $this->path)->call($file), $newPath);

                return $newPath;
            }

            $storeMethod = $component->getVisibility() === 'public' ? 'storePubliclyAs' : 'storeAs';

            return $file->{$storeMethod}(
                $component->getDirectory(),
                $component->getUploadedFileNameForStorage($file),
                $component->getDiskName(),
            );
        });
    }

    protected function callAfterStateUpdatedHook(Closure $hook): void
    {
        /** @var array<string | TemporaryUploadedFile> $state */
        $state = $this->getState() ?? [];

        /** @var array<string | TemporaryUploadedFile> $oldState */
        $oldState = $this->getOldState() ?? [];

        $this->evaluate($hook, [
            'state' => $this->isMultiple() ? $state : Arr::first($state),
            'old' => $this->isMultiple() ? $oldState : Arr::first($oldState),
        ]);
    }

    /**
     * @param  array<string> | Arrayable | Closure  $types
     */
    public function acceptedFileTypes(array | Arrayable | Closure $types): static
    {
        $this->acceptedFileTypes = $types;

        $this->rule(static function (BaseFileUpload $component) {
            $types = implode(',', ($component->getAcceptedFileTypes() ?? []));

            return "mimetypes:{$types}";
        });

        return $this;
    }

    public function deletable(bool | Closure $condition = true): static
    {
        $this->isDeletable = $condition;

        return $this;
    }

    public function directory(string | Closure | null $directory): static
    {
        $this->directory = $directory;

        return $this;
    }

    public function disk(string | Closure | null $name): static
    {
        $this->diskName = $name;

        return $this;
    }

    public function downloadable(bool | Closure $condition = true): static
    {
        $this->isDownloadable = $condition;

        return $this;
    }

    public function openable(bool | Closure $condition = true): static
    {
        $this->isOpenable = $condition;

        return $this;
    }

    public function reorderable(bool | Closure $condition = true): static
    {
        $this->isReorderable = $condition;

        return $this;
    }

    public function previewable(bool | Closure $condition = true): static
    {
        $this->isPreviewable = $condition;

        return $this;
    }

    /**
     * @deprecated Use `downloadable()` instead.
     */
    public function enableDownload(bool | Closure $condition = true): static
    {
        $this->downloadable($condition);

        return $this;
    }

    /**
     * @deprecated Use `openable()` instead.
     */
    public function enableOpen(bool | Closure $condition = true): static
    {
        $this->openable($condition);

        return $this;
    }

    /**
     * @deprecated Use `reorderable()` instead.
     */
    public function enableReordering(bool | Closure $condition = true): static
    {
        $this->reorderable($condition);

        return $this;
    }

    /**
     * @deprecated Use `previewable()` instead.
     */
    public function disablePreview(bool | Closure $condition = true): static
    {
        $this->previewable(fn (BaseFileUpload $component): bool => ! $component->evaluate($condition));

        return $this;
    }

    public function storeFileNamesIn(string | Closure | null $statePath): static
    {
        $this->fileNamesStatePath = $statePath;

        return $this;
    }

    public function preserveFilenames(bool | Closure $condition = true): static
    {
        $this->shouldPreserveFilenames = $condition;

        return $this;
    }

    public function moveFiles(bool | Closure $condition = true): static
    {
        $this->shouldMoveFiles = $condition;

        return $this;
    }

    /**
     * @deprecated Use `moveFiles()` instead.
     */
    public function moveFile(bool | Closure $condition = true): static
    {
        $this->moveFiles($condition);

        return $this;
    }

    public function fetchFileInformation(bool | Closure $condition = true): static
    {
        $this->shouldFetchFileInformation = $condition;

        return $this;
    }

    public function maxSize(int | Closure | null $size): static
    {
        $this->maxSize = $size;

        $this->rule(static function (BaseFileUpload $component): string {
            $size = $component->getMaxSize();

            return "max:{$size}";
        });

        return $this;
    }

    public function minSize(int | Closure | null $size): static
    {
        $this->minSize = $size;

        $this->rule(static function (BaseFileUpload $component): string {
            $size = $component->getMinSize();

            return "min:{$size}";
        });

        return $this;
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

    public function multiple(bool | Closure $condition = true): static
    {
        $this->isMultiple = $condition;

        return $this;
    }

    public function storeFiles(bool | Closure $condition = true): static
    {
        $this->shouldStoreFiles = $condition;

        return $this;
    }

    /**
     * @deprecated Use `storeFiles()` instead.
     */
    public function storeFile(bool | Closure $condition = true): static
    {
        $this->storeFiles($condition);

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

    public function getUploadedFileUsing(?Closure $callback): static
    {
        $this->getUploadedFileUsing = $callback;

        return $this;
    }

    public function reorderUploadedFilesUsing(?Closure $callback): static
    {
        $this->reorderUploadedFilesUsing = $callback;

        return $this;
    }

    public function saveUploadedFileUsing(?Closure $callback): static
    {
        $this->saveUploadedFileUsing = $callback;

        return $this;
    }

    public function isDeletable(): bool
    {
        return (bool) $this->evaluate($this->isDeletable);
    }

    public function isDownloadable(): bool
    {
        return (bool) $this->evaluate($this->isDownloadable);
    }

    public function isOpenable(): bool
    {
        return (bool) $this->evaluate($this->isOpenable);
    }

    public function isPreviewable(): bool
    {
        return (bool) $this->evaluate($this->isPreviewable);
    }

    public function isReorderable(): bool
    {
        return (bool) $this->evaluate($this->isReorderable);
    }

    /**
     * @return array<string> | null
     */
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
        return $this->evaluate($this->diskName) ?? config('filament.default_filesystem_disk');
    }

    public function getMaxFiles(): ?int
    {
        return $this->evaluate($this->maxFiles);
    }

    public function getMinFiles(): ?int
    {
        return $this->evaluate($this->minFiles);
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

    public function shouldPreserveFilenames(): bool
    {
        return (bool) $this->evaluate($this->shouldPreserveFilenames);
    }

    public function shouldMoveFiles(): bool
    {
        return (bool) $this->evaluate($this->shouldMoveFiles);
    }

    public function shouldFetchFileInformation(): bool
    {
        return (bool) $this->evaluate($this->shouldFetchFileInformation);
    }

    public function shouldStoreFiles(): bool
    {
        return (bool) $this->evaluate($this->shouldStoreFiles);
    }

    public function getFileNamesStatePath(): ?string
    {
        if (! $this->fileNamesStatePath) {
            return null;
        }

        return $this->generateRelativeStatePath($this->fileNamesStatePath);
    }

    /**
     * @return array<mixed>
     */
    public function getValidationRules(): array
    {
        $rules = [
            $this->getRequiredValidationRule(),
            'array',
        ];

        if (filled($count = $this->getMaxFiles())) {
            $rules[] = "max:{$count}";
        }

        if (filled($count = $this->getMinFiles())) {
            $rules[] = "min:{$count}";
        }

        $rules[] = function (string $attribute, array $value, Closure $fail): void {
            $files = array_filter($value, fn (TemporaryUploadedFile | string $file): bool => $file instanceof TemporaryUploadedFile);

            $name = $this->getName();

            $validationMessages = $this->getValidationMessages();

            $validator = Validator::make(
                [$name => $files],
                ["{$name}.*" => ['file', ...parent::getValidationRules()]],
                $validationMessages ? ["{$name}.*" => $validationMessages] : [],
                ["{$name}.*" => $this->getValidationAttribute()],
            );

            if (! $validator->fails()) {
                return;
            }

            $fail($validator->errors()->first());
        };

        return $rules;
    }

    public function deleteUploadedFile(string $fileKey): static
    {
        $file = $this->removeUploadedFile($fileKey);

        if (blank($file)) {
            return $this;
        }

        $callback = $this->deleteUploadedFileUsing;

        if (! $callback) {
            return $this;
        }

        $this->evaluate($callback, [
            'file' => $file,
        ]);

        return $this;
    }

    public function removeUploadedFile(string $fileKey): string | TemporaryUploadedFile | null
    {
        $files = $this->getState();
        $file = $files[$fileKey] ?? null;

        if (! $file) {
            return null;
        }

        if (is_string($file)) {
            $this->removeStoredFileName($file);
        } elseif ($file instanceof TemporaryUploadedFile) {
            $file->delete();
        }

        unset($files[$fileKey]);

        $this->state($files);

        return $file;
    }

    public function removeStoredFileName(string $file): void
    {
        $statePath = $this->fileNamesStatePath;

        if (blank($statePath)) {
            return;
        }

        $this->evaluate(function (BaseFileUpload $component, Get $get, Set $set) use ($file, $statePath) {
            if (! $component->isMultiple()) {
                $set($statePath, null);

                return;
            }

            $fileNames = $get($statePath) ?? [];

            if (array_key_exists($file, $fileNames)) {
                unset($fileNames[$file]);
            }

            $set($statePath, $fileNames);
        });
    }

    /**
     * @param  array<array-key>  $fileKeys
     */
    public function reorderUploadedFiles(array $fileKeys): void
    {
        if (! $this->isReorderable) {
            return;
        }

        $fileKeys = array_flip($fileKeys);

        $state = collect($this->getState())
            ->sortBy(static fn ($file, $fileKey) => $fileKeys[$fileKey] ?? null) // $fileKey may not be present in $fileKeys if it was added to the state during the reorder call
            ->all();

        $this->state($state);
    }

    /**
     * @return array<array{name: string, size: int, type: string, url: string} | null> | null
     */
    public function getUploadedFiles(): ?array
    {
        $urls = [];

        foreach ($this->getState() ?? [] as $fileKey => $file) {
            if ($file instanceof TemporaryUploadedFile) {
                $urls[$fileKey] = null;

                continue;
            }

            $callback = $this->getUploadedFileUsing;

            if (! $callback) {
                return [$fileKey => null];
            }

            $urls[$fileKey] = $this->evaluate($callback, [
                'file' => $file,
                'storedFileNames' => $this->getStoredFileNames(),
            ]) ?: null;
        }

        return $urls;
    }

    public function saveUploadedFiles(): void
    {
        if (blank($this->getState())) {
            $this->state([]);

            return;
        }

        if (! $this->shouldStoreFiles()) {
            return;
        }

        $state = array_filter(array_map(function (TemporaryUploadedFile | string $file) {
            if (! $file instanceof TemporaryUploadedFile) {
                return $file;
            }

            $callback = $this->saveUploadedFileUsing;

            if (! $callback) {
                $file->delete();

                return $file;
            }

            $storedFile = $this->evaluate($callback, [
                'file' => $file,
            ]);

            if ($storedFile === null) {
                return null;
            }

            $this->storeFileName($storedFile, $file->getClientOriginalName());

            $file->delete();

            return $storedFile;
        }, Arr::wrap($this->getState())));

        if ($this->isReorderable && ($callback = $this->reorderUploadedFilesUsing)) {
            $state = $this->evaluate($callback, [
                'state' => $state,
            ]);
        }

        $this->state($state);
    }

    public function storeFileName(string $file, string $fileName): void
    {
        $statePath = $this->fileNamesStatePath;

        if (blank($statePath)) {
            return;
        }

        $this->evaluate(function (BaseFileUpload $component, Get $get, Set $set) use ($file, $fileName, $statePath) {
            if (! $component->isMultiple()) {
                $set($statePath, $fileName);

                return;
            }

            $fileNames = $get($statePath) ?? [];
            $fileNames[$file] = $fileName;

            $set($statePath, $fileNames);
        });
    }

    /**
     * @return string | array<string, string> | null
     */
    public function getStoredFileNames(): string | array | null
    {
        $state = null;
        $statePath = $this->fileNamesStatePath;

        if (filled($statePath)) {
            $state = $this->evaluate(fn (Get $get) => $get($statePath));
        }

        if (blank($state) && $this->isMultiple()) {
            return [];
        }

        return $state;
    }

    public function isMultiple(): bool
    {
        return (bool) $this->evaluate($this->isMultiple);
    }

    public function getUploadedFileNameForStorageUsing(?Closure $callback): static
    {
        $this->getUploadedFileNameForStorageUsing = $callback;

        return $this;
    }

    public function getUploadedFileNameForStorage(TemporaryUploadedFile $file): string
    {
        return $this->evaluate($this->getUploadedFileNameForStorageUsing, [
            'file' => $file,
        ]);
    }

    /**
     * @return array<string, string>
     */
    public function getStateToDehydrate(): array
    {
        $state = parent::getStateToDehydrate();

        if ($fileNamesStatePath = $this->getFileNamesStatePath()) {
            $state = [
                ...$state,
                $fileNamesStatePath => $this->getStoredFileNames(),
            ];
        }

        return $state;
    }

    /**
     * @param  array<string, array<mixed>>  $rules
     */
    public function dehydrateValidationRules(array &$rules): void
    {
        parent::dehydrateValidationRules($rules);

        if ($fileNamesStatePath = $this->getFileNamesStatePath()) {
            $rules[$fileNamesStatePath] = ['nullable'];
        }
    }
}
