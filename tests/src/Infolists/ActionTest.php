<?php

use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\Actions\Action;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Tests\Infolists\Fixtures\Actions;
use Filament\Tests\Infolists\Fixtures\Livewire;
use Filament\Tests\TestCase;
use Illuminate\Support\Str;

use function Filament\Tests\livewire;

uses(TestCase::class);

it('can call an action with data', function () {
    livewire(Actions::class)
        ->callInfolistAction('textEntry', 'setValue', data: [
            'value' => $value = Str::random(),
        ])
        ->assertDispatched('foo', $value);
});

it('can validate an action\'s data', function () {
    livewire(Actions::class)
        ->callInfolistAction('textEntry', 'setValue', data: [
            'value' => null,
        ])
        ->assertHasInfolistActionErrors(['value' => ['required']])
        ->assertNotDispatched('foo');
});

it('can set default action data when mounted', function () {
    livewire(Actions::class)
        ->mountInfolistAction('textEntry', 'setValue')
        ->assertInfolistActionDataSet([
            'value' => 'foo',
        ]);
});

it('can call an action with arguments', function () {
    livewire(Actions::class)
        ->callInfolistAction('textEntry', 'setValueFromArguments', arguments: [
            'value' => $value = Str::random(),
        ])
        ->assertDispatched('foo', $value);
});

it('can call an action and halt', function () {
    livewire(Actions::class)
        ->callInfolistAction('textEntry', 'halt')
        ->assertInfolistActionHalted('textEntry', 'halt');
});

it('can hide an action', function () {
    livewire(Actions::class)
        ->assertInfolistActionVisible('textEntry', 'visible')
        ->assertInfolistActionHidden('textEntry', 'hidden');
});

it('can disable an action', function () {
    livewire(Actions::class)
        ->assertInfolistActionEnabled('textEntry', 'enabled')
        ->assertInfolistActionDisabled('textEntry', 'disabled');
});

it('can have an icon', function () {
    livewire(Actions::class)
        ->assertInfolistActionHasIcon('textEntry', 'hasIcon', 'heroicon-m-pencil-square')
        ->assertInfolistActionDoesNotHaveIcon('textEntry', 'hasIcon', 'heroicon-m-trash');
});

it('can have a label', function () {
    livewire(Actions::class)
        ->assertInfolistActionHasLabel('textEntry', 'hasLabel', 'My Action')
        ->assertInfolistActionDoesNotHaveLabel('textEntry', 'hasLabel', 'My Other Action');
});

it('can have a color', function () {
    livewire(Actions::class)
        ->assertInfolistActionHasColor('textEntry', 'hasColor', 'primary')
        ->assertInfolistActionDoesNotHaveColor('textEntry', 'hasColor', 'gray');
});

it('can have a URL', function () {
    livewire(Actions::class)
        ->assertInfolistActionHasUrl('textEntry', 'url', 'https://filamentphp.com')
        ->assertInfolistActionDoesNotHaveUrl('textEntry', 'url', 'https://google.com');
});

it('can open a URL in a new tab', function () {
    livewire(Actions::class)
        ->assertInfolistActionShouldOpenUrlInNewTab('textEntry', 'urlInNewTab')
        ->assertInfolistActionShouldNotOpenUrlInNewTab('textEntry', 'urlNotInNewTab');
});

it('can state whether a form component action exists', function () {
    livewire(Actions::class)
        ->assertInfolistActionExists('textEntry', 'exists')
        ->assertInfolistActionDoesNotExist('textEntry', 'doesNotExist');
});

