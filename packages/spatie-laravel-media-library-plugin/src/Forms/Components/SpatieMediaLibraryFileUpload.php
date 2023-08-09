<?php

namespace Filament\Forms\Components;

use Closure;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Intervention\Image\ImageManagerStatic as InterventionImage;
use League\Flysystem\UnableToCheckFileExistence;
use Livewire\TemporaryUploadedFile;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\MediaCollections\FileAdder;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Throwable;

class SpatieMediaLibraryFileUpload extends FileUpload
{
    protected string | Closure | null $collection = null;

    protected string | Closure | null $conversion = null;

    protected string | Closure | null $conversionsDisk = null;

    protected bool | Closure $hasResponsiveImages = false;

    protected string | Closure | null $mediaName = null;

    protected array | Closure | null $customProperties = null;

    protected array | Closure | null $manipulations = null;

    protected string | Closure | null $optimize = null;

    protected array | Closure | null $properties = null;

    protected int | Closure | null $resize = null;

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

            /** @var ?Media $media */
            $media = $component->getRecord()->getRelationValue('media')->firstWhere('uuid', $file);

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
            $component->deleteAbandonedFiles();
            $component->saveUploadedFiles();
        });

        $this->saveUploadedFileUsing(static function (SpatieMediaLibraryFileUpload $component, TemporaryUploadedFile $file, ?Model $record): ?string {
            if (! method_exists($record, 'addMediaFromString')) {
                return $file;
            }

            try {
                if (! $file->exists()) {
                    return null;
                }
            } catch (UnableToCheckFileExistence $exception) {
                return null;
            }

            $compressedImage = null;
            $filename = $component->getUploadedFileNameForStorage($file);
            $originalBinaryFile = $file->get();

            if (
                str_contains($file->getMimeType(), 'image') &&
                ($component->getOptimization() || $component->getResize())
            ) {
                $image = InterventionImage::make($originalBinaryFile);

                if ($component->getOptimization()) {
                    $quality = $component->getOptimization() === 'jpeg' ||
                        $component->getOptimization() === 'jpg' ? 70 : null;

                    $image->encode($component->getOptimization(), $quality);
                }

                if ($component->getResize()) {
                    $height = null;
                    $width = null;

                    if ($image->height() > $image->width()) {
                        $height = $image->height() - ($image->height() * ($component->getResize() / 100));
                    } else {
                        $width = $image->width() - ($image->width() * ($component->getResize() / 100));
                    }

                    $image->resize($width, $height, function ($constraint) {
                        $constraint->aspectRatio();
                    });
                }

                $compressedImage = $image->encode();

                $filename = self::formatFileName($filename, $component->getOptimization());
            }

            /** @var FileAdder $mediaAdder */
            $mediaAdder = $record->addMediaFromString($compressedImage ?? $originalBinaryFile);

            $media = $mediaAdder
                ->usingFileName($filename)
                ->usingName($component->getMediaName($file) ?? pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME))
                ->storingConversionsOnDisk($component->getConversionsDisk() ?? '')
                ->withCustomProperties($component->getCustomProperties())
                ->withManipulations($component->getManipulations())
                ->withResponsiveImagesIf($component->hasResponsiveImages())
                ->withProperties($component->getProperties())
                ->toMediaCollection($component->getCollection(), $component->getDiskName());

            return $media->getAttributeValue('uuid');
        });

        $this->reorderUploadedFilesUsing(static function (SpatieMediaLibraryFileUpload $component, array $state): array {
            $uuids = array_filter(array_values($state));

            $mediaClass = config('media-library.media_model', Media::class);

            $mappedIds = $mediaClass::query()->whereIn('uuid', $uuids)->pluck('id', 'uuid')->toArray();

            $mediaClass::setNewOrder(array_merge(array_flip($uuids), $mappedIds));

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

    public function conversionsDisk(string | Closure | null $disk): static
    {
        $this->conversionsDisk = $disk;

        return $this;
    }

    public function customProperties(array | Closure | null $properties): static
    {
        $this->customProperties = $properties;

        return $this;
    }

    public function manipulations(array | Closure | null $manipulations): static
    {
        $this->manipulations = $manipulations;

        return $this;
    }

    public function optimize(string | Closure | null $optimize): static
    {
        $this->optimize = $optimize;

        return $this;
    }

    public function properties(array | Closure | null $properties): static
    {
        $this->properties = $properties;

        return $this;
    }

    public function responsiveImages(bool | Closure $condition = true): static
    {
        $this->hasResponsiveImages = $condition;

        return $this;
    }

    public function resize(int | Closure | null $reductionPercentage): static
    {
        $this->resize = $reductionPercentage;

        return $this;
    }

    public function deleteAbandonedFiles(): void
    {
        /** @var Model&HasMedia $record */
        $record = $this->getRecord();

        $record
            ->getMedia($this->getCollection())
            ->whereNotIn('uuid', array_keys($this->getState() ?? []))
            ->each(fn (Media $media) => $media->delete());
    }

    public static function formatFilename(string $filename, ?string $format): string
    {
        if (! $format) {
            return $filename;
        }

        $extension = strrpos($filename, '.');

        if ($extension !== false) {
            return substr($filename, 0, $extension + 1) . $format;
        }

        return $filename;
    }

    public function getCollection(): string
    {
        return $this->evaluate($this->collection) ?? 'default';
    }

    public function getConversion(): ?string
    {
        return $this->evaluate($this->conversion);
    }

    public function getConversionsDisk(): ?string
    {
        return $this->evaluate($this->conversionsDisk);
    }

    public function getCustomProperties(): array
    {
        return $this->evaluate($this->customProperties) ?? [];
    }

    public function getManipulations(): array
    {
        return $this->evaluate($this->manipulations) ?? [];
    }

    public function getOptimization(): ?string
    {
        return $this->evaluate($this->optimize);
    }

    public function getProperties(): array
    {
        return $this->evaluate($this->properties) ?? [];
    }

    public function getResize(): ?int
    {
        return $this->evaluate($this->resize);
    }

    public function hasResponsiveImages(): bool
    {
        return (bool) $this->evaluate($this->hasResponsiveImages);
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
