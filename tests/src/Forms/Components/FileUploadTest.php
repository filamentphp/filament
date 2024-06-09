<?php

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Form;
use Filament\Tests\Forms\Fixtures\Livewire;
use Filament\Tests\TestCase;
use Illuminate\Contracts\View\View;
use Illuminate\Http\UploadedFile;
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
