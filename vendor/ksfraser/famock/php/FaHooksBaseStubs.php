<?php

namespace {
    $GLOBALS['__fa_hooks_base_stubs_loaded'] = true;

    if (!class_exists('hooks')) {
        class hooks
        {
            public $module_name = '';

            public function install_tables()
            {
                return true;
            }

            public function install_access()
            {
                return true;
            }

    public function activate_extension($company, $check_only = true)
    {
        return true;
    }

    public function deactivate_extension($company)
    {
        return true;
    }
        }
    }
}
