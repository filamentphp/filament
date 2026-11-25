<?php

namespace Filament\Tests\Rector\Fixtures\AddInterfaceByTrait;

class DirectTrait implements OtherInterface
{
    use FirstTrait;
}

class NestedTraitUser
{
    use NestedTrait;
}

class InheritedTrait extends ParentWithTrait {}

class MultipleTraits
{
    use FirstTrait;
    use SecondTrait;
}

class AlreadyImplemented implements RequiredInterface
{
    use FirstTrait;
}

class InheritedInterface extends ParentWithInterface
{
    use FirstTrait;
}

class UnrelatedClass {}
-----
<?php

namespace Filament\Tests\Rector\Fixtures\AddInterfaceByTrait;

class DirectTrait implements OtherInterface, \Filament\Tests\Rector\Fixtures\AddInterfaceByTrait\RequiredInterface
{
    use FirstTrait;
}

class NestedTraitUser implements \Filament\Tests\Rector\Fixtures\AddInterfaceByTrait\RequiredInterface
{
    use NestedTrait;
}

class InheritedTrait extends ParentWithTrait implements \Filament\Tests\Rector\Fixtures\AddInterfaceByTrait\RequiredInterface {}

class MultipleTraits implements \Filament\Tests\Rector\Fixtures\AddInterfaceByTrait\RequiredInterface
{
    use FirstTrait;
    use SecondTrait;
}

class AlreadyImplemented implements RequiredInterface
{
    use FirstTrait;
}

class InheritedInterface extends ParentWithInterface
{
    use FirstTrait;
}

class UnrelatedClass {}
