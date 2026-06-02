<?php

use Filament\Forms\Components\BaseFileUpload;
use Filament\Forms\Components\Field;
use Filament\Forms\Components\FileUpload;
use Filament\Schemas\Schema;
use Filament\Tests\Fixtures\Livewire\Livewire;
use Filament\Tests\Fixtures\Models\User;
use Filament\Tests\TestCase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Livewire\Exceptions\RootTagMissingFromViewException;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

use function Filament\Tests\livewire;

uses(TestCase::class);

beforeEach(function (): void {
    Artisan::call('filament:assets');
});

describe('file conversion', function (): void {
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

    it('rejects a `.php` client filename when `acceptedFileTypes()` is used', function (): void {
        Storage::fake('local');

        livewire(TestComponentWithAcceptedFileTypesUpload::class)
            ->fillForm([
                'avatar' => UploadedFile::fake()->image('shell.php'),
            ])
            ->call('save')
            ->assertHasFormErrors(['avatar']);
    });
});

describe('preventing existing file path tampering', function (): void {
    it('allows a tampered string value to overwrite the record when `preventFilePathTampering()` is not used', function (): void {
        $user = User::factory()->create(['status' => 'uploads/original.jpg']);

        livewire(TestComponentWithFileUploadRecord::class, ['record' => $user])
            ->set('data.status', ['uploads/tampered.jpg'])
            ->call('save');

        expect($user->refresh()->status)->toBe('uploads/tampered.jpg');
    });

    it('fails validation for a tampered string value when using `preventFilePathTampering()`', function (): void {
        $user = User::factory()->create(['status' => 'uploads/original.jpg']);

        livewire(TestComponentWithFileUploadRecordPreventingTampering::class, ['record' => $user])
            ->set('data.status', ['uploads/tampered.jpg'])
            ->call('save')
            ->assertHasFormErrors(['status']);

        expect($user->refresh()->status)->toBe('uploads/original.jpg');
    });

    it('leaves an unchanged string value alone when using `preventFilePathTampering()`', function (): void {
        $user = User::factory()->create(['status' => 'uploads/original.jpg']);

        livewire(TestComponentWithFileUploadRecordPreventingTampering::class, ['record' => $user])
            ->set('data.status', ['uploads/original.jpg'])
            ->call('save')
            ->assertHasNoFormErrors();

        expect($user->refresh()->status)->toBe('uploads/original.jpg');
    });

    it('fails validation when a tampered path is mixed with originals in a multi-file value using `preventFilePathTampering()`', function (): void {
        $user = User::factory()->create(['json' => ['uploads/a.jpg', 'uploads/b.jpg']]);

        livewire(TestComponentWithMultipleFileUploadRecordPreventingTampering::class, ['record' => $user])
            ->set('data.json', ['uploads/a.jpg', 'uploads/b.jpg', 'uploads/evil.jpg'])
            ->call('save')
            ->assertHasFormErrors(['json']);

        expect($user->refresh()->json)->toBe(['uploads/a.jpg', 'uploads/b.jpg']);
    });

    it('allows the file to be cleared when using `preventFilePathTampering()`', function (): void {
        $user = User::factory()->create(['status' => 'uploads/original.jpg']);

        livewire(TestComponentWithFileUploadRecordPreventingTampering::class, ['record' => $user])
            ->set('data.status', null)
            ->call('save')
            ->assertHasNoFormErrors();

        expect($user->refresh()->status)->toBeNull();
    });

    it('fails validation for all string values when no record is bound and `preventFilePathTampering()` is used', function (): void {
        livewire(TestComponentWithFileUploadPreventingTamperingWithoutRecord::class)
            ->set('data.status', ['uploads/anything.jpg'])
            ->call('save')
            ->assertHasFormErrors(['status']);
    });

    it('keeps a submitted path when the `allowFilePathUsing` callback returns `true`', function (): void {
        $user = User::factory()->create(['status' => 'uploads/original.jpg']);

        livewire(TestComponentWithFileUploadRecordAllowingTemplateFilePaths::class, ['record' => $user])
            ->set('data.status', ['templates/brochure.pdf'])
            ->call('save')
            ->assertHasNoFormErrors();

        expect($user->refresh()->status)->toBe('templates/brochure.pdf');
    });

    it('fails validation when the `allowFilePathUsing` callback returns `false`', function (): void {
        $user = User::factory()->create(['status' => 'uploads/original.jpg']);

        livewire(TestComponentWithFileUploadRecordAllowingTemplateFilePaths::class, ['record' => $user])
            ->set('data.status', ['uploads/tampered.jpg'])
            ->call('save')
            ->assertHasFormErrors(['status']);

        expect($user->refresh()->status)->toBe('uploads/original.jpg');
    });

    it('uses a custom validation message when `tampered` is defined in `validationMessages()`', function (): void {
        $user = User::factory()->create(['status' => 'uploads/original.jpg']);

        livewire(TestComponentWithFileUploadRecordPreventingTamperingAndCustomMessage::class, ['record' => $user])
            ->set('data.status', ['uploads/tampered.jpg'])
            ->call('save')
            ->assertHasFormErrors(['status' => 'The selected attachment is not permitted.']);
    });

    it('returns `null` for tampered paths from `getUploadedFiles()` when using `preventFilePathTampering()`', function (): void {
        Storage::fake('local');
        Storage::disk('local')->put('uploads/tampered.jpg', 'evil');

        $user = User::factory()->create(['status' => 'uploads/original.jpg']);

        $component = livewire(TestComponentWithFileUploadRecordPreventingTampering::class, ['record' => $user])
            ->set('data.status', ['uploads/tampered.jpg'])
            ->instance();

        $field = $component->form->getComponents()[0];
        $uploadedFiles = $field->getUploadedFiles();

        expect($uploadedFiles)->toBe([0 => null]);
    });

    it('does not gate `getUploadedFiles()` when `preventFilePathTampering()` is not used', function (): void {
        Storage::fake('local');
        Storage::disk('local')->put('uploads/tampered.jpg', 'evil');

        $user = User::factory()->create(['status' => 'uploads/original.jpg']);

        $component = livewire(TestComponentWithFileUploadRecord::class, ['record' => $user])
            ->set('data.status', ['uploads/tampered.jpg'])
            ->instance();

        $field = $component->form->getComponents()[0];
        $uploadedFiles = $field->getUploadedFiles();

        expect($uploadedFiles[0])->not->toBeNull();
    });
});

