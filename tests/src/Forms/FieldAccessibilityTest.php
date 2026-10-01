<?php

use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Field;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Components\ToggleButtons;
use Filament\Schemas\Components\FusedGroup;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Text;
use Filament\Schemas\Schema;
use Filament\Tests\Fixtures\Livewire\Livewire;
use Filament\Tests\Fixtures\Models\User;
use Filament\Tests\TestCase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\HtmlString;
use Illuminate\Support\MessageBag;
use Illuminate\Support\ViewErrorBag;
use Illuminate\View\ComponentAttributeBag;

uses(TestCase::class);

dataset('accessible native and grouped fields', [
    'text' => [static fn (): Field => TextInput::make('answer')],
    'textarea' => [static fn (): Field => Textarea::make('answer')],
    'checkbox' => [static fn (): Field => Checkbox::make('answer')],
    'select' => [static fn (): Field => Select::make('answer')->options(['first' => 'First'])],
    'date-time' => [static fn (): Field => DateTimePicker::make('answer')],
    'date' => [static fn (): Field => DatePicker::make('answer')],
    'time' => [static fn (): Field => TimePicker::make('answer')],
    'radio' => [static fn (): Field => Radio::make('answer')->options(['first' => 'First', 'second' => 'Second'])],
    'checkbox list' => [static fn (): Field => CheckboxList::make('answer')->options(['first' => 'First'])],
    'toggle buttons' => [static fn (): Field => ToggleButtons::make('answer')->options(['first' => 'First'])],
    'grouped toggle buttons' => [static fn (): Field => ToggleButtons::make('answer')->grouped()->options(['first' => 'First'])],
    'multiple toggle buttons' => [static fn (): Field => ToggleButtons::make('answer')->multiple()->options(['first' => 'First'])],
    'grouped multiple toggle buttons' => [static fn (): Field => ToggleButtons::make('answer')->multiple()->grouped()->options(['first' => 'First'])],
]);

function fieldAccessibilityDocument(string $html): DOMXPath
{
    $document = new DOMDocument;
    $previous = libxml_use_internal_errors(true);
    $document->loadHTML('<meta charset="UTF-8">' . $html);
    libxml_clear_errors();
    libxml_use_internal_errors($previous);

    return new DOMXPath($document);
}

it('associates native and grouped controls with `helperText()` and errors but not hints or arbitrary slot text', function (Closure $makeField, bool $bladeWrapper): void {
    $field = $makeField()->required()->hint('Choose carefully')->helperText('Only used for delivery')
        ->aboveContent(Group::make([Text::make('Other content')->extraAttributes(['id' => 'other-content'])]))
        ->belowErrorMessage('More context')->extraInputAttributes(['data-owner' => 'application']);
    if ($bladeWrapper) {
        $field->fieldWrapperView('test-plugin-wrapper');
    }
    $schema = Schema::make(Livewire::make())->statePath('data')->components([$field]);
    $isMultiple = ($field instanceof CheckboxList) || (($field instanceof ToggleButtons) && $field->isMultiple());

    foreach ([false, true, false] as $hasError) {
        view()->share('errors', (new ViewErrorBag)->put('default', new MessageBag($hasError ? ['data.answer' => ['Please check your answer.']] : [])));
        $document = fieldAccessibilityDocument($schema->toHtml());
        expect($document->query('//input | //textarea | //select')->length)->toBe(($field instanceof Radio) ? 2 : 1);
        foreach ($document->query('//label[@for]') as $label) {
            expect($document->query('//*[@id="' . $label->getAttribute('for') . '"]')->length)->toBe(1);
        }
        foreach ($document->query('//*[@aria-labelledby]') as $control) {
            expect($document->query('//*[@id="' . $control->getAttribute('aria-labelledby') . '"]')->length)->toBe(1);
        }
        $expectedDescriptions = [
            'data.answer-helper-text' => 'Only used for delivery',
            ...($hasError ? ['data.answer-error' => 'Please check your answer.'] : []),
            ...($isMultiple ? ['data.answer-required' => 'Select at least one option.'] : []),
        ];
        foreach ($document->query('//input | //textarea | //select | //*[@role="group" or @role="radiogroup"]') as $control) {
            expect($control->getAttribute('aria-invalid'))->toBe($hasError ? 'true' : '')
                ->and($control->getAttribute('aria-describedby'))->toBe(implode(' ', array_keys($expectedDescriptions)));
            foreach ($expectedDescriptions as $reference => $description) {
                $targets = $document->query('//*[@id="' . $reference . '"]');
                expect($targets->length)->toBe(1)
                    ->and(trim($targets->item(0)->textContent))->toBe($description);
            }
        }
        foreach ($document->query('//input | //textarea | //select') as $control) {
            expect($control->getAttribute('data-owner'))->toBe('application');
        }
        expect($document->query('//*[@data-validation-error and (@role="alert" or @aria-live)]')->length)->toBe(0);
    }
})->with('accessible native and grouped fields')->with([false, true]);

