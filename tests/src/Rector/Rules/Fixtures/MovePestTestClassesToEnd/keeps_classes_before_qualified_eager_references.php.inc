<?php

namespace Tests\Example;

trait LocalTrait
{
}

class LocalRecord
{
    use LocalTrait;
}

it('has a record', function ($record) {
    expect($record)->toBeObject();
})->with([new \Tests\Example\LocalRecord]);
-----
<?php

namespace Tests\Example;

trait LocalTrait
{
}

class LocalRecord
{
    use LocalTrait;
}

it('has a record', function ($record) {
    expect($record)->toBeObject();
})->with([new \Tests\Example\LocalRecord]);
