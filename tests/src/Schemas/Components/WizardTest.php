<?php

use Filament\Actions\Action;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Wizard;
use Filament\Schemas\Components\Wizard\Step;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Schemas\Schema;
use Filament\Support\Exceptions\Halt;
use Filament\Tests\Fixtures\Livewire\Livewire;
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

it('restores query-string steps using absolute keys rather than custom IDs', function (mixed $query, int $expected): void {
    request()->query->replace(['step' => $query, 'delivery_step' => 'form.delivery.wizard.details']);

    Schema::make(Livewire::make())->key('form')->components([
        $profile = Wizard::make([
            Step::make('Details')->key('details')->id('profile-details'),
            Step::make('Contact')->key('contact')->id('profile-contact'),
        ])->key('wizard')->persistStepInQueryString()->startOnStep(2),
        Group::make([
            $delivery = Wizard::make([
                Step::make('Hidden')->hidden(),
                Step::make('Details')->key('details')->id('delivery-details'),
                Step::make('Contact')->key('contact')->id('delivery-contact'),
            ])->key('wizard')->persistStepInQueryString('delivery_step')->startOnStep(2),
        ])->key('delivery'),
    ])->fill();

    expect($profile->getStartStep())->toBe($expected)
        ->and($delivery->getStartStep())->toBe(1)
        ->and($profile->toHtml())->toContain('profile-details')
        ->and($delivery->toHtml())->toContain('delivery-details');
})->with([
    'absolute key' => ['form.wizard.details', 1],
    'custom ID is not a persisted key' => ['profile-details', 2],
    'relative key is not a persisted step key' => ['details', 2],
    'another container' => ['form.delivery.wizard.details', 2],
    'stale key' => ['removed', 2],
    'missing value' => [null, 2],
    'array value' => [['form.wizard.details'], 2],
]);

it('persists independent wizards across reload and browser history', function (): void {
    $this->actingAs(User::factory()->create());

    $browser = visit('/wizard-browser-test')
        ->assertVisible('#profile-details')
        ->assertVisible('#delivery-details')
        ->click('[data-testid="wizard-next-action"]')
        ->assertVisible('#profile-contact')
        ->assertVisible('#delivery-details')
        ->click('[data-testid="delivery-next-action"]')
        ->assertVisible('#delivery-contact');

    expect($browser->script('new URL(location.href).searchParams.get("step")'))->toBe('form.wizard.contact');
    expect($browser->script('new URL(location.href).searchParams.get("delivery_step")'))->toBe('form.delivery.wizard.contact');

    $browser->script("Livewire.navigate('/tabs-browser-test')");
    $browser->assertVisible('#profile-tabs')
        ->back()->assertVisible('#profile-contact')->assertVisible('#delivery-contact')
        ->forward()->assertVisible('#profile-tabs')
        ->back()->assertVisible('#profile-contact')->assertVisible('#delivery-contact')
        ->click('[data-testid="wizard-next-action"]')
        ->assertVisible('#profile-review')->assertVisible('#delivery-contact');

    expect($browser->script('new URL(location.href).searchParams.get("step")'))->toBe('form.wizard.review');

    $browser->refresh()->assertVisible('#profile-review')->assertVisible('#delivery-contact')
        ->assertNoAccessibilityIssues()
        ->navigate('/wizard-browser-test?step=form.wizard.details&delivery_step=form.delivery.wizard.details')
        ->assertVisible('#profile-details')->assertVisible('#delivery-details')
        ->back()->assertVisible('#profile-review')->assertVisible('#delivery-contact')
        ->forward()->assertVisible('#profile-details')->assertVisible('#delivery-details')
        ->navigate('/wizard-browser-test?step=removed&delivery_step=delivery-contact')
        ->assertVisible('#profile-details')->assertVisible('#delivery-details')
        ->assertNoSmoke();

    visit('/wizard-browser-test?step=form.wizard.contact&delivery_step=form.delivery.wizard.contact')->inDarkMode()
        ->assertVisible('#profile-contact')->assertVisible('#delivery-contact')
        ->assertNoAccessibilityIssues();
});

