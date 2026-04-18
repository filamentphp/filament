<?php

use Filament\Schemas\Components\Wizard;
use Filament\Schemas\Components\Wizard\Step;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Schemas\Schema;
use Filament\Tests\Fixtures\Models\User;
use Filament\Tests\TestCase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\HtmlString;
use Livewire\Component;

use function Filament\Tests\livewire;

uses(TestCase::class);

beforeEach(function (): void {
    Artisan::call('filament:assets');
});

it('can set `skippable()`', function (): void {
    $wizard = Wizard::make();

    expect($wizard->isSkippable())->toBeFalse();

    $wizard->skippable();

    expect($wizard->isSkippable())->toBeTrue();
});

it('can set `startOnStep()`', function (): void {
    $wizard = Wizard::make();

    expect($wizard->getStartStep())->toBe(1);

    $wizard->startOnStep(3);

    expect($wizard->getStartStep())->toBe(3);
});

it('can set `persistStepInQueryString()`', function (): void {
    $wizard = Wizard::make();

    expect($wizard->isStepPersistedInQueryString())->toBeFalse();

    $wizard->persistStepInQueryString();

    expect($wizard->isStepPersistedInQueryString())->toBeTrue();
    expect($wizard->getStepQueryStringKey())->toBe('step');
});

it('can set custom key for `persistStepInQueryString()`', function (): void {
    $wizard = Wizard::make()
        ->persistStepInQueryString('wizardStep');

    expect($wizard->getStepQueryStringKey())->toBe('wizardStep');
});

it('can set `cancelAction()`', function (): void {
    $wizard = Wizard::make();

    expect($wizard->getCancelAction())->toBeNull();

    $wizard->cancelAction('<button>Cancel</button>');

    expect($wizard->getCancelAction())->toBe('<button>Cancel</button>');
});

it('can set `submitAction()`', function (): void {
    $wizard = Wizard::make();

    expect($wizard->getSubmitAction())->toBeNull();

    $wizard->submitAction('<button>Submit</button>');

    expect($wizard->getSubmitAction())->toBe('<button>Submit</button>');
});

it('can set `alpineSubmitHandler()`', function (): void {
    $wizard = Wizard::make();

    expect($wizard->getAlpineSubmitHandler())->toBeNull();

    $wizard->alpineSubmitHandler('submitForm()');

    expect($wizard->getAlpineSubmitHandler())->toBe('submitForm()');
});

it('can set `hiddenHeader()`', function (): void {
    $wizard = Wizard::make();

    expect($wizard->isHeaderHidden())->toBeFalse();

    $wizard->hiddenHeader();

    expect($wizard->isHeaderHidden())->toBeTrue();
});

it('returns `next` for `getNextActionName()`', function (): void {
    $wizard = Wizard::make();

    expect($wizard->getNextActionName())->toBe('next');
});

it('returns `previous` for `getPreviousActionName()`', function (): void {
    $wizard = Wizard::make();

    expect($wizard->getPreviousActionName())->toBe('previous');
});

it('calculates `getCurrentStepIndex()` from `startOnStep()`', function (): void {
    $wizard = Wizard::make()
        ->startOnStep(2);

    expect($wizard->getCurrentStepIndex())->toBe(1);
});

it('can modify `nextAction()` using callback', function (): void {
    $wizard = Wizard::make()
        ->nextAction(static fn ($action) => $action->label('Continue'));

    $nextAction = $wizard->getNextAction();

    expect($nextAction->getLabel())->toBe('Continue');
});

it('can modify `previousAction()` using callback', function (): void {
    $wizard = Wizard::make()
        ->previousAction(static fn ($action) => $action->label('Go Back'));

    $previousAction = $wizard->getPreviousAction();

    expect($previousAction->getLabel())->toBe('Go Back');
});

it('can set `skippable()` with a `Closure`', function (): void {
    $wizard = Wizard::make()
        ->skippable(static fn (): bool => true);

    expect($wizard->isSkippable())->toBeTrue();
});

it('can set `startOnStep()` with a `Closure`', function (): void {
    $wizard = Wizard::make()
        ->startOnStep(static fn (): int => 4);

    expect($wizard->getStartStep())->toBe(4);
});

it('can set `hiddenHeader()` with a `Closure`', function (): void {
    $wizard = Wizard::make()
        ->hiddenHeader(static fn (): bool => true);

    expect($wizard->isHeaderHidden())->toBeTrue();
});

