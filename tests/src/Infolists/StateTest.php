<?php

namespace Filament\Tests\Infolists;

use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\KeyValueEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Schemas\Schema;
use Filament\Tests\Fixtures\Models\Post;
use Filament\Tests\TestCase;
use Livewire\Component;

use function Filament\Tests\livewire;

uses(TestCase::class);

it('can resolve `getConstantStatePath()` through nested `statePath()` with `constantState()`', function (): void {
    livewire(NestedStatePathComponent::class)
        ->assertOk()
        ->assertSchemaComponentStateSet('parent.child.data', ['key' => 'nested value'], 'infolist')
        ->assertSeeText('nested value');
});

class NestedStatePathComponent extends Component implements HasSchemas
{
    use InteractsWithSchemas;

    public function infolist(Schema $schema): Schema
    {
        return $schema
            ->constantState([
                'parent' => [
                    'child' => [
                        'data' => ['key' => 'nested value'],
                    ],
                ],
            ])
            ->components([
                Section::make()
                    ->statePath('parent')
                    ->schema([
                        Section::make()
                            ->statePath('child')
                            ->schema([
                                KeyValueEntry::make('data'),
                            ]),
                    ]),
            ]);
    }

    public function render(): string
    {
        return <<<'BLADE'
            <div>
                {{ $this->infolist }}
            </div>
            BLADE;
    }
}

it('can resolve `getConstantStatePath()` when root schema has `statePath()`', function (): void {
    livewire(RootStatePathComponent::class)
        ->assertOk()
        ->assertSchemaComponentStateSet('parent.child.value', 'nested value', 'infolist')
        ->assertSeeText('nested value');
});

class RootStatePathComponent extends Component implements HasSchemas
{
    use InteractsWithSchemas;

    public function infolist(Schema $schema): Schema
    {
        return $schema
            ->statePath('data')
            ->constantState([
                'parent' => [
                    'child' => [
                        'value' => 'nested value',
                    ],
                ],
            ])
            ->components([
                Section::make()
                    ->statePath('parent')
                    ->schema([
                        Section::make()
                            ->statePath('child')
                            ->schema([
                                TextEntry::make('value'),
                            ]),
                    ]),
            ]);
    }

    public function render(): string
    {
        return <<<'BLADE'
            <div>
                {{ $this->infolist }}
            </div>
            BLADE;
    }
}

it('can resolve `getConstantStatePath()` through 3 levels of nested `statePath()`', function (): void {
    livewire(ThreeLevelNestedComponent::class)
        ->assertOk()
        ->assertSchemaComponentStateSet('level1.level2.level3.data', ['key' => 'deeply nested value'], 'infolist')
        ->assertSeeText('deeply nested value');
});

class ThreeLevelNestedComponent extends Component implements HasSchemas
{
    use InteractsWithSchemas;

    public function infolist(Schema $schema): Schema
    {
        return $schema
            ->constantState([
                'level1' => [
                    'level2' => [
                        'level3' => [
                            'data' => ['key' => 'deeply nested value'],
                        ],
                    ],
                ],
            ])
            ->components([
                Section::make()
                    ->statePath('level1')
                    ->schema([
                        Section::make()
                            ->statePath('level2')
                            ->schema([
                                Section::make()
                                    ->statePath('level3')
                                    ->schema([
                                        KeyValueEntry::make('data'),
                                    ]),
                            ]),
                    ]),
            ]);
    }

    public function render(): string
    {
        return <<<'BLADE'
            <div>
                {{ $this->infolist }}
            </div>
            BLADE;
    }
}

it('can resolve `getConstantStatePath()` through 4 levels of nested `statePath()`', function (): void {
    livewire(FourLevelNestedComponent::class)
        ->assertOk()
        ->assertSchemaComponentStateSet('a.b.c.d.value', 'four levels deep', 'infolist')
        ->assertSeeText('four levels deep');
});

class FourLevelNestedComponent extends Component implements HasSchemas
{
    use InteractsWithSchemas;

