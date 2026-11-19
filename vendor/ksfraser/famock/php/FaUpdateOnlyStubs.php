<?php

namespace {
	/**
	 * Backwards-compatible entry point for the company-pref stubs.
	 *
	 * Some dependents (notably ksf_ModulesDAO's FrontAccountingSysPrefsStoreTest)
	 * require this file directly rather than going through FAMock.php. It now
	 * simply pulls in the single canonical implementation so that such a
	 * direct require still yields a complete, FA-faithful set.
	 *
	 * get_company_prefs(), get_company_pref() and set_company_pref() used to be
	 * defined here as well. They now live once, in FaConstantStubs.php,
	 * matching FA 2.4.3 admin/db/company_db.inc. The duplicates here were
	 * unreachable via FAMock.php (FaBusinessStubs.php loads earlier and its
	 * hard-coded defaults won), which meant the version actually serving
	 * dependents was never the one this file's own tests exercised.
	 */

	require_once __DIR__ . '/FaConstantStubs.php';
}