it('can set `alpineSubmitHandler()` with a `Closure`', function (): void {
    $wizard = Wizard::make()
        ->alpineSubmitHandler(static fn (): string => 'dynamicHandler()');

    expect($wizard->getAlpineSubmitHandler())->toBe('dynamicHandler()');
});

it('can clear `persistStepInQueryString()` with `null`', function (): void {
    $wizard = Wizard::make()
        ->persistStepInQueryString()
        ->persistStepInQueryString(null);

    expect($wizard->isStepPersistedInQueryString())->toBeFalse();
    expect($wizard->getStepQueryStringKey())->toBeNull();
});

it('returns fluent `$this` from `steps()`', function (): void {
    $wizard = Wizard::make();

    $result = $wizard->steps([]);

    expect($result)->toBe($wizard);
});

it('defaults `getCurrentStepIndex()` to `0`', function (): void {
    $wizard = Wizard::make();

    expect($wizard->getCurrentStepIndex())->toBe(0);
});

it('returns default label from `getNextAction()` without modifier', function (): void {
    $wizard = Wizard::make();

    $action = $wizard->getNextAction();

    expect($action->getLabel())->toBeString();
    expect($action->getLabel())->not->toBeEmpty();
});

it('can clear `nextAction()` modifier with `null`', function (): void {
    $wizard = Wizard::make()
        ->nextAction(static fn ($action) => $action->label('Custom'))
        ->nextAction(null);

    $action = $wizard->getNextAction();

    // After clearing, should return default label, not 'Custom'
    expect($action->getLabel())->not->toBe('Custom');
});

it('can set `persistStepInQueryString()` with a `Closure`', function (): void {
    $wizard = Wizard::make()
        ->persistStepInQueryString(static fn (): string => 'dynamicKey');

    expect($wizard->getStepQueryStringKey())->toBe('dynamicKey');
    expect($wizard->isStepPersistedInQueryString())->toBeTrue();
});

it('can set `cancelAction()` with an `Htmlable`', function (): void {
    $htmlable = new HtmlString('<button>Cancel Now</button>');
    $wizard = Wizard::make()->cancelAction($htmlable);

    expect($wizard->getCancelAction())->toBe($htmlable);
});

it('can clear `cancelAction()` with `null`', function (): void {
    $wizard = Wizard::make()
        ->cancelAction('<button>Cancel</button>')
        ->cancelAction(null);

    expect($wizard->getCancelAction())->toBeNull();
});

it('can set `submitAction()` with an `Htmlable`', function (): void {
    $htmlable = new HtmlString('<button>Submit Now</button>');
    $wizard = Wizard::make()->submitAction($htmlable);

    expect($wizard->getSubmitAction())->toBe($htmlable);
});

it('can clear `submitAction()` with `null`', function (): void {
    $wizard = Wizard::make()
        ->submitAction('<button>Submit</button>')
        ->submitAction(null);

    expect($wizard->getSubmitAction())->toBeNull();
});

it('can clear `previousAction()` modifier with `null`', function (): void {
    $wizard = Wizard::make()
        ->previousAction(static fn ($action) => $action->label('Custom'))
        ->previousAction(null);

    $action = $wizard->getPreviousAction();

    expect($action->getLabel())->not->toBe('Custom');
});

it('can clear `alpineSubmitHandler()` with `null`', function (): void {
    $wizard = Wizard::make()
        ->alpineSubmitHandler('handler()')
        ->alpineSubmitHandler(null);

    expect($wizard->getAlpineSubmitHandler())->toBeNull();
});