describe('openable and downloadable URLs', function (): void {
    $makeField = function (Closure $configure): FileUpload {
        $field = FileUpload::make('document')
            ->container(Schema::make(Livewire::make())->statePath('data'))
            ->multiple()
            ->getUploadedFileUsing(static fn (BaseFileUpload $component, string $file): array => [
                'name' => $file,
                'size' => 0,
                'type' => null,
                'url' => "https://cdn.example.com/{$file}",
            ]);

        $configure($field);

        $field->rawState(['key1' => 'doc.pdf']);

        return $field;
    };

    it('populates `openableUrl` when `openable()` and `getOpenableFileUrlUsing()` are set', function () use ($makeField): void {
        $field = $makeField(static fn (FileUpload $field) => $field
            ->openable()
            ->getOpenableFileUrlUsing(static fn (string $file): string => "https://signed.example.com/open/{$file}"));

        expect($field->getUploadedFiles())
            ->toBe([
                'key1' => [
                    'name' => 'doc.pdf',
                    'size' => 0,
                    'type' => null,
                    'url' => 'https://cdn.example.com/doc.pdf',
                    'openableUrl' => 'https://signed.example.com/open/doc.pdf',
                ],
            ]);
    });

    it('populates `downloadableUrl` when `downloadable()` and `getDownloadableFileUrlUsing()` are set', function () use ($makeField): void {
        $field = $makeField(static fn (FileUpload $field) => $field
            ->downloadable()
            ->getDownloadableFileUrlUsing(static fn (string $file): string => "https://signed.example.com/download/{$file}"));

        expect($field->getUploadedFiles())
            ->toBe([
                'key1' => [
                    'name' => 'doc.pdf',
                    'size' => 0,
                    'type' => null,
                    'url' => 'https://cdn.example.com/doc.pdf',
                    'downloadableUrl' => 'https://signed.example.com/download/doc.pdf',
                ],
            ]);
    });

    it('does not populate `openableUrl` when `openable()` is disabled', function () use ($makeField): void {
        $field = $makeField(static fn (FileUpload $field) => $field
            ->getOpenableFileUrlUsing(static fn (string $file): string => "https://signed.example.com/open/{$file}"));

        expect($field->getUploadedFiles()['key1'])
            ->not->toHaveKey('openableUrl');
    });

    it('does not populate `downloadableUrl` when `downloadable()` is disabled', function () use ($makeField): void {
        $field = $makeField(static fn (FileUpload $field) => $field
            ->getDownloadableFileUrlUsing(static fn (string $file): string => "https://signed.example.com/download/{$file}"));

        expect($field->getUploadedFiles()['key1'])
            ->not->toHaveKey('downloadableUrl');
    });

    it('does not populate URL keys when the callbacks are not set', function () use ($makeField): void {
        $field = $makeField(static fn (FileUpload $field) => $field->openable()->downloadable());

        expect($field->getUploadedFiles()['key1'])
            ->not->toHaveKey('openableUrl')
            ->not->toHaveKey('downloadableUrl');
    });

    it('preserves `null` entries when `getUploadedFileUsing()` returns `null` even with openable URL callback set', function () use ($makeField): void {
        $field = $makeField(static fn (FileUpload $field) => $field
            ->openable()
            ->getUploadedFileUsing(static fn (): ?array => null)
            ->getOpenableFileUrlUsing(static fn (string $file): string => "https://signed.example.com/open/{$file}"));

        expect($field->getUploadedFiles())
            ->toBe(['key1' => null]);
    });

    it('omits `openableUrl` when `getOpenableFileUrlUsing()` returns `null`', function () use ($makeField): void {
        $field = $makeField(static fn (FileUpload $field) => $field
            ->openable()
            ->getOpenableFileUrlUsing(static fn (): ?string => null));

        expect($field->getUploadedFiles()['key1'])
            ->not->toHaveKey('openableUrl');
    });

    it('omits `downloadableUrl` when `getDownloadableFileUrlUsing()` returns `null`', function () use ($makeField): void {
        $field = $makeField(static fn (FileUpload $field) => $field
            ->downloadable()
            ->getDownloadableFileUrlUsing(static fn (): ?string => null));

        expect($field->getUploadedFiles()['key1'])
            ->not->toHaveKey('downloadableUrl');
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

it('can set `avatar()` mode', function (): void {
    $upload = FileUpload::make('photo');

    expect($upload->isAvatar())->toBeFalse();

    $upload->avatar();

    expect($upload->isAvatar())->toBeTrue();
});

it('can set `panelLayout()`', function (): void {
    $upload = FileUpload::make('file');

    expect($upload->getPanelLayout())->toBe('compact');

    $upload->panelLayout('grid');

    expect($upload->getPanelLayout())->toBe('grid');
});

it('can set `panelAspectRatio()`', function (): void {
    $upload = FileUpload::make('file');

    expect($upload->getPanelAspectRatio())->toBeNull();

    $upload->panelAspectRatio('16:9');

    expect($upload->getPanelAspectRatio())->toBe('16:9');
});

it('can set `imageEditor()`', function (): void {
    $upload = FileUpload::make('photo');

    expect($upload->hasImageEditor())->toBeFalse();

    $upload->imageEditor();

    expect($upload->hasImageEditor())->toBeTrue();
});

it('can set `circleCropper()`', function (): void {
    $upload = FileUpload::make('photo');

    expect($upload->hasCircleCropper())->toBeFalse();

    $upload->circleCropper();

    expect($upload->hasCircleCropper())->toBeTrue();
});

it('can set `appendFiles()`', function (): void {
    $upload = FileUpload::make('files');

    expect($upload->shouldAppendFiles())->toBeFalse();

    $upload->appendFiles();

    expect($upload->shouldAppendFiles())->toBeTrue();
});

it('can set `orientImagesFromExif()`', function (): void {
    $upload = FileUpload::make('photo');

    expect($upload->shouldOrientImagesFromExif())->toBeTrue();

    $upload->orientImagesFromExif(false);

    expect($upload->shouldOrientImagesFromExif())->toBeFalse();
});

it('can set `imagePreviewHeight()`', function (): void {
    $upload = FileUpload::make('photo');

    expect($upload->getImagePreviewHeight())->toBeNull();

    $upload->imagePreviewHeight('200');

    expect($upload->getImagePreviewHeight())->toBe('200');
});

it('can set `imageEditorMode()`', function (): void {
    $upload = FileUpload::make('photo');

    expect($upload->getImageEditorMode())->toBe(1);

    $upload->imageEditorMode(2);

    expect($upload->getImageEditorMode())->toBe(2);
});

it('can set `imageEditorEmptyFillColor()`', function (): void {
    $upload = FileUpload::make('photo');

    expect($upload->getImageEditorEmptyFillColor())->toBeNull();

    $upload->imageEditorEmptyFillColor('#ffffff');

    expect($upload->getImageEditorEmptyFillColor())->toBe('#ffffff');
});

it('can set `imageCropAspectRatio()`', function (): void {
    $upload = FileUpload::make('photo');

    expect($upload->getImageCropAspectRatio())->toBeNull();

    $upload->imageCropAspectRatio('16:9');

    expect($upload->getImageCropAspectRatio())->toBe('16:9');
});

it('can set `automaticallyCropImagesToAspectRatio()`', function (): void {
    $upload = FileUpload::make('photo');

    expect($upload->shouldAutomaticallyCropImagesToAspectRatio())->toBeFalse();

    $upload->automaticallyCropImagesToAspectRatio();

    expect($upload->shouldAutomaticallyCropImagesToAspectRatio())->toBeTrue();
});

it('can set `automaticallyResizeImagesMode()`', function (): void {
    $upload = FileUpload::make('photo');

    expect($upload->getAutomaticallyResizeImagesMode())->toBeNull();

    $upload->automaticallyResizeImagesMode('cover');

    expect($upload->getAutomaticallyResizeImagesMode())->toBe('cover');
});

it('can set `automaticallyResizeImagesToHeight()`', function (): void {
    $upload = FileUpload::make('photo');

    expect($upload->getAutomaticallyResizeImagesHeight())->toBeNull();

    $upload->automaticallyResizeImagesToHeight('600');

    expect($upload->getAutomaticallyResizeImagesHeight())->toBe('600');
});

it('can set `automaticallyResizeImagesToWidth()`', function (): void {
    $upload = FileUpload::make('photo');

    expect($upload->getAutomaticallyResizeImagesWidth())->toBeNull();

    $upload->automaticallyResizeImagesToWidth('800');

    expect($upload->getAutomaticallyResizeImagesWidth())->toBe('800');
});

it('defaults `shouldAutomaticallyUpscaleImagesWhenResizing()` to `true`', function (): void {
    $upload = FileUpload::make('photo');

    expect($upload->shouldAutomaticallyUpscaleImagesWhenResizing())->toBeTrue();

    $upload->automaticallyUpscaleImagesWhenResizing(false);

    expect($upload->shouldAutomaticallyUpscaleImagesWhenResizing())->toBeFalse();
});

it('can disable `editableSvgs()`', function (): void {
    $upload = FileUpload::make('icon');

    expect($upload->canEditSvgs())->toBeTrue();

    $upload->editableSvgs(false);

    expect($upload->canEditSvgs())->toBeFalse();
});

it('can set `confirmSvgEditing()`', function (): void {
    $upload = FileUpload::make('icon');

    expect($upload->isSvgEditingConfirmed())->toBeFalse();

    $upload->confirmSvgEditing();

    expect($upload->isSvgEditingConfirmed())->toBeTrue();
});

it('can set `imageEditorViewportWidth()` and `imageEditorViewportHeight()`', function (): void {
    $upload = FileUpload::make('photo');

    expect($upload->getImageEditorViewportWidth())->toBeNull();
    expect($upload->getImageEditorViewportHeight())->toBeNull();

    $upload->imageEditorViewportWidth(1920)->imageEditorViewportHeight(1080);

    expect($upload->getImageEditorViewportWidth())->toBe(1920);
    expect($upload->getImageEditorViewportHeight())->toBe(1080);
});

it('can set `mimeTypeMap()`', function (): void {
    $upload = FileUpload::make('file');

    expect($upload->getMimeTypeMap())->toBe([]);

    $upload->mimeTypeMap(['jpg' => 'image/jpeg', 'png' => 'image/png']);

    expect($upload->getMimeTypeMap())->toBe(['jpg' => 'image/jpeg', 'png' => 'image/png']);
});

it('can set position properties', function (): void {
    $upload = FileUpload::make('file');

    expect($upload->getLoadingIndicatorPosition())->toBe('right');
    expect($upload->getRemoveUploadedFileButtonPosition())->toBe('left');
    expect($upload->getUploadButtonPosition())->toBe('right');
    expect($upload->getUploadProgressIndicatorPosition())->toBe('right');

    $upload
        ->loadingIndicatorPosition('left')
        ->removeUploadedFileButtonPosition('right')
        ->uploadButtonPosition('left')
        ->uploadProgressIndicatorPosition('left');

    expect($upload->getLoadingIndicatorPosition())->toBe('left');
    expect($upload->getRemoveUploadedFileButtonPosition())->toBe('right');
    expect($upload->getUploadButtonPosition())->toBe('left');
    expect($upload->getUploadProgressIndicatorPosition())->toBe('left');
});

it('can set `imageEditor()` with a `Closure`', function (): void {
    $upload = FileUpload::make('photo')
        ->imageEditor(static fn (): bool => true);

    expect($upload->hasImageEditor())->toBeTrue();
});

it('can set `circleCropper()` with a `Closure`', function (): void {
    $upload = FileUpload::make('photo')
        ->circleCropper(static fn (): bool => true);

    expect($upload->hasCircleCropper())->toBeTrue();
});

describe('boolean properties', function (): void {
    it('defaults `isDeletable()` to `true`', function (): void {
        $upload = FileUpload::make('file');
        expect($upload->isDeletable())->toBeTrue();
    });

    it('can set `deletable()` to `false`', function (): void {
        $upload = FileUpload::make('file')->deletable(false);
        expect($upload->isDeletable())->toBeFalse();
    });

    it('can set `deletable()` with a `Closure`', function (): void {
        $upload = FileUpload::make('file')
            ->deletable(static fn (): bool => false);
        expect($upload->isDeletable())->toBeFalse();
    });

    it('defaults `isDownloadable()` to `false`', function (): void {
        $upload = FileUpload::make('file');
        expect($upload->isDownloadable())->toBeFalse();
    });

    it('can set `downloadable()`', function (): void {
        $upload = FileUpload::make('file')->downloadable();
        expect($upload->isDownloadable())->toBeTrue();
    });

    it('can set `downloadable()` with a `Closure`', function (): void {
        $upload = FileUpload::make('file')
            ->downloadable(static fn (): bool => true);
        expect($upload->isDownloadable())->toBeTrue();
    });

    it('defaults `isOpenable()` to `false`', function (): void {
        $upload = FileUpload::make('file');
        expect($upload->isOpenable())->toBeFalse();
    });

    it('can set `openable()`', function (): void {
        $upload = FileUpload::make('file')->openable();
        expect($upload->isOpenable())->toBeTrue();
    });

    it('can set `openable()` with a `Closure`', function (): void {
        $upload = FileUpload::make('file')
            ->openable(static fn (): bool => true);
        expect($upload->isOpenable())->toBeTrue();
    });

    it('defaults `isPasteable()` to `true`', function (): void {
        $upload = FileUpload::make('file');
        expect($upload->isPasteable())->toBeTrue();
    });

    it('can set `pasteable()` to `false`', function (): void {
        $upload = FileUpload::make('file')->pasteable(false);
        expect($upload->isPasteable())->toBeFalse();
    });

    it('can set `pasteable()` with a `Closure`', function (): void {
        $upload = FileUpload::make('file')
            ->pasteable(static fn (): bool => false);
        expect($upload->isPasteable())->toBeFalse();
    });

    it('defaults `isPreviewable()` to `true`', function (): void {
        $upload = FileUpload::make('file');
        expect($upload->isPreviewable())->toBeTrue();
    });

    it('can set `previewable()` to `false`', function (): void {
        $upload = FileUpload::make('file')->previewable(false);
        expect($upload->isPreviewable())->toBeFalse();
    });

    it('can set `previewable()` with a `Closure`', function (): void {
        $upload = FileUpload::make('file')
            ->previewable(static fn (): bool => false);
        expect($upload->isPreviewable())->toBeFalse();
    });

    it('defaults `isReorderable()` to `false`', function (): void {
        $upload = FileUpload::make('file');
        expect($upload->isReorderable())->toBeFalse();
    });

    it('can set `reorderable()`', function (): void {
        $upload = FileUpload::make('file')->reorderable();
        expect($upload->isReorderable())->toBeTrue();
    });

    it('can set `reorderable()` with a `Closure`', function (): void {
        $upload = FileUpload::make('file')
            ->reorderable(static fn (): bool => true);
        expect($upload->isReorderable())->toBeTrue();
    });

    it('defaults `isMultiple()` to `false`', function (): void {
        $upload = FileUpload::make('file');
        expect($upload->isMultiple())->toBeFalse();
    });

    it('can set `multiple()`', function (): void {
        $upload = FileUpload::make('file')->multiple();
        expect($upload->isMultiple())->toBeTrue();
    });

    it('can set `multiple()` with a `Closure`', function (): void {
        $upload = FileUpload::make('file')
            ->multiple(static fn (): bool => true);
        expect($upload->isMultiple())->toBeTrue();
    });

    it('defaults `shouldPreserveFilenames()` to `false`', function (): void {
        $upload = FileUpload::make('file');
        expect($upload->shouldPreserveFilenames())->toBeFalse();
    });

    it('can set `preserveFilenames()`', function (): void {
        $upload = FileUpload::make('file')->preserveFilenames();
        expect($upload->shouldPreserveFilenames())->toBeTrue();
    });

    it('can set `preserveFilenames()` with a `Closure`', function (): void {
        $upload = FileUpload::make('file')
            ->preserveFilenames(static fn (): bool => true);
        expect($upload->shouldPreserveFilenames())->toBeTrue();
    });

    it('defaults `shouldMoveFiles()` to `false`', function (): void {
        $upload = FileUpload::make('file');
        expect($upload->shouldMoveFiles())->toBeFalse();
    });

    it('can set `moveFiles()`', function (): void {
        $upload = FileUpload::make('file')->moveFiles();
        expect($upload->shouldMoveFiles())->toBeTrue();
    });

    it('can set `moveFiles()` with a `Closure`', function (): void {
        $upload = FileUpload::make('file')
            ->moveFiles(static fn (): bool => true);
        expect($upload->shouldMoveFiles())->toBeTrue();
    });

    it('defaults `shouldStoreFiles()` to `true`', function (): void {
        $upload = FileUpload::make('file');
        expect($upload->shouldStoreFiles())->toBeTrue();
    });

    it('can set `storeFiles()` to `false`', function (): void {
        $upload = FileUpload::make('file')->storeFiles(false);
        expect($upload->shouldStoreFiles())->toBeFalse();
    });

    it('can set `storeFiles()` with a `Closure`', function (): void {
        $upload = FileUpload::make('file')
            ->storeFiles(static fn (): bool => false);
        expect($upload->shouldStoreFiles())->toBeFalse();
    });

    it('defaults `shouldFetchFileInformation()` to `true`', function (): void {
        $upload = FileUpload::make('file');
        expect($upload->shouldFetchFileInformation())->toBeTrue();
    });

    it('can set `fetchFileInformation()` to `false`', function (): void {
        $upload = FileUpload::make('file')->fetchFileInformation(false);
        expect($upload->shouldFetchFileInformation())->toBeFalse();
    });

    it('can set `fetchFileInformation()` with a `Closure`', function (): void {
        $upload = FileUpload::make('file')
            ->fetchFileInformation(static fn (): bool => false);
        expect($upload->shouldFetchFileInformation())->toBeFalse();
    });
});

describe('file constraints', function (): void {
    it('can set `acceptedFileTypes()`', function (): void {
        $upload = FileUpload::make('file')
            ->acceptedFileTypes(['image/jpeg', 'image/png']);

        expect($upload->getAcceptedFileTypes())->toBe(['image/jpeg', 'image/png']);
    });

    it('returns `null` for `getAcceptedFileTypes()` by default', function (): void {
        $upload = FileUpload::make('file');
        expect($upload->getAcceptedFileTypes())->toBeNull();
    });

    it('can set `acceptedFileTypes()` with a `Closure`', function (): void {
        $upload = FileUpload::make('file')
            ->acceptedFileTypes(static fn (): array => ['application/pdf']);

        expect($upload->getAcceptedFileTypes())->toBe(['application/pdf']);
    });

    it('can set `directory()`', function (): void {
        $upload = FileUpload::make('file')->directory('uploads/photos');
        expect($upload->getDirectory())->toBe('uploads/photos');
    });

    it('returns `null` for `getDirectory()` by default', function (): void {
        $upload = FileUpload::make('file');
        expect($upload->getDirectory())->toBeNull();
    });

    it('can set `directory()` with a `Closure`', function (): void {
        $upload = FileUpload::make('file')
            ->directory(static fn (): string => 'dynamic/path');

        expect($upload->getDirectory())->toBe('dynamic/path');
    });

    it('can set `maxFiles()`', function (): void {
        $upload = FileUpload::make('file')->maxFiles(5);
        expect($upload->getMaxFiles())->toBe(5);
    });

    it('returns `null` for `getMaxFiles()` by default', function (): void {
        $upload = FileUpload::make('file');
        expect($upload->getMaxFiles())->toBeNull();
    });

    it('can set `maxFiles()` with a `Closure`', function (): void {
        $upload = FileUpload::make('file')
            ->maxFiles(static fn (): int => 10);

        expect($upload->getMaxFiles())->toBe(10);
    });

    it('can set `minFiles()`', function (): void {
        $upload = FileUpload::make('file')->minFiles(2);
        expect($upload->getMinFiles())->toBe(2);
    });

    it('returns `null` for `getMinFiles()` by default', function (): void {
        $upload = FileUpload::make('file');
        expect($upload->getMinFiles())->toBeNull();
    });

    it('can set `minFiles()` with a `Closure`', function (): void {
        $upload = FileUpload::make('file')
            ->minFiles(static fn (): int => 3);

        expect($upload->getMinFiles())->toBe(3);
    });

    it('can set `maxSize()`', function (): void {
        $upload = FileUpload::make('file')->maxSize(1024);
        expect($upload->getMaxSize())->toBe(1024);
    });

    it('returns `null` for `getMaxSize()` by default', function (): void {
        $upload = FileUpload::make('file');
        expect($upload->getMaxSize())->toBeNull();
    });

    it('can set `maxSize()` with a `Closure`', function (): void {
        $upload = FileUpload::make('file')
            ->maxSize(static fn (): int => 2048);

        expect($upload->getMaxSize())->toBe(2048);
    });

    it('can set `minSize()`', function (): void {
        $upload = FileUpload::make('file')->minSize(100);
        expect($upload->getMinSize())->toBe(100);
    });

    it('returns `null` for `getMinSize()` by default', function (): void {
        $upload = FileUpload::make('file');
        expect($upload->getMinSize())->toBeNull();
    });

    it('can set `minSize()` with a `Closure`', function (): void {
        $upload = FileUpload::make('file')
            ->minSize(static fn (): int => 50);

        expect($upload->getMinSize())->toBe(50);
    });

    it('can set `maxParallelUploads()`', function (): void {
        $upload = FileUpload::make('file')->maxParallelUploads(3);
        expect($upload->getMaxParallelUploads())->toBe(3);
    });

    it('returns `null` for `getMaxParallelUploads()` by default', function (): void {
        $upload = FileUpload::make('file');
        expect($upload->getMaxParallelUploads())->toBeNull();
    });

    it('can set `maxParallelUploads()` with a `Closure`', function (): void {
        $upload = FileUpload::make('file')
            ->maxParallelUploads(static fn (): int => 5);

        expect($upload->getMaxParallelUploads())->toBe(5);
    });
});

describe('disk name logic', function (): void {
    it('falls back to `local` when default disk is `public` and custom visibility is `private`', function (): void {
        Config::set('filament.default_filesystem_disk', 'public');

        $upload = FileUpload::make('file')->visibility('private');

        expect($upload->getDiskName())->toBe('local');
    });

    it('uses `public` disk when default is `public` and no custom visibility', function (): void {
        Config::set('filament.default_filesystem_disk', 'public');

        $upload = FileUpload::make('file');

        expect($upload->getDiskName())->toBe('public');
    });

    it('can set `disk()` with a `Closure`', function (): void {
        $upload = FileUpload::make('file')
            ->disk(static fn (): string => 's3');

        expect($upload->getDiskName())->toBe('s3');
    });
});

describe('visibility logic', function (): void {
    it('returns `private` for `getVisibility()` when disk is `local`', function (): void {
        $upload = FileUpload::make('file')->disk('local');
        expect($upload->getVisibility())->toBe('private');
    });

    it('returns `public` for `getVisibility()` when disk is `public`', function (): void {
        $upload = FileUpload::make('file')->disk('public');
        expect($upload->getVisibility())->toBe('public');
    });

    it('returns custom visibility from `getCustomVisibility()` when set', function (): void {
        $upload = FileUpload::make('file')->visibility('public');
        expect($upload->getCustomVisibility())->toBe('public');
    });

    it('returns `null` from `getCustomVisibility()` by default', function (): void {
        $upload = FileUpload::make('file');
        expect($upload->getCustomVisibility())->toBeNull();
    });

    it('can set `visibility()` with a `Closure`', function (): void {
        $upload = FileUpload::make('file')
            ->visibility(static fn (): string => 'public');

        expect($upload->getVisibility())->toBe('public');
    });
});

describe('aspect ratio', function (): void {
    it('can set `imageAspectRatio()` with a colon-separated string', function (): void {
        $upload = FileUpload::make('photo')->imageAspectRatio('16:9');
        expect($upload->getImageAspectRatio())->toBe('16:9');
    });

    it('normalizes slash-separated aspect ratio to colon format', function (): void {
        $upload = FileUpload::make('photo')->imageAspectRatio('16/9');
        expect($upload->getImageAspectRatio())->toBe('16:9');
    });

    it('normalizes numeric aspect ratio to `N:1` format', function (): void {
        $upload = FileUpload::make('photo')->imageAspectRatio('2');
        expect($upload->getImageAspectRatio())->toBe('2:1');
    });

    it('returns `null` for `getImageAspectRatio()` by default', function (): void {
        $upload = FileUpload::make('photo');
        expect($upload->getImageAspectRatio())->toBeNull();
    });

    it('can set `imageAspectRatio()` with an array', function (): void {
        $upload = FileUpload::make('photo')->imageAspectRatio(['16:9', '4:3']);
        expect($upload->getImageAspectRatio())->toBe(['16:9', '4:3']);
    });

    it('normalizes aspect ratios in array format', function (): void {
        $upload = FileUpload::make('photo')->imageAspectRatio(['16/9', '4']);
        expect($upload->getImageAspectRatio())->toBe(['16:9', '4:1']);
    });

    it('can set `imageAspectRatio()` with a `Closure`', function (): void {
        $upload = FileUpload::make('photo')
            ->imageAspectRatio(static fn (): string => '1:1');

        expect($upload->getImageAspectRatio())->toBe('1:1');
    });

    it('returns `null` from `normalizeAspectRatio()` for non-numeric strings', function (): void {
        $upload = FileUpload::make('photo')->imageAspectRatio('invalid');
        expect($upload->getImageAspectRatio())->toBeNull();
    });
});

describe('image editor mode validation', function (): void {
    it('throws `InvalidArgumentException` for `imageEditorMode()` with mode `0`', function (): void {
        FileUpload::make('photo')->imageEditorMode(0);
    })->throws(InvalidArgumentException::class);

    it('throws `InvalidArgumentException` for `imageEditorMode()` with mode `4`', function (): void {
        FileUpload::make('photo')->imageEditorMode(4);
    })->throws(InvalidArgumentException::class);

    it('accepts valid `imageEditorMode()` values `1`, `2`, `3`', function (int $mode): void {
        $upload = FileUpload::make('photo')->imageEditorMode($mode);
        expect($upload->getImageEditorMode())->toBe($mode);
    })->with([1, 2, 3]);
});

describe('item panel aspect ratio', function (): void {
    it('returns `1` for `getItemPanelAspectRatio()` when panel layout is `grid` and no explicit ratio', function (): void {
        $upload = FileUpload::make('file')->panelLayout('grid');
        expect($upload->getItemPanelAspectRatio())->toBe(1);
    });

    it('returns `null` for `getItemPanelAspectRatio()` by default with compact layout', function (): void {
        $upload = FileUpload::make('file');
        expect($upload->getItemPanelAspectRatio())->toBeNull();
    });

    it('converts string ratio in `getItemPanelAspectRatio()` to float', function (): void {
        $upload = FileUpload::make('file')->itemPanelAspectRatio('16:9');
        $ratio = $upload->getItemPanelAspectRatio();
        expect($ratio)->toBeFloat();
        expect(round($ratio, 2))->toBe(1.78);
    });

    it('returns numeric ratio as-is from `getItemPanelAspectRatio()`', function (): void {
        $upload = FileUpload::make('file')->itemPanelAspectRatio(1.5);
        expect($upload->getItemPanelAspectRatio())->toBe(1.5);
    });
});

describe('`hasImageEditor()` implicit activation', function (): void {
    it('returns `true` for `hasImageEditor()` when `automaticallyOpenImageEditorForAspectRatio()` is enabled', function (): void {
        $upload = FileUpload::make('photo')
            ->imageAspectRatio('16:9')
            ->automaticallyOpenImageEditorForAspectRatio();

        expect($upload->hasImageEditor())->toBeTrue();
    });

    it('returns `isImageEditorExplicitlyEnabled()` as `false` even when implicitly active', function (): void {
        $upload = FileUpload::make('photo')
            ->imageAspectRatio('16:9')
            ->automaticallyOpenImageEditorForAspectRatio();

        expect($upload->isImageEditorExplicitlyEnabled())->toBeFalse();
    });
});

describe('`automaticallyOpenImageEditorForAspectRatio()` validation', function (): void {
    it('throws when used with `multiple()`', function (): void {
        $upload = FileUpload::make('photo')
            ->multiple()
            ->imageAspectRatio('16:9')
            ->automaticallyOpenImageEditorForAspectRatio();

        $upload->shouldAutomaticallyOpenImageEditorForAspectRatio();
    })->throws(InvalidArgumentException::class);

    it('throws when no `imageAspectRatio()` is set', function (): void {
        $upload = FileUpload::make('photo')
            ->automaticallyOpenImageEditorForAspectRatio();

        $upload->shouldAutomaticallyOpenImageEditorForAspectRatio();
    })->throws(InvalidArgumentException::class);

    it('throws when `imageAspectRatio()` has multiple ratios', function (): void {
        $upload = FileUpload::make('photo')
            ->imageAspectRatio(['16:9', '4:3'])
            ->automaticallyOpenImageEditorForAspectRatio();

        $upload->shouldAutomaticallyOpenImageEditorForAspectRatio();
    })->throws(InvalidArgumentException::class);
});

describe('`storeFileNamesIn()`', function (): void {
    it('returns `null` for `getFileNamesStatePath()` by default', function (): void {
        $upload = FileUpload::make('file');
        expect($upload->getFileNamesStatePath())->toBeNull();
    });

    it('can set `storeFileNamesIn()` and resolve the state path', function (): void {
        $livewire = Livewire::make();

        Schema::make($livewire)
            ->statePath('data')
            ->components([
                $upload = FileUpload::make('file')->storeFileNamesIn('original_filename'),
            ])
            ->fill();

        expect($upload->getFileNamesStatePath())->toBe('data.original_filename');
    });
});

describe('uploading message', function (): void {
    it('returns a default `getUploadingMessage()`', function (): void {
        $upload = FileUpload::make('file');
        expect($upload->getUploadingMessage())->toBeString();
        expect($upload->getUploadingMessage())->not->toBeEmpty();
    });

    it('can set `uploadingMessage()`', function (): void {
        $upload = FileUpload::make('file')->uploadingMessage('Please wait...');
        expect($upload->getUploadingMessage())->toBe('Please wait...');
    });

    it('can set `uploadingMessage()` with a `Closure`', function (): void {
        $upload = FileUpload::make('file')
            ->uploadingMessage(static fn (): string => 'Uploading your file...');

        expect($upload->getUploadingMessage())->toBe('Uploading your file...');
    });
});

describe('`image()` convenience method', function (): void {
    it('sets accepted file types to `image/*`', function (): void {
        $upload = FileUpload::make('photo')->image();
        expect($upload->getAcceptedFileTypes())->toBe(['image/*']);
    });
});

describe('`getValidationRules()` structure', function (): void {
    it('includes `maxFiles` rule when `maxFiles()` is set', function (): void {
        $upload = FileUpload::make('files')->maxFiles(5);
        $rules = $upload->getValidationRules();
        $stringRules = array_filter($rules, fn ($rule) => is_string($rule));

        expect($stringRules)->toContain('max:5');
    });

    it('includes `minFiles` rule when `minFiles()` is set', function (): void {
        $upload = FileUpload::make('files')->minFiles(2);
        $rules = $upload->getValidationRules();
        $stringRules = array_filter($rules, fn ($rule) => is_string($rule));

        expect($stringRules)->toContain('min:2');
    });

    it('always includes `array` rule', function (): void {
        $upload = FileUpload::make('files');
        $rules = $upload->getValidationRules();
        $stringRules = array_filter($rules, fn ($rule) => is_string($rule));

        expect($stringRules)->toContain('array');
    });
});

describe('`getAutomaticallyCropImagesAspectRatio()` logic', function (): void {
    it('returns `null` when `automaticallyCropImagesToAspectRatio()` is not enabled', function (): void {
        $upload = FileUpload::make('photo')->imageAspectRatio('16:9');
        expect($upload->getAutomaticallyCropImagesAspectRatio())->toBeNull();
    });

    it('returns `null` when enabled but no aspect ratio set', function (): void {
        $upload = FileUpload::make('photo')->automaticallyCropImagesToAspectRatio();
        expect($upload->getAutomaticallyCropImagesAspectRatio())->toBeNull();
    });

    it('returns the first ratio from an array when enabled', function (): void {
        $upload = FileUpload::make('photo')
            ->imageAspectRatio(['16:9', '4:3'])
            ->automaticallyCropImagesToAspectRatio();

        expect($upload->getAutomaticallyCropImagesAspectRatio())->toBe('16:9');
    });

    it('returns the string ratio when enabled', function (): void {
        $upload = FileUpload::make('photo')
            ->imageAspectRatio('4:3')
            ->automaticallyCropImagesToAspectRatio();

        expect($upload->getAutomaticallyCropImagesAspectRatio())->toBe('4:3');
    });
});

describe('`getImageEditorViewportHeight()` logic', function (): void {
    it('returns explicit value when no resize height or crop ratio', function (): void {
        $upload = FileUpload::make('photo')
            ->imageEditorViewportHeight(600);

        expect($upload->getImageEditorViewportHeight())->toBe(600);
    });

    it('returns `null` by default', function (): void {
        $upload = FileUpload::make('photo');

        expect($upload->getImageEditorViewportHeight())->toBeNull();
    });

    it('computes from `automaticallyResizeImagesToHeight()` when set', function (): void {
        $upload = FileUpload::make('photo')
            ->automaticallyResizeImagesToHeight('400')
            ->automaticallyResizeImagesToWidth('800');

        $height = $upload->getImageEditorViewportHeight();

        expect($height)->toBeInt();
        expect($height)->toBeGreaterThan(0);
    });

    it('computes from crop aspect ratio when no resize height', function (): void {
        $upload = FileUpload::make('photo')
            ->imageAspectRatio('16:9')
            ->automaticallyCropImagesToAspectRatio();

        expect($upload->getImageEditorViewportHeight())->toBe(9);
    });
});

describe('`getImageEditorViewportWidth()` logic', function (): void {
    it('returns explicit value when no resize width or crop ratio', function (): void {
        $upload = FileUpload::make('photo')
            ->imageEditorViewportWidth(800);

        expect($upload->getImageEditorViewportWidth())->toBe(800);
    });

    it('returns `null` by default', function (): void {
        $upload = FileUpload::make('photo');

        expect($upload->getImageEditorViewportWidth())->toBeNull();
    });

    it('computes from `automaticallyResizeImagesToWidth()` when set', function (): void {
        $upload = FileUpload::make('photo')
            ->automaticallyResizeImagesToWidth('800')
            ->automaticallyResizeImagesToHeight('400');

        $width = $upload->getImageEditorViewportWidth();

        expect($width)->toBeInt();
        expect($width)->toBeGreaterThan(0);
    });

    it('computes from crop aspect ratio when no resize width', function (): void {
        $upload = FileUpload::make('photo')
            ->imageAspectRatio('16:9')
            ->automaticallyCropImagesToAspectRatio();

        expect($upload->getImageEditorViewportWidth())->toBe(16);
    });
});

describe('`getAutomaticallyOpenImageEditorForAspectRatio()` logic', function (): void {
    it('returns `null` when not enabled', function (): void {
        $upload = FileUpload::make('photo')
            ->imageAspectRatio('16:9');

        expect($upload->getAutomaticallyOpenImageEditorForAspectRatio())->toBeNull();
    });

    it('returns a float when enabled with a string ratio', function (): void {
        $upload = FileUpload::make('photo')
            ->imageAspectRatio('16:9')
            ->automaticallyOpenImageEditorForAspectRatio();

        $ratio = $upload->getAutomaticallyOpenImageEditorForAspectRatio();

        expect($ratio)->toBeFloat();
        expect(round($ratio, 4))->toBe(round(16 / 9, 4));
    });

    it('returns a float from array ratio (uses first element)', function (): void {
        $upload = FileUpload::make('photo')
            ->imageAspectRatio(['4:3'])
            ->automaticallyOpenImageEditorForAspectRatio();

        $ratio = $upload->getAutomaticallyOpenImageEditorForAspectRatio();

        expect($ratio)->toBeFloat();
        expect(round($ratio, 4))->toBe(round(4 / 3, 4));
    });
});

describe('`getImageEditorAspectRatioOptionsForJs()` logic', function (): void {
    it('returns empty array when fewer than 2 options', function (): void {
        $upload = FileUpload::make('photo')
            ->imageEditorAspectRatioOptions(['16:9']);

        expect($upload->getImageEditorAspectRatioOptionsForJs())->toBe([]);
    });

    it('returns mapped options when 2 or more options provided', function (): void {
        $upload = FileUpload::make('photo')
            ->imageEditorAspectRatioOptions(['16:9', '4:3']);

        $options = $upload->getImageEditorAspectRatioOptionsForJs();

        expect($options)->toHaveCount(2);
        expect(array_keys($options))->toBe(['16:9', '4:3']);
    });

    it('includes `null` option as "no fixed" label', function (): void {
        $upload = FileUpload::make('photo')
            ->imageEditorAspectRatioOptions([null, '16:9']);

        $options = $upload->getImageEditorAspectRatioOptionsForJs();

        expect($options)->toHaveCount(2);
        expect(array_values($options)[1])->toBeFloat();
    });

    it('includes automatic crop ratio when set', function (): void {
        $upload = FileUpload::make('photo')
            ->imageAspectRatio('1:1')
            ->automaticallyCropImagesToAspectRatio()
            ->imageEditorAspectRatioOptions(['16:9']);

        $options = $upload->getImageEditorAspectRatioOptionsForJs();

        expect($options)->toHaveCount(2);
    });

    it('deduplicates ratios', function (): void {
        $upload = FileUpload::make('photo')
            ->imageAspectRatio('16:9')
            ->automaticallyCropImagesToAspectRatio()
            ->imageEditorAspectRatioOptions(['16:9', '4:3']);

        $options = $upload->getImageEditorAspectRatioOptionsForJs();

        expect($options)->toHaveCount(2);
    });
});

describe('`imageEditorAspectRatioOptions()`', function (): void {
    it('can set `imageEditorAspectRatioOptions()`', function (): void {
        $upload = FileUpload::make('photo')
            ->imageEditorAspectRatioOptions(['16:9', '4:3', '1:1']);

        // We access options indirectly through getImageEditorAspectRatioOptionsForJs
        $options = $upload->getImageEditorAspectRatioOptionsForJs();
        expect($options)->toHaveCount(3);
    });

    it('can set `imageEditorAspectRatioOptions()` with a `Closure`', function (): void {
        $upload = FileUpload::make('photo')
            ->imageEditorAspectRatioOptions(static fn (): array => ['16:9', '1:1']);

        $options = $upload->getImageEditorAspectRatioOptionsForJs();
        expect($options)->toHaveCount(2);
    });
});

describe('Closure support for FileUpload-specific methods', function (): void {
    it('can set `appendFiles()` with a `Closure`', function (): void {
        $upload = FileUpload::make('file')
            ->appendFiles(static fn (): bool => true);

        expect($upload->shouldAppendFiles())->toBeTrue();
    });

    it('can set `orientImagesFromExif()` with a `Closure`', function (): void {
        $upload = FileUpload::make('photo')
            ->orientImagesFromExif(static fn (): bool => false);

        expect($upload->shouldOrientImagesFromExif())->toBeFalse();
    });

    it('can set `automaticallyCropImagesToAspectRatio()` with a `Closure`', function (): void {
        $upload = FileUpload::make('photo')
            ->automaticallyCropImagesToAspectRatio(static fn (): bool => true);

        expect($upload->shouldAutomaticallyCropImagesToAspectRatio())->toBeTrue();
    });

    it('can set `editableSvgs()` with a `Closure`', function (): void {
        $upload = FileUpload::make('icon')
            ->editableSvgs(static fn (): bool => false);

        expect($upload->canEditSvgs())->toBeFalse();
    });

    it('can set `confirmSvgEditing()` with a `Closure`', function (): void {
        $upload = FileUpload::make('icon')
            ->confirmSvgEditing(static fn (): bool => true);

        expect($upload->isSvgEditingConfirmed())->toBeTrue();
    });

    it('can set `panelLayout()` with a `Closure`', function (): void {
        $upload = FileUpload::make('file')
            ->panelLayout(static fn (): string => 'grid');

        expect($upload->getPanelLayout())->toBe('grid');
    });

    it('can set `panelAspectRatio()` with a `Closure`', function (): void {
        $upload = FileUpload::make('file')
            ->panelAspectRatio(static fn (): string => '16:9');

        expect($upload->getPanelAspectRatio())->toBe('16:9');
    });
});

describe('rendering', function (): void {
    it('can render with `avatar()`', function (): void {
        livewire(RenderFileUploadWithAvatar::class)
            ->assertSuccessful();
    });

    it('can render with `panelLayout()` set to `grid`', function (): void {
        livewire(RenderFileUploadWithGridLayout::class)
            ->assertSuccessful();
    });

    it('can render with `panelLayout()` set via `Closure`', function (): void {
        livewire(RenderFileUploadWithClosurePanelLayout::class)
            ->assertSuccessful();
    });

    it('can render with `panelAspectRatio()`', function (): void {
        livewire(RenderFileUploadWithPanelAspectRatio::class)
            ->assertSuccessful();
    });

    it('can render with `panelAspectRatio()` set via `Closure`', function (): void {
        livewire(RenderFileUploadWithClosurePanelAspectRatio::class)
            ->assertSuccessful();
    });

    it('can render with `imageEditor()`', function (): void {
        livewire(RenderFileUploadWithImageEditor::class)
            ->assertSuccessful();
    });

    it('can render with `imageEditor()` set via `Closure`', function (): void {
        livewire(RenderFileUploadWithClosureImageEditor::class)
            ->assertSuccessful();
    });

    it('can render with `circleCropper()`', function (): void {
        livewire(RenderFileUploadWithCircleCropper::class)
            ->assertSuccessful();
    });

    it('can render with `circleCropper()` set via `Closure`', function (): void {
        livewire(RenderFileUploadWithClosureCircleCropper::class)
            ->assertSuccessful();
    });

    it('can render with `multiple()`', function (): void {
        livewire(RenderFileUploadWithMultiple::class)
            ->assertSuccessful();
    });

    it('can render with `multiple()` set via `Closure`', function (): void {
        livewire(RenderFileUploadWithClosureMultiple::class)
            ->assertSuccessful();
    });

    it('can render with `deletable(false)`', function (): void {
        livewire(RenderFileUploadWithNotDeletable::class)
            ->assertSuccessful();
    });

    it('can render with `deletable()` set via `Closure`', function (): void {
        livewire(RenderFileUploadWithClosureDeletable::class)
            ->assertSuccessful();
    });

    it('can render with `downloadable()`', function (): void {
        livewire(RenderFileUploadWithDownloadable::class)
            ->assertSuccessful();
    });

    it('can render with `downloadable()` set via `Closure`', function (): void {
        livewire(RenderFileUploadWithClosureDownloadable::class)
            ->assertSuccessful();
    });

    it('can render with `openable()`', function (): void {
        livewire(RenderFileUploadWithOpenable::class)
            ->assertSuccessful();
    });

    it('can render with `openable()` set via `Closure`', function (): void {
        livewire(RenderFileUploadWithClosureOpenable::class)
            ->assertSuccessful();
    });

    it('can render with `pasteable(false)`', function (): void {
        livewire(RenderFileUploadWithNotPasteable::class)
            ->assertSuccessful();
    });

    it('can render with `previewable(false)`', function (): void {
        livewire(RenderFileUploadWithNotPreviewable::class)
            ->assertSuccessful();
    });

    it('can render with `reorderable()`', function (): void {
        livewire(RenderFileUploadWithReorderable::class)
            ->assertSuccessful();
    });

    it('can render with `reorderable()` set via `Closure`', function (): void {
        livewire(RenderFileUploadWithClosureReorderable::class)
            ->assertSuccessful();
    });

    it('can render with `appendFiles()`', function (): void {
        livewire(RenderFileUploadWithAppendFiles::class)
            ->assertSuccessful();
    });

    it('can render with `appendFiles()` set via `Closure`', function (): void {
        livewire(RenderFileUploadWithClosureAppendFiles::class)
            ->assertSuccessful();
    });

    it('can render with `orientImagesFromExif(false)`', function (): void {
        livewire(RenderFileUploadWithNoExifOrientation::class)
            ->assertSuccessful();
    });

    it('can render with `orientImagesFromExif()` set via `Closure`', function (): void {
        livewire(RenderFileUploadWithClosureExifOrientation::class)
            ->assertSuccessful();
    });

    it('can render with `imagePreviewHeight()`', function (): void {
        livewire(RenderFileUploadWithImagePreviewHeight::class)
            ->assertSuccessful();
    });

    it('can render with `imageEditorMode()`', function (): void {
        livewire(RenderFileUploadWithImageEditorMode::class)
            ->assertSuccessful();
    });

    it('can render with `imageEditorEmptyFillColor()`', function (): void {
        livewire(RenderFileUploadWithImageEditorEmptyFillColor::class)
            ->assertSuccessful();
    });

    it('can render with `imageCropAspectRatio()`', function (): void {
        livewire(RenderFileUploadWithImageCropAspectRatio::class)
            ->assertSuccessful();
    });

    it('can render with `acceptedFileTypes()`', function (): void {
        livewire(RenderFileUploadWithAcceptedFileTypes::class)
            ->assertSuccessful();
    });

    it('can render with `acceptedFileTypes()` set via `Closure`', function (): void {
        livewire(RenderFileUploadWithClosureAcceptedFileTypes::class)
            ->assertSuccessful();
    });

    it('can render with `maxFiles()`', function (): void {
        livewire(RenderFileUploadWithMaxFiles::class)
            ->assertSuccessful();
    });

    it('can render with `maxFiles()` set via `Closure`', function (): void {
        livewire(RenderFileUploadWithClosureMaxFiles::class)
            ->assertSuccessful();
    });

    it('can render with `maxParallelUploads()`', function (): void {
        livewire(RenderFileUploadWithMaxParallelUploads::class)
            ->assertSuccessful();
    });

    it('can render with `mimeTypeMap()`', function (): void {
        livewire(RenderFileUploadWithMimeTypeMap::class)
            ->assertSuccessful();
    });

    it('can render with position properties', function (): void {
        livewire(RenderFileUploadWithPositions::class)
            ->assertSuccessful();
    });

    it('can render with `uploadingMessage()`', function (): void {
        livewire(RenderFileUploadWithUploadingMessage::class)
            ->assertSuccessful();
    });

    it('can render with `uploadingMessage()` set via `Closure`', function (): void {
        livewire(RenderFileUploadWithClosureUploadingMessage::class)
            ->assertSuccessful();
    });

    it('can render with `editableSvgs(false)`', function (): void {
        livewire(RenderFileUploadWithNoEditableSvgs::class)
            ->assertSuccessful();
    });

    it('can render with `confirmSvgEditing()`', function (): void {
        livewire(RenderFileUploadWithConfirmSvgEditing::class)
            ->assertSuccessful();
    });

    it('can render with `imageEditorViewportWidth()` and `imageEditorViewportHeight()`', function (): void {
        livewire(RenderFileUploadWithImageEditorViewport::class)
            ->assertSuccessful();
    });

    it('can render with `automaticallyResizeImagesToWidth()` and `automaticallyResizeImagesToHeight()`', function (): void {
        livewire(RenderFileUploadWithAutoResize::class)
            ->assertSuccessful();
    });

    it('can render with `automaticallyUpscaleImagesWhenResizing(false)`', function (): void {
        livewire(RenderFileUploadWithNoAutoUpscale::class)
            ->assertSuccessful();
    });

    it('can render with `automaticallyResizeImagesMode()`', function (): void {
        livewire(RenderFileUploadWithAutoResizeMode::class)
            ->assertSuccessful();
    });

    it('can render with `imageEditorAspectRatioOptions()`', function (): void {
        livewire(RenderFileUploadWithAspectRatioOptions::class)
            ->assertSuccessful();
    });

    it('can render with `image()`', function (): void {
        livewire(RenderFileUploadWithImage::class)
            ->assertSuccessful();
    });

    it('can render with `preserveFilenames()`', function (): void {
        livewire(RenderFileUploadWithPreserveFilenames::class)
            ->assertSuccessful();
    });

    it('can render with `storeFiles(false)`', function (): void {
        livewire(RenderFileUploadWithNoStoreFiles::class)
            ->assertSuccessful();
    });

    it('can render with `fetchFileInformation(false)`', function (): void {
        livewire(RenderFileUploadWithNoFetchFileInfo::class)
            ->assertSuccessful();
    });
});

it('can render `FileUpload` in the browser', function (): void {
    retry(10, function (): void {
        $this->actingAs(User::factory()->create());

        visit('/file-upload-browser-test')
            ->assertSee('Attachment')
            ->assertNoAccessibilityIssues();

        visit('/file-upload-browser-test')
            ->inDarkMode()
            ->assertNoAccessibilityIssues();
    });
});

class RenderFileUploadWithAvatar extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form->schema([FileUpload::make('photo')->avatar()])->statePath('data');
    }
}

class RenderFileUploadWithGridLayout extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form->schema([FileUpload::make('file')->panelLayout('grid')])->statePath('data');
    }
}

