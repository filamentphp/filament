<?php

use Filament\Actions\Action;
use Filament\Forms\Components\Builder;
use Filament\Forms\Components\Builder\Block;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Text;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Schemas\Schema;
use Filament\Tests\Fixtures\Livewire\Livewire;
use Filament\Tests\Fixtures\Livewire\SpatieMediaLibraryFileUploadForm;
use Filament\Tests\Fixtures\Models\MediaPost;
use Filament\Tests\Fixtures\Models\User;
use Filament\Tests\TestCase;
use Illuminate\Contracts\View\View;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Validation\ValidationException;
use Livewire\Component;
use Livewire\Features\SupportFileUploads\FileUploadController;

use function Filament\Tests\livewire;

uses(TestCase::class);

it('can resolve schema using the primary method name', function (): void {
    $component = new class extends Livewire
    {
        public function test(Schema $schema): Schema
        {
            return $schema;
        }
    };

    expect($component->getSchema('test'))->toBeInstanceOf(Schema::class);
});

it('can resolve schema using the fallback method name', function (): void {
    $component = new class extends Livewire
    {
        public function testSchema(Schema $schema): Schema
        {
            return $schema;
        }
    };

    expect($component->getSchema('test'))->toBeInstanceOf(Schema::class);
});

it('can resolve schema using fallback method name without parameters', function (): void {
    $component = new class extends Livewire
    {
        public function testSchema(): Schema
        {
            return Schema::make($this);
        }
    };

    expect($component->getSchema('test'))->toBeInstanceOf(Schema::class);
});

it('applies an overridden default hook to a schema method without parameters', function (): void {
    $component = new TestComponentWithSchemaCaching;

    $schema = $component->getSchema('noArgument');

    expect($schema)
        ->toBeInstanceOf(Schema::class)
        ->and($schema->getExtraAttributes())
        ->toMatchArray([
            'data-default-hook' => 'base',
            'data-subclass-hook' => 'applied',
        ]);
});

it('applies a default hook before a parameterized schema method', function (): void {
    $component = new TestComponentWithSchemaCaching;

    $schema = $component->getSchema('parameterized');

    expect($schema)
        ->toBeInstanceOf(Schema::class)
        ->and($schema->getColumns())->toBe(['lg' => 1])
        ->and($schema->getExtraAttributes())->toMatchArray([
            'data-parameterized-default-hook' => 'applied',
        ]);
});

it('does not resolve a default hook without a schema method', function (): void {
    $component = new TestComponentWithSchemaCaching;

    expect($component->getSchema('defaultOnly'))->toBeNull()
        ->and($component->defaultOnlyHookCalls)->toBe(0);
});

it('returns `null` for a missing schema', function (): void {
    expect((new TestComponentWithSchemaCaching)->getSchema('missing'))->toBeNull();
});

it('returns the same cached schema on repeated access', function (string $name): void {
    $component = new TestComponentWithSchemaCaching;

    $schema = $component->getSchema($name);

    expect($component->getSchema($name))->toBe($schema)
        ->and($component->getCachedSchemas()[$name])->toBe($schema);
})->with(['noArgument', 'parameterized']);

it('caches a replacement schema returned by the default hook', function (): void {
    $component = new TestComponentWithReplacementSchema;

    $schema = $component->getSchema('replacement');

    expect($component->getSchemaPassedToDefaultHook())->toBe($component->getSchemaReturnedByMethod())
        ->and($schema)->toBe($component->getSchemaReturnedByDefaultHook())
        ->and($schema?->getKey())->toBe('replacement')
        ->and($component->getSchema('replacement'))->toBe($schema)
        ->and($component->getCachedSchemas()['replacement'])->toBe($schema);
});

it('renders schemas with default hook customization through Livewire and Blade', function (): void {
    livewire(TestComponentWithSchemaCaching::class)
        ->assertSuccessful()
        ->assertSee('No-argument schema')
        ->assertSee('Parameterized schema')
        ->assertSeeHtml('data-default-hook="base"')
        ->assertSeeHtml('data-subclass-hook="applied"')
        ->assertSeeHtml('data-parameterized-default-hook="applied"');
});

