<?php

function localValue(): string
{
    return 'value';
}

it('calls the helper', function () {
    expect(localValue())->toBe('value');
    expect(eval('return localValue();'))->toBe('value');
});
-----
<?php

function localValue(): string
{
    return 'value';
}

it('calls the helper', function () {
    expect(localValue())->toBe('value');
    expect(eval('return localValue();'))->toBe('value');
});
