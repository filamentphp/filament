<?php

namespace Filament\Resources\Pages\Concerns;

trait HasRecordBreadcrumb
{
    protected function getBreadcrumbs(): array
    {
        $resource = static::getResource();

        $breadcrumbs = [
            $resource::getUrl() => $resource::getBreadcrumb(),
        ];

        if ($this->getRecord()->exists && $resource::hasRecordTitle()) {
            if ($resource::hasPage('view') && $resource::canView($this->getRecord())) {
                $breadcrumbs[
                    $resource::getUrl('view', ['record' => $this->getRecord()])
                ] = $this->getRecordTitle();
            } elseif ($resource::hasPage('edit') && $resource::canEdit($this->getRecord())) {
                $breadcrumbs[
                    $resource::getUrl('edit', ['record' => $this->getRecord()])
                ] = $this->getRecordTitle();
            } else {
                $breadcrumbs[] = $this->getRecordTitle();
            }
        }

        $breadcrumbs[] = $this->getBreadcrumb();

        return $breadcrumbs;
    }
}
