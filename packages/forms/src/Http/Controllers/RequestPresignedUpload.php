<?php

namespace Filament\Forms\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Features\SupportFileUploads\FileUploadConfiguration;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

class RequestPresignedUpload
{
    public function __invoke(Request $request): JsonResponse
    {
        ['disk' => $disk, 'fileName' => $fileName, 'fileType' => $fileType] = $request->validate([
            'disk' => 'required|string',
            'fileName' => 'required|string',
            'fileType' => 'required|string',
        ]);

        $fileHashName = TemporaryUploadedFile::generateHashNameWithOriginalNameEmbedded(
            file: UploadedFile::fake()->create(name: $fileName, mimeType: $fileType)
        );

        ['url' => $url] = Storage::disk($disk)->temporaryUploadUrl(
            path: FileUploadConfiguration::path($fileHashName),
            expiration: now()->addMinutes(FileUploadConfiguration::maxUploadTime()),
        );

        return response()->json([
            'path' => $fileHashName,
            'url' => $url,
        ], 201);
    }
}
