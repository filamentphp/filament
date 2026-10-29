<?php

namespace Filament\Tests\Upgrade\Fixtures\ExportCsvRuntime;

use ArrayAccess;
use Closure;
use Filament\Actions\ExportAction;
use Filament\Actions\ExportBulkAction;
use Filament\Actions\ImportAction;
use RuntimeException;

function check(bool $value): void
{
    if (! $value) {
        throw new RuntimeException('CSV migration changed behavior');
    }
}

function receiver(ExportAction | ExportBulkAction $action): ExportAction | ExportBulkAction
{
    $GLOBALS['events'][] = 'receiver';

    return $action;
}

function delimiter(): Closure
{
    $GLOBALS['events'][] = 'argument';

    return static function (): string {
        throw new RuntimeException('Delimiter Closure must never be invoked');
    };
}

function label(): string
{
    $GLOBALS['events'][] = 'label';

    return 'Export';
}

class Holder
{
    public function __construct(public ?ExportAction $action) {}

    public function action(): ?ExportAction
    {
        $GLOBALS['events'][] = 'computed';

        return $this->action;
    }
}

/** @property-read ExportAction | null $action */
class MagicHolder
{
    public function __construct(private ?ExportAction $value) {}

    public function __get(string $property): ?ExportAction
    {
        $GLOBALS['events'][] = 'get';

        return $this->value;
    }

    public function __isset(string $property): bool
    {
        $GLOBALS['events'][] = 'isset';

        return false;
    }
}

/** @implements ArrayAccess<string, ExportAction | null> */
class ActionCollection implements ArrayAccess
{
    public function __construct(private ?ExportAction $action) {}

    public function offsetGet(mixed $offset): ?ExportAction
    {
        $GLOBALS['events'][] = 'offsetGet';

        return $this->action;
    }

    public function offsetExists(mixed $offset): bool
    {
        $GLOBALS['events'][] = 'offsetExists';

        return false;
    }

    public function offsetSet(mixed $offset, mixed $value): void {}

    public function offsetUnset(mixed $offset): void {}
}

class CustomAction extends ExportAction
{
    public int $calls = 0;

    public function csvDelimiter(string | Closure | null $delimiter): static
    {
        $this->calls++;

        return parent::csvDelimiter($delimiter)->label('Custom');
    }
}

class InheritedAction extends ExportAction {}

class ChildCustomAction extends CustomAction
{
    public function csvDelimiter(string | Closure | null $delimiter): static
    {
        return parent::csvDelimiter($delimiter)->label('Child custom');
    }
}

trait AuditsCsvConfiguration
{
    public int $calls = 0;

    public function csvDelimiter(string | Closure | null $delimiter): static
    {
        $this->calls++;

        return parent::csvDelimiter($delimiter);
    }
}

class TraitAction extends ExportAction
{
    use AuditsCsvConfiguration;
}

class TraitChildAction extends CustomAction
{
    use AuditsCsvConfiguration;
}

class TraitImportAction extends ImportAction
{
    use AuditsCsvConfiguration;
}

class OtherAction
{
    public function csvDelimiter(string $delimiter): string
    {
        return $delimiter;
    }
}

class NamedCustomAction extends ExportAction
{
    public function csvDelimiter(string | Closure | null $delimiter, ?string &$action = null): static
    {
        $action = 'audited';

        return parent::csvDelimiter($delimiter);
    }
}

function namedArguments(ExportAction $export): void
{
    $audit = null;
    check($export->csvDelimiter(delimiter: ';', action: $audit) === $export);
    check($audit === 'audited');
    $audit = null;
    check($export->csvDelimiter(...['delimiter' => ';', 'action' => &$audit]) === $export);
    check($audit === 'audited');
    $audit = null;
    $callable = $export->csvDelimiter(...);
    check($callable(delimiter: ';', action: $audit) === $export);
    check($audit === 'audited');
}

function fallback(): string
{
    $GLOBALS['events'][] = 'fallback';

    return 'fallback';
}

function lookups(?ExportAction $action, bool $present): void
{
    $GLOBALS['events'] = [];
    check(isset($action?->csvDelimiter(delimiter())->getArguments()['optional']) === $present);
    check($GLOBALS['events'] === ($action ? ['argument'] : []));

    $GLOBALS['events'] = [];
    check(empty($action?->csvDelimiter(delimiter())->getArguments()['optional']) === (! $present));
    check($GLOBALS['events'] === ($action ? ['argument'] : []));

    $GLOBALS['events'] = [];
    check(($action?->csvDelimiter(delimiter())->getArguments()['optional'] ?? fallback()) === ($present ? 'value' : 'fallback'));
    check($GLOBALS['events'] === [...($action ? ['argument'] : []), ...($present ? [] : ['fallback'])]);

    $GLOBALS['events'] = [];
    check(isset($action?->csvDelimiter(delimiter())->getArguments()['optional'], $action?->csvDelimiter(delimiter())->getArguments()['optional']) === $present);
    check($GLOBALS['events'] === ($present ? ['argument', 'argument'] : ($action ? ['argument'] : [])));
}

