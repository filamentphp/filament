<?php

namespace Filament\Forms\Support;

use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Symfony\Component\Mime\MimeTypes;
use Throwable;

/**
 * Reads MIME type and size for a Livewire temp upload from sidecar metadata when possible.
 *
 * `TemporaryUploadedFile::getMimeType()` and `TemporaryUploadedFile::getSize()` call Flysystem,
 * which can report a generic Content-Type or throw `UnableToRetrieveMetadata` when `file_size`
 * is missing. Livewire already stores `name` / `type` / `size` in `{path}.json` next to the file.
 */
class TemporaryUploadedFileMetadata
{
    /**
     * @var list<string>
     */
    protected const BLOCKED_PHP_EXTENSIONS = [
        'php', 'php3', 'php4', 'php5', 'php7', 'php8', 'phtml', 'phar',
    ];

    /**
     * @var list<string>
     */
    protected const GENERIC_MIME_TYPES = [
        'application/octet-stream',
        'application/x-empty',
        'inode/x-empty',
    ];

    /**
     * @var array<string, list<string>>
     */
    protected const MIME_ALIASES = [
        'application/zip' => ['application/x-zip-compressed', 'application/x-zip'],
        'application/x-zip-compressed' => ['application/zip', 'application/x-zip'],
        'application/x-zip' => ['application/zip', 'application/x-zip-compressed'],
        'image/jpeg' => ['image/jpg', 'image/pjpeg'],
        'image/jpg' => ['image/jpeg', 'image/pjpeg'],
        'image/pjpeg' => ['image/jpeg', 'image/jpg'],
    ];

    public function exists(TemporaryUploadedFile $file): bool
    {
        try {
            return $file->exists();
        } catch (Throwable) {
            return false;
        }
    }

    /**
     * @param  array<string, string>  $extensionMimeMap
     */
    public function mimeType(TemporaryUploadedFile $file, array $extensionMimeMap = []): ?string
    {
        $fromMeta = $this->metaString($file, 'type');

        if ($this->isUsableMime($fromMeta)) {
            return $fromMeta;
        }

        $fromFilename = $this->mimeFromFilename($this->clientOriginalName($file) ?? '', $extensionMimeMap)
            ?? $this->mimeFromFilename($this->storedFilename($file) ?? '', $extensionMimeMap);

        if ($fromFilename !== null) {
            return $fromFilename;
        }

        try {
            $fromDisk = $file->getMimeType();
        } catch (Throwable) {
            return null;
        }

        return $this->isUsableMime($fromDisk) ? $fromDisk : null;
    }

    public function sizeBytes(TemporaryUploadedFile $file): ?int
    {
        $fromMeta = $this->meta($file)['size'] ?? null;

        if (is_int($fromMeta) && $fromMeta >= 0) {
            return $fromMeta;
        }

        if (is_numeric($fromMeta) && (int) $fromMeta >= 0) {
            return (int) $fromMeta;
        }

        try {
            return $file->getSize();
        } catch (Throwable) {
            return null;
        }
    }

    /**
     * @param  list<string>  $allowed
     * @param  array<string, string>  $extensionMimeMap
     */
    public function mimeMatches(TemporaryUploadedFile $file, array $allowed, array $extensionMimeMap = []): bool
    {
        $mime = $this->mimeType($file, $extensionMimeMap);

        if ($mime === null) {
            return false;
        }

        return $this->mimeIsAllowed($mime, $allowed);
    }

    /**
     * @param  list<string>  $allowed
     */
    public function hasBlockedPhpExtension(TemporaryUploadedFile $file, array $allowed): bool
    {
        if (in_array('php', $allowed, true)) {
            return false;
        }

        $extension = strtolower($this->clientOriginalExtension($file) ?? '');

        return in_array($extension, self::BLOCKED_PHP_EXTENSIONS, true);
    }

    /**
     * @param  list<string> | null  $allowedMimeTypes
     * @param  array<string, string>  $extensionMimeMap
     */
    public function isValid(TemporaryUploadedFile $file, ?array $allowedMimeTypes, ?int $maxSizeKb, array $extensionMimeMap = []): bool
    {
        if (
            is_array($allowedMimeTypes) &&
            ($allowedMimeTypes !== []) &&
            $this->hasBlockedPhpExtension($file, $allowedMimeTypes)
        ) {
            return false;
        }

        if (! $this->exists($file)) {
            return false;
        }

        if (
            is_array($allowedMimeTypes) &&
            ($allowedMimeTypes !== []) &&
            (! $this->mimeMatches($file, $allowedMimeTypes, $extensionMimeMap))
        ) {
            return false;
        }

        if ($maxSizeKb !== null) {
            $bytes = $this->sizeBytes($file);

            if ($bytes === null || $bytes > $maxSizeKb * 1024) {
                return false;
            }
        }

        return true;
    }

    /**
     * @param  array<string, string>  $extensionMimeMap
     */
    public function mimeFromFilename(string $filename, array $extensionMimeMap = []): ?string
    {
        $extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

        if ($extension === '') {
            return null;
        }

        if (isset($extensionMimeMap[$extension])) {
            return $extensionMimeMap[$extension];
        }

        $mimeTypes = MimeTypes::getDefault()->getMimeTypes($extension);

        return $mimeTypes[0] ?? null;
    }

    /**
     * @param  list<string>  $allowed
     */
    protected function mimeIsAllowed(string $mime, array $allowed): bool
    {
        foreach ($allowed as $allowedMime) {
            if ($allowedMime === $mime) {
                return true;
            }

            if (str_ends_with($allowedMime, '/*')) {
                $prefix = substr($allowedMime, 0, -1);

                if (str_starts_with($mime, $prefix)) {
                    return true;
                }
            }

            foreach (self::MIME_ALIASES[$allowedMime] ?? [] as $alias) {
                if ($alias === $mime) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * @return array<string, mixed>
     */
    protected function meta(TemporaryUploadedFile $file): array
    {
        try {
            $meta = $file->metaFileData();
        } catch (Throwable) {
            return [];
        }

        return is_array($meta) ? $meta : [];
    }

    protected function metaString(TemporaryUploadedFile $file, string $key): ?string
    {
        $value = $this->meta($file)[$key] ?? null;

        return is_string($value) && $value !== '' ? $value : null;
    }

    protected function isUsableMime(?string $mime): bool
    {
        if ($mime === null) {
            return false;
        }

        return ! in_array($mime, self::GENERIC_MIME_TYPES, true);
    }

    protected function clientOriginalName(TemporaryUploadedFile $file): ?string
    {
        try {
            $name = $file->getClientOriginalName();
        } catch (Throwable) {
            return null;
        }

        return filled($name) ? $name : null;
    }

    protected function storedFilename(TemporaryUploadedFile $file): ?string
    {
        try {
            $filename = $file->getFilename();
        } catch (Throwable) {
            return null;
        }

        return filled($filename) ? $filename : null;
    }

    protected function clientOriginalExtension(TemporaryUploadedFile $file): ?string
    {
        try {
            $extension = $file->getClientOriginalExtension();
        } catch (Throwable) {
            return null;
        }

        return filled($extension) ? $extension : null;
    }
}
