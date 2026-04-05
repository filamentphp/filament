<?php

use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Schemas\Schema;
use Filament\Support\Enums\IconSize;
use Filament\Support\Icons\Heroicon;
use Filament\Tests\Fixtures\Models\Profile;
use Filament\Tests\Fixtures\Models\User;
use Filament\Tests\TestCase;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\HtmlString;
use Livewire\Component;

use function Filament\Tests\livewire;

uses(TestCase::class);

beforeEach(function (): void {
    Artisan::call('filament:assets');
});

it('can load state from a `HasOne` relationship using eager loaded data without additional queries', function (): void {
    $user = User::factory()->create();
    $profile = Profile::factory()->create(['user_id' => $user->id]);

    $freshUser = $user->fresh();
    expect($freshUser->relationLoaded('profile'))->toBeFalse();

    DB::enableQueryLog();
    DB::flushQueryLog();

    livewire(SectionWithHasOneRelationship::class, ['record' => $freshUser])
        ->assertSchemaStateSet(function (array $state) use ($profile) {
            expect($state['profile']['bio'])->toBe($profile->bio);

            return [];
        });

    $queriesWithoutEagerLoading = count(DB::getQueryLog());

    $eagerUser = $user->fresh();
    $eagerUser->load('profile');
    expect($eagerUser->relationLoaded('profile'))->toBeTrue();

    DB::flushQueryLog();

    livewire(SectionWithHasOneRelationship::class, ['record' => $eagerUser])
        ->assertSchemaStateSet(function (array $state) use ($profile) {
            expect($state['profile']['bio'])->toBe($profile->bio);

            return [];
        });

    $queriesWithEagerLoading = count(DB::getQueryLog());
    DB::disableQueryLog();

    $queriesSaved = $queriesWithoutEagerLoading - $queriesWithEagerLoading;
    expect($queriesSaved)->toBe(1, "Expected to save 1 query with eager loading, but saved {$queriesSaved}");
});

it('can set `aside()`', function (): void {
    $section = Section::make('Test');

    expect($section->isAside())->toBeFalse();

    $section->aside();

    expect($section->isAside())->toBeTrue();

    $section->aside(false);

    expect($section->isAside())->toBeFalse();
});

it('can set `formBefore()`', function (): void {
    $section = Section::make('Test');

    expect($section->isFormBefore())->toBeFalse();

    $section->formBefore();

    expect($section->isFormBefore())->toBeTrue();
});

it('returns `true` for `canConcealComponents()` when collapsible', function (): void {
    $section = Section::make('Test');

    expect($section->canConcealComponents())->toBeFalse();

    $section->collapsible();

    expect($section->canConcealComponents())->toBeTrue();
});

it('returns correct `getHeadingsCount()`', function (): void {
    $sectionWithHeading = Section::make('My Section');

    expect($sectionWithHeading->getHeadingsCount())->toBe(1);

    $sectionWithoutHeading = Section::make();

    expect($sectionWithoutHeading->getHeadingsCount())->toBe(0);
});

it('can set `aside()` with a `Closure`', function (): void {
    $section = Section::make('Test')
        ->aside(static fn (): bool => true);

    expect($section->isAside())->toBeTrue();
});

it('returns `false` for `isAside()` when set to `null`', function (): void {
    $section = Section::make('Test')
        ->aside()
        ->aside(null);

    expect($section->isAside())->toBeFalse();
});

it('can set `formBefore()` with a `Closure`', function (): void {
    $section = Section::make('Test')
        ->formBefore(static fn (): bool => true);

    expect($section->isFormBefore())->toBeTrue();
});

it('can construct `Section` with an array of components', function (): void {
    $section = Section::make([
        TextInput::make('name'),
        TextInput::make('email'),
    ]);

    expect($section->getHeading())->toBeNull();
    expect($section->getHeadingsCount())->toBe(0);
});

it('returns fluent `$this` from slot methods', function (): void {
    $section = Section::make('Test');

    expect($section->afterHeader([]))->toBe($section);
    expect($section->footer([]))->toBe($section);
    expect($section->beforeLabel([]))->toBe($section);
    expect($section->afterLabel([]))->toBe($section);
    expect($section->aboveContent([]))->toBe($section);
    expect($section->belowContent([]))->toBe($section);
});

