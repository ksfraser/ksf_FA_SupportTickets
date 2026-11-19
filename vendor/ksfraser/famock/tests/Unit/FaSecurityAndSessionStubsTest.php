<?php
declare(strict_types=1);

namespace Ksfraser\FAMock\Tests\Unit;

use PHPUnit\Framework\TestCase;

final class FaSecurityAndSessionStubsTest extends TestCase
{
    public function testSecurityStubsLoadedAndAccessAlwaysTrue(): void
    {
        $this->assertTrue(($GLOBALS['__fa_security_stubs_loaded'] ?? false) === true);
        $this->assertTrue(user_check_access('anything'));
        add_access_extensions();
        $this->assertTrue(true);
    }

    public function testSessionStubsLoadedAndCurrentUserExists(): void
    {
        $this->assertTrue(($GLOBALS['__fa_session_stubs_loaded'] ?? false) === true);

        $this->assertIsArray($_SESSION);
        $this->assertObjectHasProperty('company', $_SESSION['wa_current_user']);
        $this->assertSame(0, $_SESSION['wa_current_user']->company);

        $this->assertNull(get_company_pref('anything'));
    }
}
