<?php

namespace Filament\Tests\Rector\Fixtures\AddInterfaceByTrait;

interface RequiredInterface {}

interface OtherInterface {}

trait FirstTrait {}

trait SecondTrait {}

trait NestedTrait
{
    use FirstTrait;
}

class ParentWithTrait
{
    use FirstTrait;
}

class ParentWithInterface implements RequiredInterface {}
