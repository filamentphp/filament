<?php

namespace Filament\Tests\Upgrade;

use Rector\Testing\PHPUnit\AbstractRectorTestCase;

class RenameApiParametersRectorTest extends AbstractRectorTestCase
{
    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();

        if (! defined('PHPUNIT_COMPOSER_INSTALL')) {
            define('PHPUNIT_COMPOSER_INSTALL', dirname(__DIR__, 3) . '/vendor/autoload.php');
        }
    }

    public function test_migrates_named_arguments_and_supported_override_parameters(): void
    {
        $this->doTestFile(__DIR__ . '/Fixtures/api_parameters.php.inc');
    }

    public function test_migrates_trait_hooks_and_promoted_properties_without_creating_duplicate_parameters(): void
    {
        $this->doTestFile(__DIR__ . '/Fixtures/api_parameter_overrides.php.inc');
    }

    public function test_keeps_ambiguous_alias_declarations_and_calls_together(): void
    {
        $fixture = __DIR__ . '/Fixtures/ApiParameters/ambiguous_alias.php.inc';
        $this->doTestFile($fixture);

        require_once $fixture;

        $this->assertSame(['title', 'TITLE', 'title!', 'direct'], Fixtures\AmbiguousParameterAlias\result());
    }

    public function test_preserves_independent_destination_locals_and_colliding_parameters(): void
    {
        $fixture = __DIR__ . '/Fixtures/ApiParameters/collisions.php.inc';
        $this->doTestFile($fixture);

        require_once $fixture;

        $this->assertSame(['original/independent', 'original', 'preview!', 'preview', 'Keep!'], Fixtures\ParameterCollisions\result());
    }

    public function provideConfigFilePath(): string
    {
        return dirname(__DIR__, 3) . '/packages/upgrade/src/rector.php';
    }
}
