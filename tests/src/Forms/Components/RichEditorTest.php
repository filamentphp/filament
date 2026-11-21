<?php

use Filament\Forms\ComponentContainer;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Form;
use Filament\Tests\Forms\Fixtures\Livewire;
use Filament\Tests\TestCase;
use Illuminate\Contracts\View\View;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

use function Filament\Tests\livewire;

uses(TestCase::class);

it('sanitizes stored HTML when rendering the disabled state to prevent stored XSS', function () {
    livewire(TestComponentWithDisabledRichEditor::class)
        ->fillForm([
            'content' => '<p>Safe paragraph</p><img src=x onerror="window.__xss = true">',
        ])
        ->assertDontSeeHtml('onerror="window.__xss = true"');
});

it('does not persist attachments through `saveUploadedFileAttachment()` when the editor is disabled', function (string $editorClass, bool $isFormDisabled, bool $isChildDisabled, bool $isParentDisabled, bool $isFieldDisabled): void {
    Storage::fake('tmp-for-tests');
    Storage::fake('public');

    $uploadedFile = UploadedFile::fake()->image('attachment.jpg');
    $temporaryFileName = TemporaryUploadedFile::generateHashNameWithOriginalNameEmbedded($uploadedFile);
    Storage::disk('tmp-for-tests')->put("livewire-tmp/{$temporaryFileName}", $uploadedFile->getContent());
    $temporaryFile = TemporaryUploadedFile::createFromLivewire($temporaryFileName);

    $editor = $editorClass::make('content')
        ->fileAttachmentsDisk('public')
        ->disabled($isFieldDisabled)
        ->dehydrated()
        ->saveUploadedFileAttachmentsUsing(static fn (TemporaryUploadedFile $file): string => $file->storeAs('attachments', 'attachment.jpg', 'public'))
        ->getUploadedAttachmentUrlUsing(static fn (): string => 'https://example.test/attachment.jpg');

    $form = Form::make($livewire = Livewire::make())->statePath('data')->disabled($isFormDisabled);
    $parent = Group::make()->container($form)->disabled($isParentDisabled);
    ComponentContainer::make($livewire)
        ->parentComponent($parent)
        ->disabled($isChildDisabled)
        ->components([$editor])
        ->fill();

    $shouldStore = (! $isFormDisabled) && (! $isChildDisabled) && (! $isParentDisabled) && (! $isFieldDisabled);

    expect($editor->saveUploadedFileAttachment($temporaryFile))->toBe($shouldStore ? 'https://example.test/attachment.jpg' : null);

    if ($shouldStore) {
        expect(Storage::disk('public')->get('attachments/attachment.jpg'))->toBe($uploadedFile->getContent());
    } else {
        Storage::disk('public')->assertMissing('attachments/attachment.jpg');
    }
})->with([
    'rich editor' => RichEditor::class,
    'markdown editor' => MarkdownEditor::class,
])->with([
    'enabled' => [false, false, false, false],
    'disabled top-level form' => [true, false, false, false],
    'disabled child container' => [false, true, false, false],
    'disabled parent layout' => [false, false, true, false],
    'disabled editor' => [false, false, false, true],
]);

class TestComponentWithDisabledRichEditor extends Livewire
{
    public function form(Form $form): Form
    {
        return $form
            ->schema([
                RichEditor::make('content')
                    ->disabled(),
            ])
            ->statePath('data');
    }

    public function render(): View
    {
        return view('forms.fixtures.form');
    }
}