it('removes replaced or empty `helperText()` associations', function (bool $bladeWrapper): void {
    $field = TextInput::make('answer')->hint('Hint only');
    if ($bladeWrapper) {
        $field->fieldWrapperView('test-plugin-wrapper');
    }
    $schema = Schema::make(Livewire::make())->components([$field]);
    expect($field->getHelperTextId())->toBeNull();

    foreach ([
        ['Instructions', 'Instructions'],
        [null, null],
        ['', null],
        [new HtmlString('<strong>Formatted instructions</strong>'), 'Formatted instructions'],
        ['0', '0'],
    ] as [$content, $expectedText]) {
        expect($field->helperText($content))->toBe($field);
        $document = fieldAccessibilityDocument($schema->toHtml());
        $targets = $document->query('//*[@id="answer-helper-text"]');
        expect($field->getHelperTextId())->toBe($expectedText === null ? null : 'answer-helper-text')
            ->and($document->query('//input')->item(0)->getAttribute('aria-describedby'))->toBe($expectedText === null ? '' : 'answer-helper-text')
            ->and($targets->length)->toBe($expectedText === null ? 0 : 1);
        if ($expectedText !== null) {
            expect(trim($targets->item(0)->textContent))->toBe($expectedText);
        }
    }

    foreach ([false, true] as $directChildComponents) {
        $field->helperText('Removed instructions');
        $schema->toHtml();
        if ($directChildComponents) {
            $field->childComponents(Text::make('Replacement text'), Field::BELOW_CONTENT_SCHEMA_KEY);
        } else {
            $field->belowContent(static fn (): Text => Text::make('Replacement text'));
        }
        $document = fieldAccessibilityDocument($schema->toHtml());
        expect($field->getHelperTextId())->toBeNull()
            ->and($document->query('//input')->item(0)->hasAttribute('aria-describedby'))->toBeFalse()
            ->and($document->query('//*[@id="answer-helper-text"]')->length)->toBe(0);
    }
})->with([false, true]);

it('evaluates conditional `helperText()` against the current field state', function (bool $bladeWrapper): void {
    $field = TextInput::make('answer')->helperText(static fn (?string $state): ?string => $state === 'show' ? 'Conditional instructions' : null);
    if ($bladeWrapper) {
        $field->fieldWrapperView('test-plugin-wrapper');
    }
    $schema = Schema::make(Livewire::make())->statePath('data')->components([$field]);
    foreach (['hide', 'show', 'hide'] as $state) {
        $schema->fill(['answer' => $state]);
        $document = fieldAccessibilityDocument($schema->toHtml());
        expect($document->query('//input')->item(0)->getAttribute('aria-describedby'))->toBe($state === 'show' ? 'data.answer-helper-text' : '')
            ->and($document->query('//*[@id="data.answer-helper-text"]')->length)->toBe($state === 'show' ? 1 : 0);
    }
})->with([false, true]);

it('preserves configured helper text attributes and excludes hidden helpers', function (bool $hidden, bool $bladeWrapper, bool $fusedGroup): void {
    Text::configureUsing(
        static fn (Text $text) => $text->hidden($hidden)->extraAttributes(['id' => 'configured-text', 'data-owner' => 'application']),
        during: function () use ($hidden, $bladeWrapper, $fusedGroup): void {
            $component = $fusedGroup ? FusedGroup::make([TextInput::make('answer')])->key('answer') : TextInput::make('answer');
            $component->helperText('Configured instructions');
            if ($bladeWrapper) {
                $component->fieldWrapperView('test-plugin-wrapper');
            }
            $document = fieldAccessibilityDocument(Schema::make(Livewire::make())->components([$component])->toHtml());
            $control = $document->query($fusedGroup ? '//*[@role="group"]' : '//input')->item(0);
            expect($control->getAttribute('aria-describedby'))->toBe($hidden ? '' : 'answer-helper-text')
                ->and($component->getHelperTextId())->toBe($hidden ? null : 'answer-helper-text')
                ->and($document->query('//*[@id="answer-helper-text"]')->length)->toBe($hidden ? 0 : 1);
            if (! $hidden) {
                expect($document->query('//*[@id="configured-text" and @data-owner="application"]')->length)->toBe(1)
                    ->and(trim($document->query('//*[@id="answer-helper-text"]')->item(0)->textContent))->toBe('Configured instructions');
            }
        },
    );
})->with([false, true])->with([false, true])->with([false, true]);