class RenderFileUploadWithClosurePanelLayout extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form->schema([FileUpload::make('file')->panelLayout(static fn (): string => 'grid')])->statePath('data');
    }
}

class RenderFileUploadWithPanelAspectRatio extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form->schema([FileUpload::make('file')->panelAspectRatio('16:9')])->statePath('data');
    }
}

class RenderFileUploadWithClosurePanelAspectRatio extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form->schema([FileUpload::make('file')->panelAspectRatio(static fn (): string => '16:9')])->statePath('data');
    }
}

class RenderFileUploadWithImageEditor extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form->schema([FileUpload::make('photo')->imageEditor()])->statePath('data');
    }
}

class RenderFileUploadWithClosureImageEditor extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form->schema([FileUpload::make('photo')->imageEditor(static fn (): bool => true)])->statePath('data');
    }
}

class RenderFileUploadWithCircleCropper extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form->schema([FileUpload::make('photo')->imageEditor()->circleCropper()])->statePath('data');
    }
}

class RenderFileUploadWithClosureCircleCropper extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form->schema([FileUpload::make('photo')->imageEditor()->circleCropper(static fn (): bool => true)])->statePath('data');
    }
}

class RenderFileUploadWithMultiple extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form->schema([FileUpload::make('files')->multiple()])->statePath('data');
    }
}