describe('collapsing', function (): void {
    it('defaults `isCollapsible()` to `false`', function (): void {
        $section = Section::make('Test');

        expect($section->isCollapsible())->toBeFalse();
    });

    it('can set `collapsible()`', function (): void {
        $section = Section::make('Test')->collapsible();

        expect($section->isCollapsible())->toBeTrue();
    });

    it('can set `collapsible()` with a `Closure`', function (): void {
        $section = Section::make('Test')
            ->collapsible(static fn (): bool => true);

        expect($section->isCollapsible())->toBeTrue();
    });

    it('can undo `collapsible()` with `null`', function (): void {
        $section = Section::make('Test')
            ->collapsible()
            ->collapsible(null);

        expect($section->isCollapsible())->toBeFalse();
    });

    it('defaults `isCollapsed()` to `false`', function (): void {
        $section = Section::make('Test');

        expect($section->isCollapsed())->toBeFalse();
    });

    it('can set `collapsed()` and it implies `collapsible()`', function (): void {
        $section = Section::make('Test')->collapsed();

        expect($section->isCollapsed())->toBeTrue();
        expect($section->isCollapsible())->toBeTrue();
    });

    it('can set `collapsed()` without making collapsible', function (): void {
        $section = Section::make('Test')->collapsed(shouldMakeComponentCollapsible: false);

        expect($section->isCollapsed())->toBeTrue();
        expect($section->isCollapsible())->toBeFalse();
    });

    it('does not override explicit `collapsible()` when `collapsed()` is called', function (): void {
        $section = Section::make('Test')
            ->collapsible(false)
            ->collapsed();

        expect($section->isCollapsed())->toBeTrue();
        expect($section->isCollapsible())->toBeFalse();
    });

    it('can set `collapsed()` with a `Closure`', function (): void {
        $section = Section::make('Test')
            ->collapsed(static fn (): bool => true);

        expect($section->isCollapsed())->toBeTrue();
    });

    it('defaults `shouldPersistCollapsed()` to `false`', function (): void {
        $section = Section::make('Test');

        expect($section->shouldPersistCollapsed())->toBeFalse();
    });

    it('can set `persistCollapsed()`', function (): void {
        $section = Section::make('Test')->persistCollapsed();

        expect($section->shouldPersistCollapsed())->toBeTrue();
    });

    it('can set `persistCollapsed()` with a `Closure`', function (): void {
        $section = Section::make('Test')
            ->persistCollapsed(static fn (): bool => true);

        expect($section->shouldPersistCollapsed())->toBeTrue();
    });
});

describe('compact', function (): void {
    it('defaults `isCompact()` to `false`', function (): void {
        $section = Section::make('Test');

        expect($section->isCompact())->toBeFalse();
    });

    it('can set `compact()`', function (): void {
        $section = Section::make('Test')->compact();

        expect($section->isCompact())->toBeTrue();
    });

    it('can set `compact()` with a `Closure`', function (): void {
        $section = Section::make('Test')
            ->compact(static fn (): bool => true);

        expect($section->isCompact())->toBeTrue();
    });
});

describe('containment', function (): void {
    it('defaults `isContained()` to `true`', function (): void {
        $section = Section::make('Test');

        expect($section->isContained())->toBeTrue();
    });

    it('can set `contained()` to `false`', function (): void {
        $section = Section::make('Test')->contained(false);

        expect($section->isContained())->toBeFalse();
    });

    it('can set `contained()` with a `Closure`', function (): void {
        $section = Section::make('Test')
            ->contained(static fn (): bool => false);

        expect($section->isContained())->toBeFalse();
    });
});

describe('divided', function (): void {
    it('defaults `isDivided()` to `false`', function (): void {
        $section = Section::make('Test');

        expect($section->isDivided())->toBeFalse();
    });

    it('can set `divided()`', function (): void {
        $section = Section::make('Test')->divided();

        expect($section->isDivided())->toBeTrue();
    });

    it('can set `divided()` with a `Closure`', function (): void {
        $section = Section::make('Test')
            ->divided(static fn (): bool => true);

        expect($section->isDivided())->toBeTrue();
    });
});