it('preserves helper schema configuration hooks and references the rendered ID', function (): void {
    $field = new class('answer') extends TextInput
    {
        protected function makeChildSchema(string $key): Schema
        {
            $schema = parent::makeChildSchema($key);

            return ($key === self::BELOW_CONTENT_SCHEMA_KEY)
                ? $schema->extraAttributes(['id' => e('configured&#65;'), 'data-owner' => 'application'])
                : $schema;
        }
    };
    $field->helperText('Configured instructions');
    $document = fieldAccessibilityDocument(Schema::make(Livewire::make())->components([$field])->toHtml());
    expect($document->query('//input')->item(0)->getAttribute('aria-describedby'))->toBe('configured&#65;')
        ->and($document->query('//*[@id="configured&#65;" and @data-owner="application"]')->length)->toBe(1);
});

it('does not describe helpers suppressed by direct or inherited plain wrappers', function (bool $nested): void {
    $group = FusedGroup::make([TextInput::make('answer')])->key('answer')->helperText('Suppressed instructions');
    $component = $nested ? FusedGroup::make([$group]) : $group->fieldWrapperView('filament-forms::plain-field-wrapper');
    $document = fieldAccessibilityDocument(Schema::make(Livewire::make())->components([$component])->toHtml());
    expect($group->getHelperTextId())->toBeNull()
        ->and($document->query('//*[@aria-describedby]')->length)->toBe(0)
        ->and($document->query('//*[@id="answer-helper-text"]')->length)->toBe(0);
})->with([false, true]);

it('keeps `helperText()` IDs attached to the current field when cloning nested fields', function (bool $cloneFirst): void {
    $field = TextInput::make('name')->helperText('Guest instructions');
    Schema::make(Livewire::make())->components([$field])->toHtml();
    $clone = $field->getClone()->id('second-guest');
    $groups = [
        Group::make([$field])->statePath('first'),
        Group::make([$clone])->statePath('second'),
    ];
    $schema = Schema::make(Livewire::make())->key('form')->statePath('data')->components($cloneFirst ? array_reverse($groups) : $groups);
    for ($render = 0; $render < 2; $render++) {
        $document = fieldAccessibilityDocument($schema->toHtml());
        foreach (['form.first.name', 'second-guest'] as $id) {
            $control = $document->query('//input[@id="' . $id . '"]')->item(0);
            expect($control->getAttribute('aria-describedby'))->toBe("{$id}-helper-text")
                ->and($document->query('//*[@id="' . $id . '-helper-text"]')->length)->toBe(1);
        }
    }
})->with([false, true]);

it('lets input attributes override accessibility defaults', function (Closure $makeField, bool $bladeWrapper): void {
    $field = $makeField()->required()->helperText('Default instructions')->extraInputAttributes(['aria-describedby' => 'application-note', 'aria-invalid' => 'false', 'required' => false]);
    if ($bladeWrapper) {
        $field->fieldWrapperView('test-plugin-wrapper');
    }
    view()->share('errors', (new ViewErrorBag)->put('default', new MessageBag(['answer' => ['Check your answer.']])));
    $document = fieldAccessibilityDocument('<p id="application-note">Application instructions</p>' . Schema::make(Livewire::make())->components([$field])->toHtml());
    foreach ($document->query('//input | //textarea | //select') as $control) {
        expect($control->getAttribute('aria-describedby'))->toBe('application-note')
            ->and($control->getAttribute('aria-invalid'))->toBe('false')
            ->and($control->hasAttribute('required'))->toBeFalse();
    }
})->with('accessible native and grouped fields')->with([false, true]);

