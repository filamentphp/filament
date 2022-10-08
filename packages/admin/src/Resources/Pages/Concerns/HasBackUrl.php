<?php

namespace Filament\Resources\Pages\Concerns;

use Filament\Pages\Actions\Action;
use Filament\Pages\Actions\DeleteAction;
use Filament\Pages\Actions\ForceDeleteAction;
use Filament\Resources\Pages\Page;

trait HasBackUrl
{
    public ?string $backUrl = null;

    public function mount($record = null): void
    {
        $this->backUrl = url()->previous();
        parent::mount($record);
    }

    protected function getRedirectUrl(): string
    {
        return $this->backUrl;
    }

    protected function getCancelFormAction(): Action
    {
        return parent::getCancelFormAction()
            ->url(fn(Page $livewire) => $livewire->backUrl ?? static::getResource()::getUrl());
    }

    protected function configureDeleteAction(DeleteAction $action): void
    {
        if (is_callable('parent::configureDeleteAction')) {
            parent::configureDeleteAction($action);
            if ($this->backUrl) {
                $action->successRedirectUrl($this->backUrl);
            }
        }
    }

    protected function configureForceDeleteAction(ForceDeleteAction $action): void
    {
        if (is_callable('parent::configureForceDeleteAction')) {
            parent::configureForceDeleteAction($action);
            if ($this->backUrl) {
                $action->successRedirectUrl($this->backUrl);
            }
        }
    }

    protected function getBreadcrumbs(): array
    {
        if (is_callable('parent::getBreadcrumbs')) {
            $breadcrumbs = parent::getBreadcrumbs();
            if ($this->backUrl) {
                return array(
                    $this->backUrl => array_shift($breadcrumbs),
                    ...$breadcrumbs
                );
            }
            return $breadcrumbs;
        }
        return [];
    }
}
