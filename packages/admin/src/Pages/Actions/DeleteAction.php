<?php

namespace Filament\Pages\Actions;

use Filament\Pages\Contracts\HasRecord;
use Filament\Pages\Page;
use Filament\Support\Actions\Concerns\CanDeleteRecords;
use Illuminate\Database\Eloquent\Model;

class DeleteAction extends Action
{
    use CanDeleteRecords {
        setUp as setUpTrait;
    }

    protected function setUp(): void
    {
        $this->setUpTrait();

        $this->keyBindings(['mod+d']);

        $this->record(function (Page $livewire): ?Model {
            if (! $livewire instanceof HasRecord) {
                return null;
            }

            return $livewire->getRecord();
        });
    }
}