function receiverLookups(MagicHolder $holder, ActionCollection $actions, bool $present): void
{
    $GLOBALS['events'] = [];
    check(isset($holder->action?->csvDelimiter(delimiter())->getArguments()['optional']) === $present);
    check($GLOBALS['events'] === ($present ? ['get', 'argument'] : ['get']));

    $GLOBALS['events'] = [];
    check(empty($actions['export']?->csvDelimiter(delimiter())->getArguments()['optional']) === (! $present));
    check($GLOBALS['events'] === ($present ? ['offsetGet', 'argument'] : ['offsetGet']));

    $GLOBALS['events'] = [];
    check(($holder->action?->csvDelimiter(delimiter())->getArguments()['missing'] ?? fallback()) === 'fallback');
    check($GLOBALS['events'] === ($present ? ['get', 'argument', 'fallback'] : ['get', 'fallback']));

    /** @var array<string, ExportAction> $missing */
    $missing = [];
    $warnings = [];
    set_error_handler(static function (int $severity, string $message) use (&$warnings): bool {
        $warnings[] = $message;

        return true;
    });

    try {
        check(! isset($missing['export']?->csvDelimiter(delimiter())->getArguments()['optional']));
        check($warnings === ['Undefined array key "export"']);
    } finally {
        restore_error_handler();
    }
}

function run(ExportAction | ExportBulkAction $action): void
{
    $GLOBALS['events'] = [];
    check(receiver($action)->label('Before')->csvDelimiter(delimiter: delimiter())->label(label()) === $action);
    check($GLOBALS['events'] === ['receiver', 'argument', 'label']);
    check($action->getLabel() === 'Export');

    $GLOBALS['events'] = [];
    $value = null;
    check($action->csvDelimiter($value = delimiter()) === $action);
    check($value instanceof Closure);
    check($GLOBALS['events'] === ['argument']);
    check($action->csvDelimiter(...['delimiter' => ';']) === $action);

    $GLOBALS['events'] = [];
    $callable = receiver($action)->csvDelimiter(...);
    check($GLOBALS['events'] === ['receiver']);
    check($callable(delimiter: delimiter()) === $action);
    check($GLOBALS['events'] === ['receiver', 'argument']);
    check($action->csvDelimiter(null)->label('Middle')->csvDelimiter(';') === $action);
}

foreach ([new ExportAction('export'), new ExportBulkAction('export'), new InheritedAction('export'), new CustomAction('export'), new ChildCustomAction('export'), new TraitAction('export'), new TraitChildAction('export')] as $action) {
    run($action);

    if ($action instanceof CustomAction) {
        check($action->calls === ($action instanceof TraitChildAction ? 12 : 6));
        check($action->getLabel() === ($action instanceof ChildCustomAction ? 'Child custom' : 'Custom'));
    }

    if ($action instanceof TraitAction) {
        check($action->calls === 6);
    }
}

function nullable(?Holder $holder, ?ExportAction $action): void
{
    $GLOBALS['events'] = [];
    $value = null;
    $result = $holder?->action()?->csvDelimiter($value = delimiter())->label(label());
    check($result === $action);
    check($GLOBALS['events'] === ($holder ? ($action ? ['computed', 'argument', 'label'] : ['computed']) : []));
    check(($value instanceof Closure) === ($action !== null));

    $GLOBALS['events'] = [];
    $result = $holder?->action?->csvDelimiter(delimiter())->getLabel();
    check($result === ($action ? 'Export' : null));
    check($GLOBALS['events'] === ($action ? ['argument'] : []));
}

$action = new ExportAction('export');
set_error_handler(static function (int $severity, string $message): never {
    throw new RuntimeException($message);
});
lookups(null, false);
lookups($action, false);
lookups($action->arguments(['optional' => 'value']), true);
restore_error_handler();
receiverLookups(new MagicHolder($action), new ActionCollection($action), true);
receiverLookups(new MagicHolder(null), new ActionCollection(null), false);
namedArguments(new NamedCustomAction('export'));
$holder = new Holder($action);
nullable($holder, $action);
nullable(new Holder(null), null);
nullable(null, null);
check($holder->action->csvDelimiter(';') === $action);
$GLOBALS['events'] = [];
check($holder->action()?->csvDelimiter(delimiter())->label(label()) === $action);
check($GLOBALS['events'] === ['computed', 'argument', 'label']);

$holder->action = null;
$GLOBALS['events'] = [];
check($holder->action()?->csvDelimiter(delimiter())->label(label()) === null);
check($GLOBALS['events'] === ['computed']);

$holder = null;
$GLOBALS['events'] = [];
check($holder?->action()?->csvDelimiter(delimiter())->label(label()) === null);
check($GLOBALS['events'] === []);

$GLOBALS['events'] = [];

try {
    receiver($action)->csvDelimiter(throw new RuntimeException('Argument failed'));

    throw new RuntimeException('Argument exception was swallowed');
} catch (RuntimeException $exception) {
    check($exception->getMessage() === 'Argument failed');
    check($GLOBALS['events'] === ['receiver']);
}

$csvDelimiter = 'label';
check($action === $action->{$csvDelimiter}('Dynamic'));
check($action->getLabel() === 'Dynamic');
check((new OtherAction)->csvDelimiter(';') === ';');
check((new ImportAction('import'))->csvDelimiter(';')->getCsvDelimiter() === ';');
check((new TraitImportAction('import'))->csvDelimiter(';')->getCsvDelimiter() === ';');
echo "PASS: evaluation, effects, identity, closures, callables, nullsafe, custom dispatch, import\n";
