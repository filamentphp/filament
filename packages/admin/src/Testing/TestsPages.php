<?php

namespace Filament\Testing;

/**
 * @mixin \Livewire\Testing\TestableLivewire
 */
class TestsPages
{
    public function callPageAction()
    {
        return function (string $name, array $formData = []) {
            $this
                ->call('mountAction', $name)
                ->assertSet('mountedAction', $name)
                ->set('mountedActionData', $formData);

            $action = $this->instance()->getMountedAction();

            if ($action->shouldOpenModal()) {
                $this
                ->assertDispatchedBrowserEvent('open-modal', [
                    'id' => 'page-action'
                ])
                ->call('callMountedAction');
            }

            return $this->call('callMountedAction');
        };
    }
}