    public function infolist(Schema $schema): Schema
    {
        return $schema
            ->constantState([
                'a' => ['b' => ['c' => ['d' => ['value' => 'four levels deep']]]],
            ])
            ->components([
                Section::make()->statePath('a')->schema([
                    Section::make()->statePath('b')->schema([
                        Section::make()->statePath('c')->schema([
                            Section::make()->statePath('d')->schema([
                                TextEntry::make('value'),
                            ]),
                        ]),
                    ]),
                ]),
            ]);
    }

    public function render(): string
    {
        return <<<'BLADE'
            <div>
                {{ $this->infolist }}
            </div>
            BLADE;
    }
}

it('can resolve `getConstantStatePath()` through nested `Grid` components', function (): void {
    livewire(NestedGridComponent::class)
        ->assertOk()
        ->assertSchemaComponentStateSet('parent.child.value', 'grid nested', 'infolist')
        ->assertSeeText('grid nested');
});

class NestedGridComponent extends Component implements HasSchemas
{
    use InteractsWithSchemas;

    public function infolist(Schema $schema): Schema
    {
        return $schema
            ->constantState([
                'parent' => ['child' => ['value' => 'grid nested']],
            ])
            ->components([
                Grid::make()
                    ->statePath('parent')
                    ->schema([
                        Grid::make()
                            ->statePath('child')
                            ->schema([
                                TextEntry::make('value'),
                            ]),
                    ]),
            ]);
    }

    public function render(): string
    {
        return <<<'BLADE'
            <div>
                {{ $this->infolist }}
            </div>
            BLADE;
    }
}

it('can resolve `getConstantStatePath()` through nested `Group` components', function (): void {
    livewire(NestedGroupComponent::class)
        ->assertOk()
        ->assertSchemaComponentStateSet('outer.inner.value', 'group nested', 'infolist')
        ->assertSeeText('group nested');
});

class NestedGroupComponent extends Component implements HasSchemas
{
    use InteractsWithSchemas;

    public function infolist(Schema $schema): Schema
    {
        return $schema
            ->constantState([
                'outer' => ['inner' => ['value' => 'group nested']],
            ])
            ->components([
                Group::make()
                    ->statePath('outer')
                    ->schema([
                        Group::make()
                            ->statePath('inner')
                            ->schema([
                                TextEntry::make('value'),
                            ]),
                    ]),
            ]);
    }

    public function render(): string
    {
        return <<<'BLADE'
            <div>
                {{ $this->infolist }}
            </div>
            BLADE;
    }
}

it('can resolve `getConstantStatePath()` through mixed nested layout components', function (): void {
    livewire(MixedLayoutComponent::class)
        ->assertOk()
        ->assertSchemaComponentStateSet('section.grid.group.value', 'mixed layouts', 'infolist')
        ->assertSeeText('mixed layouts');
});

class MixedLayoutComponent extends Component implements HasSchemas
{
    use InteractsWithSchemas;

    public function infolist(Schema $schema): Schema
    {
        return $schema
            ->constantState([
                'section' => ['grid' => ['group' => ['value' => 'mixed layouts']]],
            ])
            ->components([
                Section::make()
                    ->statePath('section')
                    ->schema([
                        Grid::make()
                            ->statePath('grid')
                            ->schema([
                                Group::make()
                                    ->statePath('group')
                                    ->schema([
                                        TextEntry::make('value'),
                                    ]),
                            ]),
                    ]),
            ]);
    }

    public function render(): string
    {
        return <<<'BLADE'
            <div>
                {{ $this->infolist }}
            </div>
            BLADE;
    }
}

it('can resolve `getConstantStatePath()` when schema has `record()`', function (): void {
    $post = Post::factory()->create(['title' => 'Test Post', 'content' => 'Post content']);

    livewire(SchemaWithRecordComponent::class, ['post' => $post])
        ->assertOk()
        ->assertSchemaComponentStateSet('title', 'Test Post', 'infolist')
        ->assertSeeText('Test Post');
});

class SchemaWithRecordComponent extends Component implements HasSchemas
{
    use InteractsWithSchemas;

    public Post $post;

    public function mount(Post $post): void
    {
        $this->post = $post;
    }

    public function infolist(Schema $schema): Schema
    {
        return $schema
            ->record($this->post)
            ->components([
                TextEntry::make('title'),
            ]);
    }

