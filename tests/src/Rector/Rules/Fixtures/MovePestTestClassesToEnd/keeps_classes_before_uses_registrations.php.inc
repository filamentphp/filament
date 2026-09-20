<?php

trait LocalTrait
{
}

class LocalTestCase extends \PHPUnit\Framework\TestCase
{
    use LocalTrait;
}

uses('LocalTestCase');

it('passes', function () {
    expect(true)->toBeTrue();
});
-----
<?php

trait LocalTrait
{
}

class LocalTestCase extends \PHPUnit\Framework\TestCase
{
    use LocalTrait;
}

uses('LocalTestCase');

it('passes', function () {
    expect(true)->toBeTrue();
});