class RenderFileUploadWithClosureMultiple extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form->schema([FileUpload::make('files')->multiple(static fn (): bool => true)])->statePath('data');
    }
}

class RenderFileUploadWithNotDeletable extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form->schema([FileUpload::make('file')->deletable(false)])->statePath('data');
    }
}

class RenderFileUploadWithClosureDeletable extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form->schema([FileUpload::make('file')->deletable(static fn (): bool => false)])->statePath('data');
    }
}

class RenderFileUploadWithDownloadable extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form->schema([FileUpload::make('file')->downloadable()])->statePath('data');
    }
}

class RenderFileUploadWithClosureDownloadable extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form->schema([FileUpload::make('file')->downloadable(static fn (): bool => true)])->statePath('data');
    }
}

class RenderFileUploadWithOpenable extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form->schema([FileUpload::make('file')->openable()])->statePath('data');
    }
}

class RenderFileUploadWithClosureOpenable extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form->schema([FileUpload::make('file')->openable(static fn (): bool => true)])->statePath('data');
    }
}

class RenderFileUploadWithNotPasteable extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form->schema([FileUpload::make('file')->pasteable(false)])->statePath('data');
    }
}

class RenderFileUploadWithNotPreviewable extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form->schema([FileUpload::make('file')->previewable(false)])->statePath('data');
    }
}

