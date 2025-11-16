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
                    ->extraAttributes([
                        'class' => 'p-16 max-w-xl',
                    ])
                    ->id('simple')
                    ->schema([
                        TextInput::make('simple')
                            ->label('Name')
                            ->default('Dan Harrin'),
                    ]),
                Group::make()
                    ->extraAttributes([
                        'class' => 'p-16 max-w-xl',
                    ])
                    ->id('disabled')
                    ->schema([
                        TextInput::make('disabled')
                            ->label('Name')
                            ->disabled()
                            ->default('Dan Harrin'),
                    ]),
                Group::make()
                    ->extraAttributes([
                        'class' => 'p-16 max-w-xl',
                    ])
                    ->id('inlineLabel')
                    ->schema([
                        TextInput::make('inlineLabel')
                            ->label('Name')
                            ->inlineLabel(),
                    ]),
                Group::make()
                    ->extraAttributes([
                        'class' => 'p-16 max-w-xl',
                    ])
                    ->id('inlineLabelSection')
                    ->schema([
                        Section::make('Details')
                            ->inlineLabel()
                            ->schema([
                                TextInput::make('inlineLabelSectionName')
                                    ->label('Name'),
                                TextInput::make('inlineLabelSectionEmail')
                                    ->label('Email address'),
                                TextInput::make('inlineLabelSectionPhone')
                                    ->label('Phone number'),
                            ]),
                    ]),
                Group::make()
                    ->extraAttributes([
                        'class' => 'p-16 max-w-xl',
                    ])
                    ->id('placeholder')
                    ->schema([
                        TextInput::make('placeholder')
                            ->label('Name')
                            ->placeholder('Dan Harrin'),
                    ]),
                Group::make()
                    ->extraAttributes([
                        'class' => 'p-16 max-w-xl',
                    ])
                    ->id('fused')
                    ->schema([
                        FusedGroup::make([
                            TextInput::make('city')
                                ->placeholder('City'),
                            Select::make('country')
                                ->placeholder('Country'),
                        ]),
                    ]),
                Group::make()
                    ->extraAttributes([
                        'class' => 'p-16 max-w-xl',
                    ])
                    ->id('fusedLabel')
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
                    ->extraAttributes([
                        'class' => 'p-16 max-w-xl',
                    ])
                    ->id('fusedColumns')
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
                    ->extraAttributes([
                        'class' => 'p-16 max-w-xl',
                    ])
                    ->id('fusedColumnsSpan')
                    ->schema([
                        FusedGroup::make([
                            TextInput::make('city')
                                ->columnSpan(2)
                                ->placeholder('City'),
                            Select::make('country')
                                ->placeholder('Country'),
                        ])
                            ->label('Location')
                            ->columns(3),
                    ]),
                Group::make()
                    ->extraAttributes([
                        'class' => 'p-16 max-w-xl',
                    ])
                    ->id('textBelowContent')
                    ->schema([
                        TextInput::make('name')
                            ->belowContent('This is the user\'s full name.'),
                    ]),
                Group::make()
                    ->extraAttributes([
                        'class' => 'p-16 max-w-xl',
                    ])
                    ->id('componentBelowContent')
                    ->schema([
                        TextInput::make('name')
                            ->belowContent(Text::make('This is the user\'s full name.')->weight(FontWeight::Bold)),
                    ]),
                Group::make()
                    ->extraAttributes([
                        'class' => 'p-16 max-w-xl',
                    ])
                    ->id('actionBelowContent')
                    ->schema([
                        TextInput::make('name')
                            ->belowContent(Action::make('generate')),
                    ]),
                Group::make()
                    ->extraAttributes([
                        'class' => 'p-16 max-w-xl',
                    ])
                    ->id('belowContent')
                    ->schema([
                        TextInput::make('name')
                            ->belowContent([
                                Icon::make(Heroicon::InformationCircle),
                                'This is the user\'s full name.',
                                Action::make('generate'),
                            ]),
                    ]),
                Group::make()
                    ->extraAttributes([
                        'class' => 'p-16 max-w-xl',
                    ])
                    ->id('belowContentAlignment')
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
                    ->extraAttributes([
                        'class' => 'p-16 max-w-xl',
                    ])
                    ->id('aboveLabel')
                    ->schema([
                        TextInput::make('aboveLabel')
                            ->label('Name')
                            ->aboveLabel([
                                Icon::make(Heroicon::Star),
                                'This is the content above the field\'s label',
                            ]),
                    ]),
                Group::make()
                    ->extraAttributes([
                        'class' => 'p-16 max-w-xl',
                    ])
                    ->id('beforeLabel')
                    ->schema([
                        TextInput::make('beforeLabel')
                            ->label('Name')
                            ->beforeLabel(Icon::make(Heroicon::Star)),
                    ]),
                Group::make()
                    ->extraAttributes([
                        'class' => 'p-16 max-w-xl',
                    ])
                    ->id('afterLabel')
                    ->schema([
                        TextInput::make('afterLabel')
                            ->label('Name')
                            ->afterLabel([
                                Icon::make(Heroicon::Star),
                                'This is the content after the field\'s label',
                            ]),
                    ]),
                Group::make()
                    ->extraAttributes([
                        'class' => 'p-16 max-w-xl',
                    ])
                    ->id('afterLabelAlignedStart')
                    ->schema([
                        TextInput::make('afterLabelAlignedStart')
                            ->label('Name')
                            ->afterLabel(Schema::start([
                                Icon::make(Heroicon::Star),
                                'This is the content after the field\'s label',
                            ])),
                    ]),
                Group::make()
                    ->extraAttributes([
                        'class' => 'p-16 max-w-xl',
                    ])
                    ->id('belowLabel')
                    ->schema([
                        TextInput::make('belowLabel')
                            ->label('Name')
                            ->belowLabel([
                                Icon::make(Heroicon::Star),
                                'This is the content below the field\'s label',
                            ]),
                    ]),
                Group::make()
                    ->extraAttributes([
                        'class' => 'p-16 max-w-xl',
                    ])
                    ->id('aboveContent')
                    ->schema([
                        TextInput::make('aboveContent')
                            ->label('Name')
                            ->belowLabel([
                                Icon::make(Heroicon::Star),
                                'This is the content above the field\'s content',
                            ]),
                    ]),
                Group::make()
                    ->extraAttributes([
                        'class' => 'p-16 max-w-xl',
                    ])
                    ->id('beforeContent')
                    ->schema([
                        TextInput::make('beforeContent')
                            ->label('Name')
                            ->beforeContent(Icon::make(Heroicon::Star)),
                    ]),
                Group::make()
                    ->extraAttributes([
                        'class' => 'p-16 max-w-xl',
                    ])
                    ->id('afterContent')
                    ->schema([
                        TextInput::make('afterContent')
                            ->label('Name')
                            ->afterContent(Icon::make(Heroicon::Star)),
                    ]),
                Group::make()
                    ->extraAttributes([
                        'class' => 'p-16 max-w-xl',
                    ])
                    ->id('aboveErrorMessage')
                    ->schema([
                        TextInput::make('aboveErrorMessage')
                            ->label('Name')
                            ->aboveErrorMessage([
                                Icon::make(Heroicon::Star),
                                'This is the content above the field\'s error message',
                            ])
                            ->required(),
                    ]),
                Group::make()
                    ->extraAttributes([
                        'class' => 'p-16 max-w-xl',
                    ])
                    ->id('belowErrorMessage')
                    ->schema([
                        TextInput::make('belowErrorMessage')
                            ->label('Name')
                            ->belowErrorMessage([
                                Icon::make(Heroicon::Star),
                                'This is the content below the field\'s error message',
                            ])
                            ->required(),
                    ]),
                Group::make()
                    ->extraAttributes([
                        'class' => 'p-16 max-w-xl',
                    ])
                    ->id('textInput')
                    ->schema([
                        TextInput::make('textInput')
                            ->label('Name')
                            ->default('Dan Harrin'),
                    ]),
                Group::make()
                    ->extraAttributes([
                        'class' => 'p-16 max-w-xl',
                    ])
                    ->id('textInputAffix')
                    ->schema([
                        TextInput::make('textInputAffix')
                            ->label('Domain')
                            ->prefix('https://')
                            ->suffix('.com')
                            ->default('filamentphp'),
                    ]),
                Group::make()
                    ->extraAttributes([
                        'class' => 'p-16 max-w-xl',
                    ])
                    ->id('textInputSuffixIcon')
                    ->schema([
                        TextInput::make('textInputSuffixIcon')
                            ->label('Domain')
                            ->suffixIcon(Heroicon::GlobeAlt)
                            ->default('https://filamentphp.com'),
                    ]),
                Group::make()
                    ->extraAttributes([
                        'class' => 'p-16 max-w-xl',
                    ])
                    ->id('textInputRevealablePassword')
                    ->schema([
                        TextInput::make('textInputRevealablePassword')
                            ->label('Password')
                            ->password()
                            ->revealable()
                            ->default('filament123'),
                        TextInput::make('textInputRevealedPassword')
                            ->label('Password')
                            ->suffixActions([
                                TextInput\Actions\HidePasswordAction::make()
                                    ->extraAttributes([]),
                            ])
                            ->default('filament123'),
                    ]),
                Group::make()
                    ->extraAttributes([
                        'class' => 'p-16 max-w-xl',
                    ])
                    ->id('select')
                    ->schema([
                        Select::make('select')
                            ->label('Status'),
                    ]),
                Group::make()
                    ->extraAttributes([
                        'class' => 'px-16 pt-16 pb-48 max-w-xl',
                    ])
                    ->id('javascriptSelect')
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
                    ->extraAttributes([
                        'class' => 'px-16 pt-16 pb-72 max-w-xl',
                    ])
                    ->id('searchableSelect')
                    ->schema([
                        Select::make('searchableSelect')
                            ->label('Author')
                            ->options([
                                'dan' => 'Dan Harrin',
                                'ryan' => 'Ryan Chandler',
                                'zep' => 'Zep Fietje',
                                'dennis' => 'Dennis Koch',
                                'adam' => 'Adam Weston',
                            ])
                            ->searchable(),
                    ]),
                Group::make()
                    ->extraAttributes([
                        'class' => 'px-16 pt-16 pb-44 max-w-xl',
                    ])
                    ->id('multipleSelect')
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
                    ->extraAttributes([
                        'class' => 'px-16 pt-16 pb-96 max-w-xl',
                    ])
                    ->id('groupedSelect')
                    ->schema([
                        Select::make('groupedSelect')
                            ->label('Status')
                            ->options([
                                'In Process' => [
                                    'draft' => 'Draft',
                                    'reviewing' => 'Reviewing',
                                ],
                                'Reviewed' => [
                                    'published' => 'Published',
                                    'rejected' => 'Rejected',
                                ],
                            ])
                            ->searchable(),
                    ]),
                Group::make()
                    ->extraAttributes([
                        'class' => 'p-16 max-w-xl',
                    ])
                    ->id('createSelectOption')
                    ->schema([
                        Select::make('createSelectOption')
                            ->label('Author')
                            ->createOptionForm([
                                TextInput::make('name'),
                            ]),
                    ]),
                Group::make()
                    ->extraAttributes([
                        'class' => 'p-16 max-w-xl',
                    ])
                    ->id('editSelectOption')
                    ->schema([
                        Select::make('editSelectOption')
                            ->label('Author')
                            ->editOptionForm([
                                TextInput::make('name'),
                            ])
                            ->fillEditOptionActionFormUsing(fn () => ['name' => 'Dan Harrin'])
                            ->options([
                                'dan' => 'Dan Harrin',
                            ])
                            ->default('dan'),
                    ]),
                Group::make()
                    ->extraAttributes([
                        'class' => 'p-16 max-w-xl',
                    ])
                    ->id('selectAffix')
                    ->schema([
                        Select::make('selectAffix')
                            ->label('Domain')
                            ->options([
                                'filament' => 'filamentphp',
                            ])
                            ->prefix('https://')
                            ->suffix('.com')
                            ->default('filament'),
                    ]),
                Group::make()
                    ->extraAttributes([
                        'class' => 'p-16 max-w-xl',
                    ])
                    ->id('selectSuffixIcon')
                    ->schema([
                        Select::make('selectSuffixIcon')
                            ->label('Domain')
                            ->options([
                                'filament' => 'filamentphp',
                            ])
                            ->suffixIcon(Heroicon::GlobeAlt)
                            ->default('filament'),
                    ]),
                Group::make()
                    ->extraAttributes([
                        'class' => 'p-16 max-w-xl',
                    ])
                    ->id('checkbox')
                    ->schema([
                        Checkbox::make('checkbox')
                            ->label('Is admin'),
                    ]),
                Group::make()
                    ->extraAttributes([
                        'class' => 'p-16 max-w-xl',
                    ])
                    ->id('inlineCheckbox')
                    ->schema([
                        Checkbox::make('inlineCheckbox')
                            ->label('Is admin')
                            ->inline(),
                    ]),
                Group::make()
                    ->extraAttributes([
                        'class' => 'p-16 max-w-xl',
                    ])
                    ->id('notInlineCheckbox')
                    ->schema([
                        Checkbox::make('notInlineCheckbox')
                            ->label('Is admin')
                            ->inline(false),
                    ]),
                Group::make()
                    ->extraAttributes([
                        'class' => 'p-16 max-w-xl',
                    ])
                    ->id('toggle')
                    ->schema([
                        Toggle::make('toggle')
                            ->label('Is admin'),
                    ]),
                Group::make()
                    ->extraAttributes([
                        'class' => 'p-16 max-w-xl',
                    ])
                    ->id('toggleIcons')
                    ->schema([
                        Toggle::make('toggleIcons')
                            ->label('Is admin')
                            ->offIcon(Heroicon::User)
                            ->onIcon(Heroicon::Bolt),
                    ]),
                Group::make()
                    ->extraAttributes([
                        'class' => 'p-16 max-w-xl',
                    ])
                    ->id('toggleOffColor')
                    ->schema([
                        Toggle::make('toggleOffColor')
                            ->label('Is admin')
                            ->offColor('danger')
                            ->onColor('success')
                            ->default(false),
                    ]),
                Group::make()
                    ->extraAttributes([
                        'class' => 'p-16 max-w-xl',
                    ])
                    ->id('toggleOnColor')
                    ->schema([
                        Toggle::make('toggleOnColor')
                            ->label('Is admin')
                            ->offColor('danger')
                            ->onColor('success')
                            ->default(true),
                    ]),
                Group::make()
                    ->extraAttributes([
                        'class' => 'p-16 max-w-xl',
                    ])
                    ->id('inlineToggle')
                    ->schema([
                        Toggle::make('inlineToggle')
                            ->label('Is admin')
                            ->inline(),
                    ]),
                Group::make()
                    ->extraAttributes([
                        'class' => 'p-16 max-w-xl',
                    ])
                    ->id('notInlineToggle')
                    ->schema([
                        Toggle::make('notInlineToggle')
                            ->label('Is admin')
                            ->inline(false),
                    ]),
                Group::make()
                    ->extraAttributes([
                        'class' => 'p-16 max-w-xl',
                    ])
                    ->id('checkboxList')
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
                    ->extraAttributes([
                        'class' => 'p-16 max-w-xl',
                    ])
                    ->id('checkboxListOptionDescriptions')
                    ->schema([
                        CheckboxList::make('checkboxListOptionDescriptions')
                            ->label('Technologies')
                            ->descriptions([
                                'tailwind' => 'A utility-first CSS framework for rapidly building modern websites without ever leaving your HTML.',
                                'alpine' => new HtmlString('A rugged, minimal tool for composing behavior <strong>directly in your markup</strong>.'),
                                'laravel' => str('A **web application** framework with expressive, elegant syntax.')->inlineMarkdown()->toHtmlString(),
                                'livewire' => 'A full-stack framework for Laravel building dynamic interfaces simple, without leaving the comfort of Laravel.',
                            ])
                            ->options([
                                'tailwind' => 'Tailwind CSS',
                                'alpine' => 'Alpine.js',
                                'laravel' => 'Laravel',
                                'livewire' => 'Laravel Livewire',
                            ]),
                    ]),
                Group::make()
                    ->extraAttributes([
                        'class' => 'p-16 max-w-xl',
                    ])
                    ->id('checkboxListColumns')
                    ->schema([
                        CheckboxList::make('checkboxListColumns')
                            ->label('Technologies')
                            ->columns(2)
                            ->options([
                                'tailwind' => 'Tailwind CSS',
                                'alpine' => 'Alpine.js',
                                'laravel' => 'Laravel',
                                'livewire' => 'Laravel Livewire',
                            ])
                            ->default(['tailwind', 'laravel']),
                    ]),
                Group::make()
                    ->extraAttributes([
                        'class' => 'p-16 max-w-xl',
                    ])
                    ->id('checkboxListRows')
                    ->schema([
                        CheckboxList::make('checkboxListRows')
                            ->label('Technologies')
                            ->columns(2)
                            ->gridDirection('row')
                            ->options([
                                'tailwind' => 'Tailwind CSS',
                                'alpine' => 'Alpine.js',
                                'laravel' => 'Laravel',
                                'livewire' => 'Laravel Livewire',
                            ])
                            ->default(['tailwind', 'laravel']),
                    ]),
                Group::make()
                    ->extraAttributes([
                        'class' => 'p-16 max-w-xl',
                    ])
                    ->id('searchableCheckboxList')
                    ->schema([
                        CheckboxList::make('searchableCheckboxList')
                            ->label('Technologies')
                            ->options([
                                'tailwind' => 'Tailwind CSS',
                                'alpine' => 'Alpine.js',
                                'laravel' => 'Laravel',
                                'livewire' => 'Laravel Livewire',
                            ])
                            ->searchable()
                            ->default(['tailwind', 'laravel']),
                    ]),
                Group::make()
                    ->extraAttributes([
                        'class' => 'p-16 max-w-xl',
                    ])
                    ->id('bulkToggleableCheckboxList')
                    ->schema([
                        CheckboxList::make('bulkToggleableCheckboxList')
                            ->label('Technologies')
                            ->bulkToggleable()
                            ->options([
                                'tailwind' => 'Tailwind CSS',
                                'alpine' => 'Alpine.js',
                                'laravel' => 'Laravel',
                                'livewire' => 'Laravel Livewire',
                            ])
                            ->default(['tailwind', 'laravel']),
                    ]),
                Group::make()
                    ->extraAttributes([
                        'class' => 'p-16 max-w-xl',
                    ])
                    ->id('radio')
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
                    ->extraAttributes([
                        'class' => 'p-16 max-w-xl',
                    ])
                    ->id('radioOptionDescriptions')
                    ->schema([
                        Radio::make('radioOptionDescriptions')
                            ->label('Status')
                            ->descriptions([
                                'draft' => 'Is not visible.',
                                'scheduled' => 'Will be visible.',
                                'published' => 'Is visible.',
                            ])
                            ->options([
                                'draft' => 'Draft',
                                'scheduled' => 'Scheduled',
                                'published' => 'Published',
                            ])
                            ->default('draft'),
                    ]),
                Group::make()
                    ->extraAttributes([
                        'class' => 'p-16 max-w-xl',
                    ])
                    ->id('booleanRadio')
                    ->schema([
                        Radio::make('booleanRadio')
                            ->label('Like this post?')
                            ->boolean()
                            ->default(true),
                    ]),
                Group::make()
                    ->extraAttributes([
                        'class' => 'p-16 max-w-xl',
                    ])
                    ->id('inlineRadio')
                    ->schema([
                        Radio::make('inlineRadio')
                            ->label('Like this post?')
                            ->boolean()
                            ->inline()
                            ->default(true),
                    ]),
                Group::make()
                    ->extraAttributes([
                        'class' => 'p-16 max-w-xl',
                    ])
                    ->id('disabledOptionRadio')
                    ->schema([
                        Radio::make('disabledOptionRadio')
                            ->label('Status')
                            ->disableOptionWhen(fn (string $value): bool => $value === 'published')
                            ->options([
                                'draft' => 'Draft',
                                'scheduled' => 'Scheduled',
                                'published' => 'Published',
                            ])
                            ->default('draft'),
                    ]),
                Group::make()
                    ->extraAttributes([
                        'class' => 'p-16 max-w-xl',
                    ])
                    ->id('dateTimePickers')
                    ->schema([
                        DateTimePicker::make('dateTimePicker')
                            ->label('Published at'),
                        DatePicker::make('datePickers')
                            ->label('Date of birth'),
                        TimePicker::make('timePicker')
                            ->label('Alarm at'),
                    ]),
                Group::make()
                    ->extraAttributes([
                        'class' => 'p-16 max-w-xl',
                    ])
                    ->id('dateTimePickerWithoutSeconds')
                    ->schema([
                        DateTimePicker::make('dateTimePickerWithoutSeconds')
                            ->label('Published at')
                            ->seconds(false),
                    ]),
                Group::make()
                    ->extraAttributes([
                        'class' => 'px-16 pt-16 pb-96 max-w-xl',
                    ])
                    ->id('javascriptDateTimePicker')
                    ->schema([
                        DatePicker::make('javascriptDateTimePicker')
                            ->label('Date of birth')
                            ->native(false)
                            ->default('2000-01-01'),
                    ]),
                Group::make()
                    ->extraAttributes([
                        'class' => 'p-16 max-w-xl',
                    ])
                    ->id('dateTimePickerDisplayFormat')
                    ->schema([
                        DatePicker::make('dateTimePickerDisplayFormat')
                            ->label('Date of birth')
                            ->displayFormat('d/m/Y')
                            ->native(false)
                            ->default('2000-01-01'),
                    ]),
                Group::make()
                    ->extraAttributes([
                        'class' => 'px-16 pt-16 pb-96 max-w-xl',
                    ])
                    ->id('dateTimePickerWeekStartsOnSunday')
                    ->schema([
                        DatePicker::make('dateTimePickerWeekStartsOnSunday')
                            ->label('Published at')
                            ->native(false)
                            ->weekStartsOnSunday()
                            ->default('2000-01-01'),
                    ]),
                Group::make()
                    ->extraAttributes([
                        'class' => 'px-16 pt-16 pb-96 max-w-xl',
                    ])
                    ->id('dateTimePickerDisabledDates')
                    ->schema([
                        DatePicker::make('dateTimePickerDisabledDates')
                            ->label('Date')
                            ->disabledDates(['2000-01-03', '2000-01-15', '2000-01-20'])
                            ->native(false)
                            ->default('2000-01-01'),
                    ]),
                Group::make()
                    ->extraAttributes([
                        'class' => 'p-16 max-w-xl',
                    ])
                    ->id('dateTimePickerAffix')
                    ->schema([
                        DatePicker::make('dateTimePickerAffix')
                            ->label('Date')
                            ->prefix('Starts')
                            ->suffix('at midnight')
                            ->default('2000-01-01'),
                    ]),
                Group::make()
                    ->extraAttributes([
                        'class' => 'p-16 max-w-xl',
                    ])
                    ->id('dateTimePickerPrefixIcon')
                    ->schema([
                        TimePicker::make('dateTimePickerPrefixIcon')
                            ->label('At')
                            ->prefixIcon(Heroicon::Play)
                            ->default('2000-01-01'),
                    ]),
                Group::make()
                    ->extraAttributes([
                        'class' => 'p-16 max-w-xl',
                    ])
                    ->id('fileUpload')
                    ->schema([
                        FileUpload::make('fileUpload')
                            ->label('Attachment'),
                    ]),
                Group::make()
                    ->extraAttributes([
                        'class' => 'p-16 max-w-5xl',
                    ])
                    ->id('richEditor')
                    ->schema([
                        RichEditor::make('richEditor')
                            ->label('Content'),
                    ]),
                Group::make()
                    ->extraAttributes([
                        'class' => 'p-16 max-w-5xl',
                    ])
                    ->id('markdownEditor')
                    ->schema([
                        MarkdownEditor::make('markdownEditor')
                            ->label('Content'),
                    ]),
                Group::make()
                    ->extraAttributes([
                        'class' => 'p-16 max-w-5xl',
                    ])
                    ->id('repeater')
                    ->schema([
                        Repeater::make('repeater')
                            ->label('Members')
                            ->columns(2)
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
                    ->extraAttributes([
                        'class' => 'p-16 max-w-5xl',
                    ])
                    ->id('repeaterTable')
                    ->schema([
                        Repeater::make('repeaterTable')
                            ->label('Members')
                            ->columns(2)
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
                            ->table([
                                Repeater\TableColumn::make('Name'),
                                Repeater\TableColumn::make('Role'),
                            ])
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
                    ->extraAttributes([
                        'class' => 'p-16 max-w-5xl',
                    ])
                    ->id('repeaterTableCompact')
                    ->schema([
                        Repeater::make('repeaterTable')
                            ->label('Members')
                            ->columns(2)
                            ->compact()
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
                            ->table([
                                Repeater\TableColumn::make('Name'),
                                Repeater\TableColumn::make('Role'),
                            ])
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
                    ->extraAttributes([
                        'class' => 'p-16 max-w-5xl',
                    ])
                    ->id('repeaterReorderableWithButtons')
                    ->schema([
                        Repeater::make('repeaterReorderableWithButtons')
                            ->label('Members')
                            ->columns(2)
                            ->reorderableWithButtons()
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
                            ]),
                    ]),
                Group::make()
                    ->extraAttributes([
                        'class' => 'p-16 max-w-5xl',
                    ])
                    ->id('collapsedRepeater')
                    ->schema([
                        Repeater::make('collapsedRepeater')
                            ->label('Qualifications')
                            ->collapsed()
                            ->defaultItems(3),
                    ]),
                Group::make()
                    ->extraAttributes([
                        'class' => 'p-16 max-w-5xl',
                    ])
                    ->id('cloneableRepeater')
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
                    ->extraAttributes([
                        'class' => 'p-16 max-w-5xl',
                    ])
                    ->id('gridRepeater')
                    ->schema([
                        Repeater::make('gridRepeater')
                            ->label('Qualifications')
                            ->grid(2)
                            ->schema([
                                TextInput::make('name')->required(),
                            ])
                            ->default([
                                ['name' => 'Tailwind CSS Level 1'],
                                ['name' => 'Alpine.js Level 1'],
                                ['name' => 'Laravel Level 1'],
                                ['name' => 'Livewire Level 1'],
                            ]),
                    ]),
                Group::make()
                    ->extraAttributes([
                        'class' => 'p-16 max-w-5xl',
                    ])
                    ->id('labelledRepeater')
                    ->schema([
                        Repeater::make('labelledRepeater')
                            ->label('Members')
                            ->columns(2)
                            ->itemLabel(fn (array $state): ?string => $state['name'] ?? null)
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
                            ]),
                    ]),
                Group::make()
                    ->extraAttributes([
                        'class' => 'p-16 max-w-5xl',
                    ])
                    ->id('simpleRepeater')
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
                    ->extraAttributes([
                        'class' => 'p-16 max-w-5xl',
                    ])
                    ->id('builder')
                    ->schema([
                        Builder::make('builder')
                            ->label('Content')
                            ->schema([
                                Builder\Block::make('heading')
                                    ->columns(2)
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
                                    ]),
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
                    ->extraAttributes([
                        'class' => 'p-16 max-w-5xl',
                    ])
                    ->id('labelledBuilder')
                    ->schema([
                        Builder::make('labelledBuilder')
                            ->label('Content')
                            ->schema([
                                Builder\Block::make('heading')
                                    ->label(function (?array $state): string {
                                        if ($state === null) {
                                            return 'Heading';
                                        }

                                        return $state['content'] ?? 'Untitled heading';
                                    })
                                    ->columns(2)
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
                                    'type' => 'heading',
                                    'data' => [
                                        'content' => null,
                                        'level' => 'h3',
                                    ],
                                ],
                            ]),
                    ]),
                Group::make()
                    ->extraAttributes([
                        'class' => 'px-16 pt-16 pb-40 max-w-5xl',
                    ])
                    ->id('builderIcons')
                    ->schema([
                        Builder::make('builderIcons')
                            ->label('Content')
                            ->schema([
                                Builder\Block::make('paragraph')
                                    ->icon(Heroicon::Bars3BottomLeft)
                                    ->schema([
                                        Textarea::make('content')
                                            ->label('Paragraph')
                                            ->required(),
                                    ]),
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
                    ->extraAttributes([
                        'class' => 'p-16 max-w-5xl',
                    ])
                    ->id('builderReorderableWithButtons')
                    ->schema([
                        Builder::make('builderReorderableWithButtons')
                            ->label('Content')
                            ->reorderableWithButtons()
                            ->schema([
                                Builder\Block::make('paragraph')
                                    ->icon(Heroicon::Bars3BottomLeft)
                                    ->schema([
                                        Textarea::make('content')
                                            ->label('Paragraph')
                                            ->required(),
                                    ]),
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
                            ]),
                    ]),
                Group::make()
                    ->extraAttributes([
                        'class' => 'p-16 max-w-5xl',
                    ])
                    ->id('collapsedBuilder')
                    ->schema([
                        Builder::make('collapsedBuilder')
                            ->label('Content')
                            ->collapsed()
                            ->schema([
                                Builder\Block::make('paragraph')
                                    ->icon(Heroicon::Bars3BottomLeft)
                                    ->schema([
                                        Textarea::make('content')
                                            ->label('Paragraph')
                                            ->required(),
                                    ]),
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
                            ]),
                    ]),
                Group::make()
                    ->extraAttributes([
                        'class' => 'p-16 max-w-5xl',
                    ])
                    ->id('cloneableBuilder')
                    ->schema([
                        Builder::make('cloneableBuilder')
                            ->label('Content')
                            ->schema([
                                Builder\Block::make('paragraph')
                                    ->icon(Heroicon::Bars3BottomLeft)
                                    ->schema([
                                        Textarea::make('content')
                                            ->label('Paragraph')
                                            ->required(),
                                    ]),
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
                    ->extraAttributes([
                        'class' => 'p-16 max-w-xl',
                    ])
                    ->id('tagsInput')
                    ->schema([
                        TagsInput::make('tagsInput')
                            ->label('Tags')
                            ->default(['Tailwind CSS', 'Alpine.js']),
                    ]),
                Group::make()
                    ->extraAttributes([
                        'class' => 'p-16 max-w-xl',
                    ])
                    ->id('textarea')
                    ->schema([
                        Textarea::make('textarea')
                            ->label('Description')
                            ->default('Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nullam euismod, nisl eget aliquam ultricies, quam sapien aliquet nunc, eget aliquam velit nisl quis nunc.'),
                    ]),
                Group::make()
                    ->extraAttributes([
                        'class' => 'p-16 max-w-5xl',
                    ])
                    ->id('keyValue')
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
                    ->extraAttributes([
                        'class' => 'p-16 max-w-5xl',
                    ])
                    ->id('reorderableKeyValue')
                    ->schema([
                        KeyValue::make('reorderableKeyValue')
                            ->label('Meta')
                            ->reorderable()
                            ->default([
                                'description' => 'Filament is a collection of Laravel packages',
                                'og:type' => 'website',
                                'og:site_name' => 'Filament',
                            ]),
                    ]),
                Group::make()
                    ->extraAttributes([
                        'class' => 'p-16 max-w-xl',
                    ])
                    ->id('colorPicker')
                    ->schema([
                        ColorPicker::make('colorPicker')
                            ->label('Color')
                            ->default('#3490dc'),
                    ]),
                Group::make()
                    ->extraAttributes([
                        'class' => 'p-16 max-w-xl',
                    ])
                    ->id('toggleButtons')
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
                    ->extraAttributes([
                        'class' => 'p-16 max-w-xl',
                    ])
                    ->id('toggleButtonsColors')
                    ->schema([
                        ToggleButtons::make('toggleButtonsColors')
                            ->label('Status')
                            ->colors([
                                'draft' => 'info',
                                'scheduled' => 'warning',
                                'published' => 'success',
                            ])
                            ->options([
                                'draft' => 'Draft',
                                'scheduled' => 'Scheduled',
                                'published' => 'Published',
                            ])
                            ->default('draft'),
                    ]),
                Group::make()
                    ->extraAttributes([
                        'class' => 'p-16 max-w-xl',
                    ])
                    ->id('toggleButtonsIcons')
                    ->schema([
                        ToggleButtons::make('toggleButtonsIcons')
                            ->label('Status')
                            ->icons([
                                'draft' => Heroicon::OutlinedPencil,
                                'scheduled' => Heroicon::OutlinedClock,
                                'published' => Heroicon::OutlinedCheckCircle,
                            ])
                            ->options([
                                'draft' => 'Draft',
                                'scheduled' => 'Scheduled',
                                'published' => 'Published',
                            ])
                            ->default('scheduled'),
                    ]),
                Group::make()
                    ->extraAttributes([
                        'class' => 'p-16 max-w-xl',
                    ])
                    ->id('toggleButtonsBoolean')
                    ->schema([
                        ToggleButtons::make('toggleButtonsBoolean')
                            ->label('Like this post?')
                            ->boolean()
                            ->default(true),
                    ]),
                Group::make()
                    ->extraAttributes([
                        'class' => 'p-16 max-w-xl',
                    ])
                    ->id('toggleButtonsInline')
                    ->schema([
                        ToggleButtons::make('toggleButtonsInline')
                            ->label('Like this post?')
                            ->boolean()
                            ->inline()
                            ->default(false),
                    ]),
                Group::make()
                    ->extraAttributes([
                        'class' => 'p-16 max-w-xl',
                    ])
                    ->id('toggleButtonsGrouped')
                    ->schema([
                        ToggleButtons::make('toggleButtonsGrouped')
                            ->label('Like this post?')
                            ->boolean()
                            ->grouped()
                            ->default(true),
                    ]),
                Group::make()
                    ->extraAttributes([
                        'class' => 'p-16 max-w-xl',
                    ])
                    ->id('toggleButtonsMultiple')
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
                    ->extraAttributes([
                        'class' => 'p-16 max-w-xl',
                    ])
                    ->id('toggleButtonsColumns')
                    ->schema([
                        ToggleButtons::make('toggleButtonsColumns')
                            ->label('Technologies')
                            ->columns(2)
                            ->options([
                                'tailwind' => 'Tailwind CSS',
                                'alpine' => 'Alpine.js',
                                'laravel' => 'Laravel',
                                'livewire' => 'Laravel Livewire',
                            ])
                            ->default('alpine'),
                    ]),
                Group::make()
                    ->extraAttributes([
                        'class' => 'p-16 max-w-xl',
                    ])
                    ->id('toggleButtonsRows')
                    ->schema([
                        ToggleButtons::make('toggleButtonsRows')
                            ->label('Technologies')
                            ->columns(2)
                            ->gridDirection('row')
                            ->options([
                                'tailwind' => 'Tailwind CSS',
                                'alpine' => 'Alpine.js',
                                'laravel' => 'Laravel',
                                'livewire' => 'Laravel Livewire',
                            ])
                            ->default('alpine'),
                    ]),
                Group::make()
                    ->extraAttributes([
                        'class' => 'p-16 max-w-xl',
                    ])
                    ->id('disabledOptionToggleButtons')
                    ->schema([
                        ToggleButtons::make('disabledOptionToggleButtons')
                            ->label('Status')
                            ->disableOptionWhen(fn (string $value): bool => $value === 'published')
                            ->options([
                                'draft' => 'Draft',
                                'scheduled' => 'Scheduled',
                                'published' => 'Published',
                            ])
                            ->default('draft'),
                    ]),
                Group::make()
                    ->extraAttributes([
                        'class' => 'p-16 max-w-xl',
                    ])
                    ->id('suffixAction')
                    ->schema([
                        TextInput::make('suffixAction')
                            ->label('Cost')
                            ->prefix('€')
                            ->suffixAction(
                                Action::make('copyCostToPrice')
                                    ->icon(Heroicon::Clipboard),
                            )
                            ->default('22.66'),
                    ]),
                Group::make()
                    ->extraAttributes([
                        'class' => 'p-16 max-w-xl',
                    ])
                    ->id('slider')
                    ->schema([
                        Slider::make('slider')
                            ->label('Slider')
                            ->default(50),
                    ]),
                Group::make()
                    ->extraAttributes([
                        'class' => 'p-16 max-w-xl',
                    ])
                    ->id('sliderRange')
                    ->schema([
                        Slider::make('sliderRange')
                            ->label('Slider')
                            ->range(minValue: 40, maxValue: 80)
                            ->default(50),
                    ]),
                Group::make()
                    ->extraAttributes([
                        'class' => 'p-16 max-w-xl',
                    ])
                    ->id('sliderMultiple')
                    ->schema([
                        Slider::make('sliderMultiple')
                            ->label('Slider')
                            ->default([20, 70]),
                    ]),
                Group::make()
                    ->extraAttributes([
                        'class' => 'p-16 max-w-xl',
                    ])
                    ->id('sliderVertical')
                    ->schema([
                        Slider::make('sliderVertical')
                            ->label('Slider')
                            ->vertical()
                            ->default(50),
                    ]),
                Group::make()
                    ->extraAttributes([
                        'class' => 'p-16 max-w-xl',
                    ])
                    ->id('sliderTooltips')
                    ->schema([
                        Slider::make('sliderTooltips')
                            ->label('Slider')
                            ->tooltips()
                            ->default(50),
                    ]),
                Group::make()
                    ->extraAttributes([
                        'class' => 'p-16 max-w-xl',
                    ])
                    ->id('sliderTooltipsMultiple')
                    ->schema([
                        Slider::make('sliderTooltipsMultiple')
                            ->label('Slider')
                            ->tooltips([true, false])
                            ->default([20, 70]),
                    ]),
                Group::make()
                    ->extraAttributes([
                        'class' => 'p-16 max-w-xl',
                    ])
                    ->id('sliderTooltipsVertical')
                    ->schema([
                        Slider::make('sliderTooltipsVertical')
                            ->label('Slider')
                            ->tooltips()
                            ->vertical()
                            ->default(50),
                    ]),
                Group::make()
                    ->extraAttributes([
                        'class' => 'p-16 max-w-xl',
                    ])
                    ->id('sliderTooltipsFormatting')
                    ->schema([
                        Slider::make('sliderTooltipsFormatting')
                            ->label('Slider')
                            ->tooltips(RawJs::make('`$${$value.toFixed(2)}`'))
                            ->default(64.99),
                    ]),
                Group::make()
                    ->extraAttributes([
                        'class' => 'p-16 max-w-xl',
                    ])
                    ->id('sliderFill')
                    ->schema([
                        Slider::make('sliderFill')
                            ->label('Slider')
                            ->fillTrack()
                            ->default(50),
                    ]),
                Group::make()
                    ->extraAttributes([
                        'class' => 'p-16 max-w-xl',
                    ])
                    ->id('sliderFillMultiple')
                    ->schema([
                        Slider::make('sliderFillMultiple')
                            ->label('Slider')
                            ->fillTrack([false, true, false])
                            ->default([20, 70]),
                    ]),
                Group::make()
                    ->extraAttributes([
                        'class' => 'p-16 max-w-xl',
                    ])
                    ->id('sliderFillVertical')
                    ->schema([
                        Slider::make('sliderFillVertical')
                            ->label('Slider')
                            ->fillTrack()
                            ->vertical()
                            ->default(50),
                    ]),
                Group::make()
                    ->extraAttributes([
                        'class' => 'p-16 max-w-xl',
                    ])
                    ->id('sliderPips')
                    ->schema([
                        Slider::make('sliderPips')
                            ->label('Slider')
                            ->pips()
                            ->default(50),
                    ]),
                Group::make()
                    ->extraAttributes([
                        'class' => 'p-16 max-w-xl',
                    ])
                    ->id('sliderPipsMultiple')
                    ->schema([
                        Slider::make('sliderPipsMultiple')
                            ->label('Slider')
                            ->pips()
                            ->default([20, 70]),
                    ]),
                Group::make()
                    ->extraAttributes([
                        'class' => 'p-16 max-w-xl',
                    ])
                    ->id('sliderPipsVertical')
                    ->schema([
                        Slider::make('sliderPipsVertical')
                            ->label('Slider')
                            ->pips()
                            ->vertical()
                            ->default(50),
                    ]),
                Group::make()
                    ->extraAttributes([
                        'class' => 'p-16 max-w-xl',
                    ])
                    ->id('sliderPipsDensity')
                    ->schema([
                        Slider::make('sliderPipsDensity')
                            ->label('Slider')
                            ->pips(density: 5)
                            ->default(50),
                    ]),
                Group::make()
                    ->extraAttributes([
                        'class' => 'p-16 max-w-xl',
                    ])
                    ->id('sliderPipsFormatting')
                    ->schema([
                        Slider::make('sliderPipsFormatting')
                            ->label('Slider')
                            ->pips()
                            ->pipsFormatter(RawJs::make('`$${$value.toFixed(2)}`'))
                            ->default(50),
                    ]),
                Group::make()
                    ->extraAttributes([
                        'class' => 'p-16 max-w-xl',
                    ])
                    ->id('sliderPipsSteps')
                    ->schema([
                        Slider::make('sliderPipsSteps')
                            ->label('Slider')
                            ->pips(PipsMode::Steps)
                            ->step(10)
                            ->default(50),
                    ]),
                Group::make()
                    ->extraAttributes([
                        'class' => 'p-16 max-w-xl',
                    ])
                    ->id('sliderPipsStepsDensity')
                    ->schema([
                        Slider::make('sliderPipsStepsDensity')
                            ->label('Slider')
                            ->pips(PipsMode::Steps, density: 5)
                            ->step(10)
                            ->default(50),
                    ]),
                Group::make()
                    ->extraAttributes([
                        'class' => 'p-16 max-w-xl',
                    ])
                    ->id('sliderPipsPositions')
                    ->schema([
                        Slider::make('sliderPipsPositions')
                            ->label('Slider')
                            ->pips(PipsMode::Positions)
                            ->pipsValues([0, 25, 50, 75, 100])
                            ->default(50),
                    ]),
                Group::make()
                    ->extraAttributes([
                        'class' => 'p-16 max-w-xl',
                    ])
                    ->id('sliderPipsCount')
                    ->schema([
                        Slider::make('sliderPipsCount')
                            ->label('Slider')
                            ->pips(PipsMode::Count)
                            ->pipsValues(5)
                            ->default(50),
                    ]),
                Group::make()
                    ->extraAttributes([
                        'class' => 'p-16 max-w-xl',
                    ])
                    ->id('sliderPipsValues')
                    ->schema([
                        Slider::make('sliderPipsValues')
                            ->label('Slider')
                            ->pips(PipsMode::Values)
                            ->pipsValues([5, 15, 25, 35, 45, 55, 65, 75, 85, 95])
                            ->default(50),
                    ]),
                Group::make()
                    ->extraAttributes([
                        'class' => 'p-16 max-w-xl',
                    ])
                    ->id('sliderPipsValuesDensity')
                    ->schema([
                        Slider::make('sliderPipsValuesDensity')
                            ->label('Slider')
                            ->pips(PipsMode::Values, density: 5)
                            ->pipsValues([5, 15, 25, 35, 45, 55, 65, 75, 85, 95])
                            ->default(50),
                    ]),
                Group::make()
                    ->extraAttributes([
                        'class' => 'p-16 max-w-xl',
                    ])
                    ->id('sliderPipsFilter')
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
                    ->extraAttributes([
                        'class' => 'p-16 max-w-xl',
                    ])
                    ->id('sliderNonLinear')
                    ->schema([
                        Slider::make('sliderNonLinear')
                            ->label('Slider')
                            ->nonLinearPoints(['20%' => 50, '50%' => 75])
                            ->pips()
                            ->default(50),
                    ]),
                Group::make()
                    ->extraAttributes([
                        'class' => 'p-16 max-w-xl',
                    ])
                    ->id('codeEditor')
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
                    ->extraAttributes([
                        'class' => 'p-16 max-w-xl',
                    ])
                    ->id('codeEditorLanguage')
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
