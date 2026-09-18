<?php

use function localValue as renamedValue;

function localValue(): string
{
    return 'value';
}

it('calls the helper through an alias', function () {
    expect(renamedValue())->toBe('value');
});
-----
<?php

use function localValue as renamedValue;
$localValue = function (): string {
    return 'value';
};


it('calls the helper through an alias', function () use ($localValue) {
    expect($localValue())->toBe('value');
});
