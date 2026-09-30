<?php

use Filament\Forms\Components\Component;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Form;
use Filament\Tests\Forms\Fixtures\Livewire;
use Filament\Tests\TestCase;
use Illuminate\Contracts\View\View;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Features\SupportFileUploads\FileUploadConfiguration;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

use function Filament\Tests\livewire;

uses(TestCase::class);

it('UploadedFile should be converted to TemporaryUploadedFile', function () {
    livewire(TestComponentWithFileUpload::class)
        ->fillForm([
            'single-file' => UploadedFile::fake()->image('single-file.jpg'),
            'multiple-files' => [
                UploadedFile::fake()->image('multiple-file1.jpg'),
                UploadedFile::fake()->image('multiple-file2.jpg'),
            ],
        ])
        ->assertFormSet(function (array $data) {
            expect($data['single-file'][0])->toBeInstanceOf(TemporaryUploadedFile::class)
                ->and($data['multiple-files'][0])->toBeInstanceOf(TemporaryUploadedFile::class)
                ->and($data['multiple-files'][1])->toBeInstanceOf(TemporaryUploadedFile::class);
        });
});

it('uses `hashName()` to derive the stored file extension from its MIME type', function () {
    FileUploadConfiguration::storage();

    $gifContents = UploadedFile::fake()->image('image.gif')->getContent();
    $temporaryFileName = TemporaryUploadedFile::generateHashNameWithOriginalNameEmbedded(
        UploadedFile::fake()->createWithContent('image.html', $gifContents),
    );

    Storage::disk('tmp-for-tests')->put("livewire-tmp/{$temporaryFileName}", $gifContents);

    $file = TemporaryUploadedFile::createFromLivewire($temporaryFileName);
    $component = livewire(TestComponentWithFileUpload::class);
    $field = $component->instance()->form->getComponent(
        static fn (Component $component): bool => $component->getName() === 'single-file',
    );
    $storedFileName = $field->getUploadedFileNameForStorage($file);

    expect($file)->toBeInstanceOf(TemporaryUploadedFile::class)
        ->and($file->getMimeType())->toBe('image/gif')
        ->and($file->getClientOriginalExtension())->toBe('html')
        ->and($storedFileName)
        ->toBe($file->hashName())
        ->toEndWith('.gif')
        ->not->toEndWith('.html');
});

class TestComponentWithFileUpload extends Livewire
{
    public function form(Form $form): Form
    {
        return $form
            ->schema([
                FileUpload::make('single-file'),
                FileUpload::make('multiple-files')->multiple(),
            ])
            ->statePath('data');
    }

    public function render(): View
    {
        return view('forms.fixtures.form');
    }
}