it('does not turn an unknown browser step into the first step', function (): void {
    $this->actingAs(User::factory()->create());

    $browser = visit('/wizard-browser-test')->assertVisible('#profile-details');

    expect($browser->script(<<<'JS'
        (() => {
            const wizard = Alpine.$data(document.querySelector('#profile-wizard'))
            wizard.goToStep('removed')
            return wizard.step
        })()
        JS))->toBe('form.wizard.details');

    expect($browser->script(<<<'JS'
        (async () => {
            const wizard = Alpine.$data(document.querySelector('#profile-wizard'))
            wizard.step = 'removed'
            await wizard.requestNextStep()
            wizard.goToNextStep()
            return [wizard.getStepIndex('removed'), wizard.step]
        })()
        JS))->toBe([-1, 'removed']);

    $browser->assertMissing('#profile-contact')->assertNoSmoke();
});

it('advances to the resolved visible step when a validation hook hides its own step', function (): void {
    $this->actingAs(User::factory()->create());

    $browser = visit('/wizard-browser-test?hide_contact_after_validation=1')
        ->assertVisible('#profile-details')
        ->click('[data-testid="wizard-next-action"]')
        ->assertVisible('#profile-contact')
        ->click('[data-testid="wizard-next-action"]')
        ->assertVisible('#profile-review')
        ->assertMissing('#profile-contact')
        ->hover('#profile-review')
        ->wait(0.3)
        ->assertNoAccessibilityIssues()
        ->assertNoSmoke();

    expect($browser->script('new URL(location.href).searchParams.get("step")'))->toBe('form.wizard.review');

    visit('/wizard-browser-test?step=form.wizard.review')->inDarkMode()
        ->assertVisible('#profile-review')->assertNoAccessibilityIssues();
});

it('rejects invalid `nextStep()` indexes before hooks or state changes', function (array $arguments, bool $skippable): void {
    $component = livewire(WizardTransitions::class, ['skippable' => $skippable])
        ->call('callSchemaComponentMethod', 'form.wizard', 'nextStep', $arguments)
        ->assertNotDispatched('next-wizard-step')
        ->assertSet('hooks', [])
        ->assertSet('data.name', 'Ada')
        ->assertHasNoErrors();

    expect($component->instance()->form->getComponent('wizard')->getCurrentStepIndex())->toBe(0);
})->with([
    'missing' => [[]],
    'null' => [['currentStepIndex' => null]],
    'negative' => [['currentStepIndex' => -1]],
    'numeric string' => [['currentStepIndex' => '0']],
    'malformed string' => [['currentStepIndex' => 'invalid']],
    'boolean' => [['currentStepIndex' => false]],
    'float' => [['currentStepIndex' => 0.5]],
    'array' => [['currentStepIndex' => []]],
    'last' => [['currentStepIndex' => 2]],
    'last plus one' => [['currentStepIndex' => 3]],
    'overflow' => [['currentStepIndex' => PHP_INT_MAX]],
])->with([false, true]);

it('rejects stale `nextStep()` indexes after visibility changes', function (int $index, string $key): void {
    livewire(WizardTransitions::class)
        ->set('hideFirst', true)
        ->call('callSchemaComponentMethod', 'form.wizard', 'nextStep', [
            'currentStepIndex' => $index,
            'currentStepKey' => $key,
        ])
        ->assertSet('hooks', [])
        ->assertNotDispatched('next-wizard-step')
        ->assertHasNoErrors();
})->with([
    'in-range index now identifies another step' => [0, 'form.wizard.details'],
    'old middle index is now last' => [1, 'form.wizard.contact'],
    'old last index is out of range' => [2, 'form.wizard.review'],
]);

it('validates only the current visible step before advancing', function (bool $hideFirst, string $expectedStep): void {
    $component = livewire(WizardTransitions::class, ['hideFirst' => $hideFirst])
        ->call('callSchemaComponentMethod', 'form.wizard', 'nextStep', [
            'currentStepIndex' => 0,
            'currentStepKey' => "form.wizard.{$expectedStep}",
        ])
        ->assertSet('hooks', ["{$expectedStep}:before", "{$expectedStep}:after"])
        ->assertDispatched('next-wizard-step', key: 'form.wizard')
        ->assertHasNoErrors();

    expect($component->instance()->form->getComponent('wizard')->getCurrentStepIndex())->toBe(1);
})->with([[false, 'details'], [true, 'contact']]);

it('does not advance on validation failure or `Halt`', function (string $failure, array $expectedHooks): void {
    $component = livewire(WizardTransitions::class, ['failure' => $failure])
        ->call('callSchemaComponentMethod', 'form.wizard', 'nextStep', ['currentStepIndex' => 0])
        ->assertSet('hooks', $expectedHooks)
        ->assertNotDispatched('next-wizard-step');

    if ($failure === 'validation') {
        $component->assertHasErrors(['data.name' => 'required']);
    } else {
        $component->assertHasNoErrors();
    }

    expect($component->instance()->form->getComponent('wizard')->getCurrentStepIndex())->toBe(0);
})->with([
    ['validation', ['details:before']],
    ['before', ['details:before']],
    ['after', ['details:before', 'details:after']],
]);