describe('label', function (): void {
    it('returns `null` for `getLabel()` by default', function (): void {
        $wizard = Wizard::make();

        expect($wizard->getLabel())->toBeNull();
    });

    it('can set `label()`', function (): void {
        $wizard = Wizard::make()->label('Registration');

        expect($wizard->getLabel())->toBe('Registration');
    });

    it('can set `label()` with a `Closure`', function (): void {
        $wizard = Wizard::make()
            ->label(static fn (): string => 'Dynamic Label');

        expect($wizard->getLabel())->toBe('Dynamic Label');
    });

    it('can set `label()` with an `Htmlable`', function (): void {
        $htmlable = new HtmlString('<strong>Bold</strong>');
        $wizard = Wizard::make()->label($htmlable);

        expect($wizard->getLabel())->toBe($htmlable);
    });

    it('reports `hasCustomLabel()` as `false` by default', function (): void {
        $wizard = Wizard::make();

        expect($wizard->hasCustomLabel())->toBeFalse();
    });

    it('reports `hasCustomLabel()` as `true` after `label()` is set', function (): void {
        $wizard = Wizard::make()->label('Custom');

        expect($wizard->hasCustomLabel())->toBeTrue();
    });

    it('defaults `isLabelHidden()` to `false`', function (): void {
        $wizard = Wizard::make();

        expect($wizard->isLabelHidden())->toBeFalse();
    });

    it('can set `hiddenLabel()`', function (): void {
        $wizard = Wizard::make()->hiddenLabel();

        expect($wizard->isLabelHidden())->toBeTrue();
    });

    it('can set `hiddenLabel()` with a `Closure`', function (): void {
        $wizard = Wizard::make()
            ->hiddenLabel(static fn (): bool => true);

        expect($wizard->isLabelHidden())->toBeTrue();
    });

    it('can translate label with `translateLabel()`', function (): void {
        $wizard = Wizard::make()
            ->label('validation.required')
            ->translateLabel();

        expect($wizard->getLabel())->toBe(__('validation.required'));
    });
});

describe('containment', function (): void {
    it('defaults `isContained()` to `true`', function (): void {
        $wizard = Wizard::make();

        expect($wizard->isContained())->toBeTrue();
    });

    it('can set `contained()` to `false`', function (): void {
        $wizard = Wizard::make()->contained(false);

        expect($wizard->isContained())->toBeFalse();
    });

    it('can set `contained()` with a `Closure`', function (): void {
        $wizard = Wizard::make()
            ->contained(static fn (): bool => false);

        expect($wizard->isContained())->toBeFalse();
    });
});

describe('extra Alpine attributes', function (): void {
    it('returns empty array for `getExtraAlpineAttributes()` by default', function (): void {
        $wizard = Wizard::make();

        expect($wizard->getExtraAlpineAttributes())->toBe([]);
    });

    it('can set `extraAlpineAttributes()`', function (): void {
        $wizard = Wizard::make()
            ->extraAlpineAttributes(['x-on:click' => 'open = true']);

        expect($wizard->getExtraAlpineAttributes())->toBe(['x-on:click' => 'open = true']);
    });

    it('can merge `extraAlpineAttributes()`', function (): void {
        $wizard = Wizard::make()
            ->extraAlpineAttributes(['x-on:click' => 'open = true'])
            ->extraAlpineAttributes(['x-bind:class' => 'active'], merge: true);

        $attributes = $wizard->getExtraAlpineAttributes();

        expect($attributes)->toHaveKey('x-on:click', 'open = true');
        expect($attributes)->toHaveKey('x-bind:class', 'active');
    });

    it('can set `extraAlpineAttributes()` with a `Closure`', function (): void {
        $wizard = Wizard::make()
            ->extraAlpineAttributes(static fn (): array => ['x-data' => '{}']);

        expect($wizard->getExtraAlpineAttributes())->toBe(['x-data' => '{}']);
    });
});

describe('rendering', function (): void {
    it('can render', function (): void {
        livewire(RenderWizard::class)->assertSuccessful();
    });

    it('can render with `skippable()`', function (): void {
        livewire(RenderWizardWithSkippable::class)->assertSuccessful();
    });

    it('can render with `skippable()` set via `Closure`', function (): void {
        livewire(RenderWizardWithClosureSkippable::class)->assertSuccessful();
    });

    it('can render with `hiddenHeader()`', function (): void {
        livewire(RenderWizardWithHiddenHeader::class)->assertSuccessful();
    });

    it('can render with `hiddenHeader()` set via `Closure`', function (): void {
        livewire(RenderWizardWithClosureHiddenHeader::class)->assertSuccessful();
    });

    it('can render with `contained(false)`', function (): void {
        livewire(RenderWizardWithContainedFalse::class)->assertSuccessful();
    });

    it('can render with `persistStepInQueryString()`', function (): void {
        livewire(RenderWizardWithPersistStep::class)->assertSuccessful();
    });

    it('can render with label', function (): void {
        livewire(RenderWizardWithLabel::class)->assertSuccessful();
    });

    it('can render with `cancelAction()`', function (): void {
        livewire(RenderWizardWithCancelAction::class)->assertSuccessful();
    });

    it('can render with `submitAction()`', function (): void {
        livewire(RenderWizardWithSubmitAction::class)->assertSuccessful();
    });
});

