<?php

namespace Filament\Tests\Fixtures\Pages;

use Filament\Forms\Components\JsField;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\RepeatableEntry\TableColumn;
use Filament\Pages\Page;
use Filament\Schemas\Components\Flex;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Html;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Components\UnorderedList;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Wizard;
use Filament\Schemas\Components\Wizard\Step;
use Filament\Schemas\Schema;
use Filament\Support\Components\Attributes\ExposedLivewireMethod;
use Filament\Support\Facades\FilamentAsset;
use Filament\Support\RawJs;
use Filament\Tests\Fixtures\Schemas\Components\BladeMethodEntry;
use Filament\Tests\Fixtures\Schemas\Components\BladeMethodField;
use Filament\Tests\Fixtures\Schemas\Components\FrameworkField;
use Livewire\Attributes\Locked;

class JsFieldFrameworkTest extends Page
{
    protected string $view = 'pages.js-field-framework-test';

    #[Locked]
    public string $framework = 'react';

    public array $data = [];

    public bool $locked = false;

    public bool $mounted = true;

    public bool $blurIsLive = true;

    #[Locked]
    public bool $hasAlternateLayout = false;

    #[Locked]
    public string $scenario = '';

    public function mount(): void
    {
        $this->framework = request('framework', 'react');
        abort_unless(in_array($this->framework, ['react', 'vue', 'svelte']), 404);
        $this->mounted = ! request()->boolean('unmounted');
        $this->blurIsLive = ! request()->boolean('deferredBlur');
        $this->hasAlternateLayout = request()->boolean('layout');
        $this->scenario = request('scenario', '');
        $this->resetFields();
    }

    public function resetFields(): void
    {
        $value = ['title' => 'Original café', 'enabled' => true, 'tags' => ['email']];
        $this->form->fill([
            'caption' => 'Root "caption" <em>literal</em>',
            'deferred' => $value,
            'live' => $value,
            'blur' => $value,
            'debounce' => $value,
            'unavailable' => $value,
            'items' => [
                ['caption' => 'First caption', 'field' => $value],
                ['caption' => 'Second caption', 'field' => ['title' => 'Second row', 'enabled' => false, 'tags' => ['push']]],
            ],
            ...($this->hasAlternateLayout ? ['list' => $value, 'liberated' => $value] : []),
        ]);
    }

    public function replaceFields(): void
    {
        foreach (['deferred', 'live', 'blur', 'debounce'] as $name) {
            $this->data[$name] = ['title' => '', 'enabled' => false, 'tags' => []];
        }
    }

    public function removeFirst(): void
    {
        unset($this->data['items'][0]);
    }

    public function changeCaption(string $caption): array
    {
        $this->data['caption'] = $caption;

        return ['caption' => $caption, 'framework' => $this->framework];
    }

    public function showValidationError(): void
    {
        if ($this->getErrorBag()->has('data.live')) {
            $this->resetErrorBag();

            return;
        }
        $this->addError('data.live', 'Choose a different title.');
    }

