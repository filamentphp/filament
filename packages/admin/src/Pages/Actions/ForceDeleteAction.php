<?php

namespace Filament\Pages\Actions;

use Filament\Pages\Contracts\HasRecord;
use Filament\Pages\Page;
use Filament\Support\Actions\Concerns\CanForceDeleteRecords;
use Illuminate\Database\Eloquent\Model;

class ForceDeleteAction extends Action
{
    use CanForceDeleteRecords {
        setUp as setUpTrait;
    }

    protected function setUp(): void
    {
        $this->setUpTrait();

        $this->record(function (Page $livewire): ?Model {
            if (! $livewire instanceof HasRecord) {
                return null;
            }

            return $livewire->getRecord();
        });
    }
}