describe('secondary', function (): void {
    it('defaults `isSecondary()` to `false`', function (): void {
        $section = Section::make('Test');

        expect($section->isSecondary())->toBeFalse();
    });

    it('can set `secondary()`', function (): void {
        $section = Section::make('Test')->secondary();

        expect($section->isSecondary())->toBeTrue();
    });

    it('can set `secondary()` with a `Closure`', function (): void {
        $section = Section::make('Test')
            ->secondary(static fn (): bool => true);

        expect($section->isSecondary())->toBeTrue();
    });
});

describe('heading', function (): void {
    it('can be constructed with a heading', function (): void {
        $section = Section::make('Personal Info');

        expect($section->getHeading())->toBe('Personal Info');
    });

    it('returns `null` for `getHeading()` when no heading given', function (): void {
        $section = Section::make();

        expect($section->getHeading())->toBeNull();
    });

    it('can set `heading()` with a `Closure`', function (): void {
        $section = Section::make()
            ->heading(static fn (): string => 'Dynamic Heading');

        expect($section->getHeading())->toBe('Dynamic Heading');
    });

    it('can set `heading()` with an `Htmlable`', function (): void {
        $htmlable = new HtmlString('<strong>Bold</strong>');
        $section = Section::make()->heading($htmlable);

        expect($section->getHeading())->toBe($htmlable);
    });

    it('can clear `heading()` with `null`', function (): void {
        $section = Section::make('Heading')->heading(null);

        expect($section->getHeading())->toBeNull();
    });
});

describe('description', function (): void {
    it('returns `null` for `getDescription()` by default', function (): void {
        $section = Section::make('Test');

        expect($section->getDescription())->toBeNull();
    });

    it('can set `description()`', function (): void {
        $section = Section::make('Test')->description('Some description');

        expect($section->getDescription())->toBe('Some description');
    });

    it('can set `description()` with a `Closure`', function (): void {
        $section = Section::make('Test')
            ->description(static fn (): string => 'Dynamic');

        expect($section->getDescription())->toBe('Dynamic');
    });

    it('can set `description()` with an `Htmlable`', function (): void {
        $htmlable = new HtmlString('<em>Rich</em>');
        $section = Section::make('Test')->description($htmlable);

        expect($section->getDescription())->toBe($htmlable);
    });

    it('can clear `description()` with `null`', function (): void {
        $section = Section::make('Test')
            ->description('text')
            ->description(null);

        expect($section->getDescription())->toBeNull();
    });
});

describe('label', function (): void {
    it('returns `null` for `getLabel()` by default', function (): void {
        $section = Section::make('Test');

        expect($section->getLabel())->toBeNull();
    });

    it('can set `label()`', function (): void {
        $section = Section::make('Test')->label('My Label');

        expect($section->getLabel())->toBe('My Label');
    });

    it('can set `label()` with a `Closure`', function (): void {
        $section = Section::make('Test')
            ->label(static fn (): string => 'Dynamic Label');

        expect($section->getLabel())->toBe('Dynamic Label');
    });

    it('reports `hasCustomLabel()` as `false` by default', function (): void {
        $section = Section::make('Test');

        expect($section->hasCustomLabel())->toBeFalse();
    });

    it('reports `hasCustomLabel()` as `true` after `label()` is set', function (): void {
        $section = Section::make('Test')->label('Custom');

        expect($section->hasCustomLabel())->toBeTrue();
    });

    it('defaults `isLabelHidden()` to `false`', function (): void {
        $section = Section::make('Test');

        expect($section->isLabelHidden())->toBeFalse();
    });

    it('can set `hiddenLabel()`', function (): void {
        $section = Section::make('Test')->hiddenLabel();

        expect($section->isLabelHidden())->toBeTrue();
    });

    it('can set `hiddenLabel()` with a `Closure`', function (): void {
        $section = Section::make('Test')
            ->hiddenLabel(static fn (): bool => true);

        expect($section->isLabelHidden())->toBeTrue();
    });

    it('can translate label with `translateLabel()`', function (): void {
        $section = Section::make('Test')
            ->label('validation.required')
            ->translateLabel();

        expect($section->getLabel())->toBe(__('validation.required'));
    });
});

