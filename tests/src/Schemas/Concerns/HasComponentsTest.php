<?php

use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Forms\Components\Field;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Components\Fieldset;
use Filament\Schemas\Components\Html;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Text;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Tests\Fixtures\Livewire\Livewire;
use Filament\Tests\Fixtures\Models\User;
use Filament\Tests\TestCase;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;

uses(TestCase::class);

describe('component binding', function (): void {
    it('can bind components to the schema', function (): void {
        $components = [];

        foreach (range(1, $count = rand(2, 10)) as $index) {
            $components[] = new Component;
        }

        $componentsBoundToContainer = ($schema = Schema::make(Livewire::make()))
            ->components($components)
            ->getComponents();

        expect($componentsBoundToContainer)
            ->toHaveCount($count)
            ->each(
                fn ($component) => $component
                    ->toBeInstanceOf(Component::class)
                    ->getContainer()->toBe($schema),
            );
    });

    it('can evaluate `components()` from a `Closure`', function (): void {
        $components = [];

        foreach (range(1, $count = rand(2, 10)) as $index) {
            $components[] = new Component;
        }

        $componentsBoundToContainer = ($schema = Schema::make(Livewire::make()))
            ->components(fn (): array => $components)
            ->getComponents();

        expect($componentsBoundToContainer)
            ->toHaveCount($count)
            ->each(
                fn ($component) => $component
                    ->toBeInstanceOf(Component::class)
                    ->getContainer()->toBe($schema),
            );
    });

    it('can return a component by name and callback', function (): void {
        $schema = Schema::make(Livewire::make())
            ->components([
                $input = Field::make($statePath = Str::random()),
                Section::make()->key('details')->schema([
                    $nestedInput = TextInput::make('name'),
                ]),
            ]);

        expect($schema)
            ->getComponent($statePath)->toBe($input)
            ->getComponent(fn (Component $component) => $component->getName() === $statePath)->toBe($input)
            ->getComponent('details.name')->toBe($nestedInput)
            ->getComponent('details.name', isAbsoluteKey: true)->toBe($nestedInput)
            ->getComponent('details.name', skipComponentsChildContainersWhileSearching: [$input])->toBe($nestedInput);
    });
});

