<?php

namespace Filament\Forms\Components;

use Closure;
use Hoa\File\Temporary\Temporary;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Livewire\TemporaryUploadedFile;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\MediaCollections\FileAdder;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Throwable;

class SpatieMediaLibraryFileUpload extends FileUpload
{
    protected string | Closure | null $collection = null;

    protected string | Closure | null $conversion = null;

    protected array | Closure | null $customProperties = null;

    protected string | Closure | null $mediaName = null;

    protected function setUp(): void
    {
        parent::setUp();

        $this->loadStateFromRelationshipsUsing(static function (SpatieMediaLibraryFileUpload $component, HasMedia $record): void {
            /** @var Model&HasMedia $record */
            $files = $record->load('media')->getMedia($component->getCollection())
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

        $this->afterStateHydrated(static function (BaseFileUpload $component, string | array | null $state): void {
            if (is_array($state)) {
                return;
            }

            $component->state([]);
        });

        $this->beforeStateDehydrated(null);

        $this->dehydrated(false);

        $this->getUploadedFileUrlUsing(static function (SpatieMediaLibraryFileUpload $component, string $file): ?string {
            if (! $component->getRecord()) {
                return null;
            }

            $mediaClass = config('media-library.media_model', Media::class);

            /** @var ?Media $media */
            $media = $mediaClass::findByUuid($file);

            if ($component->getVisibility() === 'private') {
                try {
                    return $media?->getTemporaryUrl(
                        now()->addMinutes(5),
                    );
                } catch (Throwable $exception) {
                    // This driver does not support creating temporary URLs.
                }
            }

            if ($component->getConversion() && $media->hasGeneratedConversion($component->getConversion())) {
                return $media?->getUrl($component->getConversion());
            }

            return $media?->getUrl();
        });

        $this->saveRelationshipsUsing(static function (SpatieMediaLibraryFileUpload $component) {
            $component->saveUploadedFiles();
        });

        $this->saveUploadedFileUsing(static function (SpatieMediaLibraryFileUpload $component, TemporaryUploadedFile $file, ?Model $record): string {
            if (! method_exists($record, 'addMediaFromString')) {
                return $file;
            }

            /** @var FileAdder $mediaAdder */
            $mediaAdder = $record->addMediaFromString($file->get());

            $filename = $component->getUploadedFileNameForStorage($file);

            $media = $mediaAdder
                ->usingFileName($filename)
                ->usingName($component->getMediaName($file) ?? pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME))
                ->withCustomProperties($component->getCustomProperties())
                ->toMediaCollection($component->getCollection(), $component->getDiskName());

            return $media->getAttributeValue('uuid');
        });

        $this->deleteUploadedFileUsing(static function (SpatieMediaLibraryFileUpload $component, string $file): void {
            if (! $file) {
                return;
            }

            $mediaClass = config('media-library.media_model', Media::class);

            $mediaClass::findByUuid($file)?->delete();
        });

        $this->reorderUploadedFilesUsing(static function (SpatieMediaLibraryFileUpload $component, array $state): array {
            $uuids = array_filter(array_values($state));
            $mappedIds = Media::query()->whereIn('uuid', $uuids)->pluck('id', 'uuid')->toArray();

            Media::setNewOrder(array_merge(array_flip($uuids), $mappedIds));

            return $state;
        });
    }

    public function collection(string | Closure | null $collection): static
    {
        $this->collection = $collection;

        return $this;
    }

    public function conversion(string | Closure | null $conversion): static
    {
        $this->conversion = $conversion;

        return $this;
    }

    public function customProperties(array | Closure | null $properties): static
    {
        $this->customProperties = $properties;

        return $this;
    }

    public function getCollection(): string
    {
        return $this->evaluate($this->collection) ?? 'default';
    }

    public function getConversion(): ?string
    {
        return $this->evaluate($this->conversion);
    }

    public function getCustomProperties(): array
    {
        return $this->evaluate($this->customProperties) ?? [];
    }

    public function mediaName(string | Closure | null $name): static
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
}
