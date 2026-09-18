<?php

uses(TestCase::class)->beforeEach(function () {
    helperValue();
});

it('uses the helper', function () {
    helperValue();
});

function helperValue(): void
{
}
-----
<?php

$helperValue = function (): void {
};

uses(TestCase::class)->beforeEach(function () use ($helperValue) {
    $helperValue();
});

it('uses the helper', function () use ($helperValue) {
    $helperValue();
});
