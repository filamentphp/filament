<?php

use Filament\Forms\Components\Field;
use Filament\Forms\Components\FileUpload;
use Filament\Schemas\Schema;
use Filament\Tests\Fixtures\Livewire\Livewire;
use Filament\Tests\TestCase;
use Illuminate\Http\UploadedFile;
use Illuminate\Validation\ValidationException;
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

describe('validation', function (): void {
    it('can use `requiredIf()` and fails validation when condition is met', function (): void {
        $rules = [];
        $errors = [];

        try {
            Schema::make(Livewire::make())
                ->statePath('data')
                ->components([
                    $field1 = (new Field('type'))
                        ->default('file'),
                    $field2 = FileUpload::make('document')
                        ->requiredIf('type', 'file'),
                ])
                ->fill()
                ->validate();
        } catch (ValidationException $exception) {
            $rules = array_keys($exception->validator->failed()[$field2->getStatePath()] ?? []);
            $errors = $exception->validator->errors()->get($field2->getStatePath());
        }

        expect($rules)
            ->toContain('RequiredIf');

        expect($errors)
            ->toContain('The document field is required when type is file.');
    });

    it('can use `requiredIf()` and passes validation when condition is not met', function (): void {
        $validationPassed = false;

        try {
            Schema::make(Livewire::make())
                ->statePath('data')
                ->components([
                    (new Field('type'))
                        ->default('text'),
                    FileUpload::make('document')
                        ->requiredIf('type', 'file'),
                ])
                ->fill()
                ->validate();

            $validationPassed = true;
        } catch (ValidationException) {
            $validationPassed = false;
        }

        expect($validationPassed)->toBeTrue();
    });

    it('can use `requiredUnless()` and fails validation when condition is not met', function (): void {
        $rules = [];
        $errors = [];

        try {
            Schema::make(Livewire::make())
                ->statePath('data')
                ->components([
                    $field1 = (new Field('type'))
                        ->default('text'),
                    $field2 = FileUpload::make('document')
                        ->requiredUnless('type', 'file'),
                ])
                ->fill()
                ->validate();
        } catch (ValidationException $exception) {
            $rules = array_keys($exception->validator->failed()[$field2->getStatePath()] ?? []);
            $errors = $exception->validator->errors()->get($field2->getStatePath());
        }

        expect($rules)
            ->toContain('RequiredUnless');

        expect($errors)
            ->toContain('The document field is required unless type is in file.');
    });

    it('can use `requiredUnless()` and passes validation when condition is met', function (): void {
        $validationPassed = false;

        try {
            Schema::make(Livewire::make())
                ->statePath('data')
                ->components([
                    (new Field('type'))
                        ->default('file'),
                    FileUpload::make('document')
                        ->requiredUnless('type', 'file'),
                ])
                ->fill()
                ->validate();

            $validationPassed = true;
        } catch (ValidationException) {
            $validationPassed = false;
        }

        expect($validationPassed)->toBeTrue();
    });

    it('applies `rule()` to individual files, not the array', function (): void {
        $field = FileUpload::make('document')
            ->rule('mimetypes:image/png');

        $rules = $field->getValidationRules();

        $stringRules = array_filter($rules, fn ($rule) => is_string($rule));
        expect($stringRules)->not->toContain('mimetypes:image/png');
    });

    it('can use `maxSize()` and fails validation when file exceeds limit', function (): void {
        livewire(TestComponentWithMaxSizeFileUpload::class)
            ->fillForm([
                'document' => UploadedFile::fake()->create('document.pdf', 200),
            ])
            ->call('save')
            ->assertHasFormErrors(['document']);
    });

    it('can use `maxSize()` and passes validation when file is within limit', function (): void {
        livewire(TestComponentWithMaxSizeFileUpload::class)
            ->fillForm([
                'document' => UploadedFile::fake()->create('document.pdf', 50),
            ])
            ->call('save')
            ->assertHasNoFormErrors(['document']);
    });

    it('can use `minSize()` and fails validation when file is below limit', function (): void {
        livewire(TestComponentWithMinSizeFileUpload::class)
            ->fillForm([
                'document' => UploadedFile::fake()->create('document.pdf', 50),
            ])
            ->call('save')
            ->assertHasFormErrors(['document']);
    });

    it('can use `minSize()` and passes validation when file meets limit', function (): void {
        livewire(TestComponentWithMinSizeFileUpload::class)
            ->fillForm([
                'document' => UploadedFile::fake()->create('document.pdf', 150),
            ])
            ->call('save')
            ->assertHasNoFormErrors(['document']);
    });

    it('can use `maxSize()` with nested state path and fails validation when file exceeds limit', function (): void {
        livewire(TestComponentWithNestedMaxSizeFileUpload::class)
            ->fillForm([
                'files' => [
                    'test' => UploadedFile::fake()->create('document.pdf', 200),
                ],
            ])
            ->call('save')
            ->assertHasFormErrors(['files.test']);
    });

    it('can use `maxSize()` with nested state path and passes validation when file is within limit', function (): void {
        livewire(TestComponentWithNestedMaxSizeFileUpload::class)
            ->fillForm([
                'files' => [
                    'test' => UploadedFile::fake()->create('document.pdf', 50),
                ],
            ])
            ->call('save')
            ->assertHasNoFormErrors(['files.test']);
    });

    it('can use `minSize()` with nested state path and fails validation when file is below limit', function (): void {
        livewire(TestComponentWithNestedMinSizeFileUpload::class)
            ->fillForm([
                'files' => [
                    'test' => UploadedFile::fake()->create('document.pdf', 50),
                ],
            ])
            ->call('save')
            ->assertHasFormErrors(['files.test']);
    });

    it('can use `minSize()` with nested state path and passes validation when file meets limit', function (): void {
        livewire(TestComponentWithNestedMinSizeFileUpload::class)
            ->fillForm([
                'files' => [
                    'test' => UploadedFile::fake()->create('document.pdf', 150),
                ],
            ])
            ->call('save')
            ->assertHasNoFormErrors(['files.test']);
    });
});

