<?php

namespace Filament\Resources\Tables\Columns\Concerns;

trait InteractsWithResource
{
    public function resourceUrl($resource, $route = 'edit', $shouldOpenInNewTab = false)
    {
        $this->url(function ($record) use ($resource, $route) {
            return $resource::generateUrl($route, ['record' => $record]);
        }, $shouldOpenInNewTab);

        return $this;
    }
}
