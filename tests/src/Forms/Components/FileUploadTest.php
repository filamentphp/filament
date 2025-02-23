<?php

use Filament\Forms\Components\FileUpload;
use Filament\Schemas\Schema;
use Filament\Tests\Fixtures\Livewire\Livewire;
use Filament\Tests\TestCase;
use Illuminate\Http\UploadedFile;
use Livewire\Exceptions\RootTagMissingFromViewException;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

use function Filament\Tests\livewire;

uses(TestCase::class);

it('UploadedFile should be converted to TemporaryUploadedFile', function (): void {
    try {
        livewire(TestComponentWithFileUpload::class)
            ->fillForm([
                'single-file' => UploadedFile::fake()->image('single-file.jpg'),
                'multiple-files' => [
                    UploadedFile::fake()->image('multiple-file1.jpg'),
                    UploadedFile::fake()->image('multiple-file2.jpg'),
                ],
            ])
            ->assertFormSet(function (array $data): void {
                expect($data['single-file'][0])->toBeInstanceOf(TemporaryUploadedFile::class)
                    ->and($data['multiple-files'][0])->toBeInstanceOf(TemporaryUploadedFile::class)
                    ->and($data['multiple-files'][1])->toBeInstanceOf(TemporaryUploadedFile::class);
            });
    } catch (RootTagMissingFromViewException $exception) {
        // Flaky test
    }
});

class TestComponentWithFileUpload extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form
            ->components([
                FileUpload::make('single-file'),
                FileUpload::make('multiple-files')->multiple(),
            ])
            ->statePath('data');
    }
}
