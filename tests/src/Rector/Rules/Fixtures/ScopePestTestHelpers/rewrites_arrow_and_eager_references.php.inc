<?php

$values = [helperValue()];

dataset('values', $values);

it('resolves an arrow function helper', fn () => helperValue())
    ->with('values');

function helperValue(): string
{
    return 'value';
}
-----
<?php

$helperValue = function (): string {
    return 'value';
};

$values = [$helperValue()];

dataset('values', $values);

it('resolves an arrow function helper', fn () => $helperValue())
    ->with('values');
