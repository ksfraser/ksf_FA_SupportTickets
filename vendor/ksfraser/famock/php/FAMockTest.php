<?php

namespace Ksfraser\FAMock\Test;

use PHPUnit\Framework\TestCase;

/**
 * Test FAMock functionality
 */
class FAMockTest extends TestCase
{
    public function testFaHooksFunctionExists(): void
    {
        $this->assertTrue(function_exists('fa_hooks'));
        $hooks = fa_hooks();
        $this->assertIsObject($hooks);
        $this->assertTrue(method_exists($hooks, 'apply_filters'));
    }

    public function testUserCheckAccessFunctionExists(): void
    {
        $this->assertTrue(function_exists('user_check_access'));
        $result = user_check_access('some_permission');
        $this->assertTrue($result); // Mock always returns true
    }

    public function testPathToRootGlobalExists(): void
    {
        $this->assertTrue(isset($GLOBALS['path_to_root']));
        $this->assertEquals('/mock/fa/root', $GLOBALS['path_to_root']);
    }

    public function testSessionMockExists(): void
    {
        $this->assertTrue(isset($_SESSION));
        $this->assertTrue(isset($_SESSION['wa_current_user']));
        $this->assertEquals(0, $_SESSION['wa_current_user']->company);
        $this->assertEquals(1, $_SESSION['wa_current_user']->user);
    }

    public function testTablePrefixConstant(): void
    {
        $this->assertTrue(defined('TB_PREF'));
        $this->assertEquals('fa_', TB_PREF);
    }
}