<?php

namespace Filament\Tests\Upgrade;

use Rector\Testing\PHPUnit\AbstractRectorTestCase;

class RenameDeprecatedForwardingParentCallsRectorTest extends AbstractRectorTestCase
{
    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();

        if (! defined('PHPUNIT_COMPOSER_INSTALL')) {
            define('PHPUNIT_COMPOSER_INSTALL', dirname(__DIR__, 3) . '/vendor/autoload.php');
        }
    }

    public function test_repairs_parent_calls_without_renaming_custom_aliases_or_their_dispatch(): void
    {
        $this->doTestFile(__DIR__ . '/Fixtures/deprecated_parent_forwarding_methods.php.inc');
    }

    public function test_preserves_custom_parent_methods_and_unsupported_dispatch(): void
    {
        $this->doTestFile(__DIR__ . '/Fixtures/deprecated_parent_forwarding_boundaries.php.inc');
    }

    public function test_repairs_filter_and_static_alignment_parent_calls(): void
    {
        $this->doTestFile(__DIR__ . '/Fixtures/filter_and_alignment_parent_aliases.php.inc');
    }

    public function provideConfigFilePath(): string
    {
        return dirname(__DIR__, 3) . '/packages/upgrade/src/rector.php';
    }
}