it('does not call a disabled or unauthorized next action', function (string $restriction): void {
    livewire(WizardTransitions::class, ['restriction' => $restriction])
        ->call('callSchemaComponentMethod', 'form.wizard', 'nextStep', ['currentStepIndex' => 0])
        ->assertSet('hooks', [])
        ->assertNotDispatched('next-wizard-step');
})->with(['disabled', 'unauthorized']);

it('preserves valid index-only calls and skips validation only for `skippable()` wizards', function (): void {
    $component = livewire(WizardTransitions::class, ['skippable' => true, 'failure' => 'validation'])
        ->call('callSchemaComponentMethod', 'form.wizard', 'nextStep', ['currentStepIndex' => 0])
        ->assertSet('hooks', [])
        ->assertDispatched('next-wizard-step', key: 'form.wizard')
        ->assertHasNoErrors();

    expect($component->instance()->form->getComponent('wizard')->getCurrentStepIndex())->toBe(1);
});

it('keeps the accepted later step on failure and validates it again on retry', function (string $failure, bool $withKey): void {
    $arguments = ['currentStepIndex' => 1];

    if ($withKey) {
        $arguments['currentStepKey'] = 'form.wizard.contact';
    }

    $component = livewire(WizardTransitions::class, ['failure' => $failure])
        ->goToNextWizardStep()
        ->assertWizardCurrentStep(2)
        ->call('callSchemaComponentMethod', 'form.wizard', 'nextStep', $arguments)
        ->assertWizardCurrentStep(2)
        ->assertNotDispatched('next-wizard-step');

    if ($failure === 'contact-validation') {
        $component->assertHasErrors(['data.contact_notes' => 'required']);
    } else {
        $component->assertHasNoErrors();
    }

    $component->set('failure', '')
        ->call('callSchemaComponentMethod', 'form.wizard', 'nextStep', $arguments)
        ->assertWizardCurrentStep(3)
        ->assertDispatched('next-wizard-step', step: 'form.wizard.review')
        ->assertHasNoErrors();

    expect(array_slice($component->get('hooks'), -2))->toBe(['contact:before', 'contact:after']);
})->with(['contact-validation', 'contact-before', 'contact-after'])->with([false, true]);

it('resolves and initializes the visible destination after hooks change the sequence', function (string $mutation, string $destination, int $index, string $field): void {
    $component = livewire(WizardTransitions::class, ['mutation' => $mutation])
        ->set('data', ['name' => 'Ada'])
        ->call('callSchemaComponentMethod', 'form.wizard', 'nextStep', [
            'currentStepIndex' => 0,
            'currentStepKey' => 'form.wizard.details',
        ])
        ->assertSet('hooks', ['details:before', 'details:after'])
        ->assertDispatched('next-wizard-step', step: "form.wizard.{$destination}", currentStep: 'form.wizard.details')
        ->assertHasNoErrors();

    expect($component->instance()->form->getComponent('wizard')->getCurrentStepIndex())->toBe($index)
        ->and($component->get('data'))->toBe(['name' => 'Ada', $field => null]);
})->with([
    ['hide-current', 'contact', 0, 'contact_notes'],
    ['hide-next', 'review', 1, 'review_notes'],
    ['replace-next', 'replacement', 1, 'replacement_notes'],
]);

it('checks `previousStep()` bounds without changing valid backward navigation', function (array $arguments, int $expected): void {
    $component = livewire(WizardTransitions::class, ['startStep' => 3])
        ->call('callSchemaComponentMethod', 'form.wizard', 'previousStep', $arguments)
        ->assertSet('hooks', [])
        ->assertNotDispatched('next-wizard-step')
        ->assertHasNoErrors();

    expect($component->instance()->form->getComponent('wizard')->getCurrentStepIndex())->toBe($expected);
})->with([
    'missing' => [[], 2],
    'malformed' => [['currentStepIndex' => 'invalid'], 2],
    'negative' => [['currentStepIndex' => -1], 2],
    'first' => [['currentStepIndex' => 0], 2],
    'last' => [['currentStepIndex' => 2], 1],
    'past last' => [['currentStepIndex' => 3], 2],
]);

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

    it('preserves a custom `livewireTarget()` on the next action', function (): void {
        livewire(RenderWizardWithCustomNextActionLivewireTarget::class)
            ->assertSeeHtml('wire:target="customTarget"');
    });
});

