<?php
declare(strict_types=1);

namespace Ksfraser\FAMock\Tests\Unit;

use PHPUnit\Framework\TestCase;

final class FaUIStubsTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        unset($GLOBALS['test_notifications'], $GLOBALS['test_errors']);
    }

    public function testUiStubsLoadedAndTranslationAndConstantsExist(): void
    {
        $this->assertTrue(($GLOBALS['__fa_ui_stubs_loaded'] ?? false) === true);
        $this->assertTrue(function_exists('_'));
        $this->assertSame('abc', _('abc'));
        $this->assertTrue(defined('TABLESTYLE2'));
        $this->assertSame('tablestyle2', TABLESTYLE2);
    }

    public function testNotificationsAreCollected(): void
    {
        display_notification('ok');
        display_error('bad');

        $this->assertSame(['ok'], $GLOBALS['test_notifications']);
        $this->assertSame(['bad'], $GLOBALS['test_errors']);
    }

    public function testBasicFormOutputFunctionsEchoHtml(): void
    {
        $_SERVER['PHP_SELF'] = '/test.php';

        ob_start();
        start_form(false, null, 'POST');
        hidden('x', 'y');
        end_form();
        $out = ob_get_clean();

        $this->assertStringContainsString('<form', $out);
        $this->assertStringContainsString('action="/test.php"', $out);
        $this->assertStringContainsString('type="hidden"', $out);
        $this->assertStringContainsString('</form>', $out);
    }
}
