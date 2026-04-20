<?php

use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Tests\Fixtures\Livewire\SpatieMediaLibraryFileUploadForm;
use Filament\Tests\Fixtures\Models\MediaPost;
use Filament\Tests\TestCase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

use function Filament\Tests\livewire;

uses(TestCase::class);

describe('collection', function (): void {
    it('returns `null` for `getCollection()` by default', function (): void {
        $component = SpatieMediaLibraryFileUpload::make('media');

        expect($component->getCollection())->toBeNull();
    });

    it('can set `collection()`', function (): void {
        $component = SpatieMediaLibraryFileUpload::make('media')
            ->collection('avatars');

        expect($component->getCollection())->toBe('avatars');
    });

    it('can set `collection()` with a `Closure`', function (): void {
        $component = SpatieMediaLibraryFileUpload::make('media')
            ->collection(static fn (): string => 'dynamic');

        expect($component->getCollection())->toBe('dynamic');
    });

    it('can clear `collection()` with `null`', function (): void {
        $component = SpatieMediaLibraryFileUpload::make('media')
            ->collection('avatars')
            ->collection(null);

        expect($component->getCollection())->toBeNull();
    });
});

describe('conversion', function (): void {
    it('returns `null` for `getConversion()` by default', function (): void {
        $component = SpatieMediaLibraryFileUpload::make('media');

        expect($component->getConversion())->toBeNull();
    });

    it('can set `conversion()`', function (): void {
        $component = SpatieMediaLibraryFileUpload::make('media')
            ->conversion('thumb');

        expect($component->getConversion())->toBe('thumb');
    });

    it('can set `conversion()` with a `Closure`', function (): void {
        $component = SpatieMediaLibraryFileUpload::make('media')
            ->conversion(static fn (): string => 'preview');

        expect($component->getConversion())->toBe('preview');
    });

    it('can clear `conversion()` with `null`', function (): void {
        $component = SpatieMediaLibraryFileUpload::make('media')
            ->conversion('thumb')
            ->conversion(null);

        expect($component->getConversion())->toBeNull();
    });
});

describe('conversions disk', function (): void {
    it('returns `null` for `getConversionsDisk()` by default', function (): void {
        $component = SpatieMediaLibraryFileUpload::make('media');

        expect($component->getConversionsDisk())->toBeNull();
    });

    it('can set `conversionsDisk()`', function (): void {
        $component = SpatieMediaLibraryFileUpload::make('media')
            ->conversionsDisk('s3');

        expect($component->getConversionsDisk())->toBe('s3');
    });

    it('can set `conversionsDisk()` with a `Closure`', function (): void {
        $component = SpatieMediaLibraryFileUpload::make('media')
            ->conversionsDisk(static fn (): string => 'local');

        expect($component->getConversionsDisk())->toBe('local');
    });

    it('can clear `conversionsDisk()` with `null`', function (): void {
        $component = SpatieMediaLibraryFileUpload::make('media')
            ->conversionsDisk('s3')
            ->conversionsDisk(null);

        expect($component->getConversionsDisk())->toBeNull();
    });
});

describe('responsive images', function (): void {
    it('defaults `hasResponsiveImages()` to `false`', function (): void {
        $component = SpatieMediaLibraryFileUpload::make('media');

        expect($component->hasResponsiveImages())->toBeFalse();
    });

    it('can set `responsiveImages()`', function (): void {
        $component = SpatieMediaLibraryFileUpload::make('media')
            ->responsiveImages();

        expect($component->hasResponsiveImages())->toBeTrue();
    });

    it('can set `responsiveImages()` to `false`', function (): void {
        $component = SpatieMediaLibraryFileUpload::make('media')
            ->responsiveImages()
            ->responsiveImages(false);

        expect($component->hasResponsiveImages())->toBeFalse();
    });

    it('can set `responsiveImages()` with a `Closure`', function (): void {
        $component = SpatieMediaLibraryFileUpload::make('media')
            ->responsiveImages(static fn (): bool => true);

        expect($component->hasResponsiveImages())->toBeTrue();
    });
});