it('can render `Wizard` in the browser', function (): void {
    retry(10, function (): void {
        $this->actingAs(User::factory()->create());

        visit('/wizard-browser-test')
            ->assertNoSmoke()
            ->assertNoAccessibilityIssues();

        visit('/wizard-browser-test')
            ->inDarkMode()
            ->assertNoAccessibilityIssues();
    });
});

it('only shows the next action loading indicator for its own request', function (): void {
    retry(10, function (): void {
        $this->actingAs(User::factory()->create());

        $nextAction = '[data-testid="wizard-next-action"]';
        $nextActionLoadingIndicator = "{$nextAction} .fi-loading-indicator";

        $browser = visit('/wizard-browser-test')
            ->click('[data-testid="wizard-dynamic-select"] .fi-select-input-btn')
            ->wait(0.3);

        expect($browser->script(
            "document.querySelector('{$nextAction}').parentElement.hasAttribute('inert')",
        ))->toBeTrue();

        expect($browser->script(
            "Boolean(document.querySelector('{$nextActionLoadingIndicator}')?.getClientRects().length)",
        ))->toBeFalse();

        $browser
            ->click('Draft')
            ->click($nextAction)
            ->assertVisible($nextActionLoadingIndicator)
            ->assertNoSmoke();
    });
});

class WizardTransitions extends Livewire
{
    public array $hooks = [];

    public bool $hideFirst = false;

    public bool $skippable = false;

    public string $failure = '';

    public string $restriction = '';

    public int $startStep = 1;

    public string $mutation = '';

    public bool $changed = false;

    public function form(Schema $schema): Schema
    {
        return $schema->statePath('data')->components([
            Wizard::make(fn (): array => [
                Step::make('Details')->key('details')
                    ->hidden(fn (): bool => $this->hideFirst || ($this->changed && ($this->mutation === 'hide-current')))
                    ->beforeValidation(function (): void {
                        $this->hooks[] = 'details:before';

                        if ($this->failure === 'before') {
                            throw new Halt;
                        }
                    })
                    ->afterValidation(function (): void {
                        $this->hooks[] = 'details:after';
                        $this->changed = true;

                        if ($this->failure === 'after') {
                            throw new Halt;
                        }
                    })
                    ->schema([
                        TextInput::make('name')->required()->default(fn (): ?string => $this->failure === 'validation' ? null : 'Ada'),
                    ]),
                Step::make('Contact')->key(($this->changed && ($this->mutation === 'replace-next')) ? 'replacement' : 'contact')
                    ->hidden(fn (): bool => $this->changed && ($this->mutation === 'hide-next'))
                    ->beforeValidation(function (): void {
                        $this->hooks[] = 'contact:before';

                        if ($this->failure === 'contact-before') {
                            throw new Halt;
                        }
                    })
                    ->afterValidation(function (): void {
                        $this->hooks[] = 'contact:after';

                        if ($this->failure === 'contact-after') {
                            throw new Halt;
                        }
                    })
                    ->schema([
                        TextInput::make(($this->changed && ($this->mutation === 'replace-next')) ? 'replacement_notes' : 'contact_notes')
                            ->required(fn (): bool => $this->failure === 'contact-validation'),
                    ]),
                Step::make('Review')->key('review')
                    ->beforeValidation(function (): void {
                        $this->hooks[] = 'review:before';
                    })
                    ->schema([TextInput::make('review_notes')]),
            ])->key('wizard')->skippable(fn (): bool => $this->skippable)
                ->startOnStep(fn (): int => $this->startStep)
                ->nextAction(fn (Action $action): Action => $action
                    ->disabled($this->restriction === 'disabled')
                    ->authorize($this->restriction !== 'unauthorized')),
        ]);
    }
}

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

class RenderWizardWithCustomNextActionLivewireTarget extends Component implements HasSchemas
{
    use InteractsWithSchemas;

    public function infolist(Schema $schema): Schema
    {
        return $schema
            ->state([])
            ->components([
                Wizard::make([
                    Step::make('Step 1'),
                    Step::make('Step 2'),
                ])->nextAction(
                    static fn (Action $action): Action => $action->livewireTarget('customTarget'),
                ),
            ]);
    }

    public function render(): string
    {
        return '<div>{{ $this->infolist }}</div>';
    }
}
