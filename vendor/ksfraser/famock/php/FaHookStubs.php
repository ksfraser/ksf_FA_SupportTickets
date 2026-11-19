<?php

namespace {
    $GLOBALS['__fa_hook_stubs_loaded'] = true;

    if (!isset($GLOBALS['__fa_test_filters'])) {
        $GLOBALS['__fa_test_filters'] = [];
    }

    // Hook System Functions
    if (!function_exists('fa_hooks')) {
        function fa_hooks() {
            // Mock hook manager for testing
            if (!isset($GLOBALS['mock_fa_hooks'])) {
                $GLOBALS['mock_fa_hooks'] = new class {
                    public function apply_filters($filter, $value, ...$args) {
                        return $value; // Return unchanged for testing
                    }
                    public function do_action($action, ...$args) {
                        // Do nothing for testing
                    }
                    public function add_filter($filter, $callback, $priority = 10) {
                        // Do nothing for testing
                    }
                    public function add_action($action, $callback, $priority = 10) {
                        // Do nothing for testing
                    }
                };
            }
            return $GLOBALS['mock_fa_hooks'];
        }
    }

    if (!function_exists('add_lapp_function')) {
        function add_lapp_function($app, $menu, $label, $link, $id, $priority) {
            if (!isset($app->menu_items)) {
                $app->menu_items = [];
            }
            $app->menu_items[] = [
                'label' => $label,
                'link' => $link,
                'menu' => $menu,
            ];
        }
    }

    if (!function_exists('add_rapp_function')) {
        function add_rapp_function($app, $menu, $label, $link, $id, $priority) {
            add_lapp_function($app, $menu, $label, $link, $id, $priority);
        }
    }

    // Global Hook System Functions (for direct use in tests)
    if (!function_exists('add_filter')) {
        function add_filter($filterName, $callback, $priority = 10) {
            // Mock FA add_filter - register a test filter
            $key = (string)$filterName;

            if (!isset($GLOBALS['__fa_test_filters'][$key])) {
                $GLOBALS['__fa_test_filters'][$key] = [];
            }

            $GLOBALS['__fa_test_filters'][$key][] = $callback;
        }
    }

    if (!function_exists('apply_filters')) {
        function apply_filters($filterName, $value) {
            // Mock FA apply_filters - in real FA this would call registered filter functions
            // For testing, we'll return the value unchanged unless specific test filters are registered
            $key = (string)$filterName;

            if (isset($GLOBALS['__fa_test_filters'][$key])) {
                foreach ($GLOBALS['__fa_test_filters'][$key] as $callback) {
                    $value = call_user_func($callback, $value);
                }
            }
            
            return $value;
        }
    }

    // Global Variables
    if (!isset($GLOBALS['path_to_root'])) {
        $GLOBALS['path_to_root'] = '/mock/fa/root'; // Mock path for testing
    }
}