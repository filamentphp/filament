<?php

namespace Filament\Tests\Upgrade\Fixtures\PlainSchemaAliases;

use ArgumentCountError;
use Closure;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Infolists\Concerns\InteractsWithInfolists;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Schemas\Schema;
use Illuminate\Container\Container;
use Livewire\Component;
use ReflectionFunction;
use RuntimeException;
use TypeError;

function check(bool $condition): void
{
    if (! $condition) {
        throw new RuntimeException('Schema alias migration changed behavior');
    }
}

function effect(string $value): string
{
    $GLOBALS['schemaEvents'][] = $value;

    return $value;
}

trait SharedCalls
{
    public function sharedReceiver(): static
    {
        return $this;
    }

    public function fromTrait(): mixed
    {
        return $this->getForm(name: 'form');
    }

    public function fromStoredTrait(): array
    {
        $component = $this;

        return [$component->getForm(name: 'form'), $component->sharedReceiver()->getForm('form'), ($component->getForm(...))('form')];
    }
}

class Owner extends Component implements HasSchemas
{
    use InteractsWithForms;
    use InteractsWithInfolists;
    use RuntimeAliases;
    use SharedCalls;

    public function fluent(): static
    {
        return $this;
    }

    public function exerciseCache(Schema $schema): void
    {
        check($this->cacheForm(form: $schema, name: 'first') === $schema);
        check($this->hasCachedForm(name: 'first'));
        check($this->cacheForm(...['name' => 'first', 'form' => null]) === null);
        check(! $this->hasCachedForm('first'));
        $factory = function () use ($schema): Schema {
            effect('factory');

            return $schema;
        };
        check($this->cacheForm(...['first', $factory, effect('extra')]) === $schema);
        check($this->cacheInfolist(schema: $schema, name: 'info') === $schema);
        check($this->hasCachedInfolist('info'));
        $cache = $this->cacheForm(...);
        check((new ReflectionFunction($cache))->getNumberOfRequiredParameters() === 2);
        check($cache(form: null, name: 'first') === null);

        try {
            $cache('first');

            throw new RuntimeException('Missing argument accepted');
        } catch (ArgumentCountError) {
        }

        try {
            $this->cacheForm('first');

            throw new RuntimeException('Missing argument accepted');
        } catch (ArgumentCountError) {
        }

        try {
            $this->cacheInfolist('info', null);

            throw new RuntimeException('Null infolist accepted');
        } catch (TypeError) {
        }

        $this->oldSchemaState = ['nested' => ['value' => 'old']];
        check($this->getOldFormState(statePath: 'nested.value') === 'old');
        check($this->getActiveFormsLocale() === null);
        check(! $this->isCachingForms());
        check($this->getCachedForms() === $this->getCachedSchemas());
        check($this->getCachedInfolists() === $this->getCachedSchemas());
    }
}

class InheritedOwner extends Owner {}

class CustomOwner extends Owner
{
    public function getForm(string $customName): ?Schema
    {
        effect($customName);

        return null;
    }
}

class CanonicalOwner extends Owner
{
    public function getSchema(string $name, bool $extra = false): ?Schema
    {
        check(! $extra);
        effect($name);

        return null;
    }

    public function getActiveSchemaLocale(bool $extra = false): ?string
    {
        check(! $extra);

        return 'en';
    }

    protected function cacheSchema(string $customName, Schema | Closure | null $customSchema = null): ?Schema
    {
        check(func_num_args() === 2);
        check($customName === 'custom');

        return $customSchema;
    }

    public function customCache(): void
    {
        check($this->cacheForm(name: 'custom', form: null) === null);
        $cache = $this->cacheForm(...);
        check($cache(...['name' => 'custom', 'form' => null]) === null);
    }
}

class ParentCaller extends Owner
{
    public function getForm(string $customName): ?Schema
    {
        return parent::getForm($customName);
    }
}

trait SharedParentCalls
{
    public function fromParent(): mixed
    {
        return parent::getForm('form');
    }
}

class SharedParentOwner extends Owner
{
    use SharedParentCalls;
}

class Unrelated
{
    use SharedCalls;

