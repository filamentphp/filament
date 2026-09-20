<?php

function handleLocalWarning(int $severity, string $message): bool
{
    return true;
}

it('handles a warning', function () {
    set_error_handler(sprintf('%s%s', 'handleLocal', 'Warning'));

    try {
        expect(trigger_error('Expected warning', E_USER_WARNING))->toBeTrue();
    } finally {
        restore_error_handler();
    }
});
-----
<?php

function handleLocalWarning(int $severity, string $message): bool
{
    return true;
}

it('handles a warning', function () {
    set_error_handler(sprintf('%s%s', 'handleLocal', 'Warning'));

    try {
        expect(trigger_error('Expected warning', E_USER_WARNING))->toBeTrue();
    } finally {
        restore_error_handler();
    }
});