    public function form(Schema $schema): Schema
    {
        if ($this->scenario === 'panels') {
            return $schema->statePath('data')->components([
                Tabs::make('Locations')->tabs([
                    MethodTab::make('First tab')->schema([
                        FrameworkField::make('deferred')->extraAttributes(['data-field' => 'deferred']),
                    ])->extraAttributes(['data-method-tab' => 'first']),
                    MethodTab::make('Second tab')->schema([
                        FrameworkField::make('live')->extraAttributes(['data-field' => 'live']),
                    ])->extraAttributes(['data-method-tab' => 'second']),
                ]),
                Wizard::make([
                    MethodStep::make('First step')->schema([
                        TextInput::make('first_focus')->autofocus()->required(),
                    ])->extraAttributes(['data-method-step' => 'first']),
                    MethodStep::make('Second step')->schema([
                        TextInput::make('second_focus')->autofocus(),
                    ])->extraAttributes(['data-method-step' => 'second']),
                ])->alpineSubmitHandler('$wire.$refresh()'),
            ]);
        }

        $embeddedContent = static fn (): Html => Html::make(<<<'HTML'
            <div data-embedded-scope>
                <span x-text="$get('caption')"></span>
                <button type="button" x-on:click="$set('caption', 'Embedded café', false, true)">Set sibling caption</button>
            </div>
            HTML)->liberatedFromContainerGrid();

        if ($this->hasAlternateLayout) {
            return $schema->statePath('data')->components([
                TextInput::make('caption'),
                UnorderedList::make([
                    Select::make('choice')->searchable()->live()
                        ->getSearchResultsUsing(static fn (string $search): array => ['chosen' => 'Match ' . $search])
                        ->getOptionLabelUsing(static fn (): string => 'Chosen option')
                        ->extraAttributes(['data-list-select' => true]),
                    FrameworkField::make('list')->live()->extraAttributes(['data-field' => 'list']),
                ]),
                FrameworkField::make('liberated')->liberatedFromContainerGrid()->live()->belowContent($embeddedContent())->extraAttributes(['data-field' => 'liberated']),
                RepeatableEntry::make('items')->state(fn (): array => $this->data['items'])->table([TableColumn::make('Field'), TableColumn::make('Caption')])->schema([
                    FrameworkField::make('field')->belowContent($embeddedContent())->extraAttributes(['data-field' => 'table']),
                    BladeMethodEntry::make('caption'),
                ]),
                Html::make('<div class="fi-hidden" data-liberated-hidden>Hidden content</div>')->liberatedFromContainerGrid(),
                Html::make('<div class="fi-grid-col fi-width-md" data-liberated-width>Width-constrained content</div>')->liberatedFromContainerGrid(),
                Group::make()->schema(Schema::make()->inline()->components([
                    Html::make('<div class="fi-growable" data-liberated-grow>Growing content</div>')->liberatedFromContainerGrid(),
                ])),
            ]);
        }

        if ($this->scenario === 'inline-blade') {
            view()->addNamespace('description-test', dirname(__DIR__, 4) . '/packages/forms/resources/views');
        }

        $field = fn (string $name): JsField => JsField::make($name)
            ->renderer(FilamentAsset::getScriptSrc($this->framework, 'tests/js-fields'))
            ->helperText('Choose a title and notification channels.')
            ->inlineLabel(str_starts_with($this->scenario, 'inline-'))
            ->fieldWrapperView($this->scenario === 'inline-blade' ? 'description-test::field-wrapper' : 'filament-forms::field-wrapper')
            ->rendererProps(static fn (Get $get): array => blank($get('caption')) ? [] : ['caption' => $get('caption'), 'nested' => ['quote' => '"', 'html' => '<em>literal</em>']])
            ->disabled(fn (): bool => $this->locked && ($name !== 'blur'))
            ->readOnly(fn (): bool => $this->locked && ($name === 'blur'))
            ->extraAttributes(['data-field' => $name]);

        if (($this->scenario === 'description') || str_starts_with($this->scenario, 'inline-')) {
            return $schema->statePath('data')->components([$field('live')->live()]);
        }

        if ($this->scenario === 'failure') {
            return $schema->statePath('data')->components([
                $field('live')->live(),
                $field('unavailable')->renderer(RawJs::make('async (context) => {
                        const mount = (await import("/js/tests/js-fields/' . $this->framework . '.js")).default
                        const instance = await mount(context)
                        instance.destroy()
                        throw new Error("Fixture mount failure after framework cleanup")
                    }')),
            ]);
        }

        return $schema->statePath('data')->stateBindingModifiers($this->scenario === 'modifiers' ? ['live'] : null)->components([
            TextInput::make('caption'),
            BladeMethodField::make('blade'),
            Flex::make([BladeMethodField::make('flex_blade')]),
            $field('deferred'),
            $field('live')->live()->stateBindingModifiers($this->scenario === 'modifiers' ? [] : null),
            $field('blur')->live(onBlur: true, condition: fn (): bool => $this->blurIsLive)->stateBindingModifiers($this->scenario === 'modifiers' ? ['blur'] : null),
            $field('debounce')->live(debounce: 500)->stateBindingModifiers($this->scenario === 'modifiers' ? ['live', 'debounce', '700ms'] : null),
            Repeater::make('items')->generateUuidUsing(false)->addable(false)->deletable(false)->reorderable(false)->schema([
                TextInput::make('caption'),
                FrameworkField::make('field')->live()->extraAttributes(['data-field' => 'nested']),
            ]),
        ]);
    }
}

class MethodTab extends Tab
{
    #[ExposedLivewireMethod]
    public function scopeLabel(): string
    {
        return 'Tab: ' . $this->getLabel();
    }
}

class MethodStep extends Step
{
    #[ExposedLivewireMethod]
    public function nextStep(int $currentStepIndex): void
    {
        $this->getLivewire()->data['incorrect_step_call'] = $currentStepIndex;
    }

    #[ExposedLivewireMethod]
    public function scopeLabel(): string
    {
        return 'Step: ' . $this->getLabel();
    }
}
