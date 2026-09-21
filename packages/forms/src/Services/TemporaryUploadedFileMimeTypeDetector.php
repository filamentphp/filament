<?php

namespace Filament\Forms\Services;

use League\MimeTypeDetection\FinfoMimeTypeDetector;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

class TemporaryUploadedFileMimeTypeDetector
{
    public function detect(TemporaryUploadedFile $file): string
    {
        $stream = $file->readStream();

        if (! is_resource($stream)) {
            return 'application/octet-stream';
        }

        try {
            $contents = stream_get_contents($stream, 64 * 1024);
        } finally {
            fclose($stream);
        }

        if (($contents === false) || ($contents === '')) {
            return 'application/octet-stream';
        }

        return (new FinfoMimeTypeDetector)->detectMimeTypeFromBuffer($contents)
            ?: 'application/octet-stream';
    }
}
