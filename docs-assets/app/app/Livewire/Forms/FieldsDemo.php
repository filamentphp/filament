<?php

namespace App\Livewire\Forms;

use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Components\Builder;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\CodeEditor;
use Filament\Forms\Components\CodeEditor\Enums\Language;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Slider;
use Filament\Forms\Components\Slider\Enums\PipsMode;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\ToggleButtons;
use Filament\Schemas\Components\Flex;
use Filament\Schemas\Components\FusedGroup;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Icon;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Text;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Schemas\Schema;
use Filament\Support\Enums\FontWeight;
use Filament\Support\Icons\Heroicon;
use Filament\Support\RawJs;
use Illuminate\Support\HtmlString;
use Livewire\Component;

class FieldsDemo extends Component implements HasActions, HasSchemas
{
    use InteractsWithActions;
    use InteractsWithSchemas;

    public $data = [];

    public function mount(): void
    {
        $this->form->fill();
        $this->validate(
            ['data.aboveErrorMessage' => ['required'], 'data.belowErrorMessage' => ['required']],
            attributes: ['data.aboveErrorMessage' => 'name', 'data.belowErrorMessage' => 'name'],
        );
    }

    public function form(Schema $form): Schema
    {
        return $form
            ->statePath('data')
            ->components([
                Group::make()
                    ->id('simple')
                    ->extraAttributes([
                        'class' => 'p-16 max-w-xl',
                    ])
                    ->schema([
                        TextInput::make('simple')
                            ->label('Name')
                            ->default('Dan Harrin'),
                    ]),
                Group::make()
                    ->id('disabled')
                    ->extraAttributes([
                        'class' => 'p-16 max-w-xl',
                    ])
                    ->schema([
                        TextInput::make('disabled')
                            ->label('Name')
                            ->disabled()
                            ->default('Dan Harrin'),
                    ]),
                Group::make()
                    ->id('inlineLabel')
                    ->extraAttributes([
                        'class' => 'p-16 max-w-xl',
                    ])
                    ->schema([
                        TextInput::make('inlineLabel')
                            ->label('Name')
                            ->inlineLabel(),
                    ]),
                Group::make()
                    ->id('inlineLabelSection')
                    ->extraAttributes([
                        'class' => 'p-16 max-w-xl',
                    ])
                    ->schema([
                        Section::make('Details')
                            ->schema([
                                TextInput::make('inlineLabelSectionName')
                                    ->label('Name'),
                                TextInput::make('inlineLabelSectionEmail')
                                    ->label('Email address'),
                                TextInput::make('inlineLabelSectionPhone')
                                    ->label('Phone number'),
                            ])
                            ->inlineLabel(),
                    ]),
                Group::make()
                    ->id('placeholder')
                    ->extraAttributes([
                        'class' => 'p-16 max-w-xl',
                    ])
                    ->schema([
                        TextInput::make('placeholder')
                            ->label('Name')
                            ->placeholder('Dan Harrin'),
                    ]),
                Group::make()
                    ->id('fused')
                    ->extraAttributes([
                        'class' => 'p-16 max-w-xl',
                    ])
                    ->schema([
                        FusedGroup::make([
                            TextInput::make('city')
                                ->placeholder('City'),
                            Select::make('country')
                                ->placeholder('Country'),
                        ]),
                    ]),
                Group::make()
                    ->id('fusedLabel')
                    ->extraAttributes([
                        'class' => 'p-16 max-w-xl',
                    ])
                    ->schema([
                        FusedGroup::make([
                            TextInput::make('city')
                                ->placeholder('City'),
                            Select::make('country')
                                ->placeholder('Country'),
                        ])
                            ->label('Location'),
                    ]),
                Group::make()
                    ->id('fusedColumns')
                    ->extraAttributes([
                        'class' => 'p-16 max-w-xl',
                    ])
                    ->schema([
                        FusedGroup::make([
                            TextInput::make('city')
                                ->placeholder('City'),
                            Select::make('country')
                                ->placeholder('Country'),
                        ])
                            ->label('Location')
                            ->columns(2),
                    ]),
                Group::make()
                    ->id('fusedColumnsSpan')
                    ->extraAttributes([
                        'class' => 'p-16 max-w-xl',
                    ])
                    ->schema([
                        FusedGroup::make([
                            TextInput::make('city')
                                ->placeholder('City')
                                ->columnSpan(2),
                            Select::make('country')
                                ->placeholder('Country'),
                        ])
                            ->label('Location')
                            ->columns(3),
                    ]),
                Group::make()
                    ->id('textBelowContent')
                    ->extraAttributes([
                        'class' => 'p-16 max-w-xl',
                    ])
                    ->schema([
                        TextInput::make('name')
                            ->belowContent('This is the user\'s full name.'),
                    ]),
                Group::make()
                    ->id('componentBelowContent')
                    ->extraAttributes([
                        'class' => 'p-16 max-w-xl',
                    ])
                    ->schema([
                        TextInput::make('name')
                            ->belowContent(Text::make('This is the user\'s full name.')->weight(FontWeight::Bold)),
                    ]),
                Group::make()
                    ->id('actionBelowContent')
                    ->extraAttributes([
                        'class' => 'p-16 max-w-xl',
                    ])
                    ->schema([
                        TextInput::make('name')
                            ->belowContent(Action::make('generate')),
                    ]),
                Group::make()
                    ->id('belowContent')
                    ->extraAttributes([
                        'class' => 'p-16 max-w-xl',
                    ])
                    ->schema([
                        TextInput::make('name')
                            ->belowContent([
                                Icon::make(Heroicon::InformationCircle),
                                'This is the user\'s full name.',
                                Action::make('generate'),
                            ]),
                    ]),
                Group::make()
                    ->id('belowContentAlignment')
                    ->extraAttributes([
                        'class' => 'p-16 max-w-xl',
                    ])
                    ->schema([
                        TextInput::make('name')
                            ->belowContent(Schema::end([
                                Icon::make(Heroicon::InformationCircle),
                                'This is the user\'s full name.',
                                Action::make('generate'),
                            ])),
                        TextInput::make('name')
                            ->belowContent(Schema::between([
                                Icon::make(Heroicon::InformationCircle),
                                'This is the user\'s full name.',
                                Action::make('generate'),
                            ])),
                        TextInput::make('name')
                            ->belowContent(Schema::between([
                                Flex::make([
                                    Icon::make(Heroicon::InformationCircle)
                                        ->grow(false),
                                    'This is the user\'s full name.',
                                ]),
                                Action::make('generate'),
                            ])),
                    ]),
                Group::make()
                    ->id('aboveLabel')
                    ->extraAttributes([
                        'class' => 'p-16 max-w-xl',
                    ])
                    ->schema([
                        TextInput::make('aboveLabel')
                            ->label('Name')
                            ->aboveLabel([
                                Icon::make(Heroicon::Star),
                                'This is the content above the field\'s label',
                            ]),
                    ]),
                Group::make()
                    ->id('beforeLabel')
                    ->extraAttributes([
                        'class' => 'p-16 max-w-xl',
                    ])
                    ->schema([
                        TextInput::make('beforeLabel')
                            ->label('Name')
                            ->beforeLabel(Icon::make(Heroicon::Star)),
                    ]),
                Group::make()
                    ->id('afterLabel')
                    ->extraAttributes([
                        'class' => 'p-16 max-w-xl',
                    ])
                    ->schema([
                        TextInput::make('afterLabel')
                            ->label('Name')
                            ->afterLabel([
                                Icon::make(Heroicon::Star),
                                'This is the content after the field\'s label',
                            ]),
                    ]),
                Group::make()
                    ->id('afterLabelAlignedStart')
                    ->extraAttributes([
                        'class' => 'p-16 max-w-xl',
                    ])
                    ->schema([
                        TextInput::make('afterLabelAlignedStart')
                            ->label('Name')
                            ->afterLabel(Schema::start([
                                Icon::make(Heroicon::Star),
                                'This is the content after the field\'s label',
                            ])),
                    ]),
                Group::make()
                    ->id('belowLabel')
                    ->extraAttributes([
                        'class' => 'p-16 max-w-xl',
                    ])
                    ->schema([
                        TextInput::make('belowLabel')
                            ->label('Name')
                            ->belowLabel([
                                Icon::make(Heroicon::Star),
                                'This is the content below the field\'s label',
                            ]),
                    ]),
                Group::make()
                    ->id('aboveContent')
                    ->extraAttributes([
                        'class' => 'p-16 max-w-xl',
                    ])
                    ->schema([
                        TextInput::make('aboveContent')
                            ->label('Name')
                            ->belowLabel([
                                Icon::make(Heroicon::Star),
                                'This is the content above the field\'s content',
                            ]),
                    ]),
                Group::make()
                    ->id('beforeContent')
                    ->extraAttributes([
                        'class' => 'p-16 max-w-xl',
                    ])
                    ->schema([
                        TextInput::make('beforeContent')
                            ->label('Name')
                            ->beforeContent(Icon::make(Heroicon::Star)),
                    ]),
                Group::make()
                    ->id('afterContent')
                    ->extraAttributes([
                        'class' => 'p-16 max-w-xl',
                    ])
                    ->schema([
                        TextInput::make('afterContent')
                            ->label('Name')
                            ->afterContent(Icon::make(Heroicon::Star)),
                    ]),
                Group::make()
                    ->id('aboveErrorMessage')
                    ->extraAttributes([
                        'class' => 'p-16 max-w-xl',
                    ])
                    ->schema([
                        TextInput::make('aboveErrorMessage')
                            ->label('Name')
                            ->required()
                            ->aboveErrorMessage([
                                Icon::make(Heroicon::Star),
                                'This is the content above the field\'s error message',
                            ]),
                    ]),
                Group::make()
                    ->id('belowErrorMessage')
                    ->extraAttributes([
                        'class' => 'p-16 max-w-xl',
                    ])
                    ->schema([
                        TextInput::make('belowErrorMessage')
                            ->label('Name')
                            ->required()
                            ->belowErrorMessage([
                                Icon::make(Heroicon::Star),
                                'This is the content below the field\'s error message',
                            ]),
                    ]),
                Group::make()
                    ->id('textInput')
                    ->extraAttributes([
                        'class' => 'p-16 max-w-xl',
                    ])
                    ->schema([
                        TextInput::make('textInput')
                            ->label('Name')
                            ->default('Dan Harrin'),
                    ]),
                Group::make()
                    ->id('textInputAffix')
                    ->extraAttributes([
                        'class' => 'p-16 max-w-xl',
                    ])
                    ->schema([
                        TextInput::make('textInputAffix')
                            ->label('Domain')
                            ->default('filamentphp')
                            ->prefix('https://')
                            ->suffix('.com'),
                    ]),
                Group::make()
                    ->id('textInputSuffixIcon')
                    ->extraAttributes([
                        'class' => 'p-16 max-w-xl',
                    ])
                    ->schema([
                        TextInput::make('textInputSuffixIcon')
                            ->label('Domain')
                            ->default('https://filamentphp.com')
                            ->suffixIcon(Heroicon::GlobeAlt),
                    ]),
                Group::make()
                    ->id('textInputRevealablePassword')
                    ->extraAttributes([
                        'class' => 'p-16 max-w-xl',
                    ])
                    ->schema([
                        TextInput::make('textInputRevealablePassword')
                            ->label('Password')
                            ->default('filament123')
                            ->password()
                            ->revealable(),
                        TextInput::make('textInputRevealedPassword')
                            ->label('Password')
                            ->default('filament123')
                            ->suffixActions([
                                TextInput\Actions\HidePasswordAction::make()
                                    ->extraAttributes([]),
                            ]),
                    ]),
                Group::make()
                    ->id('select')
                    ->extraAttributes([
                        'class' => 'p-16 max-w-xl',
                    ])
                    ->schema([
                        Select::make('select')
                            ->label('Status'),
                    ]),
                Group::make()
                    ->id('javascriptSelect')
                    ->extraAttributes([
                        'class' => 'px-16 pt-16 pb-48 max-w-xl',
                    ])
                    ->schema([
                        Select::make('javascriptSelect')
                            ->label('Status')
                            ->native(false)
                            ->options([
                                'draft' => 'Draft',
                                'reviewing' => 'Reviewing',
                                'published' => 'Published',
                            ]),
                    ]),
                Group::make()
                    ->id('searchableSelect')
                    ->extraAttributes([
                        'class' => 'px-16 pt-16 pb-72 max-w-xl',
                    ])
                    ->schema([
                        Select::make('searchableSelect')
                            ->label('Author')
                            ->searchable()
                            ->options([
                                'dan' => 'Dan Harrin',
                                'ryan' => 'Ryan Chandler',
                                'zep' => 'Zep Fietje',
                                'dennis' => 'Dennis Koch',
                                'adam' => 'Adam Weston',
                            ]),
                    ]),
                Group::make()
                    ->id('multipleSelect')
                    ->extraAttributes([
                        'class' => 'px-16 pt-16 pb-44 max-w-xl',
                    ])
                    ->schema([
                        Select::make('multipleSelect')
                            ->label('Technologies')
                            ->multiple()
                            ->options([
                                'tailwind' => 'Tailwind CSS',
                                'alpine' => 'Alpine.js',
                                'laravel' => 'Laravel',
                                'livewire' => 'Laravel Livewire',
                            ]),
                    ]),
                Group::make()
                    ->id('groupedSelect')
                    ->extraAttributes([
                        'class' => 'px-16 pt-16 pb-96 max-w-xl',
                    ])
                    ->schema([
                        Select::make('groupedSelect')
                            ->label('Status')
                            ->searchable()
                            ->options([
                                'In Process' => [
                                    'draft' => 'Draft',
                                    'reviewing' => 'Reviewing',
                                ],
                                'Reviewed' => [
                                    'published' => 'Published',
                                    'rejected' => 'Rejected',
                                ],
                            ]),
                    ]),
                Group::make()
                    ->id('createSelectOption')
                    ->extraAttributes([
                        'class' => 'p-16 max-w-xl',
                    ])
                    ->schema([
                        Select::make('createSelectOption')
                            ->label('Author')
                            ->createOptionForm([
                                TextInput::make('name'),
                            ]),
                    ]),
                Group::make()
                    ->id('editSelectOption')
                    ->extraAttributes([
                        'class' => 'p-16 max-w-xl',
                    ])
                    ->schema([
                        Select::make('editSelectOption')
                            ->label('Author')
                            ->default('dan')
                            ->options([
                                'dan' => 'Dan Harrin',
                            ])
                            ->fillEditOptionActionFormUsing(fn () => ['name' => 'Dan Harrin'])
                            ->editOptionForm([
                                TextInput::make('name'),
                            ]),
                    ]),
                Group::make()
                    ->id('selectAffix')
                    ->extraAttributes([
                        'class' => 'p-16 max-w-xl',
                    ])
                    ->schema([
                        Select::make('selectAffix')
                            ->label('Domain')
                            ->default('filament')
                            ->options([
                                'filament' => 'filamentphp',
                            ])
                            ->prefix('https://')
                            ->suffix('.com'),
                    ]),
                Group::make()
                    ->id('selectSuffixIcon')
                    ->extraAttributes([
                        'class' => 'p-16 max-w-xl',
                    ])
                    ->schema([
                        Select::make('selectSuffixIcon')
                            ->label('Domain')
                            ->default('filament')
                            ->options([
                                'filament' => 'filamentphp',
                            ])
                            ->suffixIcon(Heroicon::GlobeAlt),
                    ]),
                Group::make()
                    ->id('checkbox')
                    ->extraAttributes([
                        'class' => 'p-16 max-w-xl',
                    ])
                    ->schema([
                        Checkbox::make('checkbox')
                            ->label('Is admin'),
                    ]),
                Group::make()
                    ->id('inlineCheckbox')
                    ->extraAttributes([
                        'class' => 'p-16 max-w-xl',
                    ])
                    ->schema([
                        Checkbox::make('inlineCheckbox')
                            ->label('Is admin')
                            ->inline(),
                    ]),
                Group::make()
                    ->id('notInlineCheckbox')
                    ->extraAttributes([
                        'class' => 'p-16 max-w-xl',
                    ])
                    ->schema([
                        Checkbox::make('notInlineCheckbox')
                            ->label('Is admin')
                            ->inline(false),
                    ]),
                Group::make()
                    ->id('toggle')
                    ->extraAttributes([
                        'class' => 'p-16 max-w-xl',
                    ])
                    ->schema([
                        Toggle::make('toggle')
                            ->label('Is admin'),
                    ]),
                Group::make()
                    ->id('toggleIcons')
                    ->extraAttributes([
                        'class' => 'p-16 max-w-xl',
                    ])
                    ->schema([
                        Toggle::make('toggleIcons')
                            ->label('Is admin')
                            ->onIcon(Heroicon::Bolt)
                            ->offIcon(Heroicon::User),
                    ]),
                Group::make()
                    ->id('toggleOffColor')
                    ->extraAttributes([
                        'class' => 'p-16 max-w-xl',
                    ])
                    ->schema([
                        Toggle::make('toggleOffColor')
                            ->label('Is admin')
                            ->default(false)
                            ->onColor('success')
                            ->offColor('danger'),
                    ]),
                Group::make()
                    ->id('toggleOnColor')
                    ->extraAttributes([
                        'class' => 'p-16 max-w-xl',
                    ])
                    ->schema([
                        Toggle::make('toggleOnColor')
                            ->label('Is admin')
                            ->default(true)
                            ->onColor('success')
                            ->offColor('danger'),
                    ]),
                Group::make()
                    ->id('inlineToggle')
                    ->extraAttributes([
                        'class' => 'p-16 max-w-xl',
                    ])
                    ->schema([
                        Toggle::make('inlineToggle')
                            ->label('Is admin')
                            ->inline(),
                    ]),
                Group::make()
                    ->id('notInlineToggle')
                    ->extraAttributes([
                        'class' => 'p-16 max-w-xl',
                    ])
                    ->schema([
                        Toggle::make('notInlineToggle')
                            ->label('Is admin')
                            ->inline(false),
                    ]),
                Group::make()
                    ->id('checkboxList')
                    ->extraAttributes([
                        'class' => 'p-16 max-w-xl',
                    ])
                    ->schema([
                        CheckboxList::make('checkboxList')
                            ->label('Technologies')
                            ->options([
                                'tailwind' => 'Tailwind CSS',
                                'alpine' => 'Alpine.js',
                                'laravel' => 'Laravel',
                                'livewire' => 'Laravel Livewire',
                            ]),
                    ]),
                Group::make()
                    ->id('checkboxListOptionDescriptions')
                    ->extraAttributes([
                        'class' => 'p-16 max-w-xl',
                    ])
                    ->schema([
                        CheckboxList::make('checkboxListOptionDescriptions')
                            ->label('Technologies')
                            ->options([
                                'tailwind' => 'Tailwind CSS',
                                'alpine' => 'Alpine.js',
                                'laravel' => 'Laravel',
                                'livewire' => 'Laravel Livewire',
                            ])
                            ->descriptions([
                                'tailwind' => 'A utility-first CSS framework for rapidly building modern websites without ever leaving your HTML.',
                                'alpine' => new HtmlString('A rugged, minimal tool for composing behavior <strong>directly in your markup</strong>.'),
                                'laravel' => str('A **web application** framework with expressive, elegant syntax.')->inlineMarkdown()->toHtmlString(),
                                'livewire' => 'A full-stack framework for Laravel building dynamic interfaces simple, without leaving the comfort of Laravel.',
                            ]),
                    ]),
                Group::make()
                    ->id('checkboxListColumns')
                    ->extraAttributes([
                        'class' => 'p-16 max-w-xl',
                    ])
                    ->schema([
                        CheckboxList::make('checkboxListColumns')
                            ->label('Technologies')
                            ->options([
                                'tailwind' => 'Tailwind CSS',
                                'alpine' => 'Alpine.js',
                                'laravel' => 'Laravel',
                                'livewire' => 'Laravel Livewire',
                            ])
                            ->default(['tailwind', 'laravel'])
                            ->columns(2),
                    ]),
                Group::make()
                    ->id('checkboxListRows')
                    ->extraAttributes([
                        'class' => 'p-16 max-w-xl',
                    ])
                    ->schema([
                        CheckboxList::make('checkboxListRows')
                            ->label('Technologies')
                            ->options([
                                'tailwind' => 'Tailwind CSS',
                                'alpine' => 'Alpine.js',
                                'laravel' => 'Laravel',
                                'livewire' => 'Laravel Livewire',
                            ])
                            ->default(['tailwind', 'laravel'])
                            ->columns(2)
                            ->gridDirection('row'),
                    ]),
                Group::make()
                    ->id('searchableCheckboxList')
                    ->extraAttributes([
                        'class' => 'p-16 max-w-xl',
                    ])
                    ->schema([
                        CheckboxList::make('searchableCheckboxList')
                            ->label('Technologies')
                            ->options([
                                'tailwind' => 'Tailwind CSS',
                                'alpine' => 'Alpine.js',
                                'laravel' => 'Laravel',
                                'livewire' => 'Laravel Livewire',
                            ])
                            ->default(['tailwind', 'laravel'])
                            ->searchable(),
                    ]),
                Group::make()
                    ->id('bulkToggleableCheckboxList')
                    ->extraAttributes([
                        'class' => 'p-16 max-w-xl',
                    ])
                    ->schema([
                        CheckboxList::make('bulkToggleableCheckboxList')
                            ->label('Technologies')
                            ->options([
                                'tailwind' => 'Tailwind CSS',
                                'alpine' => 'Alpine.js',
                                'laravel' => 'Laravel',
                                'livewire' => 'Laravel Livewire',
                            ])
                            ->default(['tailwind', 'laravel'])
                            ->bulkToggleable(),
                    ]),
                Group::make()
                    ->id('radio')
                    ->extraAttributes([
                        'class' => 'p-16 max-w-xl',
                    ])
                    ->schema([
                        Radio::make('radio')
                            ->label('Status')
                            ->options([
                                'draft' => 'Draft',
                                'scheduled' => 'Scheduled',
                                'published' => 'Published',
                            ])
                            ->default('draft'),
                    ]),
                Group::make()
                    ->id('radioOptionDescriptions')
                    ->extraAttributes([
                        'class' => 'p-16 max-w-xl',
                    ])
                    ->schema([
                        Radio::make('radioOptionDescriptions')
                            ->label('Status')
                            ->options([
                                'draft' => 'Draft',
                                'scheduled' => 'Scheduled',
                                'published' => 'Published',
                            ])
                            ->descriptions([
                                'draft' => 'Is not visible.',
                                'scheduled' => 'Will be visible.',
                                'published' => 'Is visible.',
                            ])
                            ->default('draft'),
                    ]),
                Group::make()
                    ->id('booleanRadio')
                    ->extraAttributes([
                        'class' => 'p-16 max-w-xl',
                    ])
                    ->schema([
                        Radio::make('booleanRadio')
                            ->label('Like this post?')
                            ->boolean()
                            ->default(true),
                    ]),
                Group::make()
                    ->id('inlineRadio')
                    ->extraAttributes([
                        'class' => 'p-16 max-w-xl',
                    ])
                    ->schema([
                        Radio::make('inlineRadio')
                            ->label('Like this post?')
                            ->boolean()
                            ->inline()
                            ->default(true),
                    ]),
                Group::make()
                    ->id('disabledOptionRadio')
                    ->extraAttributes([
                        'class' => 'p-16 max-w-xl',
                    ])
                    ->schema([
                        Radio::make('disabledOptionRadio')
                            ->label('Status')
                            ->options([
                                'draft' => 'Draft',
                                'scheduled' => 'Scheduled',
                                'published' => 'Published',
                            ])
                            ->default('draft')
                            ->disableOptionWhen(fn (string $value): bool => $value === 'published'),
                    ]),
                Group::make()
                    ->id('dateTimePickers')
                    ->extraAttributes([
                        'class' => 'p-16 max-w-xl',
                    ])
                    ->schema([
                        DateTimePicker::make('dateTimePicker')
                            ->label('Published at'),
                        DatePicker::make('datePickers')
                            ->label('Date of birth'),
                        TimePicker::make('timePicker')
                            ->label('Alarm at'),
                    ]),
                Group::make()
                    ->id('dateTimePickerWithoutSeconds')
                    ->extraAttributes([
                        'class' => 'p-16 max-w-xl',
                    ])
                    ->schema([
                        DateTimePicker::make('dateTimePickerWithoutSeconds')
                            ->label('Published at')
                            ->seconds(false),
                    ]),
                Group::make()
                    ->id('javascriptDateTimePicker')
                    ->extraAttributes([
                        'class' => 'px-16 pt-16 pb-96 max-w-xl',
                    ])
                    ->schema([
                        DatePicker::make('javascriptDateTimePicker')
                            ->label('Date of birth')
                            ->native(false)
                            ->default('2000-01-01'),
                    ]),
                Group::make()
                    ->id('dateTimePickerDisplayFormat')
                    ->extraAttributes([
                        'class' => 'p-16 max-w-xl',
                    ])
                    ->schema([
                        DatePicker::make('dateTimePickerDisplayFormat')
                            ->label('Date of birth')
                            ->native(false)
                            ->displayFormat('d/m/Y')
                            ->default('2000-01-01'),
                    ]),
                Group::make()
                    ->id('dateTimePickerWeekStartsOnSunday')
                    ->extraAttributes([
                        'class' => 'px-16 pt-16 pb-96 max-w-xl',
                    ])
                    ->schema([
                        DatePicker::make('dateTimePickerWeekStartsOnSunday')
                            ->label('Published at')
                            ->native(false)
                            ->weekStartsOnSunday()
                            ->default('2000-01-01'),
                    ]),
                Group::make()
                    ->id('dateTimePickerDisabledDates')
                    ->extraAttributes([
                        'class' => 'px-16 pt-16 pb-96 max-w-xl',
                    ])
                    ->schema([
                        DatePicker::make('dateTimePickerDisabledDates')
                            ->label('Date')
                            ->native(false)
                            ->disabledDates(['2000-01-03', '2000-01-15', '2000-01-20'])
                            ->default('2000-01-01'),
                    ]),
                Group::make()
                    ->id('dateTimePickerAffix')
                    ->extraAttributes([
                        'class' => 'p-16 max-w-xl',
                    ])
                    ->schema([
                        DatePicker::make('dateTimePickerAffix')
                            ->label('Date')
                            ->prefix('Starts')
                            ->suffix('at midnight')
                            ->default('2000-01-01'),
                    ]),
                Group::make()
                    ->id('dateTimePickerPrefixIcon')
                    ->extraAttributes([
                        'class' => 'p-16 max-w-xl',
                    ])
                    ->schema([
                        TimePicker::make('dateTimePickerPrefixIcon')
                            ->label('At')
                            ->prefixIcon(Heroicon::Play)
                            ->default('2000-01-01'),
                    ]),
                Group::make()
                    ->id('fileUpload')
                    ->extraAttributes([
                        'class' => 'p-16 max-w-xl',
                    ])
                    ->schema([
                        FileUpload::make('fileUpload')
                            ->label('Attachment'),
                    ]),
                Group::make()
                    ->id('richEditor')
                    ->extraAttributes([
                        'class' => 'p-16 max-w-5xl',
                    ])
                    ->schema([
                        RichEditor::make('richEditor')
                            ->label('Content'),
                    ]),
                Group::make()
                    ->id('markdownEditor')
                    ->extraAttributes([
                        'class' => 'p-16 max-w-5xl',
                    ])
                    ->schema([
                        MarkdownEditor::make('markdownEditor')
                            ->label('Content'),
                    ]),
                Group::make()
                    ->id('repeater')
                    ->extraAttributes([
                        'class' => 'p-16 max-w-5xl',
                    ])
                    ->schema([
                        Repeater::make('repeater')
                            ->label('Members')
                            ->schema([
                                TextInput::make('name')->required(),
                                Select::make('role')
                                    ->options([
                                        'member' => 'Member',
                                        'administrator' => 'Administrator',
                                        'owner' => 'Owner',
                                    ])
                                    ->required(),
                            ])
                            ->columns(2)
                            ->default([
                                [
                                    'name' => 'Dan Harrin',
                                    'role' => 'owner',
                                ],
                                [
                                    'name' => 'Ryan Chandler',
                                    'role' => 'administrator',
                                ],
                                [
                                    'name' => 'Zep Fietje',
                                    'role' => 'member',
                                ],
                                [
                                    'name' => null,
                                    'role' => null,
                                ],
                            ]),
                    ]),
                Group::make()
                    ->id('repeaterTable')
                    ->extraAttributes([
                        'class' => 'p-16 max-w-5xl',
                    ])
                    ->schema([
                        Repeater::make('repeaterTable')
                            ->label('Members')
                            ->table([
                                Repeater\TableColumn::make('Name'),
                                Repeater\TableColumn::make('Role'),
                            ])
                            ->schema([
                                TextInput::make('name')->required(),
                                Select::make('role')
                                    ->options([
                                        'member' => 'Member',
                                        'administrator' => 'Administrator',
                                        'owner' => 'Owner',
                                    ])
                                    ->required(),
                            ])
                            ->columns(2)
                            ->default([
                                [
                                    'name' => 'Dan Harrin',
                                    'role' => 'owner',
                                ],
                                [
                                    'name' => 'Ryan Chandler',
                                    'role' => 'administrator',
                                ],
                                [
                                    'name' => 'Zep Fietje',
                                    'role' => 'member',
                                ],
                                [
                                    'name' => null,
                                    'role' => null,
                                ],
                            ]),
                    ]),
                Group::make()
                    ->id('repeaterReorderableWithButtons')
                    ->extraAttributes([
                        'class' => 'p-16 max-w-5xl',
                    ])
                    ->schema([
                        Repeater::make('repeaterReorderableWithButtons')
                            ->label('Members')
                            ->schema([
                                TextInput::make('name')->required(),
                                Select::make('role')
                                    ->options([
                                        'member' => 'Member',
                                        'administrator' => 'Administrator',
                                        'owner' => 'Owner',
                                    ])
                                    ->required(),
                            ])
                            ->columns(2)
                            ->default([
                                [
                                    'name' => 'Dan Harrin',
                                    'role' => 'owner',
                                ],
                                [
                                    'name' => 'Ryan Chandler',
                                    'role' => 'administrator',
                                ],
                                [
                                    'name' => 'Zep Fietje',
                                    'role' => 'member',
                                ],
                            ])
                            ->reorderableWithButtons(),
                    ]),
                Group::make()
                    ->id('collapsedRepeater')
                    ->extraAttributes([
                        'class' => 'p-16 max-w-5xl',
                    ])
                    ->schema([
                        Repeater::make('collapsedRepeater')
                            ->label('Qualifications')
                            ->defaultItems(3)
                            ->collapsed(),
                    ]),
                Group::make()
                    ->id('cloneableRepeater')
                    ->extraAttributes([
                        'class' => 'p-16 max-w-5xl',
                    ])
                    ->schema([
                        Repeater::make('cloneableRepeater')
                            ->label('Qualifications')
                            ->schema([
                                TextInput::make('name')->required(),
                            ])
                            ->cloneable()
                            ->default([
                                ['name' => 'Tailwind CSS Level 1'],
                                ['name' => 'Alpine.js Level 1'],
                                ['name' => 'Laravel Level 1'],
                                ['name' => 'Livewire Level 1'],
                            ]),
                    ]),
                Group::make()
                    ->id('gridRepeater')
                    ->extraAttributes([
                        'class' => 'p-16 max-w-5xl',
                    ])
                    ->schema([
                        Repeater::make('gridRepeater')
                            ->label('Qualifications')
                            ->schema([
                                TextInput::make('name')->required(),
                            ])
                            ->grid(2)
                            ->default([
                                ['name' => 'Tailwind CSS Level 1'],
                                ['name' => 'Alpine.js Level 1'],
                                ['name' => 'Laravel Level 1'],
                                ['name' => 'Livewire Level 1'],
                            ]),
                    ]),
                Group::make()
                    ->id('labelledRepeater')
                    ->extraAttributes([
                        'class' => 'p-16 max-w-5xl',
                    ])
                    ->schema([
                        Repeater::make('labelledRepeater')
                            ->label('Members')
                            ->schema([
                                TextInput::make('name')->required(),
                                Select::make('role')
                                    ->options([
                                        'member' => 'Member',
                                        'administrator' => 'Administrator',
                                        'owner' => 'Owner',
                                    ])
                                    ->required(),
                            ])
                            ->columns(2)
                            ->default([
                                [
                                    'name' => 'Dan Harrin',
                                    'role' => 'owner',
                                ],
                                [
                                    'name' => 'Ryan Chandler',
                                    'role' => 'administrator',
                                ],
                                [
                                    'name' => 'Zep Fietje',
                                    'role' => 'member',
                                ],
                            ])
                            ->itemLabel(fn (array $state): ?string => $state['name'] ?? null),
                    ]),
                Group::make()
                    ->id('simpleRepeater')
                    ->extraAttributes([
                        'class' => 'p-16 max-w-5xl',
                    ])
                    ->schema([
                        Repeater::make('simpleRepeater')
                            ->label('Invitations')
                            ->simple(
                                TextInput::make('email')
                                    ->email()
                                    ->required(),
                            )
                            ->default([
                                'dan@filamentphp.com',
                                'ryan@filamentphp.com',
                            ]),
                    ]),
                Group::make()
                    ->id('builder')
                    ->extraAttributes([
                        'class' => 'p-16 max-w-5xl',
                    ])
                    ->schema([
                        Builder::make('builder')
                            ->label('Content')
                            ->schema([
                                Builder\Block::make('heading')
                                    ->schema([
                                        TextInput::make('content')
                                            ->label('Heading')
                                            ->required(),
                                        Select::make('level')
                                            ->options([
                                                'h1' => 'Heading 1',
                                                'h2' => 'Heading 2',
                                                'h3' => 'Heading 3',
                                                'h4' => 'Heading 4',
                                                'h5' => 'Heading 5',
                                                'h6' => 'Heading 6',
                                            ])
                                            ->required(),
                                    ])
                                    ->columns(2),
                                Builder\Block::make('paragraph')
                                    ->schema([
                                        Textarea::make('content')
                                            ->label('Paragraph')
                                            ->required(),
                                    ]),
                                Builder\Block::make('image')
                                    ->schema([
                                        FileUpload::make('url')
                                            ->label('Image')
                                            ->image()
                                            ->required(),
                                        TextInput::make('alt')
                                            ->label('Alt text')
                                            ->required(),
                                    ]),
                            ])
                            ->default([
                                [
                                    'type' => 'heading',
                                    'data' => [
                                        'content' => 'Lorem ipsum dolor sit amet',
                                        'level' => 'h2',
                                    ],
                                ],
                                [
                                    'type' => 'paragraph',
                                    'data' => [
                                        'content' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nullam euismod, nisl eget aliquam ultricies, quam sapien aliquet nunc, eget aliquam velit nisl quis nunc. Donec euismod, nisl eget aliquam ultricies, quam sapien aliquet nunc, eget aliquam velit nisl quis nunc.',
                                    ],
                                ],
                                [
                                    'type' => 'image',
                                    'data' => [
                                        'url' => null,
                                        'alt' => 'Lorem ipsum dolor sit amet',
                                    ],
                                ],
                                [
                                    'type' => 'paragraph',
                                    'data' => [
                                        'content' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nullam euismod, nisl eget aliquam ultricies, quam sapien aliquet nunc, eget aliquam velit nisl quis nunc. Donec euismod, nisl eget aliquam ultricies, quam sapien aliquet nunc, eget aliquam velit nisl quis nunc.',
                                    ],
                                ],
                                [
                                    'type' => 'image',
                                    'data' => [
                                        'url' => null,
                                        'alt' => 'Lorem ipsum dolor sit amet',
                                    ],
                                ],
                            ]),
                    ]),
                Group::make()
                    ->id('labelledBuilder')
                    ->extraAttributes([
                        'class' => 'p-16 max-w-5xl',
                    ])
                    ->schema([
                        Builder::make('labelledBuilder')
                            ->label('Content')
                            ->schema([
                                Builder\Block::make('heading')
                                    ->schema([
                                        TextInput::make('content')
                                            ->label('Heading')
                                            ->required(),
                                        Select::make('level')
                                            ->options([
                                                'h1' => 'Heading 1',
                                                'h2' => 'Heading 2',
                                                'h3' => 'Heading 3',
                                                'h4' => 'Heading 4',
                                                'h5' => 'Heading 5',
                                                'h6' => 'Heading 6',
                                            ])
                                            ->required(),
                                    ])
                                    ->columns(2)
                                    ->label(function (?array $state): string {
                                        if ($state === null) {
                                            return 'Heading';
                                        }

                                        return $state['content'] ?? 'Untitled heading';
                                    }),
                            ])
                            ->default([
                                [
                                    'type' => 'heading',
                                    'data' => [
                                        'content' => 'Lorem ipsum dolor sit amet',
                                        'level' => 'h2',
                                    ],
                                ],
                                [
                                    'type' => 'heading',
                                    'data' => [
                                        'content' => null,
                                        'level' => 'h3',
                                    ],
                                ],
                            ]),
                    ]),
                Group::make()
                    ->id('builderIcons')
                    ->extraAttributes([
                        'class' => 'px-16 pt-16 pb-40 max-w-5xl',
                    ])
                    ->schema([
                        Builder::make('builderIcons')
                            ->label('Content')
                            ->schema([
                                Builder\Block::make('paragraph')
                                    ->schema([
                                        Textarea::make('content')
                                            ->label('Paragraph')
                                            ->required(),
                                    ])
                                    ->icon(Heroicon::Bars3BottomLeft),
                                Builder\Block::make('image')
                                    ->icon(Heroicon::Photo),
                            ])
                            ->default([
                                [
                                    'type' => 'paragraph',
                                    'data' => [
                                        'content' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nullam euismod, nisl eget aliquam ultricies, quam sapien aliquet nunc, eget aliquam velit nisl quis nunc. Donec euismod, nisl eget aliquam ultricies, quam sapien aliquet nunc, eget aliquam velit nisl quis nunc.',
                                    ],
                                ],
                            ]),
                    ]),
                Group::make()
                    ->id('builderReorderableWithButtons')
                    ->extraAttributes([
                        'class' => 'p-16 max-w-5xl',
                    ])
                    ->schema([
                        Builder::make('builderReorderableWithButtons')
                            ->label('Content')
                            ->schema([
                                Builder\Block::make('paragraph')
                                    ->schema([
                                        Textarea::make('content')
                                            ->label('Paragraph')
                                            ->required(),
                                    ])
                                    ->icon(Heroicon::Bars3BottomLeft),
                            ])
                            ->default([
                                [
                                    'type' => 'paragraph',
                                    'data' => [
                                        'content' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nullam euismod, nisl eget aliquam ultricies, quam sapien aliquet nunc, eget aliquam velit nisl quis nunc. Donec euismod, nisl eget aliquam ultricies, quam sapien aliquet nunc, eget aliquam velit nisl quis nunc.',
                                    ],
                                ],
                                [
                                    'type' => 'paragraph',
                                    'data' => [
                                        'content' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nullam euismod, nisl eget aliquam ultricies, quam sapien aliquet nunc, eget aliquam velit nisl quis nunc. Donec euismod, nisl eget aliquam ultricies, quam sapien aliquet nunc, eget aliquam velit nisl quis nunc.',
                                    ],
                                ],
                                [
                                    'type' => 'paragraph',
                                    'data' => [
                                        'content' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nullam euismod, nisl eget aliquam ultricies, quam sapien aliquet nunc, eget aliquam velit nisl quis nunc. Donec euismod, nisl eget aliquam ultricies, quam sapien aliquet nunc, eget aliquam velit nisl quis nunc.',
                                    ],
                                ],
                            ])
                            ->reorderableWithButtons(),
                    ]),
                Group::make()
                    ->id('collapsedBuilder')
                    ->extraAttributes([
                        'class' => 'p-16 max-w-5xl',
                    ])
                    ->schema([
                        Builder::make('collapsedBuilder')
                            ->label('Content')
                            ->schema([
                                Builder\Block::make('paragraph')
                                    ->schema([
                                        Textarea::make('content')
                                            ->label('Paragraph')
                                            ->required(),
                                    ])
                                    ->icon(Heroicon::Bars3BottomLeft),
                            ])
                            ->default([
                                [
                                    'type' => 'paragraph',
                                    'data' => [
                                        'content' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nullam euismod, nisl eget aliquam ultricies, quam sapien aliquet nunc, eget aliquam velit nisl quis nunc. Donec euismod, nisl eget aliquam ultricies, quam sapien aliquet nunc, eget aliquam velit nisl quis nunc.',
                                    ],
                                ],
                                [
                                    'type' => 'paragraph',
                                    'data' => [
                                        'content' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nullam euismod, nisl eget aliquam ultricies, quam sapien aliquet nunc, eget aliquam velit nisl quis nunc. Donec euismod, nisl eget aliquam ultricies, quam sapien aliquet nunc, eget aliquam velit nisl quis nunc.',
                                    ],
                                ],
                                [
                                    'type' => 'paragraph',
                                    'data' => [
                                        'content' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nullam euismod, nisl eget aliquam ultricies, quam sapien aliquet nunc, eget aliquam velit nisl quis nunc. Donec euismod, nisl eget aliquam ultricies, quam sapien aliquet nunc, eget aliquam velit nisl quis nunc.',
                                    ],
                                ],
                            ])
                            ->collapsed(),
                    ]),
                Group::make()
                    ->id('cloneableBuilder')
                    ->extraAttributes([
                        'class' => 'p-16 max-w-5xl',
                    ])
                    ->schema([
                        Builder::make('cloneableBuilder')
                            ->label('Content')
                            ->schema([
                                Builder\Block::make('paragraph')
                                    ->schema([
                                        Textarea::make('content')
                                            ->label('Paragraph')
                                            ->required(),
                                    ])
                                    ->icon(Heroicon::Bars3BottomLeft),
                            ])
                            ->default([
                                [
                                    'type' => 'paragraph',
                                    'data' => [
                                        'content' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nullam euismod, nisl eget aliquam ultricies, quam sapien aliquet nunc, eget aliquam velit nisl quis nunc. Donec euismod, nisl eget aliquam ultricies, quam sapien aliquet nunc, eget aliquam velit nisl quis nunc.',
                                    ],
                                ],
                                [
                                    'type' => 'paragraph',
                                    'data' => [
                                        'content' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nullam euismod, nisl eget aliquam ultricies, quam sapien aliquet nunc, eget aliquam velit nisl quis nunc. Donec euismod, nisl eget aliquam ultricies, quam sapien aliquet nunc, eget aliquam velit nisl quis nunc.',
                                    ],
                                ],
                                [
                                    'type' => 'paragraph',
                                    'data' => [
                                        'content' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nullam euismod, nisl eget aliquam ultricies, quam sapien aliquet nunc, eget aliquam velit nisl quis nunc. Donec euismod, nisl eget aliquam ultricies, quam sapien aliquet nunc, eget aliquam velit nisl quis nunc.',
                                    ],
                                ],
                            ])
                            ->cloneable(),
                    ]),
                Group::make()
                    ->id('tagsInput')
                    ->extraAttributes([
                        'class' => 'p-16 max-w-xl',
                    ])
                    ->schema([
                        TagsInput::make('tagsInput')
                            ->label('Tags')
                            ->default(['Tailwind CSS', 'Alpine.js']),
                    ]),
                Group::make()
                    ->id('textarea')
                    ->extraAttributes([
                        'class' => 'p-16 max-w-xl',
                    ])
                    ->schema([
                        Textarea::make('textarea')
                            ->label('Description')
                            ->default('Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nullam euismod, nisl eget aliquam ultricies, quam sapien aliquet nunc, eget aliquam velit nisl quis nunc.'),
                    ]),
                Group::make()
                    ->id('keyValue')
                    ->extraAttributes([
                        'class' => 'p-16 max-w-5xl',
                    ])
                    ->schema([
                        KeyValue::make('keyValue')
                            ->label('Meta')
                            ->default([
                                'description' => 'Filament is a collection of Laravel packages',
                                'og:type' => 'website',
                                'og:site_name' => 'Filament',
                            ]),
                    ]),
                Group::make()
                    ->id('reorderableKeyValue')
                    ->extraAttributes([
                        'class' => 'p-16 max-w-5xl',
                    ])
                    ->schema([
                        KeyValue::make('reorderableKeyValue')
                            ->label('Meta')
                            ->default([
                                'description' => 'Filament is a collection of Laravel packages',
                                'og:type' => 'website',
                                'og:site_name' => 'Filament',
                            ])
                            ->reorderable(),
                    ]),
                Group::make()
                    ->id('colorPicker')
                    ->extraAttributes([
                        'class' => 'p-16 max-w-xl',
                    ])
                    ->schema([
                        ColorPicker::make('colorPicker')
                            ->label('Color')
                            ->default('#3490dc'),
                    ]),
                Group::make()
                    ->id('toggleButtons')
                    ->extraAttributes([
                        'class' => 'p-16 max-w-xl',
                    ])
                    ->schema([
                        ToggleButtons::make('toggleButtons')
                            ->label('Status')
                            ->options([
                                'draft' => 'Draft',
                                'scheduled' => 'Scheduled',
                                'published' => 'Published',
                            ])
                            ->default('published'),
                    ]),
                Group::make()
                    ->id('toggleButtonsColors')
                    ->extraAttributes([
                        'class' => 'p-16 max-w-xl',
                    ])
                    ->schema([
                        ToggleButtons::make('toggleButtonsColors')
                            ->label('Status')
                            ->options([
                                'draft' => 'Draft',
                                'scheduled' => 'Scheduled',
                                'published' => 'Published',
                            ])
                            ->colors([
                                'draft' => 'info',
                                'scheduled' => 'warning',
                                'published' => 'success',
                            ])
                            ->default('draft'),
                    ]),
                Group::make()
                    ->id('toggleButtonsIcons')
                    ->extraAttributes([
                        'class' => 'p-16 max-w-xl',
                    ])
                    ->schema([
                        ToggleButtons::make('toggleButtonsIcons')
                            ->label('Status')
                            ->options([
                                'draft' => 'Draft',
                                'scheduled' => 'Scheduled',
                                'published' => 'Published',
                            ])
                            ->icons([
                                'draft' => Heroicon::OutlinedPencil,
                                'scheduled' => Heroicon::OutlinedClock,
                                'published' => Heroicon::OutlinedCheckCircle,
                            ])
                            ->default('scheduled'),
                    ]),
                Group::make()
                    ->id('toggleButtonsBoolean')
                    ->extraAttributes([
                        'class' => 'p-16 max-w-xl',
                    ])
                    ->schema([
                        ToggleButtons::make('toggleButtonsBoolean')
                            ->label('Like this post?')
                            ->boolean()
                            ->default(true),
                    ]),
                Group::make()
                    ->id('toggleButtonsInline')
                    ->extraAttributes([
                        'class' => 'p-16 max-w-xl',
                    ])
                    ->schema([
                        ToggleButtons::make('toggleButtonsInline')
                            ->label('Like this post?')
                            ->boolean()
                            ->inline()
                            ->default(false),
                    ]),
                Group::make()
                    ->id('toggleButtonsGrouped')
                    ->extraAttributes([
                        'class' => 'p-16 max-w-xl',
                    ])
                    ->schema([
                        ToggleButtons::make('toggleButtonsGrouped')
                            ->label('Like this post?')
                            ->boolean()
                            ->grouped()
                            ->default(true),
                    ]),
                Group::make()
                    ->id('toggleButtonsMultiple')
                    ->extraAttributes([
                        'class' => 'p-16 max-w-xl',
                    ])
                    ->schema([
                        ToggleButtons::make('toggleButtonsMultiple')
                            ->label('Technologies')
                            ->multiple()
                            ->options([
                                'tailwind' => 'Tailwind CSS',
                                'alpine' => 'Alpine.js',
                                'laravel' => 'Laravel',
                                'livewire' => 'Laravel Livewire',
                            ])
                            ->default(['tailwind', 'laravel']),
                    ]),
                Group::make()
                    ->id('toggleButtonsColumns')
                    ->extraAttributes([
                        'class' => 'p-16 max-w-xl',
                    ])
                    ->schema([
                        ToggleButtons::make('toggleButtonsColumns')
                            ->label('Technologies')
                            ->options([
                                'tailwind' => 'Tailwind CSS',
                                'alpine' => 'Alpine.js',
                                'laravel' => 'Laravel',
                                'livewire' => 'Laravel Livewire',
                            ])
                            ->columns(2)
                            ->default('alpine'),
                    ]),
                Group::make()
                    ->id('toggleButtonsRows')
                    ->extraAttributes([
                        'class' => 'p-16 max-w-xl',
                    ])
                    ->schema([
                        ToggleButtons::make('toggleButtonsRows')
                            ->label('Technologies')
                            ->options([
                                'tailwind' => 'Tailwind CSS',
                                'alpine' => 'Alpine.js',
                                'laravel' => 'Laravel',
                                'livewire' => 'Laravel Livewire',
                            ])
                            ->columns(2)
                            ->gridDirection('row')
                            ->default('alpine'),
                    ]),
                Group::make()
                    ->id('disabledOptionToggleButtons')
                    ->extraAttributes([
                        'class' => 'p-16 max-w-xl',
                    ])
                    ->schema([
                        ToggleButtons::make('disabledOptionToggleButtons')
                            ->label('Status')
                            ->options([
                                'draft' => 'Draft',
                                'scheduled' => 'Scheduled',
                                'published' => 'Published',
                            ])
                            ->default('draft')
                            ->disableOptionWhen(fn (string $value): bool => $value === 'published'),
                    ]),
                Group::make()
                    ->id('suffixAction')
                    ->extraAttributes([
                        'class' => 'p-16 max-w-xl',
                    ])
                    ->schema([
                        TextInput::make('suffixAction')
                            ->label('Cost')
                            ->prefix('€')
                            ->default('22.66')
                            ->suffixAction(
                                Action::make('copyCostToPrice')
                                    ->icon(Heroicon::Clipboard),
                            ),
                    ]),
                Group::make()
                    ->id('slider')
                    ->extraAttributes([
                        'class' => 'p-16 max-w-xl',
                    ])
                    ->schema([
                        Slider::make('slider')
                            ->label('Slider')
                            ->default(50),
                    ]),
                Group::make()
                    ->id('sliderRange')
                    ->extraAttributes([
                        'class' => 'p-16 max-w-xl',
                    ])
                    ->schema([
                        Slider::make('sliderRange')
                            ->label('Slider')
                            ->range(minValue: 40, maxValue: 80)
                            ->default(50),
                    ]),
                Group::make()
                    ->id('sliderMultiple')
                    ->extraAttributes([
                        'class' => 'p-16 max-w-xl',
                    ])
                    ->schema([
                        Slider::make('sliderMultiple')
                            ->label('Slider')
                            ->default([20, 70]),
                    ]),
                Group::make()
                    ->id('sliderVertical')
                    ->extraAttributes([
                        'class' => 'p-16 max-w-xl',
                    ])
                    ->schema([
                        Slider::make('sliderVertical')
                            ->label('Slider')
                            ->vertical()
                            ->default(50),
                    ]),
                Group::make()
                    ->id('sliderTooltips')
                    ->extraAttributes([
                        'class' => 'p-16 max-w-xl',
                    ])
                    ->schema([
                        Slider::make('sliderTooltips')
                            ->label('Slider')
                            ->tooltips()
                            ->default(50),
                    ]),
                Group::make()
                    ->id('sliderTooltipsMultiple')
                    ->extraAttributes([
                        'class' => 'p-16 max-w-xl',
                    ])
                    ->schema([
                        Slider::make('sliderTooltipsMultiple')
                            ->label('Slider')
                            ->tooltips([true, false])
                            ->default([20, 70]),
                    ]),
                Group::make()
                    ->id('sliderTooltipsVertical')
                    ->extraAttributes([
                        'class' => 'p-16 max-w-xl',
                    ])
                    ->schema([
                        Slider::make('sliderTooltipsVertical')
                            ->label('Slider')
                            ->tooltips()
                            ->vertical()
                            ->default(50),
                    ]),
                Group::make()
                    ->id('sliderTooltipsFormatting')
                    ->extraAttributes([
                        'class' => 'p-16 max-w-xl',
                    ])
                    ->schema([
                        Slider::make('sliderTooltipsFormatting')
                            ->label('Slider')
                            ->tooltips(RawJs::make('`$${$value.toFixed(2)}`'))
                            ->default(64.99),
                    ]),
                Group::make()
                    ->id('sliderFill')
                    ->extraAttributes([
                        'class' => 'p-16 max-w-xl',
                    ])
                    ->schema([
                        Slider::make('sliderFill')
                            ->label('Slider')
                            ->fillTrack()
                            ->default(50),
                    ]),
                Group::make()
                    ->id('sliderFillMultiple')
                    ->extraAttributes([
                        'class' => 'p-16 max-w-xl',
                    ])
                    ->schema([
                        Slider::make('sliderFillMultiple')
                            ->label('Slider')
                            ->fillTrack([false, true, false])
                            ->default([20, 70]),
                    ]),
                Group::make()
                    ->id('sliderFillVertical')
                    ->extraAttributes([
                        'class' => 'p-16 max-w-xl',
                    ])
                    ->schema([
                        Slider::make('sliderFillVertical')
                            ->label('Slider')
                            ->fillTrack()
                            ->vertical()
                            ->default(50),
                    ]),
                Group::make()
                    ->id('sliderPips')
                    ->extraAttributes([
                        'class' => 'p-16 max-w-xl',
                    ])
                    ->schema([
                        Slider::make('sliderPips')
                            ->label('Slider')
                            ->pips()
                            ->default(50),
                    ]),
                Group::make()
                    ->id('sliderPipsMultiple')
                    ->extraAttributes([
                        'class' => 'p-16 max-w-xl',
                    ])
                    ->schema([
                        Slider::make('sliderPipsMultiple')
                            ->label('Slider')
                            ->pips()
                            ->default([20, 70]),
                    ]),
                Group::make()
                    ->id('sliderPipsVertical')
                    ->extraAttributes([
                        'class' => 'p-16 max-w-xl',
                    ])
                    ->schema([
                        Slider::make('sliderPipsVertical')
                            ->label('Slider')
                            ->pips()
                            ->vertical()
                            ->default(50),
                    ]),
                Group::make()
                    ->id('sliderPipsDensity')
                    ->extraAttributes([
                        'class' => 'p-16 max-w-xl',
                    ])
                    ->schema([
                        Slider::make('sliderPipsDensity')
                            ->label('Slider')
                            ->pips(density: 5)
                            ->default(50),
                    ]),
                Group::make()
                    ->id('sliderPipsFormatting')
                    ->extraAttributes([
                        'class' => 'p-16 max-w-xl',
                    ])
                    ->schema([
                        Slider::make('sliderPipsFormatting')
                            ->label('Slider')
                            ->pips()
                            ->pipsFormatter(RawJs::make('`$${$value.toFixed(2)}`'))
                            ->default(50),
                    ]),
                Group::make()
                    ->id('sliderPipsSteps')
                    ->extraAttributes([
                        'class' => 'p-16 max-w-xl',
                    ])
                    ->schema([
                        Slider::make('sliderPipsSteps')
                            ->label('Slider')
                            ->step(10)
                            ->pips(PipsMode::Steps)
                            ->default(50),
                    ]),
                Group::make()
                    ->id('sliderPipsStepsDensity')
                    ->extraAttributes([
                        'class' => 'p-16 max-w-xl',
                    ])
                    ->schema([
                        Slider::make('sliderPipsStepsDensity')
                            ->label('Slider')
                            ->step(10)
                            ->pips(PipsMode::Steps, density: 5)
                            ->default(50),
                    ]),
                Group::make()
                    ->id('sliderPipsPositions')
                    ->extraAttributes([
                        'class' => 'p-16 max-w-xl',
                    ])
                    ->schema([
                        Slider::make('sliderPipsPositions')
                            ->label('Slider')
                            ->pips(PipsMode::Positions)
                            ->pipsValues([0, 25, 50, 75, 100])
                            ->default(50),
                    ]),
                Group::make()
                    ->id('sliderPipsCount')
                    ->extraAttributes([
                        'class' => 'p-16 max-w-xl',
                    ])
                    ->schema([
                        Slider::make('sliderPipsCount')
                            ->label('Slider')
                            ->pips(PipsMode::Count)
                            ->pipsValues(5)
                            ->default(50),
                    ]),
                Group::make()
                    ->id('sliderPipsValues')
                    ->extraAttributes([
                        'class' => 'p-16 max-w-xl',
                    ])
                    ->schema([
                        Slider::make('sliderPipsValues')
                            ->label('Slider')
                            ->pips(PipsMode::Values)
                            ->pipsValues([5, 15, 25, 35, 45, 55, 65, 75, 85, 95])
                            ->default(50),
                    ]),
                Group::make()
                    ->id('sliderPipsValuesDensity')
                    ->extraAttributes([
                        'class' => 'p-16 max-w-xl',
                    ])
                    ->schema([
                        Slider::make('sliderPipsValuesDensity')
                            ->label('Slider')
                            ->pips(PipsMode::Values, density: 5)
                            ->pipsValues([5, 15, 25, 35, 45, 55, 65, 75, 85, 95])
                            ->default(50),
                    ]),
                Group::make()
                    ->id('sliderPipsFilter')
                    ->extraAttributes([
                        'class' => 'p-16 max-w-xl',
                    ])
                    ->schema([
                        Slider::make('sliderPipsFilter')
                            ->label('Slider')
                            ->pips(density: 5)
                            ->pipsFilter(RawJs::make(<<<'JS'
                                ($value % 50) === 0
                                    ? 1
                                    : ($value % 10) === 0
                                        ? 2
                                        : ($value % 25) === 0
                                            ? 0
                                            : -1
                                JS))
                            ->default(50),
                    ]),
                Group::make()
                    ->id('sliderNonLinear')
                    ->extraAttributes([
                        'class' => 'p-16 max-w-xl',
                    ])
                    ->schema([
                        Slider::make('sliderNonLinear')
                            ->label('Slider')
                            ->nonLinearPoints(['20%' => 50, '50%' => 75])
                            ->pips()
                            ->default(50),
                    ]),
                Group::make()
                    ->id('codeEditor')
                    ->extraAttributes([
                        'class' => 'p-16 max-w-xl',
                    ])
                    ->schema([
                        CodeEditor::make('code')
                            ->default(<<<'YAML'
                                name: Filament
                                framework: Laravel
                                packageManager: Composer
                                releaseYear: 2021
                                website: https://filamentphp.com
                                YAML),
                    ]),
                Group::make()
                    ->id('codeEditorLanguage')
                    ->extraAttributes([
                        'class' => 'p-16 max-w-xl',
                    ])
                    ->schema([
                        CodeEditor::make('codeWithLanguage')
                            ->label('Code')
                            ->language(Language::JavaScript)
                            ->default(<<<'JS'
                                const fetchUser = async (id) => {
                                    const res = await fetch(`https://api.example.com/users/${id}`)

                                    if (! res.ok) {
                                        throw new Error('User not found')
                                    }

                                    return res.json()
                                }

                                fetchUser(1)
                                    .then((user) => console.log(`👤 ${user.name}`))
                                    .catch((error) => console.error('⚠️', error.message))
                                JS),
                    ]),
            ]);
    }

    public function render()
    {
        return view('livewire.forms.fields');
    }
}
