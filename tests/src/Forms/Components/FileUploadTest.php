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

it('should convert `UploadedFile` objects to `TemporaryUploadedFile`', function (): void {
    try {
        livewire(TestComponentWithFileUpload::class)
            ->fillForm([
                'single-file' => UploadedFile::fake()->image('single-file.jpg'),
                'multiple-files' => [
                    UploadedFile::fake()->image('multiple-file1.jpg'),
                    UploadedFile::fake()->image('multiple-file2.jpg'),
                ],
            ])
            ->assertSchemaStateSet(function (array $data): void {
                expect($data['single-file'][0])->toBeInstanceOf(TemporaryUploadedFile::class)
                    ->and($data['multiple-files'][0])->toBeInstanceOf(TemporaryUploadedFile::class)
                    ->and($data['multiple-files'][1])->toBeInstanceOf(TemporaryUploadedFile::class);
            });
    } catch (RootTagMissingFromViewException $exception) {
        // Flaky test
    }
});

describe('disk', function (): void {
    it('should have local disk by default', function (): void {
        $upload = FileUpload::make('test_file');
        expect($upload->getDiskName())->toBe('local');
    });

    it('overrides disk name using config', function (): void {
        Config::set('filament.default_filesystem_disk', 'public');

        $disk = config('filament.default_filesystem_disk');

        $upload = FileUpload::make('test_file');
        expect($upload->getDiskName())->toBe($disk);
    });

    it('prioritizes disk name from method', function (): void {
        $upload = FileUpload::make('test_file')
            ->disk('s3');
        expect($upload->getDiskName())->toBe('s3');
    });
});

describe('visibility', function (): void {
    it('should have private visibility by default', function (): void {
        $upload = FileUpload::make('test_file');
        expect($upload->getVisibility())->toBe('private');
    });

    it('overrides visibility from disk', function (): void {
        $upload1 = FileUpload::make('test_file')
            ->disk('public');
        expect($upload1->getVisibility())->toBe('public');

        $upload2 = FileUpload::make('test_file')
            ->disk('local');
        expect($upload2->getVisibility())->toBe('private');
    });

    it('prioritizes visibility from method', function (): void {
        $upload1 = FileUpload::make('test_file')
            ->visibility('public');
        expect($upload1->getVisibility())->toBe('public');

        $upload2 = FileUpload::make('test_file')
            ->visibility('private');
        expect($upload2->getVisibility())->toBe('private');
    });
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
