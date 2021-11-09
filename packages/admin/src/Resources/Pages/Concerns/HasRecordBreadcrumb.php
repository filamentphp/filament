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
            [$recordBreadcrumbUrl, $recordBreadcrumbLabel] = $this->getRecordBreadcrumb();

            $breadcrumbs[$recordBreadcrumbUrl] = $recordBreadcrumbLabel;
        }

        $breadcrumbs[] = $this->getBreadcrumb();

        return $breadcrumbs;
    }

    protected function getRecordBreadcrumb(): array
    {
        $resource = static::getResource();

        if ($resource::hasViewPage()) {
            $url = $resource::getViewUrl($this->record);
        } else {
            $url = $resource::getEditUrl($this->record);
        }

        return [$url, $resource::getPrimaryAttributeForModel($this->record)];
    }
}
