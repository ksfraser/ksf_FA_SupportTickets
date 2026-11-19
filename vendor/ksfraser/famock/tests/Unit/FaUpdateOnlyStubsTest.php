<?php
declare(strict_types=1);

namespace Ksfraser\FAMock\Tests\Unit;

use PHPUnit\Framework\TestCase;

final class FaUpdateOnlyStubsTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $GLOBALS['__fa_prefs'] = [];
    }

    public function testGetAndUpdateCompanyPrefs(): void
    {
        $this->assertSame([], get_company_prefs());

        update_company_prefs(['a' => 1, 'b' => 'x']);
        $this->assertSame(['a' => 1, 'b' => 'x'], get_company_prefs());

        update_company_prefs(['b' => 'y']);
        $this->assertSame(['a' => 1, 'b' => 'y'], get_company_prefs());
    }
}