describe('content trust', function (): void {
    it('escapes text exactly once and preserves explicit `Htmlable` content', function (string $type, bool $isArray, string $location): void {
        $markup = '<em>A & B &amp;</em>';
        $content = match ($type) {
            'string' => $markup,
            'closure string' => static fn (): string => $markup,
            'stringable' => str($markup),
            'closure stringable' => static fn () => str($markup),
            'htmlable' => new HtmlString($markup),
            'closure htmlable' => static fn (): HtmlString => new HtmlString($markup),
            'htmlable without string conversion' => new class implements Htmlable
            {
                public function toHtml(): string
                {
                    return '<em>A & B &amp;</em>';
                }
            },
            'null' => null,
            'closure null' => static fn () => null,
        };

        $schema = Schema::make(Livewire::make());
        $content = $isArray ? [$content] : $content;

        if ($location === 'schema') {
            $container = $schema->components($content ?? []);
        } else {
            $section = Section::make('Content');
            $schema->components([$section])->getComponents();

            if ($location === 'child') {
                $section->schema(($isArray || ($content instanceof Closure)) ? $content : static fn () => $content);
                $container = $section->getChildSchema();
            } else {
                $section->belowContent($content);
                $container = $section->getChildSchema(Section::BELOW_CONTENT_SCHEMA_KEY);
            }
        }

        $components = $container?->getComponents() ?? [];

        if (str_contains($type, 'null')) {
            expect($components)->toHaveCount($isArray ? 1 : 0);

            foreach ($components as $component) {
                if ($type === 'closure null') {
                    expect($component)->toBeInstanceOf(Text::class);
                    expect($component->getContent())->toBeNull();
                    expect($component->toEmbeddedHtml())->toBe(Text::make(null)->toEmbeddedHtml());
                } else {
                    expect($component)->toBeInstanceOf(Html::class);
                    expect($component->toEmbeddedHtml())->toBe('');
                }
            }

            return;
        }

        expect($components)->toHaveCount(1);
        expect($components[0]->getContainer())->toBe($container);
        expect($components[0])->toBeInstanceOf(
            (str_contains($type, 'htmlable') && ! ($isArray && str_starts_with($type, 'closure')))
                ? Html::class
                : Text::class,
        );

        $rendered = $components[0]->toEmbeddedHtml();

        if (str_contains($type, 'htmlable')) {
            expect($rendered)->toContain($markup)->not->toContain('&lt;em&gt;');
        } else {
            expect($rendered)
                ->toContain('&lt;em&gt;A &amp; B &amp;amp;&lt;/em&gt;')
                ->not->toContain('<em>', '&amp;lt;em&amp;gt;');
        }
    })->with([
        'string', 'closure string', 'stringable', 'closure stringable',
        'htmlable', 'closure htmlable', 'htmlable without string conversion',
        'null', 'closure null',
    ])->with([true, false])->with(['schema', 'child', 'slot']);

    it('uses the same `Text` wrapper for a string and an array-element `Closure` returning that string', function (): void {
        $markup = '<em>Caption & details</em>';
        $schema = Schema::make(Livewire::make())->components([
            $markup,
            static fn (): string => $markup,
        ]);

        [$direct, $deferred] = $schema->getComponents();

        expect($direct)->toBeInstanceOf(Text::class);
        expect($deferred)->toBeInstanceOf(Text::class);
        expect($deferred->toEmbeddedHtml())->toBe($direct->toEmbeddedHtml());
        expect($deferred->getContainer())->toBe($schema);
    });

    it('preserves array element identity, keys, ordering and binding', function (string $type): void {
        $content = match ($type) {
            'component' => Text::make('Component'),
            'action' => Action::make('run'),
            'action group' => ActionGroup::make([Action::make('grouped')]),
        };
        $schema = Schema::make(Livewire::make())->components([
            'before' => 'Before',
            'content' => $content,
            'conditional' => null,
            'after' => 'After',
        ]);

        $components = $schema->getComponents(withOriginalKeys: true);

        expect(array_keys($components))->toBe(['before', 'content', 'conditional', 'after']);
        expect($components['content'])->toBe($content);
        expect($components['conditional']->toEmbeddedHtml())->toBe('');
        expect(($content instanceof Component) ? $content->getContainer() : $content->getSchemaContainer())->toBe($schema);
        expect($schema->getComponents()[1])->toBe($content);
        expect(in_array($content, $schema->getComponents(withActions: false), strict: true))
            ->toBe($content instanceof Component);
    })->with(['component', 'action', 'action group']);

    it('evaluates content closures lazily with bound component utilities', function (): void {
        $livewire = Livewire::make();
        $livewire->data = ['caption' => '<em>First & caption</em>'];
        $schema = Schema::make($livewire)->statePath('data')->operation('edit');
        $schema->components([
            static function (Text $component, Get $get, Livewire $livewire, string $operation) use ($schema): string {
                expect($component)->toBeInstanceOf(Text::class);
                expect($component->getContainer())->toBe($schema);
                expect($component->getLivewire())->toBe($livewire);

                return $operation . ': ' . $get('caption');
            },
        ]);

        $component = $schema->getComponents()[0];
        $livewire->data['caption'] = '<em>Updated & caption</em>';

        expect($component->toEmbeddedHtml())
            ->toContain('edit: &lt;em&gt;Updated &amp; caption&lt;/em&gt;')
            ->not->toContain('First', '<em>');
    });

    it('preserves component and action cloning with trusted and text content', function (): void {
        $schema = Schema::make(Livewire::make())->components([
            Text::make('Component'),
            Action::make('run'),
            ActionGroup::make([Action::make('grouped')]),
            '<em>Text</em>',
            new HtmlString('<em>HTML</em>'),
        ]);
        $originalComponents = $schema->getComponents();
        $clone = $schema->getClone();
        $clonedComponents = $clone->getComponents();

        foreach ([0, 1, 2] as $index) {
            expect($clonedComponents[$index])->not->toBe($originalComponents[$index]);
            expect(($index === 0) ? $clonedComponents[$index]->getContainer() : $clonedComponents[$index]->getSchemaContainer())->toBe($clone);
        }

        expect($clonedComponents[3]->toEmbeddedHtml())->toContain('&lt;em&gt;Text&lt;/em&gt;');
        expect($clonedComponents[4]->toEmbeddedHtml())->toBe('<em>HTML</em>');
    });

    it('displays stored-looking array content literally in the browser', function (): void {
        Artisan::call('filament:assets');
        $this->actingAs(User::factory()->create());

        $page = visit('/schema-content-browser-test');

        $page->assertSee('<svg onload="window.schemaClosureMarker = true"><title>Stored closure & text</title></svg>')
            ->assertSee('<svg onload="window.schemaStringableMarker = true"><title>Stored stringable & text</title></svg>')
            ->assertNotPresent('svg[onload]')
            ->assertVisible('[data-testid="trusted-schema-content"]')
            ->assertSeeIn('[data-testid="trusted-schema-content"]', 'Intentional HTML')
            ->assertScript('window.schemaClosureMarker === undefined && window.schemaStringableMarker === undefined', true)
            ->assertNoSmoke()
            ->assertNoAccessibilityIssues();

        $page->inDarkMode()->assertNoAccessibilityIssues();
    });
});

