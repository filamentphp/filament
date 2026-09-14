<?php

namespace Filament\Pages\Concerns;

use Filament\Facades\Filament;
use Filament\Inertia\InertiaPlugin;
use Filament\Pages\BasePage;
use Filament\Pages\Page;
use Filament\Schemas\Components\View;
use Filament\Schemas\Schema;
use Illuminate\Support\HtmlString;
use Inertia\Response as InertiaResponse;
use Livewire\Livewire;
use LogicException;
use Symfony\Component\HttpFoundation\Response;

use function Filament\Support\original_request;

/** @mixin BasePage */
trait InteractsWithInertia
{
    abstract protected function getInertiaResponse(): InertiaResponse;

    public function __invoke(): Response
    {
        $this->authorizeInertiaAccess();

        if (request()->header('X-Inertia')) {
            return $this->getInertiaResponse()->toResponse(request());
        }

        return parent::__invoke();
    }

    public function hydrateInteractsWithInertia(): void
    {
        $this->authorizeInertiaAccess();
    }

    protected function authorizeInertiaAccess(): void
    {
        if ($this instanceof \Filament\Resources\Pages\Page) {
            throw new LogicException('Inertia content requires a custom `Page` or `SimplePage`. Resource pages depend on the Livewire record lifecycle.');
        }

        if ($this instanceof Page) {
            abort_unless(static::canAccess(), 403);
        }
    }

    public function content(Schema $schema): Schema
    {
        return $schema->components([$this->getInertiaContentComponent()]);
    }

    public function getInertiaContentComponent(): View
    {
        return View::make('filament-panels::inertia.host')
            ->key('inertia')
            ->columnSpanFull()
            ->viewData(fn (): array => [
                'renderer' => $this->getInertiaRenderer(),
                'spa' => Filament::getCurrentPanel()->hasSpaMode(),
                'spaUrlExceptions' => Filament::getCurrentPanel()->getSpaUrlExceptions(),
                'rememberScope' => hash('sha256', json_encode([
                    Filament::getCurrentPanel()->getId(),
                    Filament::getTenant()?->getKey(),
                    Filament::auth()->id(),
                    session()->getId(),
                ], JSON_THROW_ON_ERROR)),
                'rememberKey' => $this->getInertiaRememberKey(),
                'inertiaView' => $this->getInertiaView(),
            ]);
    }

    protected function getInertiaRenderer(): string
    {
        return InertiaPlugin::get()->getRenderer();
    }

    protected function getInertiaRememberKey(): string
    {
        return original_request()->getRequestUri();
    }

    protected function getInertiaView(): HtmlString
    {
        if (Livewire::isLivewireRequest()) {
            return new HtmlString('');
        }

        $this->authorizeInertiaAccess();

        return new HtmlString($this->getInertiaResponse()
            ->rootView('filament-panels::inertia.root')
            ->toResponse(request())
            ->getContent());
    }
}
