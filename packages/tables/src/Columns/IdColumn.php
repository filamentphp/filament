<?php

namespace Filament\Tables\Columns;

use Filament\Facades\Filament;
use Illuminate\Database\Eloquent\Model;

class IdColumn extends TextColumn
{
    public static function make(string $name = 'id'): static
    {
        return parent::make($name);
    }

    protected function setUp(): void
    {
        parent::setUp();

        if ($this->getName() === 'id') {
            $this->label('ID');
        }

        $this->sortable();

        $this->url(function (Model $record): ?string {
            $resource = Filament::getModelResource($record);

            if ($resource === null) {
                return null;
            }

            if ($resource::hasPage('view') && $resource::canView($record)) {
                return $resource::getUrl('view', ['record' => $record]);
            }

            if ($resource::hasPage('edit') && $resource::canEdit($record)) {
                return $resource::getUrl('edit', ['record' => $record]);
            }

            return null;
        });
    }
}
