<?php

function currentHelperName(): string
{
    return debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 1)[0]['function'];
}

it('preserves the helper frame name', function () {
    expect(currentHelperName())
        ->toBe(sprintf('%s%s', 'currentHelper', 'Name'));
});
-----
<?php

function currentHelperName(): string
{
    return debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 1)[0]['function'];
}

it('preserves the helper frame name', function () {
    expect(currentHelperName())
        ->toBe(sprintf('%s%s', 'currentHelper', 'Name'));
});
