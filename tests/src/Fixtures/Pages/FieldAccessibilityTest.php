<?php

namespace Filament\Tests\Fixtures\Pages;

use Filament\Forms\Components\Builder;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\CodeEditor;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\OneTimeCodeInput;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Slider;
use Filament\Forms\Components\TagsInput;
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

    public ?array $additionalData = [];

    public bool $saved = false;

    public bool $additionalValidationFails = false;

    public bool $additionalSaved = false;

    public function mount(): void
    {
        $this->form->fill();
        $this->additionalForm->fill();
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

    public function additionalForm(Schema $form): Schema
    {
        $fields = [
            CodeEditor::make('code')->default('const message = "Welcome";')->fieldWrapperView('test-plugin-wrapper'),
            MarkdownEditor::make('markdown')->default('Welcome to the team.')->toolbarButtons(['bold', 'italic']),
            RichEditor::make('rich')->default('<p>Welcome to the team.</p>')->toolbarButtons(['bold', 'italic']),
            DateTimePicker::make('date')->native(false)->default('2026-10-11 14:30:00'),
            Slider::make('slider')->default(35),
            Slider::make('range')->default([20, 65]),
            ColorPicker::make('color')->default('#336699'),
            OneTimeCodeInput::make('code_digits')->default('123456'),
            TagsInput::make('tags')->default(['Laravel']),
            KeyValue::make('metadata')->default(['Team' => 'Membership']),
            Repeater::make('members')->schema([TextInput::make('name')])->default([['name' => 'Alex Morgan']]),
            Builder::make('blocks')->blocks([Builder\Block::make('paragraph')->schema([TextInput::make('text')])])
                ->default([['type' => 'paragraph', 'data' => ['text' => 'Welcome']]]),
            FileUpload::make('attachment'),
            FileUpload::make('custom_attachment')->extraInputAttributes(['aria-describedby' => 'custom-file-note', 'aria-invalid' => null]),
            FileUpload::make('composed_attachment')->extraInputAttributes(static fn (FileUpload $component): array => [
                'aria-describedby' => e(implode(' ', ['custom-file-note', ...$component->getDescriptionIds()])),
            ]),
        ];

        foreach ($fields as $field) {
            if ($field instanceof FileUpload) {
                $field->required(fn (): bool => $this->additionalValidationFails);
            } else {
                $field->rule('prohibited', fn (): bool => $this->additionalValidationFails);
            }

            $field->helperText(fn (): ?string => $this->additionalSaved ? null : 'Instructions for ' . $field->getName())
                ->extraAttributes(['data-testid' => 'additional-' . $field->getName()]);
        }

        return $form->statePath('additionalData')->components($fields);
    }

    public function validateAdditionalFields(): void
    {
        $this->additionalValidationFails = true;
        $this->additionalForm->getState();
    }

    public function acceptAdditionalFields(): void
    {
        $this->additionalValidationFails = false;
        $this->additionalForm->getState();
        $this->additionalSaved = true;
    }
}
