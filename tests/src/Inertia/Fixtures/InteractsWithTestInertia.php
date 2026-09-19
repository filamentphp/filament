<?php

namespace Filament\Tests\Inertia\Fixtures;

use Filament\Pages\Concerns\InteractsWithInertia;
use Inertia\Inertia;
use Inertia\Response;

trait InteractsWithTestInertia
{
    use InteractsWithInertia;

    public int $shellRefreshes = 0;

    public function mount(): void
    {
        RequestState::$mounts++;
    }

    public function refreshShell(): void
    {
        $this->shellRefreshes++;
    }

    protected function getInertiaResponse(): Response
    {
        RequestState::$responses++;

        return Inertia::render('Report', [
            'value' => request()->query('value', 'initial'),
            'optional' => Inertia::optional(static fn (): string => 'selected'),
            'analytics' => [
                'total' => Inertia::defer(static fn (): int => 137),
                'audit' => Inertia::optional(static fn (): string => 'private audit'),
            ],
        ])->rootView('app-template-must-not-render');
    }
}
