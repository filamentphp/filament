<?php

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Concerns\RestrictsFileUploadsToSchemaComponents;
use Filament\Schemas\Schema;
use Filament\Tests\Fixtures\Livewire\Livewire;
use Filament\Tests\TestCase;

use function Filament\Tests\livewire;

uses(TestCase::class);

it('blocks `_startUpload` when the schema has no file-upload components', function (): void {
    livewire(RestrictedUploadsTestComponentWithoutFileUpload::class)
        ->call('_startUpload', 'data.text', [['name' => 'a.jpg', 'size' => 1024, 'type' => 'image/jpeg']], false)
        ->assertForbidden();
});

it('does not restrict `_startUpload` when the component does not use `RestrictsFileUploadsToSchemaComponents`', function (): void {
    livewire(UnrestrictedUploadsTestComponent::class)
        ->call('_startUpload', 'data.text', [['name' => 'a.jpg', 'size' => 1024, 'type' => 'image/jpeg']], false)
        ->assertDispatched('upload:generatedSignedUrl');
});

it('blocks `_startUpload` for a property path that does not map to any schema component', function (): void {
    livewire(RestrictedUploadsTestComponentWithFileUpload::class)
        ->call('_startUpload', 'data.somethingElse.fileKey', [['name' => 'a.jpg', 'size' => 1024, 'type' => 'image/jpeg']], false)
        ->assertForbidden();
});

it('allows `_startUpload` for the exact state path of a `FileUpload` field', function (): void {
    livewire(RestrictedUploadsTestComponentWithFileUpload::class)
        ->call('_startUpload', 'data.photo', [['name' => 'a.jpg', 'size' => 1024, 'type' => 'image/jpeg']], false)
        ->assertDispatched('upload:generatedSignedUrl');
});

it('allows `_startUpload` when the property path has a fileKey suffix appended to a `FileUpload` state path', function (): void {
    livewire(RestrictedUploadsTestComponentWithFileUpload::class)
        ->call('_startUpload', 'data.photo.fileKey', [['name' => 'a.jpg', 'size' => 1024, 'type' => 'image/jpeg']], false)
        ->assertDispatched('upload:generatedSignedUrl');
});

it('blocks `_startUpload` when the `FileUpload` field is hidden', function (): void {
    livewire(RestrictedUploadsTestComponentWithHiddenFileUpload::class)
        ->call('_startUpload', 'data.photo.fileKey', [['name' => 'a.jpg', 'size' => 1024, 'type' => 'image/jpeg']], false)
        ->assertForbidden();
});

it('allows `_startUpload` for a `FileUpload` field nested inside a layout component', function (): void {
    livewire(RestrictedUploadsTestComponentWithNestedFileUpload::class)
        ->call('_startUpload', 'data.section.photo.fileKey', [['name' => 'a.jpg', 'size' => 1024, 'type' => 'image/jpeg']], false)
        ->assertDispatched('upload:generatedSignedUrl');
});

it('blocks `_startUpload` for a layout component state path (`Section`) even though it has children with uploads', function (): void {
    livewire(RestrictedUploadsTestComponentWithNestedFileUpload::class)
        ->call('_startUpload', 'data.section.fileKey', [['name' => 'a.jpg', 'size' => 1024, 'type' => 'image/jpeg']], false)
        ->assertForbidden();
});

it('blocks `_finishUpload` when no schema component matches', function (): void {
    livewire(RestrictedUploadsTestComponentWithFileUpload::class)
        ->call('_finishUpload', 'data.somethingElse.fileKey', ['livewire-tmp/tampered.jpg'], false)
        ->assertForbidden();
});

it('allows `_finishUpload` when the property path maps to a `FileUpload` field', function (): void {
    livewire(RestrictedUploadsTestComponentWithFileUpload::class)
        ->call('_finishUpload', 'data.photo.fileKey', ['livewire-tmp/legitimate.jpg'], false)
        ->assertDispatched('upload:finished');
});

it('allows `_startUpload` for `componentFileAttachments.{statePath}` uploads targeting a component that supports file attachments', function (): void {
    livewire(RestrictedUploadsTestComponentWithMarkdownEditor::class)
        ->call('_startUpload', 'componentFileAttachments.data.content', [['name' => 'a.jpg', 'size' => 1024, 'type' => 'image/jpeg']], false)
        ->assertDispatched('upload:generatedSignedUrl');
});

it('allows `_startUpload` for `componentFileAttachments.{statePath}.{fileKey}` uploads targeting a component that supports file attachments', function (): void {
    livewire(RestrictedUploadsTestComponentWithMarkdownEditor::class)
        ->call('_startUpload', 'componentFileAttachments.data.content.fileKey', [['name' => 'a.jpg', 'size' => 1024, 'type' => 'image/jpeg']], false)
        ->assertDispatched('upload:generatedSignedUrl');
});

it('blocks `_startUpload` for `componentFileAttachments.{statePath}` when no component at that path supports file attachments', function (): void {
    livewire(RestrictedUploadsTestComponentWithFileUpload::class)
        ->call('_startUpload', 'componentFileAttachments.data.somethingElse', [['name' => 'a.jpg', 'size' => 1024, 'type' => 'image/jpeg']], false)
        ->assertForbidden();
});