it('renders a customized no-argument schema accessibly in light and dark modes', function (): void {
    Artisan::call('filament:assets');

    $this->actingAs(User::factory()->create());

    $page = visit('/schema-caching-browser-test');

    $page
        ->assertSee('Customized no-argument schema')
        ->assertPresent('[data-testid="customized-no-argument-schema"][data-default-hook="base"][data-subclass-hook="applied"]')
        ->assertNoSmoke()
        ->assertNoAccessibilityIssues();

    $page->inDarkMode()
        ->assertPresent('[data-testid="customized-no-argument-schema"]')
        ->assertNoAccessibilityIssues();
});

it('cannot validate from a client-side call', function (string $method): void {
    // `validate()` and `validateOnly()` are not part of a component's client-callable
    // surface, but Livewire only withholds methods declared on its own base component,
    // and both are redeclared here. Running them from the client would execute every
    // schema's validation rules outside the flow that is supposed to gate them.
    livewire(TestComponentWithValidation::class)
        ->fillForm(['title' => ''])
        ->call($method, ...(($method === 'validateOnly') ? ['data.title'] : []))
        ->assertForbidden();
})->with(['validateOnly', 'validate']);

it('cannot validate from a client-side call alongside another method', function (): void {
    $livewire = livewire(TestComponentWithValidation::class)
        ->fillForm(['title' => '']);

    // Both calls are made in one request, so that an implementation which only
    // guarded the first call would be caught.
    $this->withHeaders(['X-Livewire' => true])
        ->post(app('livewire')->getUpdateUri(), [
            'components' => [
                [
                    'snapshot' => json_encode($livewire->snapshot),
                    'updates' => [],
                    'calls' => [
                        ['method' => 'validateOnly', 'params' => ['data.title'], 'path' => ''],
                        ['method' => 'submit', 'params' => [], 'path' => ''],
                    ],
                ],
            ],
        ])
        ->assertForbidden();
});

it('can validate from the component itself', function (): void {
    livewire(TestComponentWithValidation::class)
        ->fillForm(['title' => ''])
        ->call('submit')
        ->assertHasFormErrors(['title' => ['required']]);
});

