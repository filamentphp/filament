<?php

namespace Filament\Forms\Components;

use Closure;
use Filament\Support\Concerns\HasMediaFilter;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use League\Flysystem\UnableToCheckFileExistence;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\MediaCollections\FileAdder;
use Spatie\MediaLibrary\MediaCollections\MediaCollection;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Throwable;

class SpatieMediaLibraryFileUpload extends FileUpload
{
    use HasMediaFilter;

    protected string|Closure|null $collection = null;

    protected string|Closure|null $conversion = null;

    protected string|Closure|null $conversionsDisk = null;

    protected bool|Closure $hasResponsiveImages = false;

    protected bool $originalStateLoaded = false;

    /**
     * @var array<string, string> The UUIDs of media items initially loaded
     */
    protected array $originalState = [];

    protected string|Closure|null $mediaName = null;

    /**
     * @var array<string, mixed>|Closure|null
     */
    protected array|Closure|null $customHeaders = null;

    /**
     * @var array<string, mixed>|Closure|null
     */
    protected array|Closure|null $customProperties = null;

    /**
     * @var array<string, array<string, string>>|Closure|null
     */
    protected array|Closure|null $manipulations = null;

    /**
     * @var array<string, mixed>|Closure|null
     */
    protected array|Closure|null $properties = null;

    protected function setUp(): void
    {
        parent::setUp();

        $this->loadStateFromRelationshipsUsing(static function (SpatieMediaLibraryFileUpload $component, HasMedia $record): void {
            /** @var Model&HasMedia $record */
            $mediaItems = $record->load('media')->getMedia($component->getCollection() ?? 'default')
                ->when(
                    $component->hasMediaFilter(),
                    fn (Collection $media) => $component->filterMedia($media)
                );

            if (! $component->isMultiple()) {
                $mediaItems = $mediaItems->take(1);
            }

            $uuids = $mediaItems
                ->mapWithKeys(fn (Media $media) => [$media->uuid => $media->uuid])
                ->toArray();

            $component->state($uuids);
            $component->originalState = $uuids;
            $component->originalStateLoaded = true;
        });

        $this->afterStateHydrated(static function (SpatieMediaLibraryFileUpload $component, string|array|null $state): void {
            if (is_array($state) && ! empty($state)) {
                return;
            }

            if ($record = $component->getRecord()) {
                $mediaItems = $record->getMedia($component->getCollection() ?? 'default')
                    ->when(
                        $component->hasMediaFilter(),
                        fn (Collection $media) => $component->filterMedia($media)
                    );

                if (! $component->isMultiple()) {
                    $mediaItems = $mediaItems->take(1);
                }

                $uuids = $mediaItems
                    ->mapWithKeys(fn (Media $media) => [$media->uuid => $media->uuid])
                    ->toArray();

                $component->state($uuids);
            } elseif (is_string($state)) {
                $component->state([$state => $state]);
            } else {
                $component->state([]);
            }
        });

        $this->beforeStateDehydrated(null);

        $this->dehydrated(false);

        $this->getUploadedFileUsing(static function (SpatieMediaLibraryFileUpload $component, string $file): ?array {
            if (! $component->getRecord()) {
                return null;
            }

            /** @var ?Media $media */
            $media = $component->getRecord()->getRelationValue('media')->firstWhere('uuid', $file);
            if (! $media) {
                return null;
            }

            $url = null;

            if ($component->getVisibility() === 'private') {
                try {
                    $url = $media->getTemporaryUrl(
                        now()->addMinutes(30)->endOfHour(),
                        $component->getConversion() && $media->hasGeneratedConversion($component->getConversion())
                            ? $component->getConversion()
                            : ''
                    );
                } catch (Throwable) {
                    // This driver does not support creating temporary URLs.
                }
            }

            if (! $url && $component->getConversion() && $media->hasGeneratedConversion($component->getConversion())) {
                $url = $media->getUrl($component->getConversion());
            }

            $url ??= $media->getUrl();

            return [
                'name' => $media->getAttributeValue('name') ?? $media->getAttributeValue('file_name'),
                'size' => $media->getAttributeValue('size'),
                'type' => $media->getAttributeValue('mime_type'),
                'url' => $url,
            ];
        });

        $this->saveRelationshipsUsing(static function (SpatieMediaLibraryFileUpload $component): void {
            /** @var Model&HasMedia $record */
            $record = $component->getRecord();
            $state = $component->getState();
            $stateUuids = is_array($state) ? array_keys($state) : [];

            $existingMediaUuids = $record
                ->getMedia($component->getCollection() ?? 'default')
                ->when($component->hasMediaFilter(), fn (Collection $media) => $component->filterMedia($media))
                ->pluck('uuid')
                ->toArray();

            $hasChanges = array_diff($existingMediaUuids, $stateUuids) || array_diff($stateUuids, $existingMediaUuids);

            if ($hasChanges) {
                $component->deleteAbandonedFiles();
            }

            $component->saveUploadedFiles();
        });

        $this->saveUploadedFileUsing(static function (SpatieMediaLibraryFileUpload $component, TemporaryUploadedFile $file, ?Model $record): ?string {
            if (! method_exists($record, 'addMediaFromString')) {
                return null;
            }

            try {
                if (! $file->exists()) {
                    return null;
                }
            } catch (UnableToCheckFileExistence) {
                return null;
            }

            /** @var FileAdder $mediaAdder */
            $mediaAdder = $record->addMediaFromString($file->get());

            $filename = $component->getUploadedFileNameForStorage($file);

            $media = $mediaAdder
                ->addCustomHeaders($component->getCustomHeaders())
                ->usingFileName($filename)
                ->usingName($component->getMediaName($file) ?? pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME))
                ->storingConversionsOnDisk($component->getConversionsDisk() ?? '')
                ->withCustomProperties($component->getCustomProperties())
                ->withManipulations($component->getManipulations())
                ->withResponsiveImagesIf($component->hasResponsiveImages())
                ->withProperties($component->getProperties())
                ->toMediaCollection($component->getCollection() ?? 'default', $component->getDiskName());

            $uuid = $media->uuid;

            // Explicitly patch state to ensure it's updated properly
            if ($component->isMultiple()) {
                $current = $component->getState() ?? [];
                $component->state([...$current, $uuid => $uuid]);
            } else {
                $component->state([$uuid => $uuid]);
            }

            return $uuid;
        });

