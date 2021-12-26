<?php

namespace Filament\Forms\Components;

use Closure;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Support\Collection;
use League\Flysystem\AwsS3v3\AwsS3Adapter;
use Livewire\TemporaryUploadedFile;
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

        $this->getUploadedFileUrlUsing(function (SpatieMediaLibraryFileUpload $component, string $file): ?string {
            /** @var FilesystemAdapter $storage */
            $storage = $component->getDisk();

            /** @var \League\Flysystem\Filesystem $storageDriver */
            $storageDriver = $storage->getDriver();

            if (! $component->getModel()) {
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
        });

        $this->saveUploadedFileUsing(function (SpatieMediaLibraryFileUpload $component, TemporaryUploadedFile $file, ?Model $record): string {
            if (! method_exists($record, 'addMediaFromString')) {
                return $file;
            }

            /** @var FileAdder $mediaAdder */
            $mediaAdder = $record->addMediaFromString($file->get());

            $media = $mediaAdder
                ->usingFileName($file->getFilename())
                ->toMediaCollection($component->getCollection());

            return $media->getAttributeValue('uuid');
        });

        $this->deleteUploadedFileUsing(function (SpatieMediaLibraryFileUpload $component, string $file): void {
            if (! $file) {
                return;
            }

            Media::findByUuid($file)?->delete();
        });
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
}
