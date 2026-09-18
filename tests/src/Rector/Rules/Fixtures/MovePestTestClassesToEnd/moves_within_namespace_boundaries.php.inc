<?php

namespace First {
    class FirstHelper
    {
    }

    it('uses the first helper', function () {
        expect(new FirstHelper)->toBeInstanceOf(FirstHelper::class);
    });
}

namespace Second {
    trait SecondHelper
    {
    }

    it('uses the second helper', function () {
        expect(true)->toBeTrue();
    });
}
-----
<?php

namespace First {
    it('uses the first helper', function () {
        expect(new FirstHelper)->toBeInstanceOf(FirstHelper::class);
    });

    class FirstHelper
    {
    }
}

namespace Second {
    it('uses the second helper', function () {
        expect(true)->toBeTrue();
    });

    trait SecondHelper
    {
    }
}