        $this->reorderUploadedFilesUsing(static function (SpatieMediaLibraryFileUpload $component, ?Model $record, array $state): array {
            $uuids = array_filter(array_values($state));

            $mediaClass = ($record && method_exists($record, 'getMediaModel')) ? $record->getMediaModel() : null;
            $mediaClass ??= config('media-library.media_model', Media::class);

            $mappedIds = $mediaClass::query()->whereIn('uuid', $uuids)->pluck(app($mediaClass)->getKeyName(), 'uuid')->toArray();

            $mediaClass::setNewOrder([
                ...array_flip($uuids),
                ...$mappedIds,
            ]);

            return $state;
        });
    }

    /**
     * Determine if the component state has changes compared to the originally loaded state.
     */
    public function hasStateChanges(): bool
    {
        if (! $this->originalStateLoaded) {
            return false;
        }

        $currentState = $this->getState();

        return $this->originalState !== ($currentState ?? []);
    }

    /**
     * Delete files that are no longer present in the current state.
     */
    public function deleteAbandonedFiles(): void
    {
        /** @var Model&HasMedia $record */
        $record = $this->getRecord();
        $state = $this->getState();

        $existingMedia = $record->getMedia($this->getCollection() ?? 'default');

        if ($this->hasMediaFilter()) {
            $existingMedia = $this->filterMedia($existingMedia);
        }

        if (is_string($state)) {
            $uuids = [$state];
        } elseif (is_array($state)) {
            $uuids = array_values($state);
        } else {
            $uuids = [];
        }

        $mediaToDelete = $existingMedia->whereNotIn('uuid', $uuids);
        $mediaToDelete->each(fn (Media $media) => $media->delete());
    }

    public function collection(string|Closure|null $collection): static
    {
        $this->collection = $collection;

        return $this;
    }

    public function conversion(string|Closure|null $conversion): static
    {
        $this->conversion = $conversion;

        return $this;
    }

    public function conversionsDisk(string|Closure|null $disk): static
    {
        $this->conversionsDisk = $disk;

        return $this;
    }

    /**
     * @param  array<string, mixed>|Closure|null  $headers
     */
    public function customHeaders(array|Closure|null $headers): static
    {
        $this->customHeaders = $headers;

        return $this;
    }

    /**
     * @param  array<string, mixed>|Closure|null  $properties
     */
    public function customProperties(array|Closure|null $properties): static
    {
        $this->customProperties = $properties;

        return $this;
    }

    /**
     * @param  array<string, array<string, string>>|Closure|null  $manipulations
     */
    public function manipulations(array|Closure|null $manipulations): static
    {
        $this->manipulations = $manipulations;

        return $this;
    }

    /**
     * @param  array<string, mixed>|Closure|null  $properties
     */
    public function properties(array|Closure|null $properties): static
    {
        $this->properties = $properties;

        return $this;
    }

    public function responsiveImages(bool|Closure $condition = true): static
    {
        $this->hasResponsiveImages = $condition;

        return $this;
    }

    public function mediaName(string|Closure|null $name): static
    {
        $this->mediaName = $name;

        return $this;
    }

    public function getMediaName(TemporaryUploadedFile $file): ?string
    {
        return $this->evaluate($this->mediaName, [
            'file' => $file,
        ]);
    }

    public function getDiskName(): string
    {
        if ($diskName = $this->evaluate($this->diskName)) {
            return $diskName;
        }

        /** @var Model&HasMedia $model */
        $model = $this->getModelInstance();
        $collection = $this->getCollection() ?? 'default';

        return $model->getRegisteredMediaCollections()
            ->firstWhere('name', $collection)
            ?->diskName ?? config('filament.default_filesystem_disk');
    }

    public function getCollection(): ?string
    {
        return $this->evaluate($this->collection);
    }

    public function getConversion(): ?string
    {
        return $this->evaluate($this->conversion);
    }

    public function getConversionsDisk(): ?string
    {
        return $this->evaluate($this->conversionsDisk);
    }

    /**
     * @return array<string, mixed>
     */
    public function getCustomHeaders(): array
    {
        return $this->evaluate($this->customHeaders) ?? [];
    }

    /**
     * @return array<string, mixed>
     */
    public function getCustomProperties(): array
    {
        return $this->evaluate($this->customProperties) ?? [];
    }

    /**
     * @return array<string, array<string, string>>
     */
    public function getManipulations(): array
    {
        return $this->evaluate($this->manipulations) ?? [];
    }

    /**
     * @return array<string, mixed>
     */
    public function getProperties(): array
    {
        return $this->evaluate($this->properties) ?? [];
    }

    public function hasResponsiveImages(): bool
    {
        return (bool) $this->evaluate($this->hasResponsiveImages);
    }
}
