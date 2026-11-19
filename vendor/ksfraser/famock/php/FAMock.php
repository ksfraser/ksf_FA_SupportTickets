<?php

/**
 * FAMock - FrontAccounting Function Mocks for Testing
 *
 * This file provides mock implementations of FrontAccounting functions
 * that are used in FA modules but not available during unit testing.
 *
 * Usage: require_once 'path/to/FAMock.php';
 */

// Load all stub files.
//
// FaConstantStubs.php is also the single definition site for the company-pref
// stubs (get_company_pref / get_company_prefs / set_company_pref /
// update_company_prefs). Those used to be duplicated across four files behind
// function_exists() guards, so require order rather than intent decided what
// any dependent actually received. They are defined once now, and
// CompanyPrefStubUniquenessTest fails if a second copy ever reappears.
require_once __DIR__ . '/FaConstantStubs.php';
require_once __DIR__ . '/FaDbStubs.php';
require_once __DIR__ . '/FaUIStubs.php';
require_once __DIR__ . '/FaBusinessStubs.php';
require_once __DIR__ . '/FaWriteStubs.php';
require_once __DIR__ . '/FaHookStubs.php';
require_once __DIR__ . '/FaSecurityStubs.php';
require_once __DIR__ . '/FaSessionStubs.php';
require_once __DIR__ . '/FaUpdateOnlyStubs.php';
require_once __DIR__ . '/FaDateStubs.php';
require_once __DIR__ . '/FaHooksBaseStubs.php';