    public function render(): string
    {
        return <<<'BLADE'
            <div>
                {{ $this->infolist }}
            </div>
            BLADE;
    }
}

it('can resolve `getConstantStatePath()` through deeply nested sections with `record()`', function (): void {
    $post = Post::factory()->create(['title' => 'Test Post']);

    livewire(DeeplyNestedWithRecordComponent::class, ['post' => $post])
        ->assertOk()
        ->assertSchemaComponentStateSet('title', 'Test Post', 'infolist')
        ->assertSeeText('Test Post');
});

class DeeplyNestedWithRecordComponent extends Component implements HasSchemas
{
    use InteractsWithSchemas;

    public Post $post;

    public function mount(Post $post): void
    {
        $this->post = $post;
    }

    public function infolist(Schema $schema): Schema
    {
        return $schema
            ->record($this->post)
            ->components([
                Section::make()
                    ->schema([
                        Section::make()
                            ->schema([
                                TextEntry::make('title'),
                            ]),
                    ]),
            ]);
    }

    public function render(): string
    {
        return <<<'BLADE'
            <div>
                {{ $this->infolist }}
            </div>
            BLADE;
    }
}

it('can resolve `getConstantStatePath()` through layouts without `statePath()` that inherit parent path', function (): void {
    livewire(InheritedStatePathComponent::class)
        ->assertOk()
        ->assertSchemaComponentStateSet('parent.value', 'inherited path value', 'infolist')
        ->assertSeeText('inherited path value');
});

class InheritedStatePathComponent extends Component implements HasSchemas
{
    use InteractsWithSchemas;

    public function infolist(Schema $schema): Schema
    {
        return $schema
            ->constantState([
                'parent' => [
                    'value' => 'inherited path value',
                ],
            ])
            ->components([
                Section::make()
                    ->statePath('parent')
                    ->schema([
                        Section::make()
                            ->schema([
                                TextEntry::make('value'),
                            ]),
                    ]),
            ]);
    }

    public function render(): string
    {
        return <<<'BLADE'
            <div>
                {{ $this->infolist }}
            </div>
            BLADE;
    }
}

it('can resolve `getConstantStatePath()` with mixed layouts some with and some without `statePath()`', function (): void {
    livewire(MixedInheritanceComponent::class)
        ->assertOk()
        ->assertSchemaComponentStateSet('level1.level2.value', 'mixed inheritance value', 'infolist')
        ->assertSeeText('mixed inheritance value');
});

class MixedInheritanceComponent extends Component implements HasSchemas
{
    use InteractsWithSchemas;

    public function infolist(Schema $schema): Schema
    {
        return $schema
            ->constantState([
                'level1' => [
                    'level2' => [
                        'value' => 'mixed inheritance value',
                    ],
                ],
            ])
            ->components([
                Section::make()
                    ->statePath('level1')
                    ->schema([
                        Grid::make()
                            ->schema([
                                Section::make()
                                    ->statePath('level2')
                                    ->schema([
                                        Group::make()
                                            ->schema([
                                                TextEntry::make('value'),
                                            ]),
                                    ]),
                            ]),
                    ]),
            ]);
    }

    public function render(): string
    {
        return <<<'BLADE'
            <div>
                {{ $this->infolist }}
            </div>
            BLADE;
    }
}

it('can resolve `getConstantStatePath()` through multiple layouts without `statePath()`', function (): void {
    livewire(MultipleInheritedLayoutsComponent::class)
        ->assertOk()
        ->assertSchemaComponentStateSet('root.value', 'deeply inherited value', 'infolist')
        ->assertSeeText('deeply inherited value');
});

class MultipleInheritedLayoutsComponent extends Component implements HasSchemas
{
    use InteractsWithSchemas;

    public function infolist(Schema $schema): Schema
    {
        return $schema
            ->constantState([
                'root' => [
                    'value' => 'deeply inherited value',
                ],
            ])
            ->components([
                Section::make()
                    ->statePath('root')
                    ->schema([
                        Grid::make()
                            ->schema([
                                Group::make()
                                    ->schema([
                                        Section::make()
                                            ->schema([
                                                TextEntry::make('value'),
                                            ]),
                                    ]),
                            ]),
                    ]),
            ]);
    }

