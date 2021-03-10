<?php

namespace Filament\Resources\Tables\Columns\Concerns;

trait InteractsWithResource
{
    public function primary()
    {
        $this->primary = true;

        if (! $this->url) {
            $this->url(function ($record) {
                if (! $this->context || ! $this->context::getResource()) {
                    return null;
                }

                $resource = $this->context::getResource();

                return $resource::generateUrl($this->context::$recordRoute, ['record' => $record]);
            });
        }

        return $this;
    }
}