it('supports explicitly associated instructions alongside automatic error descriptions', function (bool $badge, bool $bladeWrapper): void {
    $field = TextInput::make('email')
        ->belowContent(Text::make('Use your work email address.')->badge($badge)->extraAttributes(['id' => 'work-email-instructions']))
        ->extraInputAttributes(static fn (TextInput $component): array => [
            'aria-describedby' => e(implode(' ', ['work-email-instructions', ...$component->getDescriptionIds()])),
        ]);
    if ($bladeWrapper) {
        $field->fieldWrapperView('test-plugin-wrapper');
    }
    $schema = Schema::make(Livewire::make())->components([$field]);
    foreach ([false, true, false] as $hasError) {
        view()->share('errors', (new ViewErrorBag)->put('default', new MessageBag($hasError ? ['email' => ['Check your email.']] : [])));
        $document = fieldAccessibilityDocument($schema->toHtml());
        $references = explode(' ', $document->query('//input')->item(0)->getAttribute('aria-describedby'));
        expect($references)->toBe(['work-email-instructions', ...($hasError ? ['email-error'] : [])]);
        $descriptions = [];
        foreach ($references as $reference) {
            $targets = $document->query('//*[@id="' . $reference . '"]');
            expect($targets->length)->toBe(1);
            $descriptions[] = trim($targets->item(0)->textContent);
        }
        expect($descriptions)->toBe(['Use your work email address.', ...($hasError ? ['Check your email.'] : [])]);
    }
})->with([false, true])->with([false, true]);

it('preserves literal entity-like field IDs in label and error references', function (Closure $makeField, bool $bladeWrapper): void {
    $field = $makeField()->id('choice&#65;')->helperText('Instructions');
    if ($bladeWrapper) {
        $field->fieldWrapperView('test-plugin-wrapper');
    }
    view()->share('errors', (new ViewErrorBag)->put('default', new MessageBag(['answer' => ['Check your answer.']])));
    $document = fieldAccessibilityDocument(Schema::make(Livewire::make())->components([$field])->toHtml());
    foreach ($document->query('//input | //textarea | //select') as $control) {
        expect($control->getAttribute('aria-describedby'))->toBe('choice&#65;-helper-text choice&#65;-error')
            ->and($document->query('//*[@id="choice&#65;-helper-text"]')->length)->toBe(1)
            ->and($document->query('//*[@id="choice&#65;-error"]')->length)->toBe(1);
    }
    foreach ($document->query('//*[@aria-labelledby]') as $group) {
        expect($group->getAttribute('aria-labelledby'))->toBe('choice&#65;-label')
            ->and($document->query('//*[@id="choice&#65;-label"]')->length)->toBe(1);
    }
})->with('accessible native and grouped fields')->with([false, true]);

it('keeps same-name fields with distinct nested state paths associated across renders', function (): void {
    $schema = Schema::make(Livewire::make())->key('form')->statePath('data')->components([
        Group::make([TextInput::make('name')])->statePath('guests.first'),
        Group::make([TextInput::make('name')->hiddenLabel()])->statePath('guests.second'),
    ]);
    view()->share('errors', (new ViewErrorBag)->put('default', new MessageBag([
        'data.guests.first.name' => ['First guest is missing.'],
        'data.guests.second.name' => ['Second guest is missing.'],
    ])));
    $html = $schema->toHtml();
    $document = fieldAccessibilityDocument($html);
    $ids = [];
    foreach ($document->query('//input') as $index => $control) {
        $id = $control->getAttribute('id');
        $ids[] = $id;
        expect($document->query('//label[@for="' . $id . '"]')->length)->toBe(1);
        $reference = $control->getAttribute('aria-describedby');
        $targets = $document->query('//*[@id="' . $reference . '"]');
        expect($reference)->toBe($id . '-error')
            ->and($targets->length)->toBe(1)
            ->and(trim($targets->item(0)->textContent))->toBe(['First guest is missing.', 'Second guest is missing.'][$index]);
    }
    expect($ids)->toBe(['form.guests.first.name', 'form.guests.second.name'])
        ->and($schema->toHtml())->toBe($html);
});

