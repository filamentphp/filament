<?php

namespace Filament\Actions\Imports\Http\Controllers;

use Filament\Actions\Imports\Models\Import;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Gate;
use Symfony\Component\HttpFoundation\Response;

class DownloadImportFailureCsv
{
    public function __invoke(Request $request, Import $import): Response
    {
        abort_unless(auth(
            $request->hasValidSignature(absolute: false)
                ? $request->query('authGuard')
                : null,
        )->check(), 401);

        $user = auth(
            $request->hasValidSignature(absolute: false)
                ? $request->query('authGuard')
                : null,
        )->user();

        $importPolicy = Gate::getPolicyFor($import::class);

        if (filled($importPolicy) && method_exists($importPolicy, 'view')) {
            Gate::forUser($user)->authorize('view', Arr::wrap($import));
        } else {
            abort_unless($import->user()->is($user), 403);
        }

        return $import->importer::getFailedRowsDownloader()($import);
    }
}
