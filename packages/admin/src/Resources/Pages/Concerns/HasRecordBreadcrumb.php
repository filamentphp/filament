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

        if ($resource::hasPrimaryAttribute()) {
            $recordBreadcrumbUrl = null;

            if ($resource::canView($this->record)) {
                $breadcrumbs[
                    $resource::getUrl('view', ['record' => $this->record])
                ] = $this->getRecordPrimaryAttribute();;
            } elseif ($resource::canEdit($this->record)) {
                $breadcrumbs[
                    $resource::getUrl('edit', ['record' => $this->record])
                ] = $this->getRecordPrimaryAttribute();;
            } else {
                $breadcrumbs[] = $this->getRecordPrimaryAttribute();
            }
        }

        $breadcrumbs[] = $this->getBreadcrumb();

        return $breadcrumbs;
    }
}