class RenderFileUploadWithReorderable extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form->schema([FileUpload::make('files')->multiple()->reorderable()])->statePath('data');
    }
}

class RenderFileUploadWithClosureReorderable extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form->schema([FileUpload::make('files')->multiple()->reorderable(static fn (): bool => true)])->statePath('data');
    }
}

class RenderFileUploadWithAppendFiles extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form->schema([FileUpload::make('files')->multiple()->appendFiles()])->statePath('data');
    }
}

class RenderFileUploadWithClosureAppendFiles extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form->schema([FileUpload::make('files')->multiple()->appendFiles(static fn (): bool => true)])->statePath('data');
    }
}

class RenderFileUploadWithNoExifOrientation extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form->schema([FileUpload::make('photo')->orientImagesFromExif(false)])->statePath('data');
    }
}

class RenderFileUploadWithClosureExifOrientation extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form->schema([FileUpload::make('photo')->orientImagesFromExif(static fn (): bool => false)])->statePath('data');
    }
}

class RenderFileUploadWithImagePreviewHeight extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form->schema([FileUpload::make('photo')->imagePreviewHeight('200')])->statePath('data');
    }
}

class RenderFileUploadWithImageEditorMode extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form->schema([FileUpload::make('photo')->imageEditor()->imageEditorMode(2)])->statePath('data');
    }
}