    public function render(): string
    {
        return <<<'BLADE'
            <div>
                {{ $this->infolist }}
            </div>
            BLADE;
    }
}

it('can resolve `getConstantState()` for `TextEntry` in nested sections', function (): void {
    livewire(NestedInfolistComponent::class)
        ->assertOk()
        ->assertSchemaComponentStateSet('page.block.content', 'deeply nested content', 'infolist')
        ->assertSeeText('deeply nested content');
});

class NestedInfolistComponent extends Component implements HasSchemas
{
    use InteractsWithSchemas;

    public function infolist(Schema $schema): Schema
    {
        return $schema
            ->constantState([
                'page' => [
                    'block' => [
                        'content' => 'deeply nested content',
                    ],
                ],
            ])
            ->components([
                Section::make('Page')
                    ->statePath('page')
                    ->schema([
                        Section::make('Block')
                            ->statePath('block')
                            ->schema([
                                TextEntry::make('content'),
                            ]),
                    ]),
            ]);
    }

    public function render(): string
    {
        return <<<'BLADE'
            <div>
                {{ $this->infolist }}
            </div>
            BLADE;
    }
}

it('can resolve `getConstantState()` for `KeyValueEntry` in nested sections', function (): void {
    livewire(NestedKeyValueInfolistComponent::class)
        ->assertOk()
        ->assertSchemaComponentStateSet('page.block.configuration', ['config_key' => 'config_value'], 'infolist')
        ->assertSeeText('config_key')
        ->assertSeeText('config_value');
});

class NestedKeyValueInfolistComponent extends Component implements HasSchemas
{
    use InteractsWithSchemas;

    public function infolist(Schema $schema): Schema
    {
        return $schema
            ->constantState([
                'page' => [
                    'block' => [
                        'configuration' => [
                            'config_key' => 'config_value',
                        ],
                    ],
                ],
            ])
            ->components([
                Section::make('Page')
                    ->statePath('page')
                    ->schema([
                        Section::make('Block')
                            ->statePath('block')
                            ->schema([
                                KeyValueEntry::make('configuration'),
                            ]),
                    ]),
            ]);
    }

    public function render(): string
    {
        return <<<'BLADE'
            <div>
                {{ $this->infolist }}
            </div>
            BLADE;
    }
}

it('can render entry within form that reads form data', function (): void {
    livewire(EntryInFormComponent::class)
        ->assertSuccessful()
        ->assertSchemaComponentStateSet('display', 'Current value: initial', 'form')
        ->assertSeeText('Current value: initial')
        ->set('data.input_field', 'updated value')
        ->assertSchemaComponentStateSet('display', 'Current value: updated value', 'form')
        ->assertSeeText('Current value: updated value');
});

class EntryInFormComponent extends Component implements HasSchemas
{
    use InteractsWithSchemas;

    public array $data = [];

    public function mount(): void
    {
        $this->form->fill([
            'input_field' => 'initial',
        ]);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->statePath('data')
            ->components([
                TextInput::make('input_field')
                    ->live(),
                TextEntry::make('display')
                    ->label('Display')
                    ->state(fn (Get $get): string => 'Current value: ' . $get('input_field')),
            ]);
    }

    public function render(): string
    {
        return <<<'BLADE'
            <div>
                {{ $this->form }}
            </div>
            BLADE;
    }
}

it('can assert entry state within form using `assertSchemaComponentStateSet()`', function (): void {
    livewire(EntryStateInFormComponent::class)
        ->assertSuccessful()
        ->assertSchemaComponentStateSet('summary', 'Form field value: test input', 'form')
        ->assertSeeText('Form field value: test input');
});

class EntryStateInFormComponent extends Component implements HasSchemas
{
    use InteractsWithSchemas;

    public array $data = [];

    public function mount(): void
    {
        $this->form->fill([
            'input' => 'test input',
        ]);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->statePath('data')
            ->components([
                TextInput::make('input'),
                TextEntry::make('summary')
                    ->state(fn (Get $get): string => 'Form field value: ' . $get('input')),
            ]);
    }

    public function render(): string
    {
        return <<<'BLADE'
            <div>
                {{ $this->form }}
            </div>
            BLADE;
    }
}