describe('upload validation', function (): void {
    it('uses friendly field names when `WithFileUploads` is also used directly', function (): void {
        $uploadPath = 'data.avatar.24b0c021-dcd4-4b43-ad0c-3b5c241225ee';

        $component = livewire(SpatieMediaLibraryFileUploadForm::class, ['record' => MediaPost::factory()->create()])
            ->call('_uploadErrored', $uploadPath, json_encode([
                'errors' => ['files.0' => ['The files.0 field must not be greater than 1024 kilobytes.']],
            ]), false)
            ->assertHasErrors([$uploadPath]);

        expect($component->errors()->get($uploadPath))
            ->toBe(['The avatar field must not be greater than 1024 kilobytes.']);
    });

    it('uses friendly field names for temporary upload validation errors', function (string $uploadComponent, string $uploadPath, ?string $validationAttribute, string $expectedAttribute): void {
        config()->set('livewire.temporary_file_upload.rules', 'file|max:1024');

        try {
            app(FileUploadController::class)->validateAndStore([
                UploadedFile::fake()->create('download.zip', 2048, 'application/zip'),
            ], 'local');

            $this->fail('The oversized temporary upload should fail validation.');
        } catch (ValidationException $exception) {
            $errorsInJson = json_encode(['errors' => $exception->errors()]);
        }

        $component = livewire(UploadValidationTestComponent::class, [
            'uploadComponent' => $uploadComponent,
            'validationAttribute' => $validationAttribute,
        ])
            ->call('_uploadErrored', $uploadPath, $errorsInJson, false)
            ->assertHasErrors([$uploadPath])
            ->assertDispatched('upload:errored', name: $uploadPath);

        expect($component->errors()->get($uploadPath))
            ->toBe(["The {$expectedAttribute} field must not be greater than 1024 kilobytes."]);

        if (is_a($uploadComponent, FileUpload::class, allow_string: true)) {
            $component->assertSee("The {$expectedAttribute} field must not be greater than 1024 kilobytes.");
        }
    })->with([
        'file upload label' => [FileUpload::class, 'data.downloadable_file.24b0c021-dcd4-4b43-ad0c-3b5c241225ee', null, 'downloadable file'],
        'file upload validation attribute' => [FileUpload::class, 'data.downloadable_file.24b0c021-dcd4-4b43-ad0c-3b5c241225ee', 'product download', 'product download'],
        'Spatie media upload' => [SpatieMediaLibraryFileUpload::class, 'data.downloadable_file.24b0c021-dcd4-4b43-ad0c-3b5c241225ee', null, 'downloadable file'],
        'rich editor attachment' => [RichEditor::class, 'componentFileAttachments.data.downloadable_file.24b0c021-dcd4-4b43-ad0c-3b5c241225ee', null, 'downloadable file'],
        'markdown editor attachment' => [MarkdownEditor::class, 'componentFileAttachments.data.downloadable_file', null, 'downloadable file'],
    ]);

    it('uses friendly field names for generic upload failures and leaves unrelated uploads unchanged', function (string $uploadComponent, string $uploadPath, string $placeholder, string $expectedAttribute): void {
        __('validation.uploaded');
        app('translator')->addLines(['validation.uploaded' => "The {$placeholder} failed to upload."], 'en');

        $component = livewire(UploadValidationTestComponent::class, ['uploadComponent' => $uploadComponent])
            ->call('_uploadErrored', name: $uploadPath, errorsInJson: null, isMultiple: false)
            ->assertHasErrors([$uploadPath])
            ->assertDispatched('upload:errored', name: $uploadPath);

        expect($component->errors()->get($uploadPath))
            ->toBe(["The {$expectedAttribute} failed to upload."]);
    })->with([
        'file upload' => [FileUpload::class, 'data.downloadable_file.24b0c021-dcd4-4b43-ad0c-3b5c241225ee', ':attribute', 'downloadable file'],
        'editor attachment' => [RichEditor::class, 'componentFileAttachments.data.downloadable_file.24b0c021-dcd4-4b43-ad0c-3b5c241225ee', ':attribute', 'downloadable file'],
        'capitalized file upload' => [FileUpload::class, 'data.downloadable_file.24b0c021-dcd4-4b43-ad0c-3b5c241225ee', ':Attribute', 'Downloadable file'],
        'uppercase editor attachment' => [RichEditor::class, 'componentFileAttachments.data.downloadable_file.24b0c021-dcd4-4b43-ad0c-3b5c241225ee', ':ATTRIBUTE', 'DOWNLOADABLE FILE'],
        'unrelated upload' => [FileUpload::class, 'data.other_upload', ':attribute', 'data.other_upload'],
    ]);

    it('preserves translated attribute names for generic upload failures', function (): void {
        $uploadPath = 'data.downloadable_file.24b0c021-dcd4-4b43-ad0c-3b5c241225ee';

        __('validation.uploaded');
        app('translator')->addLines(["validation.attributes.{$uploadPath}" => 'translated download'], 'en');

        $component = livewire(UploadValidationTestComponent::class)
            ->call('_uploadErrored', $uploadPath, null, false);

        expect($component->errors()->get($uploadPath))
            ->toBe(['The translated download failed to upload.']);
    });

    it('preserves multiple upload error keys, unrelated errors and custom messages', function (): void {
        $component = livewire(UploadValidationTestComponent::class)
            ->call('_uploadErrored', 'data.downloadable_file', json_encode([
                'errors' => [
                    'files.0' => ['The files.0 field must not be greater than 1024 kilobytes.'],
                    'files.1' => ['This archive is not supported.'],
                    'data.other_upload' => ['The data.other_upload field is required.'],
                ],
            ]), true);

        expect($component->errors()->messages())->toEqual([
            'data.downloadable_file.0' => ['The downloadable file field must not be greater than 1024 kilobytes.'],
            'data.downloadable_file.1' => ['This archive is not supported.'],
            'data.other_upload' => ['The data.other_upload field is required.'],
        ]);
    });

    it('uses the validation attribute of the correct repeater row', function (): void {
        $component = livewire(RepeaterUploadValidationTestComponent::class);
        $rowKey = array_keys($component->get('data.products'))[1];
        $uploadPath = "data.products.{$rowKey}.downloadable_file.24b0c021-dcd4-4b43-ad0c-3b5c241225ee";

        $component->call('_uploadErrored', $uploadPath, json_encode([
            'errors' => ['files.0' => ['The files.0 field must not be greater than 1024 kilobytes.']],
        ]), false);

        expect($component->errors()->get($uploadPath))
            ->toBe(['The secondary download field must not be greater than 1024 kilobytes.']);
    });
});

