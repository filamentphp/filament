<?php

use Facades\Livewire\Features\SupportFileUploads\GenerateSignedUploadUrl as GenerateSignedUploadUrlFacade;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Tests\Forms\Fixtures\Livewire;
use Filament\Tests\TestCase;
use Illuminate\Contracts\View\View;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Livewire\Features\SupportFileUploads\FileUploadConfiguration;
use Livewire\Features\SupportFileUploads\GenerateSignedUploadUrl;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Livewire\Features\SupportTesting\Testable;

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

it('does not allow preserved file names to escape the configured `directory()`', function (string $temporaryDisk): void {
    Storage::fake('public');
    Storage::fake('tmp-for-tests');
    Storage::disk('public')->put('photos/1/me.gif', 'GIF89a uploaded by victim');

    $attackerFileName = 'x.y-meta' . str_replace('/', '_', base64_encode('../1/me.gif')) . '-';

    uploadFileThroughLivewireAndSave($this, $attackerFileName, 'GIF89a uploaded by attacker', $temporaryDisk);

    expect(Storage::disk('public')->get('photos/1/me.gif'))
        ->toBe('GIF89a uploaded by victim');

    Storage::disk('public')->assertExists("photos/2/{$attackerFileName}");
})->with(['local', 's3']);

it('does not decode a preserved file name to a PHP extension after validation', function (string $temporaryDisk): void {
    Storage::fake('public');
    Storage::fake('tmp-for-tests');

    $attackerFileName = 'x.y-meta' . base64_encode('shell.php') . '-';

    uploadFileThroughLivewireAndSave($this, $attackerFileName, 'GIF89a <?php echo 1;', $temporaryDisk);

    Storage::disk('public')->assertMissing('photos/2/shell.php');
    Storage::disk('public')->assertExists("photos/2/{$attackerFileName}");
})->with(['local', 's3']);

it('only stores uploads during `getState()` when the field and its containing forms are enabled', function (bool $isFormDisabled, bool $isParentDisabled, bool $isFieldDisabled): void {
    Storage::fake('tmp-for-tests');
    Storage::fake('public');
    Storage::disk('public')->put('uploads/existing.txt', 'Existing document');

    $temporaryFileName = TemporaryUploadedFile::generateHashNameWithOriginalNameEmbedded(
        UploadedFile::fake()->create('new-document.txt'),
    );
    Storage::disk('tmp-for-tests')->put("livewire-tmp/{$temporaryFileName}", 'New document');
    $temporaryFile = TemporaryUploadedFile::createFromLivewire($temporaryFileName);

    $field = FileUpload::make('document')
        ->disk('public')
        ->directory('uploads')
        ->multiple()
        ->preserveFilenames()
        ->disabled($isFieldDisabled)
        ->dehydrated();
    $livewire = Livewire::make();
    $form = Form::make($livewire)
        ->statePath('data')
        ->disabled($isFormDisabled)
        ->schema([
            TextInput::make('name')->default('Original name'),
            Group::make([$field])->disabled($isParentDisabled)->dehydrated(),
        ])
        ->fill();
    $livewire->data['document'] = [
        'existing-file' => 'uploads/existing.txt',
        'new-file' => $temporaryFile,
    ];

    $state = $form->getState();
    $shouldStore = (! $isFormDisabled) && (! $isParentDisabled) && (! $isFieldDisabled);

    if ($shouldStore) {
        Storage::disk('public')->assertExists('uploads/new-document.txt');
        expect($state['document'])->toBe(['uploads/existing.txt', 'uploads/new-document.txt']);
    } else {
        Storage::disk('public')->assertMissing('uploads/new-document.txt');
        expect($state)->toBe($isFormDisabled ? [] : ['name' => 'Original name', 'document' => ['uploads/existing.txt', $temporaryFile]]);

        $field->saveUploadedFiles();
        Storage::disk('public')->assertExists('uploads/new-document.txt');
    }

    expect($state['name'] ?? null)->toBe($isFormDisabled ? null : 'Original name')
        ->and(Storage::disk('public')->get('uploads/existing.txt'))->toBe('Existing document')
        ->and($temporaryFile->exists())->toBeFalse();
})->with([false, true])->with([false, true])->with([false, true]);

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

function uploadFileThroughLivewireAndSave(TestCase $testCase, string $fileName, string $contents, string $temporaryDisk): Testable
{
    $statePath = 'data.photo.' . Str::uuid();
    $fileInfo = [['name' => $fileName, 'size' => strlen($contents), 'type' => 'image/gif']];
    $component = livewire(TestComponentWithPreservedFileUpload::class);

    if ($temporaryDisk === 's3') {
        $s3Configuration = [
            'driver' => 's3',
            'key' => 'test-key',
            'secret' => 'test-secret',
            'region' => 'us-east-1',
            'bucket' => 'test-bucket',
        ];
        config()->set('livewire.temporary_file_upload.disk', 's3');
        config()->set('filesystems.disks.s3', $s3Configuration);

        $temporaryStorage = Storage::disk('tmp-for-tests');
        $originalUrlGenerator = GenerateSignedUploadUrlFacade::getFacadeRoot();

        try {
            Storage::set('tmp-for-tests', Storage::build($s3Configuration));
            // Livewire stubs S3 signing in tests, so restore the real signer.
            GenerateSignedUploadUrlFacade::swap(app()->build(GenerateSignedUploadUrl::class));

            $component->call('_startUpload', $statePath, $fileInfo, false);

            $payload = collect($component->effects['dispatches'])
                ->firstWhere('name', 'upload:generatedSignedUrlForS3')['params']['payload'];
        } finally {
            Storage::set('tmp-for-tests', $temporaryStorage);
            GenerateSignedUploadUrlFacade::swap($originalUrlGenerator);
        }

        // Replace only the S3 PUT transport, keeping the server-generated signed reference.
        $temporaryFileName = TemporaryUploadedFile::extractPathFromSignedPath($payload['path']);
        $temporaryStorage->put(FileUploadConfiguration::path($temporaryFileName), $contents);
        $temporaryPaths = [$payload['path']];
    } else {
        $component->call('_startUpload', $statePath, $fileInfo, false);

        $uploadUrl = collect($component->effects['dispatches'])
            ->firstWhere('name', 'upload:generatedSignedUrl')['params']['url'];

        $temporaryPaths = $testCase
            ->post($uploadUrl, ['files' => [UploadedFile::fake()->createWithContent($fileName, $contents)]])
            ->assertOk()
            ->json('paths');
    }

    return $component
        ->call('_finishUpload', $statePath, $temporaryPaths, false)
        ->call('save')
        ->assertHasNoErrors();
}

class TestComponentWithPreservedFileUpload extends Livewire
{
    public function form(Form $form): Form
    {
        return $form
            ->schema([
                FileUpload::make('photo')
                    ->disk('public')
                    ->directory('photos/2')
                    ->image()
                    ->preserveFilenames(),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $this->form->getState();
    }

    public function render(): View
    {
        return view('forms.fixtures.form');
    }
}