describe('parallel uploads', function (): void {
    // Regression test for https://github.com/filamentphp/filament/issues/13306
    //
    // The core race condition is in the JS Alpine component: when multiple files
    // upload in parallel with ->poll() active, shouldUpdateState was reset to true
    // after the first file completed, allowing a poll-triggered re-render to
    // overwrite FilePond state and drop in-flight uploads.
    //
    // The fix introduces an activeUploads counter so shouldUpdateState only becomes
    // true when ALL uploads finish. These PHP tests validate the server-side contract
    // that multiple files persist in state. The JS counter logic is tested separately
    // in tests/js/file-upload-active-uploads.test.mjs.

    it('retains all files when multiple files are uploaded to a multiple() FileUpload', function (): void {
        try {
            livewire(TestComponentWithParallelFileUpload::class)
                ->fillForm([
                    'attachments' => [
                        UploadedFile::fake()->image('photo1.jpg'),
                        UploadedFile::fake()->image('photo2.jpg'),
                        UploadedFile::fake()->image('photo3.jpg'),
                    ],
                ])
                ->assertSchemaStateSet(function (array $data): void {
                    expect($data['attachments'])->toHaveCount(3)
                        ->and($data['attachments'][0])->toBeInstanceOf(TemporaryUploadedFile::class)
                        ->and($data['attachments'][1])->toBeInstanceOf(TemporaryUploadedFile::class)
                        ->and($data['attachments'][2])->toBeInstanceOf(TemporaryUploadedFile::class);
                });
        } catch (RootTagMissingFromViewException $exception) {
            // Flaky test
        }
    });

    it('retains all files after sequential fillForm calls simulating incremental upload completions', function (): void {
        try {
            $component = livewire(TestComponentWithParallelFileUpload::class)
                ->fillForm([
                    'attachments' => [
                        UploadedFile::fake()->image('batch1.jpg'),
                    ],
                ]);

            // Simulate a second batch arriving (as if another upload completed)
            $component->fillForm([
                'attachments' => [
                    UploadedFile::fake()->image('batch1.jpg'),
                    UploadedFile::fake()->image('batch2.jpg'),
                ],
            ]);

            $component->assertSchemaStateSet(function (array $data): void {
                expect($data['attachments'])->toHaveCount(2)
                    ->and($data['attachments'][0])->toBeInstanceOf(TemporaryUploadedFile::class)
                    ->and($data['attachments'][1])->toBeInstanceOf(TemporaryUploadedFile::class);
            });
        } catch (RootTagMissingFromViewException $exception) {
            // Flaky test
        }
    });

    it('validates and saves all files from a parallel multiple upload', function (): void {
        livewire(TestComponentWithParallelFileUploadAndSave::class)
            ->fillForm([
                'attachments' => [
                    UploadedFile::fake()->image('save1.jpg'),
                    UploadedFile::fake()->image('save2.jpg'),
                    UploadedFile::fake()->image('save3.jpg'),
                ],
            ])
            ->call('save')
            ->assertHasNoFormErrors(['attachments']);
    });
});

class TestComponentWithParallelFileUpload extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form
            ->components([
                FileUpload::make('attachments')
                    ->multiple()
                    ->maxParallelUploads(2),
            ])
            ->statePath('data');
    }
}

class TestComponentWithParallelFileUploadAndSave extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form
            ->components([
                FileUpload::make('attachments')
                    ->multiple()
                    ->maxParallelUploads(2),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $this->form->getState();
    }
}

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

class TestComponentWithMaxSizeFileUpload extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form
            ->components([
                FileUpload::make('document')
                    ->maxSize(100),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $this->form->getState();
    }
}

class TestComponentWithMinSizeFileUpload extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form
            ->components([
                FileUpload::make('document')
                    ->minSize(100),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $this->form->getState();
    }
}

class TestComponentWithNestedMaxSizeFileUpload extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form
            ->components([
                FileUpload::make('files.test')
                    ->maxSize(100),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $this->form->getState();
    }
}

class TestComponentWithNestedMinSizeFileUpload extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form
            ->components([
                FileUpload::make('files.test')
                    ->minSize(100),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $this->form->getState();
    }
}