it('only restores available infolist action chains and their forms', function (string $restriction, array $mountedActions): void {
    $component = livewire(RestoredInfolistActions::class, ['restriction' => $restriction])
        ->set('mountedInfolistActionsInfolist', 'infolist')
        ->set('mountedInfolistActionsComponent', 'textEntry')
        ->set('mountedInfolistActions', $mountedActions)
        ->call('$refresh');

    expect($component->instance()->getMountedInfolistAction() !== null)->toBe($restriction === '');

    if (($restriction === '') && (count($mountedActions) > 1)) {
        $component->call('callMountedInfolistAction')->assertDispatched('restored-infolist-child-called')
            ->assertSet('mountedInfolistActions', []);
    }

    if ($restriction !== '') {
        $component->call('callMountedInfolistAction')->assertNotDispatched('restored-infolist-child-called')
            ->assertSet('mountedInfolistActions', []);

        expect($component->instance()->getForm('mountedInfolistActionForm'))->toBeNull();
    }
})->with(['', 'hidden', 'invisible', 'disabled', 'unauthorized'])->with([
    'root' => [['restricted']],
    'nested' => [['restricted', 'child']],
    'sparse nested' => [[1 => 'restricted', 2 => 'child']],
]);

it('preserves parent edits when mounting an unavailable infolist child', function (string $actionName): void {
    livewire(RestoredInfolistActions::class)
        ->mountInfolistAction('textEntry', 'restricted')
        ->setInfolistActionData(['value' => 'edited value'])
        ->call('mountInfolistAction', $actionName)
        ->call('$refresh')
        ->assertSet('mountedInfolistActions', ['restricted'])
        ->assertInfolistActionDataSet(['value' => 'edited value']);
})->with(['missing', 'unavailable']);

class RestoredInfolistActions extends Actions
{
    public string $restriction = '';

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist->state([])->schema([
            TextEntry::make('textEntry')->key('textEntry')->registerActions([
                Action::make('restricted')
                    ->hidden(fn (): bool => $this->restriction === 'hidden')
                    ->visible(fn (): bool => $this->restriction !== 'invisible')
                    ->disabled(fn (): bool => $this->restriction === 'disabled')
                    ->authorize(fn (): bool => $this->restriction !== 'unauthorized')
                    ->form([TextInput::make('value')])
                    ->extraModalFooterActions([
                        Action::make('unavailable')->disabled(),
                        Action::make('child')
                            ->form([TextInput::make('child_value')])
                            ->disabled(fn (): bool => $this->restriction === 'completed')
                            ->cancelParentActions()
                            ->action(function (): void {
                                $this->restriction = 'completed';
                                $this->dispatch('restored-infolist-child-called');
                            }),
                    ]),
            ]),
        ]);
    }
}

it('can address actions in repeated items without unsafe keys leaking into click handlers', function (string $itemKey, string $stateType): void {
    $items = [
        $itemKey => ['name' => 'First item'],
        'item-' . hash('sha256', $itemKey) => ['name' => 'Reserved item'],
        'safe' => ['name' => 'Last item'],
    ];
    $infolist = Infolist::make(Livewire::make())->name('test')->schema([
        $entry = RepeatableEntry::make('items')->state(static fn () => match ($stateType) {
            'collection' => collect($items),
            'generator' => (static function () use ($items): Generator {
                yield from $items;
            })(),
            default => $items,
        })->schema([
            TextEntry::make('name')
                ->key(static fn (TextEntry $component): string => $component->getContainer()->getStatePath() . '.name')
                ->suffixAction(Action::make('inspect')),
        ]),
    ]);
    $infolist->getComponents();
    $containers = $entry->getChildComponentContainers();

    expect(array_keys($containers))->toBe(array_keys($items));
    $componentKeys = [];

    foreach ($containers as $itemKey => $container) {
        $component = $container->getComponents()[0];
        $handler = $component->getAction('inspect')->getLivewireClickHandler();

        expect(preg_match("/^mountInfolistAction\\('inspect', '([^'\\\\]+)', 'test'\\)$/", $handler, $matches))->toBe(1);
        expect($infolist->getComponent($matches[1])->getContainer()->getState())->toBe($items[$itemKey]);
        $componentKeys[] = $matches[1];
    }

    expect(array_unique($componentKeys))->toHaveCount(count($items));
})->with(["part'one", 'a.b', '{first}', '', 'key&#39;value', 'key\\value', 'key${globalThis[99]=true}'])->with(['array', 'collection', 'generator']);
