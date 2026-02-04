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