it('can load nested deferred schemas independently', function (): void {
    $livewire = livewire(TestComponentWithDeferredSchemas::class);

    /** @var Schema $schema */
    $schema = $livewire->instance()->getSchema('form');

    /** @var Repeater $repeater */
    $repeater = $schema->getComponent('members');

    /** @var Builder $builder */
    $builder = $schema->getComponent('content');

    $schemaKeys = [
        'form.details',
        'form.details.moreDetails',
        array_values($repeater->getItems())[0]->getKey(),
        array_values($builder->getItems())[0]->getKey(),
    ];

    foreach ($schemaKeys as $schemaKey) {
        $livewire->call('loadDeferredSchema', $schemaKey);

        expect($livewire->get('loadedDeferredSchemas'))->toHaveKey($schemaKey, true);
    }
});

it('does not load deferred schemas beneath hidden components', function (): void {
    $livewire = livewire(TestComponentWithDeferredSchemas::class)
        ->call('loadDeferredSchema', 'form.hiddenDetails');

    expect($livewire->get('loadedDeferredSchemas'))->not->toHaveKey('form.hiddenDetails');
});

it('loads deferred schemas with no visible components', function (): void {
    $livewire = livewire(TestComponentWithDeferredSchemas::class)
        ->call('loadDeferredSchema', 'form.emptyDetails');

    expect($livewire->get('loadedDeferredSchemas'))
        ->toHaveKey('form.emptyDetails', true)
        ->and($livewire->effects['partials']['schema.form.emptyDetails'])
        ->toContain('<div wire:partial="schema.form.emptyDetails" hidden></div>');
});

it('loads deferred schemas containing validation errors', function (): void {
    $livewire = livewire(TestComponentWithDeferredSchemas::class)
        ->call('save')
        ->assertHasFormErrors(['name' => ['required']]);

    expect($livewire->get('loadedDeferredSchemas'))->toHaveKey('form.details', true);
});

it('can remove items from a list field in a mounted action form', function (): void {
    livewire(TestComponentWithListFields::class)
        ->mountAction('roles')
        ->assertSchemaStateSet(['roles' => ['viewer', 'creator']])
        ->fillForm(['roles' => ['viewer']])
        ->assertSchemaStateSet(['roles' => ['viewer']]);
});

it('can remove items from a list field in a page form', function (): void {
    livewire(TestComponentWithListFields::class)
        ->assertFormSet(['roles' => ['viewer', 'creator']])
        ->fillForm(['roles' => ['viewer']])
        ->assertFormSet(['roles' => ['viewer']]);
});

it('can clear a list field in a mounted action form', function (): void {
    livewire(TestComponentWithListFields::class)
        ->mountAction('roles')
        ->fillForm(['roles' => []])
        ->assertSchemaStateSet(['roles' => []]);
});

it('can add items to a list field in a mounted action form', function (): void {
    livewire(TestComponentWithListFields::class)
        ->mountAction('roles')
        ->fillForm(['roles' => ['viewer', 'creator', 'editor']])
        ->assertSchemaStateSet(['roles' => ['viewer', 'creator', 'editor']]);
});

class TestComponentWithListFields extends Livewire
{
    public function mount(): void
    {
        $this->form->fill(['roles' => ['viewer', 'creator']]);
    }

    public function form(Schema $form): Schema
    {
        return $form
            ->components([
                CheckboxList::make('roles')
                    ->options([
                        'viewer' => 'Viewer',
                        'creator' => 'Creator',
                        'editor' => 'Editor',
                    ]),
            ])
            ->statePath('data');
    }

    public function rolesAction(): Action
    {
        return Action::make('roles')
            ->fillForm(['roles' => ['viewer', 'creator']])
            ->schema([
                CheckboxList::make('roles')
                    ->options([
                        'viewer' => 'Viewer',
                        'creator' => 'Creator',
                        'editor' => 'Editor',
                    ]),
            ])
            ->action(static function (): void {});
    }
}

class TestComponentWithValidation extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form
            ->components([
                TextInput::make('title')
                    ->required(),
            ])
            ->statePath('data');
    }

    public function submit(): void
    {
        $this->form->validate();
    }
}

class UploadValidationTestComponent extends Livewire
{
    public string $uploadComponent = FileUpload::class;

    public ?string $validationAttribute = null;

    public function form(Schema $schema): Schema
    {
        $field = $this->uploadComponent::make('downloadable_file')
            ->validationAttribute($this->validationAttribute);

        if ($field instanceof FileUpload) {
            $field->disk('local');
        }

        return $schema
            ->components([$field])
            ->statePath('data');
    }
}

