<?php

namespace Filament\Forms\Components;

use Closure;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Support\Collection;
use League\Flysystem\AwsS3v3\AwsS3Adapter;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\MediaCollections\FileAdder;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use SplFileInfo;

class SpatieMediaLibraryFileUpload extends FileUpload
{
    protected string | Closure | null $collection = null;

    protected function setUp(): void
    {
        parent::setUp();

        $this->afterStateHydrated(function (SpatieMediaLibraryFileUpload $component, ?HasMedia $record): void {
            if (! $record) {
                $component->state([]);

                return;
            }

            $files = $record->getMedia($component->getCollection())
                ->when(
                    ! $component->isMultiple(),
                    fn (Collection $files): Collection => $files->take(1),
                )
                ->mapWithKeys(function (Media $file): array {
                    $uuid = $file->getAttributeValue('uuid');

                    return [$uuid => $uuid];
                })
                ->toArray();

            $component->state($files);
        });

        $this->beforeStateDehydrated(null);

        $this->dehydrated(false);
    }

    public function collection(string | Closure | null $collection): static
    {
        $this->collection = $collection;

        return $this;
    }

    public function saveRelationships(): void
    {
        if ($this->saveRelationshipsUsing) {
            parent::saveRelationships();

            return;
        }

        $this->saveUploadedFiles();
    }

    public function getCollection(): string
    {
        return $this->evaluate($this->collection) ?? 'default';
    }

    protected function handleUpload($file)
    {
        $model = $this->getModel();

        if (! $model) {
            return $file;
        }

        if (! method_exists($model, 'addMediaFromString')) {
            return $file;
        }

        /** @var FileAdder $mediaAdder */
        $mediaAdder = $model->addMediaFromString($file->get());

        $media = $mediaAdder
            ->usingFileName($file->getFilename())
            ->toMediaCollection($this->getCollection());

        return $media->getAttributeValue('uuid');
    }

    protected function handleUploadedFileDeletion($file): void
    {
        if (! $file) {
            return;
        }

        Media::findByUuid($file)?->delete();
    }

    protected function handleUploadedFileUrlRetrieval($file): ?string
    {
        /** @var FilesystemAdapter $storage */
        $storage = $this->getDisk();

        /** @var \League\Flysystem\Filesystem $storageDriver */
        $storageDriver = $storage->getDriver();

        if (! $this->getModel()) {
            return null;
        }

        if ($file instanceof SplFileInfo) {
            return null;
        }

        /** @var ?Media $media */
        $media = Media::findByUuid($file);

        if (
            $storageDriver->getAdapter() instanceof AwsS3Adapter &&
            $this->getVisibility() === 'private'
        ) {
            return $media?->getTemporaryUrl(now()->addMinutes(5));
        }

        return $media?->getUrl();
    }
}