describe('custom headers', function (): void {
    it('returns empty array for `getCustomHeaders()` by default', function (): void {
        $component = SpatieMediaLibraryFileUpload::make('media');

        expect($component->getCustomHeaders())->toBe([]);
    });

    it('can set `customHeaders()`', function (): void {
        $component = SpatieMediaLibraryFileUpload::make('media')
            ->customHeaders(['CacheControl' => 'max-age=86400']);

        expect($component->getCustomHeaders())->toBe(['CacheControl' => 'max-age=86400']);
    });

    it('can set `customHeaders()` with a `Closure`', function (): void {
        $component = SpatieMediaLibraryFileUpload::make('media')
            ->customHeaders(static fn (): array => ['CacheControl' => 'no-cache']);

        expect($component->getCustomHeaders())->toBe(['CacheControl' => 'no-cache']);
    });

    it('can clear `customHeaders()` with `null`', function (): void {
        $component = SpatieMediaLibraryFileUpload::make('media')
            ->customHeaders(['CacheControl' => 'max-age=86400'])
            ->customHeaders(null);

        expect($component->getCustomHeaders())->toBe([]);
    });
});

describe('custom properties', function (): void {
    it('returns empty array for `getCustomProperties()` by default', function (): void {
        $component = SpatieMediaLibraryFileUpload::make('media');
        $file = Mockery::mock(TemporaryUploadedFile::class);

        expect($component->getCustomProperties($file))->toBe([]);
    });

    it('can set `customProperties()` with an array', function (): void {
        $component = SpatieMediaLibraryFileUpload::make('media')
            ->customProperties(['alt' => 'Photo']);
        $file = Mockery::mock(TemporaryUploadedFile::class);

        expect($component->getCustomProperties($file))->toBe(['alt' => 'Photo']);
    });

    it('can set `customProperties()` with a `Closure`', function (): void {
        $component = SpatieMediaLibraryFileUpload::make('media')
            ->customProperties(static fn (): array => ['dynamic' => true]);
        $file = Mockery::mock(TemporaryUploadedFile::class);

        expect($component->getCustomProperties($file))->toBe(['dynamic' => true]);
    });

    it('can clear `customProperties()` with `null`', function (): void {
        $component = SpatieMediaLibraryFileUpload::make('media')
            ->customProperties(['alt' => 'Photo'])
            ->customProperties(null);
        $file = Mockery::mock(TemporaryUploadedFile::class);

        expect($component->getCustomProperties($file))->toBe([]);
    });
});

describe('manipulations', function (): void {
    it('returns empty array for `getManipulations()` by default', function (): void {
        $component = SpatieMediaLibraryFileUpload::make('media');

        expect($component->getManipulations())->toBe([]);
    });

    it('can set `manipulations()`', function (): void {
        $manipulations = ['thumb' => ['width' => 100, 'height' => 100]];
        $component = SpatieMediaLibraryFileUpload::make('media')
            ->manipulations($manipulations);

        expect($component->getManipulations())->toBe($manipulations);
    });

    it('can set `manipulations()` with a `Closure`', function (): void {
        $component = SpatieMediaLibraryFileUpload::make('media')
            ->manipulations(static fn (): array => ['thumb' => ['width' => 50]]);

        expect($component->getManipulations())->toBe(['thumb' => ['width' => 50]]);
    });

    it('can clear `manipulations()` with `null`', function (): void {
        $component = SpatieMediaLibraryFileUpload::make('media')
            ->manipulations(['thumb' => ['width' => 100]])
            ->manipulations(null);

        expect($component->getManipulations())->toBe([]);
    });
});

describe('properties', function (): void {
    it('returns empty array for `getProperties()` by default', function (): void {
        $component = SpatieMediaLibraryFileUpload::make('media');

        expect($component->getProperties())->toBe([]);
    });

    it('can set `properties()`', function (): void {
        $component = SpatieMediaLibraryFileUpload::make('media')
            ->properties(['order_column' => 1]);

        expect($component->getProperties())->toBe(['order_column' => 1]);
    });

    it('can set `properties()` with a `Closure`', function (): void {
        $component = SpatieMediaLibraryFileUpload::make('media')
            ->properties(static fn (): array => ['order_column' => 2]);

        expect($component->getProperties())->toBe(['order_column' => 2]);
    });

    it('can clear `properties()` with `null`', function (): void {
        $component = SpatieMediaLibraryFileUpload::make('media')
            ->properties(['order_column' => 1])
            ->properties(null);

        expect($component->getProperties())->toBe([]);
    });
});

