<?php

namespace Filament\Forms\Components;

use Closure;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use League\Flysystem\AwsS3v3\AwsS3Adapter;
use Livewire\TemporaryUploadedFile;

class BaseFileUpload extends Field
{
    protected array | Arrayable | Closure | null $acceptedFileTypes = null;

    protected string | Closure | null $directory = null;

    protected string | Closure | null $diskName = null;

    protected bool | Closure $isMultiple = false;

    protected int | Closure | null $maxSize = null;

    protected int | Closure | null $minSize = null;

    protected int | Closure | null $maxFiles = null;

    protected int | Closure | null $minFiles = null;

    protected bool | Closure $shouldPreserveFilenames = false;

    protected string | Closure $visibility = 'public';

    protected ?Closure $deleteUploadedFileUsing = null;

    protected ?Closure $getUploadedFileUrlUsing = null;

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

        $this->afterStateUpdated(function (BaseFileUpload $component, $state) {
            if (blank($state)) {
                return;
            }

            if (is_array($state)) {
                return;
            }

            $component->state([(string) Str::uuid() => $state]);
        });

        $this->beforeStateDehydrated(function (BaseFileUpload $component): void {
            $component->saveUploadedFiles();
        });

        $this->dehydrateStateUsing(function (BaseFileUpload $component, ?array $state): string | array | null {
            $files = array_values($state ?? []);

            if ($component->isMultiple()) {
                return $files;
            }

            return $files[0] ?? null;
        });

        $this->getUploadedFileUrlUsing(function (BaseFileUpload $component, string $file): string {
            /** @var FilesystemAdapter $storage */
            $storage = $component->getDisk();

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
        });

        $this->saveUploadedFileUsing(function (BaseFileUpload $component, TemporaryUploadedFile $file): string {
            $storeMethod = $component->getVisibility() === 'public' ? 'storePubliclyAs' : 'storeAs';

            $filename = $component->shouldPreserveFilenames() ? $file->getClientOriginalName() : $file->getFilename();

            return $file->{$storeMethod}($component->getDirectory(), $filename, $component->getDiskName());
        });
    }

    public function acceptedFileTypes(array | Arrayable | Closure $types): static
    {
        $this->acceptedFileTypes = $types;

        $this->rule(function () {
            $types = implode(',', ($this->getAcceptedFileTypes() ?? []));

            return "mimetypes:{$types}";
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

    public function preserveFilenames(bool | Closure $condition = true): static
    {
        $this->shouldPreserveFilenames = $condition;

        return $this;
    }

    public function maxSize(int | Closure | null $size): static
    {
        $this->maxSize = $size;

        $this->rule(function (): string {
            $size = $this->getMaxSize();

            return "max:{$size}";
        });

        return $this;
    }

    public function minSize(int | Closure | null $size): static
    {
        $this->minSize = $size;

        $this->rule(function (): string {
            $size = $this->getMinSize();

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

    public function shouldPreserveFilenames(): bool
    {
        return $this->evaluate($this->shouldPreserveFilenames);
    }

    public function getValidationRules(): array
    {
        $rules = [
            $this->getRequiredValidationRule(),
            'array',
        ];

        if (filled($count = $this->maxFiles)) {
            $rules[] = "max:{$count}";
        }

        if (filled($count = $this->minFiles)) {
            $rules[] = "min:{$count}";
        }

        $rules[] = function (string $attribute, array $value, Closure $fail): void {
            $files = array_filter($value, fn (TemporaryUploadedFile | string $file): bool => $file instanceof TemporaryUploadedFile);

            $name = $this->getName();

            $validator = Validator::make(
                data: [$name => $files],
                rules: ["{$name}.*" => array_merge(['file'], parent::getValidationRules())],
                customAttributes: ["{$name}.*" => $this->getValidationAttribute()],
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

        if ($file instanceof TemporaryUploadedFile) {
            $file->delete();
        }

        unset($files[$fileKey]);

        $this->state($files);

        return $file;
    }

    public function getUploadedFileUrl(string $fileKey): ?string
    {
        $files = $this->getState();

        $file = $files[$fileKey] ?? null;

        if (! $file) {
            return null;
        }

        $callback = $this->getUploadedFileUrlUsing;

        if (! $callback) {
            return null;
        }

        return $this->evaluate($callback, [
            'file' => $file,
        ]);
    }

    public function saveUploadedFiles(): void
    {
        if (blank($this->getState())) {
            $this->state([]);

            return;
        }

        $state = array_map(function (TemporaryUploadedFile | string $file) {
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

            $file->delete();

            return $storedFile;
        }, $this->getState());

        $this->state($state);
    }

    public function isMultiple(): bool
    {
        return $this->evaluate($this->isMultiple);
    }
}