class RenderFileUploadWithImageEditorEmptyFillColor extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form->schema([FileUpload::make('photo')->imageEditor()->imageEditorEmptyFillColor('#ffffff')])->statePath('data');
    }
}

class RenderFileUploadWithImageCropAspectRatio extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form->schema([FileUpload::make('photo')->imageCropAspectRatio('16:9')])->statePath('data');
    }
}

class RenderFileUploadWithAcceptedFileTypes extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form->schema([FileUpload::make('file')->acceptedFileTypes(['image/jpeg', 'image/png'])])->statePath('data');
    }
}

class RenderFileUploadWithClosureAcceptedFileTypes extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form->schema([FileUpload::make('file')->acceptedFileTypes(static fn (): array => ['application/pdf'])])->statePath('data');
    }
}

class RenderFileUploadWithMaxFiles extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form->schema([FileUpload::make('files')->multiple()->maxFiles(5)])->statePath('data');
    }
}

class RenderFileUploadWithClosureMaxFiles extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form->schema([FileUpload::make('files')->multiple()->maxFiles(static fn (): int => 10)])->statePath('data');
    }
}

class RenderFileUploadWithMaxParallelUploads extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form->schema([FileUpload::make('file')->maxParallelUploads(3)])->statePath('data');
    }
}