    public function getForm(string $name): string
    {
        return $name;
    }
}

class UnrelatedChild extends Unrelated
{
    use SharedParentCalls;
}

class Holder
{
    public function __construct(public ?Owner $owner) {}

    public function computed(): ?Owner
    {
        effect('receiver');

        return $this->owner;
    }
}

function normalCalls(Owner $owner): void
{
    $schema = $owner->getSchema('form');
    check($owner->getForm(name: 'form') === $schema);
    check($owner->getInfolist(...['name' => 'form']) === $schema);
    check($owner->fluent()->getForm('form') === $schema);
    check($owner->fromTrait() === $schema);
    check($owner->fromStoredTrait() === [$schema, $schema, $schema]);
    $get = $owner->getForm(...);
    check((new ReflectionFunction($get))->getNumberOfRequiredParameters() === 1);
    check($get(name: 'form') === $schema);
    $infolist = $owner->getInfolist(...);
    check($infolist(name: 'form') === $schema);
    $holder = new Holder($owner);
    check(! isset($holder?->computed()?->getCachedForms()['missing']));
    check(empty($holder?->computed()?->getCachedForms()['missing']));
    check(($holder?->computed()?->getCachedForms()['missing'] ?? 'fallback') === 'fallback');
    $GLOBALS['schemaEvents'] = [];
    check($holder->computed()?->getForm(effect('form'))->key(effect('key')) === $schema);
    check($GLOBALS['schemaEvents'] === ['receiver', 'form', 'key']);
    $GLOBALS['schemaEvents'] = [];
    $holder->owner = null;
    check($holder->computed()?->getForm(effect('skipped'))->key(effect('also skipped')) === null);
    check($GLOBALS['schemaEvents'] === ['receiver']);
    $holder = null;
    check(! isset($holder?->computed()?->getCachedForms()['missing']));
    check(empty($holder?->computed()?->getCachedForms()['missing']));
    check(($holder?->computed()?->getCachedForms()['missing'] ?? 'fallback') === 'fallback');
    $GLOBALS['schemaEvents'] = [];
    check($holder?->computed()?->getForm(effect('skipped'))->key(effect('also skipped')) === null);
    check($GLOBALS['schemaEvents'] === []);
    $holder = new Holder($owner);

    try {
        $holder->computed()->getForm(throw new RuntimeException('argument'));

        throw new RuntimeException('Argument failure swallowed');
    } catch (RuntimeException $exception) {
        check($exception->getMessage() === 'argument');
        check($GLOBALS['schemaEvents'] === ['receiver']);
    }

    $GLOBALS['schemaEvents'] = [];
    $owner->exerciseCache(new Schema($owner));
    check($GLOBALS['schemaEvents'] === ['extra', 'factory']);
}

Container::setInstance(new Container);
normalCalls(new Owner);
normalCalls(new InheritedOwner);
check((new Unrelated)->fromTrait() === 'form');
check((new Unrelated)->fromStoredTrait() === ['form', 'form', 'form']);
check((new UnrelatedChild)->fromParent() === 'form');
check((new SharedParentOwner)->fromParent() instanceof Schema);
$custom = new CustomOwner;
$GLOBALS['schemaEvents'] = [];
check($custom->getForm(customName: 'custom') === null);
$callback = $custom->getForm(...);
check($callback(customName: 'callable') === null);
check($GLOBALS['schemaEvents'] === ['custom', 'callable']);
$canonical = new CanonicalOwner;
$GLOBALS['schemaEvents'] = [];
check($canonical->getForm('canonical', effect('ignored')) === null);
$callback = $canonical->getForm(...);
check($callback('callable', true) === null);
check($GLOBALS['schemaEvents'] === ['ignored', 'canonical', 'callable']);
check($canonical->getActiveFormsLocale(true) === 'en');
$locale = $canonical->getActiveFormsLocale(...);
check($locale(true) === 'en');
$canonical->customCache();
check((new ParentCaller)->getForm('form') instanceof Schema);
echo "PASS: real schemas, caches, explicit null, required arguments, named/unpacked, stored/computed/fluent/nullsafe, callables, inheritance, shared traits, custom dispatch, ignored extras\n";
