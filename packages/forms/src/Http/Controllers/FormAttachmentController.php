<?php

namespace Filament\Forms\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class FormAttachmentController
{
    public function __invoke(Request $request)
    {
        $request->validate([
            'file' => ['required', 'file'],
        ]);

        $directory = $request->input('directory', 'attachments');
        $disk = $request->input('disk', config('forms.default_filesystem_disk'));

        $path = $request->file('file')->store($directory, $disk);

        return Storage::disk($disk)->url($path);
    }
}