it('preserves attribute hook precedence when overriding accessibility defaults', function (string $fieldClass, string $expectedReference): void {
    $field = $fieldClass::make('answer')
        ->extraInputAttributes(['aria-describedby' => e('application&note')])
        ->extraAlpineAttributes(['aria-describedby' => 'alpine-note', 'aria-invalid' => 'grammar']);
    view()->share('errors', (new ViewErrorBag)->put('default', new MessageBag(['answer' => ['Check your answer.']])));
    $document = fieldAccessibilityDocument(Schema::make(Livewire::make())->components([$field])->toHtml());
    $control = $document->query('//input | //textarea')->item(0);
    expect($control->getAttribute('aria-describedby'))->toBe($expectedReference)
        ->and($control->getAttribute('aria-invalid'))->toBe('grammar');
})->with([[TextInput::class, 'application&note'], [Textarea::class, 'alpine-note'], [DateTimePicker::class, 'application&note']]);

it('preserves `Textarea::getExtraInputAttributeBag()` overrides and Alpine precedence', function (): void {
    $field = new class('answer') extends Textarea
    {
        public function getExtraInputAttributeBag(): ComponentAttributeBag
        {
            return parent::getExtraInputAttributeBag()->merge(['aria-describedby' => 'extension-note', 'data-extension' => 'present']);
        }
    };
    $schema = Schema::make(Livewire::make())->components([$field]);
    $document = fieldAccessibilityDocument($schema->toHtml());
    expect($document->query('//textarea')->item(0)->getAttribute('aria-describedby'))->toBe('extension-note');

    $field->extraAlpineAttributes(['aria-describedby' => 'alpine-note']);
    $control = fieldAccessibilityDocument($schema->toHtml())->query('//textarea')->item(0);
    expect($control->getAttribute('aria-describedby'))->toBe('alpine-note')
        ->and($control->getAttribute('data-extension'))->toBe('present');
});

it('lets Alpine attributes suppress accessibility defaults', function (string $fieldClass, ?string $description): void {
    $field = $fieldClass::make('answer')->extraAlpineAttributes(['aria-describedby' => $description, 'aria-invalid' => null]);
    view()->share('errors', (new ViewErrorBag)->put('default', new MessageBag(['answer' => ['Check your answer.']])));
    $control = fieldAccessibilityDocument(Schema::make(Livewire::make())->components([$field])->toHtml())->query('//input | //textarea')->item(0);
    expect($control->getAttribute('aria-describedby'))->toBe('')
        ->and($control->hasAttribute('aria-describedby'))->toBe($description !== null)
        ->and($control->hasAttribute('aria-invalid'))->toBeFalse();
})->with([TextInput::class, Textarea::class, DateTimePicker::class])->with(['', null]);

it('preserves group attribute overrides without referencing an absent label', function (Closure $makeField): void {
    $field = $makeField()->name('')->required();
    $attributes = ['aria-label' => 'Contact preference', 'aria-describedby' => 'group-note', 'aria-invalid' => 'false', 'aria-required' => null];
    ($field instanceof CheckboxList) ? $field->extraAlpineAttributes($attributes) : $field->extraAttributes($attributes);
    view()->share('errors', (new ViewErrorBag)->put('default', new MessageBag(['answer' => ['Check your answer.']])));
    $document = fieldAccessibilityDocument(Schema::make(Livewire::make())->components([$field])->toHtml());
    $group = $document->query('//*[@role="group" or @role="radiogroup"]')->item(0);
    expect($group->hasAttribute('aria-labelledby'))->toBeFalse()
        ->and($group->getAttribute('aria-label'))->toBe('Contact preference')
        ->and($group->getAttribute('aria-describedby'))->toBe('group-note')
        ->and($group->getAttribute('aria-invalid'))->toBe('false')
        ->and($group->hasAttribute('aria-required'))->toBeFalse();
    foreach ($document->query('//input') as $control) {
        expect($control->getAttribute('aria-invalid'))->toBe('true');
        foreach (explode(' ', $control->getAttribute('aria-describedby')) as $reference) {
            expect($document->query('//*[@id="' . $reference . '"]')->length)->toBe(1);
        }
    }
})->with([
    [static fn (): Field => Radio::make('answer')->options(['a' => 'Option'])],
    [static fn (): Field => CheckboxList::make('answer')->options(['a' => 'Option'])],
    [static fn (): Field => ToggleButtons::make('answer')->options(['a' => 'Option'])],
    [static fn (): Field => ToggleButtons::make('answer')->grouped()->options(['a' => 'Option'])],
    [static fn (): Field => ToggleButtons::make('answer')->multiple()->options(['a' => 'Option'])],
    [static fn (): Field => ToggleButtons::make('answer')->multiple()->grouped()->options(['a' => 'Option'])],
]);