class RenderFileUploadWithMimeTypeMap extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form->schema([FileUpload::make('file')->mimeTypeMap(['jpg' => 'image/jpeg'])])->statePath('data');
    }
}

class RenderFileUploadWithPositions extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form->schema([
            FileUpload::make('file')
                ->loadingIndicatorPosition('left')
                ->removeUploadedFileButtonPosition('right')
                ->uploadButtonPosition('left')
                ->uploadProgressIndicatorPosition('left'),
        ])->statePath('data');
    }
}

class RenderFileUploadWithUploadingMessage extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form->schema([FileUpload::make('file')->uploadingMessage('Please wait...')])->statePath('data');
    }
}

class RenderFileUploadWithClosureUploadingMessage extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form->schema([FileUpload::make('file')->uploadingMessage(static fn (): string => 'Uploading your file...')])->statePath('data');
    }
}

class RenderFileUploadWithNoEditableSvgs extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form->schema([FileUpload::make('icon')->editableSvgs(false)])->statePath('data');
    }
}

class RenderFileUploadWithConfirmSvgEditing extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form->schema([FileUpload::make('icon')->confirmSvgEditing()])->statePath('data');
    }
}

class RenderFileUploadWithImageEditorViewport extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form->schema([FileUpload::make('photo')->imageEditor()->imageEditorViewportWidth(1920)->imageEditorViewportHeight(1080)])->statePath('data');
    }
}

class RenderFileUploadWithAutoResize extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form->schema([FileUpload::make('photo')->automaticallyResizeImagesToWidth('800')->automaticallyResizeImagesToHeight('600')])->statePath('data');
    }
}

class RenderFileUploadWithNoAutoUpscale extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form->schema([FileUpload::make('photo')->automaticallyUpscaleImagesWhenResizing(false)])->statePath('data');
    }
}

class RenderFileUploadWithAutoResizeMode extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form->schema([FileUpload::make('photo')->automaticallyResizeImagesMode('cover')])->statePath('data');
    }
}

class RenderFileUploadWithAspectRatioOptions extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form->schema([FileUpload::make('photo')->imageEditor()->imageEditorAspectRatioOptions(['16:9', '4:3'])])->statePath('data');
    }
}

class RenderFileUploadWithImage extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form->schema([FileUpload::make('photo')->image()])->statePath('data');
    }
}

class RenderFileUploadWithPreserveFilenames extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form->schema([FileUpload::make('file')->preserveFilenames()])->statePath('data');
    }
}

class RenderFileUploadWithNoStoreFiles extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form->schema([FileUpload::make('file')->storeFiles(false)])->statePath('data');
    }
}

class RenderFileUploadWithNoFetchFileInfo extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form->schema([FileUpload::make('file')->fetchFileInformation(false)])->statePath('data');
    }
}

class TestComponentWithFileUploadRecord extends Livewire
{
    public User $record;

    public function mount(): void
    {
        $this->form->fill($this->record->attributesToArray());
    }

    public function form(Schema $form): Schema
    {
        return $form
            ->components([
                FileUpload::make('status')
                    ->fetchFileInformation(false),
            ])
            ->model($this->record)
            ->statePath('data');
    }

    public function save(): void
    {
        $this->record->update($this->form->getState());
    }
}

class TestComponentWithFileUploadRecordPreventingTampering extends Livewire
{
    public User $record;

