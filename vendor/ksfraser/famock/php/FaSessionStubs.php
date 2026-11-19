<?php

namespace {
    $GLOBALS['__fa_session_stubs_loaded'] = true;

    // Session/Company Functions
    // get_company_pref() / get_company_prefs() / set_company_pref() are defined
    // once, in FaConstantStubs.php, matching FA 2.4.3 admin/db/company_db.inc.
    // The copy that used to live here was unreachable dead code (FAMock.php
    // loads FaConstantStubs.php first), so it was never the behaviour any
    // dependent actually received. Requiring the canonical file keeps a
    // bootstrap that loads ONLY this file working, with FA-faithful semantics.
    require_once __DIR__ . '/FaConstantStubs.php';

    // Mock session for testing
    if (!isset($_SESSION)) {
        $_SESSION = [];
    }
    if (!isset($_SESSION['wa_current_user'])) {
        $_SESSION['wa_current_user'] = new class {
            public $company = 0;
            public $user = 1;
            public $loginname = 'test_user';
        };
    }
}