it('evaluates Alpine input attributes once per render', function (string $fieldClass): void {
    $evaluations = 0;
    $field = $fieldClass::make('name')->extraAlpineAttributes(function () use (&$evaluations): array {
        $evaluations++;

        return ['aria-describedby' => 'application-note'];
    });
    Schema::make(Livewire::make())->components([$field])->toHtml();
    expect($evaluations)->toBe(1);
})->with([TextInput::class, Textarea::class, DateTimePicker::class]);

it('associates fused group helpers and errors without referencing suppressed child content', function (bool $bladeWrapper, bool $keyed): void {
    $groups = [
        FusedGroup::make([TextInput::make('name')->helperText('Suppressed child helper')])->label('Contact')->helperText('Contact instructions'),
        FusedGroup::make([TextInput::make('city')])->label('Address')->hiddenLabel()->helperText('Address instructions'),
    ];
    foreach ($groups as $index => $group) {
        if ($keyed) {
            $group->key(['contact', 'address'][$index]);
        }
        if ($bladeWrapper) {
            $group->fieldWrapperView('test-plugin-wrapper');
        }
    }
    view()->share('errors', (new ViewErrorBag)->put('default', new MessageBag([
        'data.name' => ['Name is missing.'],
        'data.city' => ['City is missing.'],
    ])));
    $document = fieldAccessibilityDocument(Schema::make(Livewire::make())->statePath('data')->components($groups)->toHtml());
    expect($document->query('//*[@id="-label" or @id="-error" or @id="-helper-text"]')->length)->toBe(0)
        ->and($document->query('//*[@data-validation-error]')->length)->toBe(2);
    foreach ($document->query('//input') as $control) {
        expect($control->hasAttribute('aria-describedby'))->toBeFalse();
    }
    foreach ($document->query('//*[@role="group"]') as $index => $control) {
        expect($control->getAttribute('aria-describedby'))->toBe($keyed ? $groups[$index]->getId() . '-helper-text ' . $groups[$index]->getId() . '-error' : '');
        foreach (['aria-describedby', 'aria-labelledby'] as $attribute) {
            if ($control->hasAttribute($attribute)) {
                foreach (explode(' ', $control->getAttribute($attribute)) as $reference) {
                    expect($document->query('//*[@id="' . $reference . '"]')->length)->toBe(1);
                }
            }
        }
    }

    $groups[0]->extraAttributes(['aria-describedby' => 'application-note']);
    $document = fieldAccessibilityDocument(Schema::make(Livewire::make())->statePath('data')->components($groups)->toHtml());
    expect($document->query('//*[@role="group"]')->item(0)->getAttribute('aria-describedby'))->toBe('application-note');
})->with([false, true])->with([false, true]);

it('links nested and multiple errors without making them live announcements', function (bool $htmlErrors, bool $multiple, bool $bladeWrapper): void {
    $field = CheckboxList::make('choices')->options(['first' => 'First'])
        ->allowHtmlValidationMessages($htmlErrors)->showAllValidationMessages();
    if ($bladeWrapper) {
        $field->fieldWrapperView('test-plugin-wrapper');
    }
    view()->share('errors', (new ViewErrorBag)->put('default', new MessageBag([
        'data.choices.0' => $multiple ? ['First error.', 'Second error.'] : ['First error.'],
    ])));
    $document = fieldAccessibilityDocument(Schema::make(Livewire::make())->statePath('data')->components([$field])->toHtml());
    $input = $document->query('//input')->item(0);
    $error = $document->query('//*[@id="' . $input->getAttribute('aria-describedby') . '"]')->item(0);
    expect($input->getAttribute('aria-invalid'))->toBe('true')
        ->and($error->nodeName)->toBe($multiple ? 'ul' : ($htmlErrors ? 'div' : 'p'))
        ->and($error->textContent)->toContain('First error.')
        ->and($error->hasAttribute('aria-live'))->toBeFalse();
})->with([false, true])->with([false, true])->with([false, true]);