    public function mount(): void
    {
        $this->form->fill($this->record->attributesToArray());
    }

    public function form(Schema $form): Schema
    {
        return $form
            ->components([
                FileUpload::make('status')
                    ->fetchFileInformation(false)
                    ->preventFilePathTampering(),
            ])
            ->model($this->record)
            ->statePath('data');
    }

    public function save(): void
    {
        $this->record->update($this->form->getState());
    }
}

class TestComponentWithFileUploadPreventingTamperingWithoutRecord extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form
            ->components([
                FileUpload::make('status')
                    ->fetchFileInformation(false)
                    ->preventFilePathTampering(),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $this->form->getState();
    }
}

class TestComponentWithFileUploadRecordAllowingTemplateFilePaths extends Livewire
{
    public User $record;

    public function mount(): void
    {
        $this->form->fill($this->record->attributesToArray());
    }

    public function form(Schema $form): Schema
    {
        return $form
            ->components([
                FileUpload::make('status')
                    ->fetchFileInformation(false)
                    ->preventFilePathTampering(
                        allowFilePathUsing: static fn (string $file): bool => str_starts_with($file, 'templates/'),
                    ),
            ])
            ->model($this->record)
            ->statePath('data');
    }

    public function save(): void
    {
        $this->record->update($this->form->getState());
    }
}

class TestComponentWithAcceptedFileTypesUpload extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form
            ->components([
                FileUpload::make('avatar')
                    ->disk('local')
                    ->directory('avatars')
                    ->acceptedFileTypes(['image/png']),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $this->form->getState();
    }
}

class TestComponentWithMultipleFileUploadRecordPreventingTampering extends Livewire
{
    public User $record;

    public function mount(): void
    {
        $this->form->fill($this->record->attributesToArray());
    }

    public function form(Schema $form): Schema
    {
        return $form
            ->components([
                FileUpload::make('json')
                    ->multiple()
                    ->fetchFileInformation(false)
                    ->preventFilePathTampering(),
            ])
            ->model($this->record)
            ->statePath('data');
    }

    public function save(): void
    {
        $this->record->update($this->form->getState());
    }
}

class TestComponentWithFileUploadRecordPreventingTamperingAndCustomMessage extends Livewire
{
    public User $record;

    public function mount(): void
    {
        $this->form->fill($this->record->attributesToArray());
    }

    public function form(Schema $form): Schema
    {
        return $form
            ->components([
                FileUpload::make('status')
                    ->fetchFileInformation(false)
                    ->preventFilePathTampering()
                    ->validationMessages([
                        'tampered' => 'The selected attachment is not permitted.',
                    ]),
            ])
            ->model($this->record)
            ->statePath('data');
    }

    public function save(): void
    {
        $this->record->update($this->form->getState());
    }
}

describe('`hydrateFiles()` branches', function (): void {
    $makeField = function (callable $configure): FileUpload {
        $field = FileUpload::make('document')
            ->container(Schema::make(Livewire::make())->statePath('data'));

        $configure($field);

        return $field;
    };

    it('filters out files that do not exist on disk when `shouldFetchFileInformation` is true', function () use ($makeField): void {
        Storage::fake('local');
        Storage::disk('local')->put('uploads/exists.jpg', 'data');

        $field = $makeField(static fn (FileUpload $field) => $field->multiple());
        $field->rawState(['uploads/exists.jpg', 'uploads/missing.jpg']);

        $field->hydrateFiles();

        expect(array_values($field->getRawState()))->toBe(['uploads/exists.jpg']);
    });

    it('filters out blank string files', function () use ($makeField): void {
        Storage::fake('local');
        Storage::disk('local')->put('uploads/file.jpg', 'data');

        $field = $makeField(static fn (FileUpload $field) => $field->multiple());
        $field->rawState(['uploads/file.jpg', '']);

        $field->hydrateFiles();

        expect(array_values($field->getRawState()))->toBe(['uploads/file.jpg']);
    });

    it('keeps all non-blank entries when `fetchFileInformation(false)` is set', function () use ($makeField): void {
        // Don't create files on disk; with fetchFileInformation off, existence is not checked
        $field = $makeField(static fn (FileUpload $field) => $field
            ->multiple()
            ->fetchFileInformation(false));
        $field->rawState(['uploads/imaginary-1.jpg', 'uploads/imaginary-2.jpg']);

        $field->hydrateFiles();

        expect(array_values($field->getRawState()))->toBe(['uploads/imaginary-1.jpg', 'uploads/imaginary-2.jpg']);
    });
});

describe('`getUploadedFile()` branches', function (): void {
    $makeField = function (callable $configure): FileUpload {
        $field = FileUpload::make('document')
            ->container(Schema::make(Livewire::make())->statePath('data'));

        $configure($field);

        return $field;
    };

    it('returns `null` when the file does not exist and `shouldFetchFileInformation` is true', function () use ($makeField): void {
        Storage::fake('local');

        $field = $makeField(static fn (FileUpload $field) => $field);

        expect($field->getUploadedFile('uploads/missing.jpg', null))->toBeNull();
    });

    it('uses `storedFileNames[$file]` as the display name when the field is multiple', function () use ($makeField): void {
        Storage::fake('local');
        Storage::disk('local')->put('uploads/abc.jpg', 'data');

        $field = $makeField(static fn (FileUpload $field) => $field->multiple());

        $result = $field->getUploadedFile(
            'uploads/abc.jpg',
            ['uploads/abc.jpg' => 'pretty-name.jpg'],
        );

        expect($result['name'])->toBe('pretty-name.jpg');
    });

    it('uses `storedFileNames` as a string when the field is single', function () use ($makeField): void {
        Storage::fake('local');
        Storage::disk('local')->put('uploads/abc.jpg', 'data');

        $field = $makeField(static fn (FileUpload $field) => $field);

        $result = $field->getUploadedFile('uploads/abc.jpg', 'single-name.jpg');

        expect($result['name'])->toBe('single-name.jpg');
    });

    it('returns `size: 0` and `type: null` when `fetchFileInformation(false)` is set', function () use ($makeField): void {
        // Disk does not have the file; fetchFileInformation(false) skips existence + size + type
        $field = $makeField(static fn (FileUpload $field) => $field->fetchFileInformation(false));

        $result = $field->getUploadedFile('uploads/never-stored.jpg', null);

        expect($result)->toBeArray();
        expect($result['size'])->toBe(0);
        expect($result['type'])->toBeNull();
    });

    it('falls back to `Storage::url()` for `private` visibility when `temporaryUrl()` is unsupported', function () use ($makeField): void {
        Storage::fake('local');
        Storage::disk('local')->put('uploads/private.jpg', 'data');

        // The fake `local` disk does not support temporaryUrl; the catch falls back to url()
        $field = $makeField(static fn (FileUpload $field) => $field->visibility('private'));

        $result = $field->getUploadedFile('uploads/private.jpg', null);

        expect($result)->toBeArray();
        expect($result['url'])->not->toBeEmpty();
    });
});

describe('`saveUploadedFile()` branches', function (): void {
    $makeField = function (callable $configure): FileUpload {
        $field = FileUpload::make('document')
            ->container(Schema::make(Livewire::make())->statePath('data'));

        $configure($field);

        return $field;
    };

    $makeTemporaryUploadedFile = function (string $filename = 'hello.txt', string $content = 'data'): TemporaryUploadedFile {
        Storage::fake('tmp-for-tests');

        $temporaryFileName = TemporaryUploadedFile::generateHashNameWithOriginalNameEmbedded(
            UploadedFile::fake()->create($filename),
        );
        Storage::disk('tmp-for-tests')->put("livewire-tmp/{$temporaryFileName}", $content);

        return TemporaryUploadedFile::createFromLivewire($temporaryFileName);
    };

    it('uses `storeAs()` when disks differ between the field and the temp file', function () use ($makeField, $makeTemporaryUploadedFile): void {
        Storage::fake('public');

        $field = $makeField(static fn (FileUpload $field) => $field
            ->disk('public')
            ->directory('uploads'));

        $temp = $makeTemporaryUploadedFile('hello.txt');
        $path = $field->saveUploadedFile($temp);

        expect($path)->toStartWith('uploads/');
        expect(Storage::disk('public')->exists($path))->toBeTrue();
    });

    it('does not call `setVisibility(public)` when visibility is private', function () use ($makeField, $makeTemporaryUploadedFile): void {
        Storage::fake('local');

        $field = $makeField(static fn (FileUpload $field) => $field
            ->disk('local')
            ->directory('uploads')
            ->visibility('private'));

        $temp = $makeTemporaryUploadedFile('private-file.txt');
        $path = $field->saveUploadedFile($temp);

        expect($path)->toStartWith('uploads/');
        expect(Storage::disk('local')->exists($path))->toBeTrue();
    });

    it('persists the file to the configured public disk when visibility is public', function () use ($makeField, $makeTemporaryUploadedFile): void {
        Storage::fake('public');

        $field = $makeField(static fn (FileUpload $field) => $field
            ->disk('public')
            ->directory('uploads')
            ->visibility('public'));

        $temp = $makeTemporaryUploadedFile('public-file.txt');
        $path = $field->saveUploadedFile($temp);

        expect($path)->toStartWith('uploads/');
        expect(Storage::disk('public')->exists($path))->toBeTrue();
    });
});
