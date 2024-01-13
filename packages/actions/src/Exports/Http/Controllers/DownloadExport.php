<?php

namespace Filament\Actions\Exports\Http\Controllers;

use Filament\Actions\Exports\Enums\DownloadFileFormat;
use Filament\Actions\Exports\Models\Export;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DownloadExport
{
    public function __invoke(Request $request, Export $export): StreamedResponse
    {
        abort_unless($export->user->is(auth()->user()), 403);

        $format = DownloadFileFormat::tryFrom($request->query('format'));

        abort_unless($format !== null, 404);

        return $format->getDownloader()($export);
    }
}
