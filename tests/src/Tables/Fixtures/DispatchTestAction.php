<?php

namespace Filament\Tests\Tables\Fixtures;

use Filament\Support\Actions\Concerns\CanCustomizeProcess;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\Concerns\CanDispatchAlpineEvents;
use Filament\Tables\Actions\Concerns\InteractsWithRelationship;
use Illuminate\Database\Eloquent\Model;

class DispatchTestAction extends Action
{
    use CanCustomizeProcess;
    use InteractsWithRelationship;
    use CanDispatchAlpineEvents;

    public static function getDefaultName(): ?string
    {
        return 'dispatch-test';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->label(__('Dispatch Test'));

        $this->color('danger');

        $this->icon('heroicon-s-map');

        $this->dispatch('test-event');

        $this->dispatchData(function (Model $record) {
            return json_encode([
                'id' => $record->id,
            ]);
        });
    }
}