it('exposes required groups without requiring every checkbox', function (Closure $makeField, bool $multiple, bool $disabled): void {
    $field = $makeField()->required()->disabled($disabled)->hiddenLabel();
    $document = fieldAccessibilityDocument(Schema::make(Livewire::make())->components([$field])->toHtml());
    $group = $document->query('//*[@role="group" or @role="radiogroup"]')->item(0);
    expect($document->query('//*[@id="' . $group->getAttribute('aria-labelledby') . '"]')->length)->toBe(1)
        ->and($group->getAttribute('aria-required'))->toBe((! $multiple && ! $disabled) ? 'true' : '');
    foreach ($document->query('//input') as $control) {
        expect($control->hasAttribute('required'))->toBe(! $multiple && ! $disabled)
            ->and($control->hasAttribute('aria-required'))->toBeFalse();
        if (! $field instanceof CheckboxList) {
            expect($control->getAttribute('id'))->toBe('answer-' . $control->getAttribute('value'));
        }
    }
    expect($field->getRequiredDescription() !== null)->toBe($multiple && ! $disabled);
})->with([
    [static fn (): Field => Radio::make('answer')->options(['a' => 'Option']), false],
    [static fn (): Field => CheckboxList::make('answer')->options(['a' => 'Option']), true],
    [static fn (): Field => ToggleButtons::make('answer')->options(['a' => 'Option']), false],
    [static fn (): Field => ToggleButtons::make('answer')->grouped()->options(['a' => 'Option']), false],
    [static fn (): Field => ToggleButtons::make('answer')->multiple()->options(['a' => 'Option']), true],
    [static fn (): Field => ToggleButtons::make('answer')->multiple()->grouped()->options(['a' => 'Option']), true],
])->with([false, true]);

it('does not require disabled or read-only native controls', function (string $fieldClass, bool $disabled, bool $readOnly): void {
    $field = $fieldClass::make('answer')->required()->disabled($disabled)->readOnly($readOnly);
    $document = fieldAccessibilityDocument(Schema::make(Livewire::make())->components([$field])->toHtml());
    expect($document->query('//input | //textarea')->item(0)->hasAttribute('required'))->toBe(! $disabled && ! $readOnly);
})->with([TextInput::class, Textarea::class, DateTimePicker::class])->with([false, true])->with([false, true]);

it('allows components to translate `getRequiredDescription()` independently', function (): void {
    app('translator')->addLines([
        'components.checkbox_list.required_description' => 'Choose an interest.',
        'components.toggle_buttons.required_description' => 'Choose an available day.',
    ], 'en', 'filament-forms');

    $schema = Schema::make(Livewire::make());

    expect(CheckboxList::make('interests')->container($schema)->required()->getRequiredDescription())->toBe('Choose an interest.')
        ->and(ToggleButtons::make('days')->container($schema)->multiple()->required()->getRequiredDescription())->toBe('Choose an available day.');
});

