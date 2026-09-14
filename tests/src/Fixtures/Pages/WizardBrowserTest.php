<?php

namespace Filament\Tests\Fixtures\Pages;

use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Pages\Page;
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

    public function mount(): void
    {
        $this->form->fill();
    }

    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                Wizard::make([
                    Step::make('Basic Details')
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

                    Step::make('Contact Information'),
                ])
                    ->nextAction(static fn (Action $action): Action => $action->extraAttributes([
                        'data-testid' => 'wizard-next-action',
                    ]))
                    ->key('wizard'),
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