describe('icon', function (): void {
    it('returns `null` for `getIcon()` by default', function (): void {
        $section = Section::make('Test');

        expect($section->getIcon())->toBeNull();
    });

    it('can set `icon()` with a string', function (): void {
        $section = Section::make('Test')->icon('heroicon-o-cog');

        expect($section->getIcon())->toBe('heroicon-o-cog');
    });

    it('can set `icon()` with a `BackedEnum`', function (): void {
        $section = Section::make('Test')->icon(Heroicon::Cog6Tooth);

        expect($section->getIcon())->toBe(Heroicon::Cog6Tooth);
    });

    it('can set `icon()` with a `Closure`', function (): void {
        $section = Section::make('Test')
            ->icon(static fn (): string => 'heroicon-o-star');

        expect($section->getIcon())->toBe('heroicon-o-star');
    });

    it('can clear `icon()` with `null`', function (): void {
        $section = Section::make('Test')
            ->icon('heroicon-o-cog')
            ->icon(null);

        expect($section->getIcon())->toBeNull();
    });

    it('can use `getIcon()` with a default', function (): void {
        $section = Section::make('Test');

        expect($section->getIcon('heroicon-o-fallback'))->toBe('heroicon-o-fallback');
    });
});

describe('icon color', function (): void {
    it('returns `null` for `getIconColor()` by default', function (): void {
        $section = Section::make('Test');

        expect($section->getIconColor())->toBeNull();
    });

    it('can set `iconColor()`', function (): void {
        $section = Section::make('Test')->iconColor('danger');

        expect($section->getIconColor())->toBe('danger');
    });

    it('can set `iconColor()` with a `Closure`', function (): void {
        $section = Section::make('Test')
            ->iconColor(static fn (): string => 'success');

        expect($section->getIconColor())->toBe('success');
    });

    it('can clear `iconColor()` with `null`', function (): void {
        $section = Section::make('Test')
            ->iconColor('danger')
            ->iconColor(null);

        expect($section->getIconColor())->toBeNull();
    });
});

describe('icon size', function (): void {
    it('returns `null` for `getIconSize()` by default', function (): void {
        $section = Section::make('Test');

        expect($section->getIconSize())->toBeNull();
    });

    it('can set `iconSize()` with an enum', function (): void {
        $section = Section::make('Test')->iconSize(IconSize::Large);

        expect($section->getIconSize())->toBe(IconSize::Large);
    });

    it('can set `iconSize()` with a `Closure`', function (): void {
        $section = Section::make('Test')
            ->iconSize(static fn (): IconSize => IconSize::Small);

        expect($section->getIconSize())->toBe(IconSize::Small);
    });

    it('can clear `iconSize()` with `null`', function (): void {
        $section = Section::make('Test')
            ->iconSize(IconSize::Large)
            ->iconSize(null);

        expect($section->getIconSize())->toBeNull();
    });
});

describe('extra Alpine attributes', function (): void {
    it('returns empty array for `getExtraAlpineAttributes()` by default', function (): void {
        $section = Section::make('Test');

        expect($section->getExtraAlpineAttributes())->toBe([]);
    });

    it('can set `extraAlpineAttributes()`', function (): void {
        $section = Section::make('Test')
            ->extraAlpineAttributes(['x-on:click' => 'open = true']);

        expect($section->getExtraAlpineAttributes())->toBe(['x-on:click' => 'open = true']);
    });

    it('can merge `extraAlpineAttributes()`', function (): void {
        $section = Section::make('Test')
            ->extraAlpineAttributes(['x-on:click' => 'open = true'])
            ->extraAlpineAttributes(['x-bind:class' => 'active'], merge: true);

        $attributes = $section->getExtraAlpineAttributes();

        expect($attributes)->toHaveKey('x-on:click', 'open = true');
        expect($attributes)->toHaveKey('x-bind:class', 'active');
    });

    it('can set `extraAlpineAttributes()` with a `Closure`', function (): void {
        $section = Section::make('Test')
            ->extraAlpineAttributes(static fn (): array => ['x-data' => '{}']);

        expect($section->getExtraAlpineAttributes())->toBe(['x-data' => '{}']);
    });
});

class SectionWithHasOneRelationship extends Component implements HasActions, HasSchemas
{
    use InteractsWithActions;
    use InteractsWithSchemas;

    public $data = [];

    public User $record;