it('can render `Wizard` in the browser', function (): void {
    retry(10, function (): void {
        $this->actingAs(User::factory()->create());

        visit('/wizard-browser-test')
            ->assertSee('Basic Details')
            ->assertNoSmoke()
            ->assertNoAccessibilityIssues();

        visit('/wizard-browser-test')
            ->inDarkMode()
            ->assertNoAccessibilityIssues();
    });
});

class RenderWizard extends Component implements HasSchemas
{
    use InteractsWithSchemas;

    public function infolist(Schema $schema): Schema
    {
        return $schema->state([])->components([Wizard::make()->steps([Step::make('Step 1'), Step::make('Step 2')])]);
    }

    public function render(): string
    {
        return '<div>{{ $this->infolist }}</div>';
    }
}

class RenderWizardWithSkippable extends Component implements HasSchemas
{
    use InteractsWithSchemas;

    public function infolist(Schema $schema): Schema
    {
        return $schema->state([])->components([Wizard::make()->steps([Step::make('Step 1')])->skippable()]);
    }

    public function render(): string
    {
        return '<div>{{ $this->infolist }}</div>';
    }
}

class RenderWizardWithClosureSkippable extends Component implements HasSchemas
{
    use InteractsWithSchemas;

    public function infolist(Schema $schema): Schema
    {
        return $schema->state([])->components([Wizard::make()->steps([Step::make('Step 1')])->skippable(static fn (): bool => true)]);
    }

    public function render(): string
    {
        return '<div>{{ $this->infolist }}</div>';
    }
}

class RenderWizardWithHiddenHeader extends Component implements HasSchemas
{
    use InteractsWithSchemas;

    public function infolist(Schema $schema): Schema
    {
        return $schema->state([])->components([Wizard::make()->steps([Step::make('Step 1')])->hiddenHeader()]);
    }

    public function render(): string
    {
        return '<div>{{ $this->infolist }}</div>';
    }
}

class RenderWizardWithClosureHiddenHeader extends Component implements HasSchemas
{
    use InteractsWithSchemas;

    public function infolist(Schema $schema): Schema
    {
        return $schema->state([])->components([Wizard::make()->steps([Step::make('Step 1')])->hiddenHeader(static fn (): bool => true)]);
    }

    public function render(): string
    {
        return '<div>{{ $this->infolist }}</div>';
    }
}

class RenderWizardWithContainedFalse extends Component implements HasSchemas
{
    use InteractsWithSchemas;

    public function infolist(Schema $schema): Schema
    {
        return $schema->state([])->components([Wizard::make()->steps([Step::make('Step 1')])->contained(false)]);
    }

    public function render(): string
    {
        return '<div>{{ $this->infolist }}</div>';
    }
}

class RenderWizardWithPersistStep extends Component implements HasSchemas
{
    use InteractsWithSchemas;

    public function infolist(Schema $schema): Schema
    {
        return $schema->state([])->components([Wizard::make()->steps([Step::make('Step 1')])->persistStepInQueryString()]);
    }

    public function render(): string
    {
        return '<div>{{ $this->infolist }}</div>';
    }
}

class RenderWizardWithLabel extends Component implements HasSchemas
{
    use InteractsWithSchemas;

    public function infolist(Schema $schema): Schema
    {
        return $schema->state([])->components([Wizard::make()->steps([Step::make('Step 1')])->label('My Wizard')]);
    }

    public function render(): string
    {
        return '<div>{{ $this->infolist }}</div>';
    }
}

class RenderWizardWithCancelAction extends Component implements HasSchemas
{
    use InteractsWithSchemas;

    public function infolist(Schema $schema): Schema
    {
        return $schema->state([])->components([Wizard::make()->steps([Step::make('Step 1')])->cancelAction(new HtmlString('<button>Cancel</button>'))]);
    }

    public function render(): string
    {
        return '<div>{{ $this->infolist }}</div>';
    }
}

class RenderWizardWithSubmitAction extends Component implements HasSchemas
{
    use InteractsWithSchemas;

    public function infolist(Schema $schema): Schema
    {
        return $schema->state([])->components([Wizard::make()->steps([Step::make('Step 1')])->submitAction(new HtmlString('<button>Submit</button>'))]);
    }

    public function render(): string
    {
        return '<div>{{ $this->infolist }}</div>';
    }
}
