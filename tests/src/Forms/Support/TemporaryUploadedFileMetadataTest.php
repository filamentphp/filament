<?php

use Filament\Forms\Support\TemporaryUploadedFileMetadata;
use Filament\Tests\TestCase;
use Illuminate\Support\Facades\Storage;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

uses(TestCase::class);

/**
 * @param  array{name?: string, type?: string, size?: int|string, hash?: string}  $meta
 */
function createLivewireTemporaryUpload(string $filename, ?string $contents, array $meta): TemporaryUploadedFile
{
    Storage::fake('tmp-for-tests');

    $path = 'livewire-tmp/' . $filename;

    if (is_string($contents)) {
        Storage::disk('tmp-for-tests')->put($path, $contents);
    }

    Storage::disk('tmp-for-tests')->put($path . '.json', json_encode($meta));

    return TemporaryUploadedFile::createFromLivewire($filename);
}

describe('TemporaryUploadedFileMetadata', function (): void {
    it('treats a jpeg as `image/*` from the original filename when the sidecar type is generic', function (): void {
        $file = createLivewireTemporaryUpload('avatar.jpeg', 'dummy-image', [
            'name' => 'avatar.jpeg',
            'type' => 'application/octet-stream',
            'size' => 4096,
            'hash' => 'avatar.jpeg',
        ]);

        $metadata = app(TemporaryUploadedFileMetadata::class);

        expect($metadata->exists($file))->toBeTrue()
            ->and($metadata->mimeType($file))->toBe('image/jpeg')
            ->and($metadata->mimeMatches($file, ['image/*']))->toBeTrue()
            ->and($metadata->sizeBytes($file))->toBe(4096);
    });

    it('accepts zip uploads from a zip alias in the sidecar type', function (): void {
        $file = createLivewireTemporaryUpload('archive.zip', 'dummy-zip', [
            'name' => 'archive.zip',
            'type' => 'application/x-zip-compressed',
            'size' => 512,
            'hash' => 'archive.zip',
        ]);

        expect(app(TemporaryUploadedFileMetadata::class)->mimeMatches($file, ['application/zip']))->toBeTrue();
    });

    it('rejects a zip against `image/*`', function (): void {
        $file = createLivewireTemporaryUpload('archive.zip', 'dummy-zip', [
            'name' => 'archive.zip',
            'type' => 'application/zip',
            'size' => 512,
            'hash' => 'archive.zip',
        ]);

        expect(app(TemporaryUploadedFileMetadata::class)->mimeMatches($file, ['image/*']))->toBeFalse();
    });

    it('reads size from the sidecar without calling Flysystem `file_size`', function (): void {
        Storage::fake('tmp-for-tests');

        $filename = 'stale-upload.jpeg';
        Storage::disk('tmp-for-tests')->put('livewire-tmp/' . $filename . '.json', json_encode([
            'name' => 'stale-upload.jpeg',
            'type' => 'image/jpeg',
            'size' => 15360,
            'hash' => $filename,
        ]));

        $file = TemporaryUploadedFile::createFromLivewire($filename);
        $metadata = app(TemporaryUploadedFileMetadata::class);

        expect($metadata->sizeBytes($file))->toBe(15360)
            ->and($metadata->mimeType($file))->toBe('image/jpeg');
    });

    it('uses `mimeTypeMap()` when the filename extension is not a well-known MIME type', function (): void {
        $file = createLivewireTemporaryUpload('floorplan.3dm', 'dummy-3dm', [
            'name' => 'floorplan.3dm',
            'type' => 'application/octet-stream',
            'size' => 1024,
            'hash' => 'floorplan.3dm',
        ]);

        expect(app(TemporaryUploadedFileMetadata::class)->mimeMatches(
            $file,
            ['x-world/x-3dmf'],
            ['3dm' => 'x-world/x-3dmf'],
        ))->toBeTrue();
    });

    it('blocks a `.php` client filename unless `php` is an accepted type', function (): void {
        $file = createLivewireTemporaryUpload('upload.php', '<?php', [
            'name' => 'upload.php',
            'type' => 'image/png',
            'size' => 32,
            'hash' => 'upload.php',
        ]);

        $metadata = app(TemporaryUploadedFileMetadata::class);

        expect($metadata->hasBlockedPhpExtension($file, ['image/png']))->toBeTrue()
            ->and($metadata->hasBlockedPhpExtension($file, ['php']))->toBeFalse()
            ->and($metadata->isValid($file, ['image/png'], null))->toBeFalse();
    });

    it('returns `null` for size when the object and sidecar size are both missing', function (): void {
        Storage::fake('tmp-for-tests');

        $file = TemporaryUploadedFile::createFromLivewire('absent.jpeg');
        $metadata = app(TemporaryUploadedFileMetadata::class);

        expect($metadata->exists($file))->toBeFalse()
            ->and($metadata->sizeBytes($file))->toBeNull()
            ->and($metadata->isValid($file, ['image/*'], 1024))->toBeFalse();
    });
});