it('updates accessible error descriptions after validation in the browser', function (): void {
    Artisan::call('filament:assets');
    $this->actingAs(User::factory()->create());

    foreach ([false, true] as $darkMode) {
        $page = $darkMode
            ? visit('/field-accessibility-test', ['reducedMotion' => 'reduce'])->inDarkMode()
            : visit('/field-accessibility-test', ['reducedMotion' => 'reduce'])->inLightMode();
        $page->assertAttribute('[data-testid="name"]', 'aria-describedby', 'form.name-helper-text')
            ->assertAttribute('[data-testid="delivery"]', 'aria-describedby', 'form.delivery-helper-text')
            ->assertAttribute('[data-testid="interests"]', 'aria-describedby', 'form.interests-helper-text form.interests-required')
            ->assertAttribute('[data-testid="notes"]', 'aria-describedby', 'privacy-note');
        expect($page->script('document.getElementById("form.name-helper-text").textContent.trim()'))->toBe('This appears on your membership card.')
            ->and($page->script('document.querySelector("[data-testid=name]").required'))->toBeTrue()
            ->and($page->script('document.getElementById("form.delivery-helper-text").textContent.trim()'))->toBe('Choose how to receive your card.');
        $page->assertNoAccessibilityIssues();

        $page->select('[data-testid="delivery"]', 'post')
            ->assertScript('document.querySelector("[data-testid=delivery]").hasAttribute("aria-describedby")', false)
            ->assertScript('document.getElementById("form.delivery-helper-text")', null);
        $page->select('[data-testid="delivery"]', '')
            ->assertAttribute('[data-testid="delivery"]', 'aria-describedby', 'form.delivery-helper-text');
        expect($page->script('document.getElementById("form.delivery-helper-text").textContent.trim()'))->toBe('Choose how to receive your card.');

        $page->click('[data-testid="save"]')
            ->assertAttribute('[data-testid="name"]', 'aria-invalid', 'true')
            ->assertEnabled('[data-testid="save"]');
        $errors = $page->script(<<<'JS'
        (() => {
            const form = document.querySelector('[data-testid="membership-form"]');
            const controls = [...form.querySelectorAll('[aria-invalid="true"]')];
            return {
                count: controls.length,
                resolved: controls.every(control => control.getAttribute('aria-describedby').split(' ').every(id => document.querySelectorAll(`[id="${id}"]`).length === 1)),
                liveErrors: form.querySelectorAll('[data-validation-error][aria-live], [data-validation-error][role="alert"]').length,
                requiredCheckboxes: form.querySelectorAll('[role="group"] input[required], [role="group"][aria-required]').length,
                readonlyRequired: form.querySelector('[data-testid="reference"]').required,
                disabledRequired: form.querySelector('[data-testid="archived"]').required,
                repeatedIds: [...form.querySelectorAll('[data-testid^="guest-"]')].map(control => control.id),
            };
        })()
        JS);
        expect($errors['count'])->toBeGreaterThan(5)
            ->and($errors['resolved'])->toBeTrue()
            ->and($errors['liveErrors'])->toBe(0)
            ->and($errors['requiredCheckboxes'])->toBe(0)
            ->and($errors['readonlyRequired'])->toBeFalse()
            ->and($errors['disabledRequired'])->toBeFalse()
            ->and($errors['repeatedIds'])->toBe(['form.guests.first.name', 'form.guests.second.name']);
        foreach (['[data-testid="name"]', '[data-testid="delivery"]', '[data-testid="consent"]', '[data-testid="contact"] input[value="email"]', '[data-testid="interests"] input[value="walking"]', '[data-testid="days"] input[value="weekday"]', '[data-testid="guest-first"]', '[data-testid="guest-second"]'] as $selector) {
            $page->assertAttribute($selector, 'aria-invalid', 'true');
            expect($page->script('(() => { const control = document.querySelector(' . json_encode($selector) . '); return control.getAttribute("aria-describedby").split(" ").some(id => document.getElementById(id)?.matches("[data-validation-error]")); })()'))->toBeTrue();
        }
        $page->assertAttribute('[data-testid="notes"]', 'aria-describedby', 'privacy-note')
            ->assertAttribute('[data-testid="name"]', 'aria-describedby', 'form.name-helper-text form.name-error')
            ->assertNoAccessibilityIssues();

        $page->fill('[data-testid="name"]', 'Alex Morgan')
            ->fill('[data-testid="guest-first"]', 'Jamie Morgan')
            ->fill('[data-testid="guest-second"]', 'Taylor Morgan')
            ->select('[data-testid="delivery"]', 'post')
            ->check('[data-testid="consent"]')
            ->check('[data-testid="contact"] input[value="email"]')
            ->check('[data-testid="interests"] input[value="walking"]')
            ->click('[data-testid="days"] label[for$="-weekday"]')
            ->click('[data-testid="save"]')
            ->assertPresent('[data-testid="saved"]')
            ->assertEnabled('[data-testid="save"]');
        expect($page->script('document.querySelectorAll("[data-validation-error], [aria-invalid=true]").length'))->toBe(0)
            ->and($page->script('document.querySelector("[data-testid=delivery]").hasAttribute("aria-describedby")'))->toBeFalse()
            ->and($page->script('document.getElementById("form.delivery-helper-text")'))->toBeNull()
            ->and($page->script('document.getElementById("form.interests-helper-text")'))->toBeNull()
            ->and($page->script('[...document.querySelectorAll("[data-testid^=guest-]")].map(control => control.id)'))->toBe($errors['repeatedIds']);
        $page->assertAttribute('[data-testid="name"]', 'aria-describedby', 'form.name-helper-text')
            ->assertAttribute('[data-testid="interests"]', 'aria-describedby', 'form.interests-required')
            ->assertAttribute('[data-testid="notes"]', 'aria-describedby', 'privacy-note');
        $page->script('Promise.all(document.getAnimations().filter(animation => animation.effect.getTiming().iterations !== Infinity).map(animation => animation.finished.catch(() => {})))');
        $page->assertNoAccessibilityIssues();
    }
});