    public function mount(): void
    {
        $this->form->fill([]);
    }

    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                Section::make('Profile')
                    ->relationship('profile')
                    ->schema([
                        TextInput::make('bio'),
                    ]),
            ])
            ->model($this->record)
            ->statePath('data');
    }

    public function render(): View
    {
        return view('livewire.form');
    }
}

describe('rendering', function (): void {
    it('can render with heading', function (): void {
        livewire(RenderSectionWithHeading::class)->assertSuccessful()->assertSee('My Section');
    });

    it('can render with `heading()` set via `Closure`', function (): void {
        livewire(RenderSectionWithClosureHeading::class)->assertSuccessful()->assertSee('Dynamic');
    });

    it('can render with `description()`', function (): void {
        livewire(RenderSectionWithDescription::class)->assertSuccessful()->assertSee('Description text');
    });

    it('can render with `description()` set via `Closure`', function (): void {
        livewire(RenderSectionWithClosureDescription::class)->assertSuccessful();
    });

    it('can render with `collapsible()`', function (): void {
        livewire(RenderSectionWithCollapsible::class)->assertSuccessful();
    });

    it('can render with `collapsed()`', function (): void {
        livewire(RenderSectionWithCollapsed::class)->assertSuccessful();
    });

    it('can render with `compact()`', function (): void {
        livewire(RenderSectionWithCompact::class)->assertSuccessful();
    });

    it('can render with `contained(false)`', function (): void {
        livewire(RenderSectionWithContainedFalse::class)->assertSuccessful();
    });

    it('can render with `aside()`', function (): void {
        livewire(RenderSectionWithAside::class)->assertSuccessful();
    });

    it('can render with `aside()` set via `Closure`', function (): void {
        livewire(RenderSectionWithClosureAside::class)->assertSuccessful();
    });

    it('can render with `divided()`', function (): void {
        livewire(RenderSectionWithDivided::class)->assertSuccessful();
    });

    it('can render with `secondary()`', function (): void {
        livewire(RenderSectionWithSecondary::class)->assertSuccessful();
    });

    it('can render with `icon()`', function (): void {
        livewire(RenderSectionWithIcon::class)->assertSuccessful();
    });

    it('can render with `icon()` set via `Closure`', function (): void {
        livewire(RenderSectionWithClosureIcon::class)->assertSuccessful();
    });

    it('can render with `iconColor()`', function (): void {
        livewire(RenderSectionWithIconColor::class)->assertSuccessful();
    });

    it('can render with `iconSize()`', function (): void {
        livewire(RenderSectionWithIconSize::class)->assertSuccessful();
    });
});

it('can render `Section` in the browser', function (): void {
    retry(10, function (): void {
        $this->actingAs(User::factory()->create());

        visit('/section-browser-test')
            ->assertSee('Personal Information')
            ->assertNoSmoke()
            ->assertNoAccessibilityIssues();

        visit('/section-browser-test')
            ->inDarkMode()
            ->assertNoAccessibilityIssues();
    });
});

class RenderSectionWithHeading extends Component implements HasSchemas
{
    use InteractsWithSchemas;

    public function infolist(Schema $schema): Schema
    {
        return $schema->state([])->components([Section::make('My Section')]);
    }

    public function render(): string
    {
        return '<div>{{ $this->infolist }}</div>';
    }
}

class RenderSectionWithClosureHeading extends Component implements HasSchemas
{
    use InteractsWithSchemas;

    public function infolist(Schema $schema): Schema
    {
        return $schema->state([])->components([Section::make(static fn (): string => 'Dynamic')]);
    }

    public function render(): string
    {
        return '<div>{{ $this->infolist }}</div>';
    }
}

class RenderSectionWithDescription extends Component implements HasSchemas
{
    use InteractsWithSchemas;

    public function infolist(Schema $schema): Schema
    {
        return $schema->state([])->components([Section::make('Test')->description('Description text')]);
    }

    public function render(): string
    {
        return '<div>{{ $this->infolist }}</div>';
    }
}

class RenderSectionWithClosureDescription extends Component implements HasSchemas
{
    use InteractsWithSchemas;

    public function infolist(Schema $schema): Schema
    {
        return $schema->state([])->components([Section::make('Test')->description(static fn (): string => 'Dynamic desc')]);
    }

