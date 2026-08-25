<?php

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\RestrictsFileUploadsToFormComponents;
use Filament\Forms\Form;
use Filament\Tests\Forms\Fixtures\Livewire;
use Filament\Tests\TestCase;
use Illuminate\Contracts\View\View;
use Livewire\Features\SupportFileUploads\FileUploadConfiguration;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

use function Filament\Tests\livewire;

uses(TestCase::class);

function temporaryUploadedFileReference(string $filename): string
{
    FileUploadConfiguration::storage();

    return (string) str(
        TemporaryUploadedFile::createFromLivewire($filename)->serializeForLivewireResponse(),
    )->after('livewire-file:');
}

it('blocks `_startUpload` when the form has no file-upload components', function () {
    livewire(RestrictedUploadsTestComponentWithoutFileUpload::class)
        ->call('_startUpload', 'data.text', [['name' => 'a.jpg', 'size' => 1024, 'type' => 'image/jpeg']], false)
        ->assertForbidden();
});

it('blocks `_startUpload` for a property path that does not map to any form component', function () {
    livewire(RestrictedUploadsTestComponentWithFileUpload::class)
        ->call('_startUpload', 'data.somethingElse.fileKey', [['name' => 'a.jpg', 'size' => 1024, 'type' => 'image/jpeg']], false)
        ->assertForbidden();
});

it('allows `_startUpload` for the exact state path of a `FileUpload` field', function () {
    livewire(RestrictedUploadsTestComponentWithFileUpload::class)
        ->call('_startUpload', 'data.photo', [['name' => 'a.jpg', 'size' => 1024, 'type' => 'image/jpeg']], false)
        ->assertDispatched('upload:generatedSignedUrl');
});

it('allows `_startUpload` when the property path has a fileKey suffix appended to a `FileUpload` state path', function () {
    livewire(RestrictedUploadsTestComponentWithFileUpload::class)
        ->call('_startUpload', 'data.photo.fileKey', [['name' => 'a.jpg', 'size' => 1024, 'type' => 'image/jpeg']], false)
        ->assertDispatched('upload:generatedSignedUrl');
});

it('blocks `_startUpload` when the `FileUpload` field is hidden', function () {
    livewire(RestrictedUploadsTestComponentWithHiddenFileUpload::class)
        ->call('_startUpload', 'data.photo.fileKey', [['name' => 'a.jpg', 'size' => 1024, 'type' => 'image/jpeg']], false)
        ->assertForbidden();
});

it('allows `_startUpload` for a `FileUpload` field nested inside a layout component', function () {
    livewire(RestrictedUploadsTestComponentWithNestedFileUpload::class)
        ->call('_startUpload', 'data.section.photo.fileKey', [['name' => 'a.jpg', 'size' => 1024, 'type' => 'image/jpeg']], false)
        ->assertDispatched('upload:generatedSignedUrl');
});

it('blocks `_startUpload` for a layout component state path (`Section`) even though it has children with uploads', function () {
    livewire(RestrictedUploadsTestComponentWithNestedFileUpload::class)
        ->call('_startUpload', 'data.section.fileKey', [['name' => 'a.jpg', 'size' => 1024, 'type' => 'image/jpeg']], false)
        ->assertForbidden();
});

it('blocks `_finishUpload` when no form component matches', function () {
    livewire(RestrictedUploadsTestComponentWithFileUpload::class)
        ->call('_finishUpload', 'data.somethingElse.fileKey', [temporaryUploadedFileReference('tampered.jpg')], false)
        ->assertForbidden();
});

it('allows `_finishUpload` when the property path maps to a `FileUpload` field', function () {
    livewire(RestrictedUploadsTestComponentWithFileUpload::class)
        ->call('_finishUpload', 'data.photo.fileKey', [temporaryUploadedFileReference('legitimate.jpg')], false)
        ->assertDispatched('upload:finished');
});

it('allows `_startUpload` for `componentFileAttachments.{statePath}` uploads targeting a component that supports file attachments', function () {
    livewire(RestrictedUploadsTestComponentWithMarkdownEditor::class)
        ->call('_startUpload', 'componentFileAttachments.data.content', [['name' => 'a.jpg', 'size' => 1024, 'type' => 'image/jpeg']], false)
        ->assertDispatched('upload:generatedSignedUrl');
});

it('allows `_startUpload` for `componentFileAttachments.{statePath}.{fileKey}` uploads targeting a component that supports file attachments', function () {
    livewire(RestrictedUploadsTestComponentWithMarkdownEditor::class)
        ->call('_startUpload', 'componentFileAttachments.data.content.fileKey', [['name' => 'a.jpg', 'size' => 1024, 'type' => 'image/jpeg']], false)
        ->assertDispatched('upload:generatedSignedUrl');
});

it('blocks `_startUpload` for `componentFileAttachments.{statePath}` when no component at that path supports file attachments', function () {
    livewire(RestrictedUploadsTestComponentWithFileUpload::class)
        ->call('_startUpload', 'componentFileAttachments.data.somethingElse', [['name' => 'a.jpg', 'size' => 1024, 'type' => 'image/jpeg']], false)
        ->assertForbidden();
});