describe('flat component and field retrieval', function (): void {
    it('can return a flat array of components', function (): void {
        $schema = Schema::make(Livewire::make())
            ->components([
                $fieldset = Fieldset::make(Str::random())
                    ->schema([
                        $field = TextInput::make($fieldName = Str::random()),
                    ]),
                $section = Section::make($sectionHeading = Str::random()),
            ]);

        expect($schema)
            ->getFlatComponents()
            ->toHaveCount(3)
            ->toBe([
                $fieldset,
                $fieldName => $field,
                Str::slug($sectionHeading) . '::section' => $section,
            ]);
    });

    it('can return a flat array of components with hidden components', function (): void {
        $schema = Schema::make(Livewire::make())
            ->components([
                $fieldset = Fieldset::make(Str::random())
                    ->hidden()
                    ->schema([
                        $field = TextInput::make($fieldName = Str::random()),
                    ]),
                $section = Section::make($sectionHeading = Str::random()),
            ]);

        expect($schema)
            ->getFlatComponents(withHidden: true)
            ->toHaveCount(3)
            ->toBe([
                $fieldset,
                $fieldName => $field,
                Str::slug($sectionHeading) . '::section' => $section,
            ]);
    });

    it('can return a flat array of fields', function (): void {
        $schema = Schema::make(Livewire::make())
            ->components([
                Fieldset::make(Str::random())
                    ->schema([
                        $field = TextInput::make($name = Str::random()),
                    ]),
                Section::make(Str::random()),
            ])
            ->statePath(Str::random());

        expect($schema)
            ->getFlatFields()
            ->toHaveCount(1)
            ->toMatchArray([
                $name => $field,
            ]);
    });

    it('can return a flat array of fields with hidden fields', function (): void {
        $schema = Schema::make(Livewire::make())
            ->components([
                Fieldset::make(Str::random())
                    ->hidden()
                    ->schema([
                        $field = TextInput::make($name = Str::random()),
                    ]),
                Section::make(Str::random()),
            ])
            ->statePath(Str::random());

        expect($schema)
            ->getFlatFields(withHidden: true)
            ->toHaveCount(1)
            ->toMatchArray([
                $name => $field,
            ]);
    });

    it('can return a flat array of fields with nested path keys', function (): void {
        $schema = Schema::make(Livewire::make())
            ->components([
                Fieldset::make(Str::random())
                    ->schema([
                        $field = TextInput::make($name = Str::random()),
                    ])
                    ->statePath($fieldsetStatePath = Str::random()),
                Section::make(Str::random()),
            ])
            ->statePath(Str::random());

        expect($schema)
            ->getFlatFields()
            ->toHaveCount(1)
            ->toMatchArray([
                "{$fieldsetStatePath}.{$name}" => $field,
            ]);
    });

    it('can return a flat array of fields with absolute path keys', function (): void {
        $schema = Schema::make(Livewire::make())
            ->components([
                Fieldset::make(Str::random())
                    ->schema([
                        $field = TextInput::make($name = Str::random()),
                    ]),
                Section::make(Str::random()),
            ])
            ->statePath($schemaStatePath = Str::random());

        expect($schema)
            ->getFlatFields(withAbsoluteKeys: true)
            ->toHaveCount(1)
            ->toMatchArray([
                "{$schemaStatePath}.{$name}" => $field,
            ]);
    });
});
