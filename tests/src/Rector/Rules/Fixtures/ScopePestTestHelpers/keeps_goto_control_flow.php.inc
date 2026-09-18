<?php

function localValue(): string
{
    return 'ok';
}

goto registerTests;

localValue();

registerTests:
it('calls the helper after a jump', function () {
    expect(localValue())->toBe('ok');
});
-----
<?php

function localValue(): string
{
    return 'ok';
}

goto registerTests;

localValue();

registerTests:
it('calls the helper after a jump', function () {
    expect(localValue())->toBe('ok');
});
