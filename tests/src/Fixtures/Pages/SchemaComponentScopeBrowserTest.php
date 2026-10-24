<?php

namespace Filament\Tests\Fixtures\Pages;

use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\RepeatableEntry\TableColumn;
use Filament\Pages\Page;
use Filament\Schemas\Components\Flex;
use Filament\Schemas\Components\Html;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Components\UnorderedList;
use Filament\Schemas\Components\Wizard;
use Filament\Schemas\Components\Wizard\Step;
use Filament\Schemas\Schema;
use Filament\Support\Components\Attributes\Exposed;
use Filament\Tests\Fixtures\Schemas\Components\BladeMethodEntry;
use Filament\Tests\Fixtures\Schemas\Components\BladeMethodField;
use Livewire\Attributes\Locked;

class SchemaComponentScopeBrowserTest extends Page
{
    protected string $view = 'pages.schema-component-scope-browser-test';

    protected static bool $shouldRegisterNavigation = false;

    public array $data = [];

    public bool $reversed = false;

    public int $incorrectStepCalls = 0;

    #[Locked]
    public string $scenario = '';

    public function mount(): void
    {
        $this->scenario = request('scenario', 'layouts');
        $this->form->fill([
            'caption' => 'Root "caption" <em>literal</em>',
            'items' => [
                ['caption' => 'First caption', 'field' => 'First field'],
                ['caption' => 'Second caption', 'field' => 'Second field'],
            ],
        ]);
    }

    public function removeFirst(): void
    {
        unset($this->data['items'][0]);
    }

    public function form(Schema $schema): Schema
    {
        $schema->statePath('data');

        if ($this->scenario === 'flex') {
            $fields = [BladeMethodField::make('first'), BladeMethodField::make('second')];

            return $schema->components([
                Flex::make($this->reversed ? array_reverse($fields) : $fields),
            ]);
        }

        if (in_array($this->scenario, ['tabs', 'wizard'])) {
            // Handle the public events on the panel itself, not a child component's scope.
            $attributes = [
                'x-on:named-scope.stop' => '$el.querySelector(\'output\').textContent = await $scopeLabel({ source: \'Named\' })',
                'x-on:general-scope.stop' => '$el.querySelector(\'output\').textContent = await $callSchemaComponentMethod(\'scopeLabel\', { source: \'General\' })',
            ];
            $buttons = static fn (): Html => Html::make(<<<'HTML'
                <div>
                    <button type="button" data-testid="named-scope" x-on:click="$dispatch('named-scope')">Named scope call</button>
                    <button type="button" data-testid="general-scope" x-on:click="$dispatch('general-scope')">General scope call</button>
                    <output></output>
                </div>
                HTML);

            if ($this->scenario === 'tabs') {
                return $schema->components([
                    Tabs::make('Locations')->id('method-tabs')->tabs([
                        ScopeMethodTab::make('First tab')->key('first')->id('first-tab')
                            ->extraAttributes($attributes)->schema([$buttons()]),
                        ScopeMethodTab::make('Second tab')->key('second')->id('second-tab')
                            ->extraAttributes($attributes)->schema([$buttons()]),
                    ]),
                ]);
            }

            return $schema->components([
                Wizard::make([
                    ScopeMethodStep::make('First step')->id('first-step')->extraAttributes($attributes)->schema([
                        TextInput::make('first_focus')->id('first-focus')->autofocus()->required(),
                        $buttons(),
                    ]),
                    ScopeMethodStep::make('Second step')->id('second-step')->extraAttributes($attributes)->schema([
                        TextInput::make('second_focus')->id('second-focus')->autofocus(),
                        $buttons(),
                    ]),
                ])
                    ->nextAction(static fn (Action $action): Action => $action->extraAttributes(['data-testid' => 'next-step']))
                    ->previousAction(static fn (Action $action): Action => $action->extraAttributes(['data-testid' => 'previous-step']))
                    ->alpineSubmitHandler('$wire.$refresh()'),
            ]);
        }

        $embeddedContent = static fn (): Html => Html::make(<<<'HTML'
            <div data-embedded-scope>
                <span x-text="$get('caption')"></span>
                <button type="button" x-on:click="$set('caption', 'Embedded café', false, true)">Set sibling caption</button>
            </div>
            HTML)->liberatedFromContainerGrid();

        return $schema->components([
            TextInput::make('caption'),
            UnorderedList::make([
                Select::make('choice')->searchable()->live()
                    ->getSearchResultsUsing(static fn (string $search): array => ['chosen' => 'Match ' . $search])
                    ->getOptionLabelUsing(static fn (): string => 'Chosen option')
                    ->extraAttributes(['data-testid' => 'list-select']),
                BladeMethodField::make('list'),
            ]),
            BladeMethodField::make('liberated')->liberatedFromContainerGrid()
                ->belowContent($embeddedContent()),
            RepeatableEntry::make('items')->state(fn (): array => $this->data['items'])
                ->table([TableColumn::make('Field'), TableColumn::make('Caption')])->schema([
                    BladeMethodField::make('field')->belowContent($embeddedContent()),
                    BladeMethodEntry::make('caption'),
                ]),
        ]);
    }
}

class ScopeMethodTab extends Tab
{
    #[Exposed]
    public function scopeLabel(string $source): string
    {
        return 'Tab: ' . $this->getLabel() . ': ' . $source;
    }
}

class ScopeMethodStep extends Step
{
    #[Exposed]
    public function nextStep(int $currentStepIndex): void
    {
        $this->getLivewire()->incorrectStepCalls++;
    }

    #[Exposed]
    public function scopeLabel(string $source): string
    {
        return 'Step: ' . $this->getLabel() . ': ' . $source;
    }
}
