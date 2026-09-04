<?php

namespace Filament\Actions\Exports\Http\Controllers;

use Filament\Actions\Exports\Enums\Contracts\ExportFormat as ExportFormatInterface;
use Filament\Actions\Exports\Enums\ExportFormat;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Gate;
use Symfony\Component\HttpFoundation\Response;

class DownloadExport
{
    public function __invoke(Request $request, Export $export): Response
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

        $exportPolicy = Gate::getPolicyFor($export::class);

        if (filled($exportPolicy) && method_exists($exportPolicy, 'view')) {
            Gate::forUser($user)->authorize('view', Arr::wrap($export));
        } else {
            abort_unless($export->user()->is($user), 403);
        }

        $format = $this->resolveFormatFromRequest($request);

        abort_unless($format !== null, 404);

        if (! is_a($export->exporter, Exporter::class, allow_string: true)) { /** @phpstan-ignore function.alreadyNarrowedType */
            return $format->getDownloader()($export);
        }

        return $export->exporter::getDownloader($format)($export);
    }

    protected function resolveFormatFromRequest(Request $request): ?ExportFormatInterface
    {
        return ExportFormat::tryFrom($request->query('format'));
    }
}