it('allows `_finishUpload` for `componentFileAttachments.{statePath}` uploads targeting a component that supports file attachments', function () {
    livewire(RestrictedUploadsTestComponentWithMarkdownEditor::class)
        ->call('_finishUpload', 'componentFileAttachments.data.content', [temporaryUploadedFileReference('legitimate.jpg')], false)
        ->assertDispatched('upload:finished');
});

it('blocks `_finishUpload` for `componentFileAttachments.{statePath}` when the underlying component is unrelated', function () {
    livewire(RestrictedUploadsTestComponentWithFileUpload::class)
        ->call('_finishUpload', 'componentFileAttachments.data.tampered', [temporaryUploadedFileReference('tampered.jpg')], false)
        ->assertForbidden();
});

it('allows `_startUpload` for a `FileUpload` field inside a `Repeater` row at the dynamic state path', function () {
    $component = livewire(RestrictedUploadsTestComponentWithRepeaterFileUpload::class);

    // Repeater rows have generated keys; grab the first row's key from state and target its `FileUpload`.
    $rows = $component->get('data.items');
    $firstKey = array_key_first($rows);

    $component
        ->call('_startUpload', "data.items.{$firstKey}.photo.fileKey", [['name' => 'a.jpg', 'size' => 1024, 'type' => 'image/jpeg']], false)
        ->assertDispatched('upload:generatedSignedUrl');
});

it('blocks `_startUpload` targeting a `Repeater` row property that does not exist', function () {
    livewire(RestrictedUploadsTestComponentWithRepeaterFileUpload::class)
        ->call('_startUpload', 'data.items.nonexistent-row-key.photo.fileKey', [['name' => 'a.jpg', 'size' => 1024, 'type' => 'image/jpeg']], false)
        ->assertForbidden();
});

it('resolves uploads across multiple forms on the same Livewire component', function () {
    livewire(RestrictedUploadsTestComponentWithMultipleForms::class)
        ->call('_startUpload', 'photoForm.photo.fileKey', [['name' => 'a.jpg', 'size' => 1024, 'type' => 'image/jpeg']], false)
        ->assertDispatched('upload:generatedSignedUrl');
});

it('blocks `_startUpload` for a property name that exists on neither of the multiple forms', function () {
    livewire(RestrictedUploadsTestComponentWithMultipleForms::class)
        ->call('_startUpload', 'photoForm.somethingElse.fileKey', [['name' => 'a.jpg', 'size' => 1024, 'type' => 'image/jpeg']], false)
        ->assertForbidden();
});

class RestrictedUploadsTestComponentWithoutFileUpload extends Livewire
{
    use RestrictsFileUploadsToFormComponents;

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('text'),
            ])
            ->statePath('data');
    }

    public function render(): View
    {
        return view('forms.fixtures.form');
    }
}

class RestrictedUploadsTestComponentWithFileUpload extends Livewire
{
    use RestrictsFileUploadsToFormComponents;

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                FileUpload::make('photo'),
            ])
            ->statePath('data');
    }

    public function render(): View
    {
        return view('forms.fixtures.form');
    }
}

class RestrictedUploadsTestComponentWithHiddenFileUpload extends Livewire
{
    use RestrictsFileUploadsToFormComponents;

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                FileUpload::make('photo')
                    ->visible(false),
            ])
            ->statePath('data');
    }

    public function render(): View
    {
        return view('forms.fixtures.form');
    }
}

class RestrictedUploadsTestComponentWithNestedFileUpload extends Livewire
{
    use RestrictsFileUploadsToFormComponents;

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make()
                    ->key('section')
                    ->statePath('section')
                    ->schema([
                        FileUpload::make('photo'),
                    ]),
            ])
            ->statePath('data');
    }

    public function render(): View
    {
        return view('forms.fixtures.form');
    }
}

class RestrictedUploadsTestComponentWithMarkdownEditor extends Livewire
{
    use RestrictsFileUploadsToFormComponents;

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                MarkdownEditor::make('content'),
            ])
            ->statePath('data');
    }

    public function render(): View
    {
        return view('forms.fixtures.form');
    }
}

class RestrictedUploadsTestComponentWithRepeaterFileUpload extends Livewire
{
    use RestrictsFileUploadsToFormComponents;

    public function mount(): void
    {
        $this->data = ['items' => [['photo' => null]]];

        $this->form->fill($this->data);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Repeater::make('items')
                    ->schema([
                        FileUpload::make('photo'),
                    ]),
            ])
            ->statePath('data');
    }

    public function render(): View
    {
        return view('forms.fixtures.form');
    }
}

class RestrictedUploadsTestComponentWithMultipleForms extends Livewire
{
    use RestrictsFileUploadsToFormComponents;

    public function mount(): void
    {
        $this->photoForm->fill();
        $this->detailsForm->fill();
    }

    protected function getForms(): array
    {
        return [
            'photoForm',
            'detailsForm',
        ];
    }

    public function photoForm(Form $form): Form
    {
        return $form
            ->schema([
                FileUpload::make('photo'),
            ])
            ->statePath('photoForm');
    }

    public function detailsForm(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name'),
            ])
            ->statePath('detailsForm');
    }

    public function render(): View
    {
        return view('forms.fixtures.form');
    }
}