describe('media name', function (): void {
    it('returns `null` for `getMediaName()` by default', function (): void {
        $component = SpatieMediaLibraryFileUpload::make('media');
        $file = Mockery::mock(TemporaryUploadedFile::class);

        expect($component->getMediaName($file))->toBeNull();
    });

    it('can set `mediaName()` with a string', function (): void {
        $component = SpatieMediaLibraryFileUpload::make('media')
            ->mediaName('custom-name');
        $file = Mockery::mock(TemporaryUploadedFile::class);

        expect($component->getMediaName($file))->toBe('custom-name');
    });

    it('can set `mediaName()` with a `Closure`', function (): void {
        $component = SpatieMediaLibraryFileUpload::make('media')
            ->mediaName(static fn (): string => 'dynamic-name');
        $file = Mockery::mock(TemporaryUploadedFile::class);

        expect($component->getMediaName($file))->toBe('dynamic-name');
    });

    it('can clear `mediaName()` with `null`', function (): void {
        $component = SpatieMediaLibraryFileUpload::make('media')
            ->mediaName('custom-name')
            ->mediaName(null);
        $file = Mockery::mock(TemporaryUploadedFile::class);

        expect($component->getMediaName($file))->toBeNull();
    });
});

describe('media filter', function (): void {
    it('defaults `hasMediaFilter()` to `false`', function (): void {
        $component = SpatieMediaLibraryFileUpload::make('media');

        expect($component->hasMediaFilter())->toBeFalse();
    });

    it('can set `filterMediaUsing()`', function (): void {
        $component = SpatieMediaLibraryFileUpload::make('media')
            ->filterMediaUsing(static fn (Collection $media): Collection => $media);

        expect($component->hasMediaFilter())->toBeTrue();
    });

    it('can clear `filterMediaUsing()` with `null`', function (): void {
        $component = SpatieMediaLibraryFileUpload::make('media')
            ->filterMediaUsing(static fn (Collection $media): Collection => $media)
            ->filterMediaUsing(null);

        expect($component->hasMediaFilter())->toBeFalse();
    });

    it('can filter a media collection with `filterMedia()`', function (): void {
        $component = SpatieMediaLibraryFileUpload::make('media')
            ->filterMediaUsing(static fn (Collection $media): Collection => $media->filter(
                static fn ($item): bool => $item['type'] === 'image'
            ));

        $media = new Collection([
            ['type' => 'image', 'uuid' => 'a'],
            ['type' => 'document', 'uuid' => 'b'],
            ['type' => 'image', 'uuid' => 'c'],
        ]);

        $filtered = $component->filterMedia($media);

        expect($filtered)->toHaveCount(2);
        expect($filtered->pluck('uuid')->values()->all())->toBe(['a', 'c']);
    });
});

