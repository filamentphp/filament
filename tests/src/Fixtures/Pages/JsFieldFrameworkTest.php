<?php

namespace Filament\Tests\Fixtures\Pages;

use Filament\Forms\Components\JsField;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\RepeatableEntry\TableColumn;
use Filament\Pages\Page;
use Filament\Schemas\Components\Flex;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Components\UnorderedList;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Support\Facades\FilamentAsset;
use Filament\Support\RawJs;
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

    public bool $reversed = false;

    #[Locked]
    public bool $hasAlternateLayout = false;

    #[Locked]
    public string $scenario = '';

    public function mount(): void
    {
        $this->framework = request('framework', 'react');
        abort_unless(in_array($this->framework, request('scenario') === 'generated'
            ? ['js', 'react', 'vue', 'svelte', 'js-ts', 'react-ts', 'vue-ts', 'svelte-ts']
            : ['react', 'vue', 'svelte']), 404);
        $this->mounted = ! request()->boolean('unmounted');
        $this->blurIsLive = ! request()->boolean('deferredBlur');
        $this->hasAlternateLayout = request()->boolean('layout');
        $this->scenario = request('scenario', '');
        $this->resetFields();
    }

    public function resetFields(): void
    {
        if ($this->scenario === 'generated') {
            $this->form->fill(['live' => 'Original café', 'blur' => 'Second field']);

            return;
        }

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
        if ($this->scenario === 'generated') {
            return $schema->statePath('data')->components([
                JsField::make('live')->label('Display name')->live()->required()
                    ->helperText('Enter the name shown on your profile.')
                    ->renderer(FilamentAsset::getScriptSrc('field-' . $this->framework, 'tests/js-field-starters'))
                    ->disabled(fn (): bool => $this->locked),
                JsField::make('blur')->label('Reference')->live(onBlur: true)
                    ->renderer(FilamentAsset::getScriptSrc('field-' . $this->framework, 'tests/js-field-starters'))
                    ->readOnly(fn (): bool => $this->locked),
            ]);
        }

        if ($this->scenario === 'flex') {
            $frameworkFields = [
                FrameworkField::make('deferred')->live()->extraAttributes(['data-field' => 'deferred']),
                FrameworkField::make('live')->live()->extraAttributes(['data-field' => 'live']),
            ];

            return $schema->statePath('data')->components([
                Flex::make($this->reversed ? array_reverse($frameworkFields) : $frameworkFields),
            ]);
        }

        if ($this->scenario === 'tabs') {
            return $schema->statePath('data')->components([
                Tabs::make('Locations')->tabs([
                    Tab::make('First tab')->schema([
                        FrameworkField::make('deferred')->extraAttributes(['data-field' => 'deferred']),
                    ]),
                    Tab::make('Second tab')->schema([
                        FrameworkField::make('live')->extraAttributes(['data-field' => 'live']),
                    ]),
                ]),
            ]);
        }

        if ($this->hasAlternateLayout) {
            return $schema->statePath('data')->components([
                UnorderedList::make([
                    FrameworkField::make('list')->live()->extraAttributes(['data-field' => 'list']),
                ]),
                FrameworkField::make('liberated')->liberatedFromContainerGrid()->live()->extraAttributes(['data-field' => 'liberated']),
                RepeatableEntry::make('items')->state(fn (): array => $this->data['items'])->table([TableColumn::make('Field')])->schema([
                    FrameworkField::make('field')->extraAttributes(['data-field' => 'table']),
                ]),
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
            ->rendererConfiguration(static fn (Get $get): array => blank($get('caption')) ? [] : ['caption' => $get('caption'), 'nested' => ['quote' => '"', 'html' => '<em>literal</em>']])
            ->disabled(fn (): bool => $this->locked && ($name !== 'blur'))
            ->readOnly(fn (): bool => $this->locked && ($name === 'blur'))
            ->extraAttributes(['data-field' => $name]);

        if (($this->scenario === 'description') || str_starts_with($this->scenario, 'inline-')) {
            return $schema->statePath('data')->components([
                $field('live')->live()->afterStateUpdated(function (array $state): void {
                    $this->resetValidation('data.live');

                    if (mb_strlen($state['title']) < 5) {
                        $this->addError('data.live', 'Enter at least five characters.');
                    }
                }),
            ]);
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
