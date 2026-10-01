<?php

namespace Filament\Tests\Fixtures\Pages;

use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Pages\Page;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Wizard;
use Filament\Schemas\Components\Wizard\Step;
use Filament\Schemas\Schema;
use Filament\Support\Components\Attributes\ExposedLivewireMethod;
use Filament\Support\Icons\Heroicon;
use Livewire\Attributes\Renderless;

use function Amp\delay;

class WizardBrowserTest extends Page
{
    protected string $view = 'pages.wizard-browser-test';

    protected static string | BackedEnum | null $navigationIcon = Heroicon::OutlinedMapPin;

    protected static ?int $navigationSort = 16;

    public ?array $data = [];

    public bool $hideContactAfterValidation = false;

    public bool $contactCompleted = false;

    public function mount(): void
    {
        $this->hideContactAfterValidation = request()->boolean('hide_contact_after_validation');
        $this->form->fill();
    }

    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                Wizard::make([
                    Step::make('Basic Details')
                        ->key('details')
                        ->id('profile-details')
                        ->beforeValidation(static function (): void {
                            delay(1);
                        })
                        ->schema([
                            WizardBrowserTestSelect::make('status')
                                ->options([
                                    'draft' => 'Draft',
                                    'published' => 'Published',
                                ])
                                ->dynamicOptions()
                                ->native(false)
                                ->extraAttributes(['data-testid' => 'wizard-dynamic-select']),
                        ]),

                    Step::make('Contact Information')->key('contact')->id('profile-contact')
                        ->hidden(fn (): bool => $this->contactCompleted)
                        ->afterValidation(function (): void {
                            $this->contactCompleted = $this->hideContactAfterValidation;
                        }),
                    Step::make('Review')->key('review')->id('profile-review')
                        ->schema([TextInput::make('notes')->label('Review notes')]),
                ])
                    ->id('profile-wizard')
                    ->persistStepInQueryString()
                    ->nextAction(static fn (Action $action): Action => $action->extraAttributes([
                        'data-testid' => 'wizard-next-action',
                    ]))
                    ->key('wizard'),
                Group::make([
                    Wizard::make([
                        Step::make('Basic Details')->key('details')->id('delivery-details'),
                        Step::make('Contact Information')->key('contact')->id('delivery-contact'),
                    ])
                        ->key('wizard')
                        ->id('delivery-wizard')
                        ->persistStepInQueryString('delivery_step')
                        ->nextAction(static fn (Action $action): Action => $action->extraAttributes([
                            'data-testid' => 'delivery-next-action',
                        ])),
                ])->key('delivery'),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $this->form->getState();
    }
}

class WizardBrowserTestSelect extends Select
{
    #[ExposedLivewireMethod]
    #[Renderless]
    public function getOptionsForJs(): array
    {
        delay(1);

        return parent::getOptionsForJs();
    }
}