class RepeaterUploadValidationTestComponent extends Livewire
{
    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Repeater::make('products')
                    ->default([
                        ['name' => 'primary download'],
                        ['name' => 'secondary download'],
                    ])
                    ->schema([
                        TextInput::make('name'),
                        FileUpload::make('downloadable_file')
                            ->disk('local')
                            ->validationAttribute(static fn (Get $get): string => $get('name')),
                    ]),
            ])
            ->statePath('data');
    }
}

class TestComponentWithDeferredSchemas extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form
            ->components([
                Section::make('Details')
                    ->key('details')
                    ->schema(
                        Schema::make()
                            ->components([
                                TextInput::make('name')
                                    ->required(),
                                Section::make('More details')
                                    ->key('moreDetails')
                                    ->schema(
                                        Schema::make()
                                            ->components([
                                                TextInput::make('description'),
                                            ])
                                            ->deferLoading(),
                                    ),
                            ])
                            ->deferLoading(),
                    ),
                Section::make('Hidden details')
                    ->key('hiddenDetails')
                    ->hidden()
                    ->schema(
                        Schema::make()
                            ->components([
                                TextInput::make('secret'),
                            ])
                            ->deferLoading(),
                    ),
                Section::make('Empty details')
                    ->key('emptyDetails')
                    ->schema(
                        Schema::make()
                            ->components([
                                TextInput::make('empty')
                                    ->hidden(),
                            ])
                            ->deferLoading(),
                    ),
                Repeater::make('members')
                    ->schema(
                        Schema::make()
                            ->components([
                                TextInput::make('name'),
                            ])
                            ->deferLoading(),
                    )
                    ->default([
                        ['name' => 'Taylor'],
                    ]),
                Builder::make('content')
                    ->blocks([
                        Block::make('paragraph')
                            ->schema(
                                Schema::make()
                                    ->components([
                                        TextInput::make('text'),
                                    ])
                                    ->deferLoading(),
                            ),
                    ])
                    ->default([
                        ['type' => 'paragraph', 'data' => ['text' => 'Hello']],
                    ]),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $this->form->getState();
    }
}

class BaseTestComponentWithSchemaCaching extends Component implements HasSchemas
{
    use InteractsWithSchemas;

    public int $defaultOnlyHookCalls = 0;

    public function mount(): void
    {
        $this->getSchema('noArgument');
        $this->getSchema('parameterized');
    }

    public function noArgumentSchema(): Schema
    {
        return Schema::make($this)
            ->components([
                Text::make('No-argument schema'),
            ]);
    }

    public function defaultNoArgument(Schema $schema): Schema
    {
        return $schema->extraAttributes([
            'data-default-hook' => 'base',
        ]);
    }

    public function parameterizedSchema(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                Text::make('Parameterized schema'),
            ]);
    }

    public function defaultParameterized(Schema $schema): Schema
    {
        return $schema
            ->columns(2)
            ->extraAttributes([
                'data-parameterized-default-hook' => 'applied',
            ]);
    }

    public function defaultDefaultOnly(Schema $schema): Schema
    {
        $this->defaultOnlyHookCalls++;

        return $schema;
    }

    public function render(): View
    {
        return view('livewire.form');
    }
}

class TestComponentWithSchemaCaching extends BaseTestComponentWithSchemaCaching
{
    public function defaultNoArgument(Schema $schema): Schema
    {
        return parent::defaultNoArgument($schema)
            ->extraAttributes([
                'data-subclass-hook' => 'applied',
            ], merge: true);
    }
}

class TestComponentWithReplacementSchema extends Component implements HasSchemas
{
    use InteractsWithSchemas;

    protected ?Schema $schemaPassedToDefaultHook = null;

    protected ?Schema $schemaReturnedByDefaultHook = null;

    protected ?Schema $schemaReturnedByMethod = null;

    public function replacementSchema(): Schema
    {
        return $this->schemaReturnedByMethod = Schema::make($this);
    }

    public function defaultReplacement(Schema $schema): Schema
    {
        $this->schemaPassedToDefaultHook = $schema;

        return $this->schemaReturnedByDefaultHook = Schema::make($this);
    }

    public function getSchemaPassedToDefaultHook(): ?Schema
    {
        return $this->schemaPassedToDefaultHook;
    }

    public function getSchemaReturnedByDefaultHook(): ?Schema
    {
        return $this->schemaReturnedByDefaultHook;
    }

    public function getSchemaReturnedByMethod(): ?Schema
    {
        return $this->schemaReturnedByMethod;
    }
}