it('blocks `_startUpload` when a component supporting file attachments has them explicitly disabled', function (): void {
    livewire(RestrictedUploadsTestComponentWithMarkdownEditorAttachmentsDisabled::class)
        ->call('_startUpload', 'componentFileAttachments.data.content', [['name' => 'a.jpg', 'size' => 1024, 'type' => 'image/jpeg']], false)
        ->assertForbidden();
});

it('allows `_finishUpload` for `componentFileAttachments.{statePath}` uploads targeting a component that supports file attachments', function (): void {
    livewire(RestrictedUploadsTestComponentWithMarkdownEditor::class)
        ->call('_finishUpload', 'componentFileAttachments.data.content', ['livewire-tmp/legitimate.jpg'], false)
        ->assertDispatched('upload:finished');
});

it('blocks `_finishUpload` for `componentFileAttachments.{statePath}` when the underlying component is unrelated', function (): void {
    livewire(RestrictedUploadsTestComponentWithFileUpload::class)
        ->call('_finishUpload', 'componentFileAttachments.data.tampered', ['livewire-tmp/tampered.jpg'], false)
        ->assertForbidden();
});

it('allows `_startUpload` for a `FileUpload` field inside a `Repeater` row at the dynamic state path', function (): void {
    $component = livewire(RestrictedUploadsTestComponentWithRepeaterFileUpload::class);

    // Repeater rows have generated keys; grab the first row's key from state and target its `FileUpload`.
    $rows = $component->get('data.items');
    $firstKey = array_key_first($rows);

    $component
        ->call('_startUpload', "data.items.{$firstKey}.photo.fileKey", [['name' => 'a.jpg', 'size' => 1024, 'type' => 'image/jpeg']], false)
        ->assertDispatched('upload:generatedSignedUrl');
});

it('blocks `_startUpload` targeting a `Repeater` row property that does not exist', function (): void {
    livewire(RestrictedUploadsTestComponentWithRepeaterFileUpload::class)
        ->call('_startUpload', 'data.items.nonexistent-row-key.photo.fileKey', [['name' => 'a.jpg', 'size' => 1024, 'type' => 'image/jpeg']], false)
        ->assertForbidden();
});

it('resolves uploads across multiple schemas on the same Livewire component', function (): void {
    livewire(RestrictedUploadsTestComponentWithMultipleSchemas::class)
        ->call('_startUpload', 'photoForm.photo.fileKey', [['name' => 'a.jpg', 'size' => 1024, 'type' => 'image/jpeg']], false)
        ->assertDispatched('upload:generatedSignedUrl');
});

it('blocks `_startUpload` for a property name that exists on neither of the multiple schemas', function (): void {
    livewire(RestrictedUploadsTestComponentWithMultipleSchemas::class)
        ->call('_startUpload', 'photoForm.somethingElse.fileKey', [['name' => 'a.jpg', 'size' => 1024, 'type' => 'image/jpeg']], false)
        ->assertForbidden();
});

class UnrestrictedUploadsTestComponent extends Livewire
{
    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('text'),
            ])
            ->statePath('data');
    }
}

class RestrictedUploadsTestComponentWithoutFileUpload extends Livewire
{
    use RestrictsFileUploadsToSchemaComponents;

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('text'),
            ])
            ->statePath('data');
    }
}

class RestrictedUploadsTestComponentWithFileUpload extends Livewire
{
    use RestrictsFileUploadsToSchemaComponents;

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                FileUpload::make('photo'),
            ])
            ->statePath('data');
    }
}

class RestrictedUploadsTestComponentWithHiddenFileUpload extends Livewire
{
    use RestrictsFileUploadsToSchemaComponents;

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                FileUpload::make('photo')
                    ->visible(false),
            ])
            ->statePath('data');
    }
}

class RestrictedUploadsTestComponentWithNestedFileUpload extends Livewire
{
    use RestrictsFileUploadsToSchemaComponents;

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make()
                    ->key('section')
                    ->statePath('section')
                    ->schema([
                        FileUpload::make('photo'),
                    ]),
            ])
            ->statePath('data');
    }
}

class RestrictedUploadsTestComponentWithMarkdownEditor extends Livewire
{
    use RestrictsFileUploadsToSchemaComponents;

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                MarkdownEditor::make('content'),
            ])
            ->statePath('data');
    }
}

class RestrictedUploadsTestComponentWithMarkdownEditorAttachmentsDisabled extends Livewire
{
    use RestrictsFileUploadsToSchemaComponents;

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                MarkdownEditor::make('content')
                    ->fileAttachments(false),
            ])
            ->statePath('data');
    }
}

class RestrictedUploadsTestComponentWithRepeaterFileUpload extends Livewire
{
    use RestrictsFileUploadsToSchemaComponents;

    public function mount(): void
    {
        $this->data = ['items' => [['photo' => null]]];

        $this->form->fill($this->data);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Repeater::make('items')
                    ->schema([
                        FileUpload::make('photo'),
                    ]),
            ])
            ->statePath('data');
    }
}

class RestrictedUploadsTestComponentWithMultipleSchemas extends Livewire
{
    use RestrictsFileUploadsToSchemaComponents;

    public function mount(): void
    {
        $this->photoForm->fill();
        $this->detailsForm->fill();
    }

    public function photoForm(Schema $schema): Schema
    {
        return $schema
            ->components([
                FileUpload::make('photo'),
            ])
            ->statePath('photoForm');
    }

    public function detailsForm(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name'),
            ])
            ->statePath('detailsForm');
    }
}
