<?php

namespace Filament\Tests\Fixtures\Pages;

use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ToggleButtons;
use Filament\Pages\Page;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Text;
use Filament\Schemas\Schema;

class FieldAccessibilityTest extends Page
{
    protected string $view = 'pages.field-accessibility-test';

    protected static bool $shouldRegisterNavigation = false;

    public ?array $data = [];

    public bool $saved = false;

    public function mount(): void
    {
        $this->form->fill();
    }

    public function form(Schema $form): Schema
    {
        return $form->statePath('data')->components([
            TextInput::make('name')->label('Full name')->required()->minLength(3)
                ->afterLabel('Use your legal name')
                ->helperText('This appears on your membership card.')
                ->extraInputAttributes(['data-testid' => 'name']),
            Textarea::make('notes')->label('Delivery notes')->hiddenLabel()
                ->belowContent('Tell us where to leave your parcel.')
                ->extraInputAttributes(['data-testid' => 'notes', 'aria-describedby' => 'privacy-note']),
            TextInput::make('reference')->default('MEM-1042')->readOnly()->required()
                ->helperText('Assigned when you joined.')
                ->extraInputAttributes(['data-testid' => 'reference']),
            TextInput::make('archived')->default('Original membership')->disabled()->required()
                ->helperText('No longer editable.')
                ->extraInputAttributes(['data-testid' => 'archived']),
            Select::make('delivery')->options(['post' => 'Post', 'collect' => 'Collection'])->required()
                ->live()->partiallyRenderAfterStateUpdated()
                ->helperText(static fn (?string $state): ?string => blank($state) ? 'Choose how to receive your card.' : null)
                ->extraInputAttributes(['data-testid' => 'delivery']),
            Checkbox::make('consent')->label('I agree to the terms')->required()->accepted()
                ->helperText('Read the membership terms before agreeing.')
                ->extraInputAttributes(['data-testid' => 'consent']),
            Radio::make('contact')->options(['email' => 'Email', 'post' => 'Letter'])->required()
                ->fieldWrapperView('test-plugin-wrapper')
                ->afterLabel(Text::make('Choose one'))->helperText('How we contact you about membership.')
                ->extraAttributes(['data-testid' => 'contact']),
            CheckboxList::make('interests')->options(['gardening' => 'Gardening', 'walking' => 'Walking'])
                ->required()->helperText(fn (): ?string => $this->saved ? null : 'Choose the activities you enjoy.')
                ->extraAlpineAttributes(['data-testid' => 'interests']),
            ToggleButtons::make('days')->options(['weekday' => 'Weekdays', 'weekend' => 'Weekends'])
                ->multiple()->grouped()->required()->helperText('Choose when you are available.')
                ->extraAttributes(['data-testid' => 'days']),
            Group::make([
                TextInput::make('first.name')->label('First guest')->required()->helperText('Name on the first guest pass.')
                    ->extraInputAttributes(['data-testid' => 'guest-first']),
                TextInput::make('second.name')->label('Second guest')->required()->helperText('Name on the second guest pass.')
                    ->extraInputAttributes(['data-testid' => 'guest-second']),
            ])->statePath('guests'),
        ]);
    }

    public function save(): void
    {
        $this->form->getState();
        $this->saved = true;
    }
}