describe('integration', function (): void {
    beforeEach(function (): void {
        Storage::fake('public');
    });

    it('can load existing media into form state', function (): void {
        $record = MediaPost::factory()->create();

        $mediaItem = $record->addMediaFromString('test-content')
            ->usingFileName('avatar.jpg')
            ->usingName('avatar')
            ->toMediaCollection('avatars');

        $record = $record->fresh();

        livewire(SpatieMediaLibraryFileUploadForm::class, ['record' => $record])
            ->assertFormSet(function (array $state) use ($mediaItem): array {
                expect($state['avatar'])->toBeArray();
                expect($state['avatar'])->toHaveKey($mediaItem->uuid);

                return [];
            });
    });

    it('loads no media when collection is empty', function (): void {
        $record = MediaPost::factory()->create();

        livewire(SpatieMediaLibraryFileUploadForm::class, ['record' => $record])
            ->assertFormSet(function (array $state): array {
                expect($state['avatar'])->toBeEmpty();

                return [];
            });
    });

    it('can upload media and save to the collection', function (): void {
        $record = MediaPost::factory()->create();

        $file = UploadedFile::fake()->image('avatar.jpg');

        livewire(SpatieMediaLibraryFileUploadForm::class, ['record' => $record])
            ->fillForm([
                'avatar' => [$file],
            ])
            ->call('save');

        $record->refresh();

        expect($record->getMedia('avatars'))->toHaveCount(1);
        expect($record->getMedia('avatars')->first()->file_name)->toEndWith('.jpg');
    });

    it('can delete abandoned media on save', function (): void {
        $record = MediaPost::factory()->create();

        $record->addMediaFromString('first-file')
            ->usingFileName('first.jpg')
            ->toMediaCollection('avatars');

        $record->addMediaFromString('second-file')
            ->usingFileName('second.jpg')
            ->toMediaCollection('avatars');

        $record->load('media');

        expect($record->getMedia('avatars'))->toHaveCount(2);

        $file = UploadedFile::fake()->image('new-avatar.jpg');

        livewire(SpatieMediaLibraryFileUploadForm::class, ['record' => $record])
            ->fillForm([
                'avatar' => [$file],
            ])
            ->call('save');

        $record->refresh();

        // Old media should be deleted, only the new one should remain
        expect($record->getMedia('avatars'))->toHaveCount(1);
        expect($record->getMedia('avatars')->first()->file_name)->toEndWith('.jpg');
    });

    it('does not affect media in other collections', function (): void {
        $record = MediaPost::factory()->create();

        $record->addMediaFromString('other-content')
            ->usingFileName('document.pdf')
            ->toMediaCollection('documents');

        $file = UploadedFile::fake()->image('avatar.jpg');

        livewire(SpatieMediaLibraryFileUploadForm::class, ['record' => $record])
            ->fillForm([
                'avatar' => [$file],
            ])
            ->call('save');

        $record->refresh();

        expect($record->getMedia('documents'))->toHaveCount(1);
        expect($record->getMedia('avatars'))->toHaveCount(1);
    });

    it('does not reorder media belonging to other records when submitted UUIDs are tampered', function (): void {
        $recordA = MediaPost::factory()->create();
        $recordB = MediaPost::factory()->create();

        $recordA->addMediaFromString('a1')
            ->usingFileName('a1.jpg')
            ->toMediaCollection('avatars');

        $bMedia1 = $recordB->addMediaFromString('b1')
            ->usingFileName('b1.jpg')
            ->toMediaCollection('avatars');

        $bMedia2 = $recordB->addMediaFromString('b2')
            ->usingFileName('b2.jpg')
            ->toMediaCollection('avatars');

        $originalBOrder = [
            $bMedia1->uuid => $bMedia1->order_column,
            $bMedia2->uuid => $bMedia2->order_column,
        ];

        livewire(ReorderableSpatieMediaLibraryFileUploadForm::class, ['record' => $recordA->fresh()])
            ->set('data.avatar', [
                $bMedia2->uuid => $bMedia2->uuid,
                $bMedia1->uuid => $bMedia1->uuid,
            ])
            ->call('save');

        expect($bMedia1->fresh()->order_column)->toBe($originalBOrder[$bMedia1->uuid]);
        expect($bMedia2->fresh()->order_column)->toBe($originalBOrder[$bMedia2->uuid]);
    });
});

class ReorderableSpatieMediaLibraryFileUploadForm extends \Livewire\Component implements \Filament\Actions\Contracts\HasActions, \Filament\Schemas\Contracts\HasSchemas
{
    use \Filament\Actions\Concerns\InteractsWithActions;
    use \Filament\Schemas\Concerns\InteractsWithSchemas;
    use \Livewire\WithFileUploads;

    public $data = [];

    public MediaPost $record;

    public function mount(MediaPost $record): void
    {
        $this->record = $record;
        $this->form->fill([]);
    }

    public function form(\Filament\Schemas\Schema $form): \Filament\Schemas\Schema
    {
        return $form
            ->schema([
                SpatieMediaLibraryFileUpload::make('avatar')
                    ->collection('avatars')
                    ->multiple()
                    ->reorderable(),
            ])
            ->model($this->record)
            ->statePath('data');
    }

    public function save(): void
    {
        $this->form->getState();
        $this->form->saveRelationships();
    }

    public function render(): \Illuminate\Contracts\View\View
    {
        return view('livewire.form');
    }
}
