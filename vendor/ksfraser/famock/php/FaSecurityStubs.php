<?php

namespace {
    $GLOBALS['__fa_security_stubs_loaded'] = true;

    // Security Functions
    if (!function_exists('user_check_access')) {
        function user_check_access($access) {
            // Mock - always return true for testing
            return true;
        }
    }

    if (!function_exists('add_access_extensions')) {
        function add_access_extensions() {
            // Mock - do nothing in tests
        }
    }

    if (!function_exists('has_access')) {
        function has_access($access, string $securityArea): bool {
            // Mock - always return true for testing
            return true;
        }
    }

    if (!function_exists('get_user')) {
        function get_user(): int {
            // Mock - return dummy user ID for testing
            return 1;
        }
    }
}