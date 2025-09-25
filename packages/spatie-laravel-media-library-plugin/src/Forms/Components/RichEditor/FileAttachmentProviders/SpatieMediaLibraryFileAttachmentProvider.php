<?php

namespace Filament\Forms\Components\RichEditor\FileAttachmentProviders;

use Closure;
use Filament\Forms\Components\RichEditor\FileAttachmentProviders\Contracts\FileAttachmentProvider;
use Filament\Forms\Components\RichEditor\RichContentAttribute;
use Filament\Support\Concerns\EvaluatesClosures;
use Illuminate\Support\Str;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use LogicException;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Collections\MediaCollection;
use Throwable;

class SpatieMediaLibraryFileAttachmentProvider implements FileAttachmentProvider
{
    use EvaluatesClosures;

    protected MediaCollection $media;

    protected RichContentAttribute $attribute;

    protected ?string $collection = null;

    protected bool | Closure $shouldPreserveFilenames = false;

    protected string | Closure | null $mediaName = null;

    /**
     * @var array<string, mixed> | Closure | null
     */
    protected array | Closure | null $customProperties = null;

    public static function make(): static
    {
        return app(static::class);
    }

    public function collection(?string $collection): static
    {
        $this->collection = $collection;

        return $this;
    }

    public function preserveFilenames(bool | Closure $condition = true): static
    {
        $this->shouldPreserveFilenames = $condition;

        return $this;
    }

    public function mediaName(string | Closure | null $name): static
    {
        $this->mediaName = $name;

        return $this;
    }

    /**
     * @param  array<string, mixed> | Closure | null  $properties
     */
    public function customProperties(array | Closure | null $properties): static
    {
        $this->customProperties = $properties;

        return $this;
    }

    public function attribute(RichContentAttribute $attribute): static
    {
        $this->attribute = $attribute;

        return $this;
    }

    public function getExistingModel(): ?HasMedia
    {
        $model = $this->attribute->getModel();

        if (! $model->exists) {
            return null;
        }

        if (! ($model instanceof HasMedia)) {
            throw new LogicException('The [' . static::class . '] requires the model to implement the [' . HasMedia::class . '] interface from the Spatie Media Library package.');
        }

        return $model;
    }

    public function getMedia(): ?MediaCollection
    {
        if (isset($this->media)) {
            return $this->media;
        }

        /** @var MediaCollection $media */
        $media = $this->getExistingModel()?->getMedia($this->getCollection())->keyBy('uuid');

        return $this->media = $media;
    }

    public function getFileAttachmentUrl(mixed $file): ?string
    {
        $media = $this->getMedia();

        if (! $media) {
            return null;
        }

        if (! $media->has($file)) {
            return null;
        }

        $fileAttachment = $media->get($file);

        if ($this->attribute->getFileAttachmentsVisibility() === 'private') {
            try {
                return $fileAttachment->getTemporaryUrl(
                    now()->addMinutes(30)->endOfHour(),
                );
            } catch (Throwable $exception) {
                // This driver does not support creating temporary URLs.
            }
        }

        return $fileAttachment->getUrl();
    }

    public function saveUploadedFileAttachment(TemporaryUploadedFile $file): mixed
    {
        $media = $this->getExistingModel() /** @phpstan-ignore method.notFound */
            ->addMediaFromString($file->get())
            ->usingFileName($this->shouldPreserveFilenames() ? $file->getClientOriginalName() : (Str::ulid() . '.' . $file->getClientOriginalExtension()))
            ->usingName($this->getMediaName($file) ?? pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME))
            ->withCustomProperties($this->getCustomProperties())
            ->toMediaCollection($this->getCollection(), diskName: $this->attribute->getFileAttachmentsDiskName() ?? '');

        $this->getMedia()->put($media->uuid, $media);

        return $media->uuid;
    }

    /**
     * @param  array<mixed>  $exceptIds
     */
    public function cleanUpFileAttachments(array $exceptIds): void
    {
        $model = $this->getExistingModel();
        $collectionName = $this->getCollection();

        $model->clearMediaCollectionExcept(
            $collectionName,
            $model->getMedia($collectionName)->whereIn('uuid', $exceptIds),
        );
    }

    public function getDefaultFileAttachmentVisibility(): ?string
    {
        return 'private';
    }

    public function isExistingRecordRequiredToSaveNewFileAttachments(): bool
    {
        return true;
    }

    public function getCollection(): string
    {
        return $this->collection ?? $this->attribute->getName();
    }

    public function shouldPreserveFilenames(): bool
    {
        return (bool) $this->evaluate($this->shouldPreserveFilenames);
    }

    public function getMediaName(TemporaryUploadedFile $file): ?string
    {
        return $this->evaluate($this->mediaName, [
            'file' => $file,
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    public function getCustomProperties(): array
    {
        return $this->evaluate($this->customProperties) ?? [];
    }
}
