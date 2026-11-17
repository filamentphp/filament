<?php

use Filament\Forms\Components\CodeEditor;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Field;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Slider;
use Filament\Forms\Components\TagsInput;
use Filament\Schemas\Schema;
use Filament\Tests\Fixtures\Livewire\Livewire;
use Filament\Tests\TestCase;
use Illuminate\Support\HtmlString;

uses(TestCase::class);

it('renders custom async loading content and can restore the default', function (string $fieldClass, string $contentType): void {
    $field = $fieldClass::make('details')
        ->loadingPlaceholder(loadingPlaceholder: static fn (Field $component) => $contentType === 'view'
            ? view('loading-placeholder', ['label' => $component->getName() . ' <loading>'])
            : new HtmlString('<div data-testid="custom-loading-placeholder">details &lt;loading&gt;</div>'));

    if (($field instanceof Select) || ($field instanceof DateTimePicker)) {
        $field->native(false);
    }

    Schema::make(Livewire::make())->components([$field])->fill();

    expect($field->toHtml())
        ->toContain('data-testid="custom-loading-placeholder"', 'details &lt;loading&gt;')
        ->not->toContain('&lt;div data-testid=');

    expect($field->loadingPlaceholder(null)->toHtml())
        ->not->toContain('data-testid="custom-loading-placeholder"')
        ->toContain(e(__('filament::loading.label')));
})->with([
    CodeEditor::class,
    ColorPicker::class,
    DateTimePicker::class,
    FileUpload::class,
    KeyValue::class,
    MarkdownEditor::class,
    RichEditor::class,
    Select::class,
    Slider::class,
    TagsInput::class,
])->with(['view', 'html']);

it('does not add a loading placeholder to usable server-rendered controls', function (): void {
    $fields = [
        Select::make('native')->options(['one' => 'One']),
        DateTimePicker::make('native_date'),
        MarkdownEditor::make('disabled')->disabled()->default('Existing content'),
    ];

    Schema::make(Livewire::make())->components($fields)->fill();

    foreach ($fields as $field) {
        $field->loadingPlaceholder(new HtmlString('<div data-testid="unexpected-loading-placeholder"></div>'));

        expect($field->toHtml())->not->toContain('data-testid="unexpected-loading-placeholder"');
    }
});
