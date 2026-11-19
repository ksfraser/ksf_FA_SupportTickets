<?php
declare(strict_types=1);

namespace Ksfraser\FAMock\Tests\Unit;

use PHPUnit\Framework\TestCase;

final class FaHookStubsTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $GLOBALS['__fa_test_filters'] = [];
        unset($GLOBALS['mock_fa_hooks']);
    }

    public function testHookStubsLoadedAndPathToRootDefined(): void
    {
        $this->assertTrue(($GLOBALS['__fa_hook_stubs_loaded'] ?? false) === true);
        $this->assertSame('/mock/fa/root', $GLOBALS['path_to_root']);
    }

    public function testFaHooksReturnsMockWithPassThroughApplyFilters(): void
    {
        $mgr = fa_hooks();
        $this->assertSame('x', $mgr->apply_filters('anything', 'x'));

        $mgr2 = fa_hooks();
        $this->assertSame($mgr, $mgr2);
    }

    public function testGlobalAddFilterAndApplyFiltersWork(): void
    {
        add_filter('f1', function ($v) { return $v . 'a'; });
        add_filter('f1', function ($v) { return $v . 'b'; });

        $this->assertSame('0ab', apply_filters('f1', '0'));
        $this->assertSame('z', apply_filters('nope', 'z'));
    }
}