    public function render(): string
    {
        return '<div>{{ $this->infolist }}</div>';
    }
}

class RenderSectionWithCollapsible extends Component implements HasSchemas
{
    use InteractsWithSchemas;

    public function infolist(Schema $schema): Schema
    {
        return $schema->state([])->components([Section::make('Test')->collapsible()]);
    }

    public function render(): string
    {
        return '<div>{{ $this->infolist }}</div>';
    }
}

class RenderSectionWithCollapsed extends Component implements HasSchemas
{
    use InteractsWithSchemas;

    public function infolist(Schema $schema): Schema
    {
        return $schema->state([])->components([Section::make('Test')->collapsed()]);
    }

    public function render(): string
    {
        return '<div>{{ $this->infolist }}</div>';
    }
}

class RenderSectionWithCompact extends Component implements HasSchemas
{
    use InteractsWithSchemas;

    public function infolist(Schema $schema): Schema
    {
        return $schema->state([])->components([Section::make('Test')->compact()]);
    }

    public function render(): string
    {
        return '<div>{{ $this->infolist }}</div>';
    }
}

class RenderSectionWithContainedFalse extends Component implements HasSchemas
{
    use InteractsWithSchemas;

    public function infolist(Schema $schema): Schema
    {
        return $schema->state([])->components([Section::make('Test')->contained(false)]);
    }

    public function render(): string
    {
        return '<div>{{ $this->infolist }}</div>';
    }
}

class RenderSectionWithAside extends Component implements HasSchemas
{
    use InteractsWithSchemas;

    public function infolist(Schema $schema): Schema
    {
        return $schema->state([])->components([Section::make('Test')->aside()]);
    }

    public function render(): string
    {
        return '<div>{{ $this->infolist }}</div>';
    }
}

class RenderSectionWithClosureAside extends Component implements HasSchemas
{
    use InteractsWithSchemas;

    public function infolist(Schema $schema): Schema
    {
        return $schema->state([])->components([Section::make('Test')->aside(static fn (): bool => true)]);
    }

    public function render(): string
    {
        return '<div>{{ $this->infolist }}</div>';
    }
}

class RenderSectionWithDivided extends Component implements HasSchemas
{
    use InteractsWithSchemas;

    public function infolist(Schema $schema): Schema
    {
        return $schema->state([])->components([Section::make('Test')->divided()]);
    }

    public function render(): string
    {
        return '<div>{{ $this->infolist }}</div>';
    }
}

class RenderSectionWithSecondary extends Component implements HasSchemas
{
    use InteractsWithSchemas;

    public function infolist(Schema $schema): Schema
    {
        return $schema->state([])->components([Section::make('Test')->secondary()]);
    }

    public function render(): string
    {
        return '<div>{{ $this->infolist }}</div>';
    }
}

class RenderSectionWithIcon extends Component implements HasSchemas
{
    use InteractsWithSchemas;

    public function infolist(Schema $schema): Schema
    {
        return $schema->state([])->components([Section::make('Test')->icon(Heroicon::Check)]);
    }

    public function render(): string
    {
        return '<div>{{ $this->infolist }}</div>';
    }
}

class RenderSectionWithClosureIcon extends Component implements HasSchemas
{
    use InteractsWithSchemas;

    public function infolist(Schema $schema): Schema
    {
        return $schema->state([])->components([Section::make('Test')->icon(static fn () => Heroicon::Star)]);
    }

    public function render(): string
    {
        return '<div>{{ $this->infolist }}</div>';
    }
}

class RenderSectionWithIconColor extends Component implements HasSchemas
{
    use InteractsWithSchemas;

    public function infolist(Schema $schema): Schema
    {
        return $schema->state([])->components([Section::make('Test')->icon(Heroicon::Check)->iconColor('success')]);
    }

    public function render(): string
    {
        return '<div>{{ $this->infolist }}</div>';
    }
}

class RenderSectionWithIconSize extends Component implements HasSchemas
{
    use InteractsWithSchemas;

    public function infolist(Schema $schema): Schema
    {
        return $schema->state([])->components([Section::make('Test')->icon(Heroicon::Check)->iconSize(IconSize::Large)]);
    }

    public function render(): string
    {
        return '<div>{{ $this->infolist }}</div>';
    }
}
