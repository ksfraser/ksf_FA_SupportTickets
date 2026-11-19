<?php

namespace {
    $GLOBALS['__fa_constant_stubs_loaded'] = true;

    /**
     * FrontAccounting Core Constants
     * 
     * Defines the standard FrontAccounting constants for:
     * - Database table prefixes
     * - Partner/Entity types (customers, suppliers)
     * - Transaction types
     * - Status codes
     * - Reference type codes
     */

    // ==========================================
    // TABLE PREFIX
    // ==========================================
    if (!defined('TB_PREF')) {
        define('TB_PREF', '0_');
    }

    // ==========================================
    // PARTNER TYPES (gl_account dimensioning)
    // ==========================================
    if (!defined('PT_CUSTOMER')) {
        // KSF family contract: matches fa_stubs.php in the consuming modules -
        // the counterparty person_type column stores the full word.
        define('PT_CUSTOMER', 'customer');       // Customer/Debtor person type
    }

    if (!defined('PT_SUPPLIER')) {
        define('PT_SUPPLIER', 'supplier');       // Supplier/Creditor person type
    }

    if (!defined('PT_BANK')) {
        define('PT_BANK', 'B');                  // Type 'B' = Bank Account
    }

    if (!defined('PT_BRANCH')) {
        define('PT_BRANCH', 'BR');               // Type 'BR' = Customer Branch
    }

    if (!defined('PT_QUICKENTRY')) {
        define('PT_QUICKENTRY', 'Q');            // Type 'Q' = Quick Entry
    }

    // ==========================================
    // TRANSACTION TYPES AND STATUS CODES
    // ==========================================
    // ==========================================
    // TRANSACTION TYPES (as defined in FA config.php)
    // ==========================================
    if (!defined('ST_JOURNAL')) {
        define('ST_JOURNAL', 0);                 // 0  = Journal Entry
    }

    if (!defined('ST_BANKPAYMENT')) {
        define('ST_BANKPAYMENT', 1);             // 1  = Bank Payment
    }

    if (!defined('ST_BANKDEPOSIT')) {
        define('ST_BANKDEPOSIT', 2);             // 2  = Bank Deposit
    }

    if (!defined('ST_BANKTRANSFER')) {
        define('ST_BANKTRANSFER', 4);            // 4  = Bank Transfer
    }

    // Sales transactions
    if (!defined('ST_SALESINVOICE')) {
        define('ST_SALESINVOICE', 10);           // 10 = Sales Invoice
    }

    if (!defined('ST_CUSTCREDIT')) {
        define('ST_CUSTCREDIT', 11);             // 11 = Customer Credit Note
    }

    if (!defined('ST_CUSTPAYMENT')) {
        define('ST_CUSTPAYMENT', 12);            // 12 = Customer Payment
    }

    if (!defined('ST_CUSTDELIVERY')) {
        define('ST_CUSTDELIVERY', 13);           // 13 = Customer Delivery
    }

    if (!defined('ST_SALESQUOTE')) {
        define('ST_SALESQUOTE', 14);             // 14 = Sales Quote
    }

    if (!defined('ST_SALESORDER')) {
        define('ST_SALESORDER', 15);             // 15 = Sales Order
    }

    if (!defined('ST_MEMO')) {
        define('ST_MEMO', 16);                   // 16 = Memo (non-financial note)
    }

    if (!defined('ST_INVADJUST')) {
        define('ST_INVADJUST', 17);              // 17 = Inventory Adjustment
    }

    if (!defined('ST_LOCTRANSFER')) {
        define('ST_LOCTRANSFER', 18);            // 18 = Location Transfer
    }

    if (!defined('ST_MANUISSUE')) {
        define('ST_MANUISSUE', 19);              // 19 = Manual Issue
    }

    if (!defined('ST_MANURECEIVE')) {
        define('ST_MANURECEIVE', 20);            // 20 = Manual Receive
    }

    // Purchasing transactions
    if (!defined('ST_SUPPINVOICE')) {
        define('ST_SUPPINVOICE', 20);            // 20 = Supplier Invoice
    }

    if (!defined('ST_SUPPCREDIT')) {
        define('ST_SUPPCREDIT', 26);             // 26 = Supplier Credit Note
    }

    if (!defined('ST_SUPPAYMENT')) {
        define('ST_SUPPAYMENT', 22);             // 22 = Supplier Payment
    }

    if (!defined('ST_SUPPRECEIVE')) {
        define('ST_SUPPRECEIVE', 28);            // 28 = Supplier Receive
    }

    if (!defined('ST_PURCHORDER')) {
        define('ST_PURCHORDER', 29);             // 29 = Purchase Order
    }

    if (!defined('ST_WORKORDER')) {
        define('ST_WORKORDER', 60);              // 60 = Work Order (Manufacturing)
    }

    // ==========================================
    // PAYMENT RECONCILIATION STATUSES
    // ==========================================
    if (!defined('STATUS_UNRECONCILED')) {
        define('STATUS_UNRECONCILED', 0);        // Not reconciled
    }

    if (!defined('STATUS_RECONCILED')) {
        define('STATUS_RECONCILED', 1);          // Reconciled
    }

    if (!defined('STATUS_PARTIAL')) {
        define('STATUS_PARTIAL', 2);             // Partially reconciled
    }

    // ==========================================
    // REFERENCE TYPES
    // ==========================================
    if (!defined('DIMENSION_TYPE_CUSTOMER')) {
        define('DIMENSION_TYPE_CUSTOMER', 1);    // Dimension for customers
    }

    if (!defined('DIMENSION_TYPE_PROJECT')) {
        define('DIMENSION_TYPE_PROJECT', 2);     // Dimension for projects
    }

    if (!defined('DIMENSION_TYPE_DEPARTMENT')) {
        define('DIMENSION_TYPE_DEPARTMENT', 3);  // Dimension for departments
    }

    if (!defined('ANY_NUMERIC')) {
        define('ANY_NUMERIC', -1);                // Generic numeric placeholder
    }

    if (!defined('MENU_IMPORT')) {
        define('MENU_IMPORT', 'menu_import');
    }

    if (!defined('MENU_MAINTENANCE')) {
        define('MENU_MAINTENANCE', 'menu_maintenance');
    }

    if (!defined('MENU_INQUIRY')) {
        define('MENU_INQUIRY', 'menu_inquiry');
    }

    /**
     * ---------------------------------------------------------------------
     * Canonical company-preference stubs. Single definition site.
     * ---------------------------------------------------------------------
     *
     * Why this file: three separate implementations of get_company_pref()
     * (here, FaBusinessStubs.php, FaSessionStubs.php) and two of
     * get_company_prefs() (FaBusinessStubs.php, FaUpdateOnlyStubs.php) once
     * existed, each behind a function_exists() guard. Which one a dependent
     * actually got was therefore decided by the require order inside
     * FAMock.php rather than by intent: this file loads first, so its version
     * silently won and the other three were unreachable dead code that still
     * read as if they were the contract.
     *
     * Why here and not in a new file: dependents do not all go through
     * FAMock.php. Many bootstraps require individual stub files, and this one
     * is already in the set they load (FaDbStubs, FaBusinessStubs,
     * FaConstantStubs, FaDateStubs, ... is the common pattern), so defining
     * here fixes the semantics without asking any dependent to load a file it
     * does not currently know about. FaBusinessStubs.php, FaSessionStubs.php
     * and FaUpdateOnlyStubs.php now require this file instead of carrying their
     * own copies.
     *
     * Semantics are taken from FA 2.4.3 admin/db/company_db.inc:
     *
     *   function get_company_pref($prefs = null)   // returns @$all[$prefs]
     *   function get_company_prefs()                // return get_company_pref(null);
     *
     * Two consequences, both asserted by this package's own suite:
     *
     *  - An UNKNOWN preference returns null, not ''. FA does not coerce, and
     *    silently substituting '' hides typos in preference names.
     *  - get_company_prefs() returns exactly what has been stored. FA reads
     *    these from the sys_prefs table and has NO built-in defaults; the
     *    hard-coded CAD / 1100 / 4200 array that FaBusinessStubs.php returned
     *    was invented here and reported preferences no test had ever set.
     *
     * To seed a preference in a test, call set_company_pref() or
     * update_company_prefs() so the value is visible to the code under test.
     *
     * LEGACY SEEDING STORE (v0.3.1 heritage, re-adopted 2026-11): $_test_company_prefs
     * IS honoured - as a fallback for keys the canonical stores do not hold.
     * Ecosystem helper files ("tests/helpers/*.php") still seed that global,
     * and because famock is loaded by the composer autoloader "files" step its
     * definitions are shadowed when famock loads first, so without this
     * fallback every such seed was silently invisible. Precedence is
     * canonical-first: a value written through set_company_pref() /
     * update_company_prefs() always wins over a stale legacy seed for the
     * same key, so suites that reset one store by name cannot be overridden
     * by leftovers from the other. Resets may target any of the three
     * globals; emptying the one that was read last is what ends the test's
     * seeding influence.
     */

    /**
     * The single backing store, merged from both historical globals.
     *
     * set_company_pref() wrote $GLOBALS['__fa_company_prefs'] while
     * update_company_prefs() wrote $GLOBALS['__fa_prefs'], so a preference set
     * one way was invisible to a read of the other. Both are now honoured on
     * read, so the two paths finally agree.
     *
     * WRITES still go to __fa_company_prefs on purpose. That is the store the
     * previously-winning implementation in this same file used, and dependents
     * reset it by name between tests - ksf_bank_import's BankImportConfig
     * tests do `global $__fa_company_prefs; $__fa_company_prefs = [];` in
     * setUp. Writing to __fa_prefs instead would have silently broken every
     * such reset and leaked preferences across test cases. __fa_prefs is read
     * (and kept for compatibility) but never written, so no existing reset
     * stops working.
     */
    if (!function_exists('__fa_mock_company_prefs')) {
        function __fa_mock_company_prefs(): array {
            $prefs = array();
            if (isset($GLOBALS['__fa_company_prefs']) && is_array($GLOBALS['__fa_company_prefs'])) {
                $prefs = $GLOBALS['__fa_company_prefs'];
            }
            if (isset($GLOBALS['__fa_prefs']) && is_array($GLOBALS['__fa_prefs'])) {
                $prefs = $GLOBALS['__fa_prefs'] + $prefs;
            }
            // Legacy seeding store, lowest precedence ("+" keeps existing keys).
            if (isset($GLOBALS['_test_company_prefs']) && is_array($GLOBALS['_test_company_prefs'])) {
                $prefs += $GLOBALS['_test_company_prefs'];
            }
            return $prefs;
        }
    }

    if (!function_exists('get_company_pref')) {
        /**
         * @param string|array|null $prefs Preference name, list of names, or null for all.
         * @return mixed Stored value, selected subset, whole pref set, or null if not found.
         */
        function get_company_pref($prefs = null) {
            $store = __fa_mock_company_prefs();

            if (is_array($prefs)) {
                $selected = array();
                foreach ($prefs as $name) {
                    $selected[$name] = isset($store[$name]) ? $store[$name] : null;
                }
                return $selected;
            }
            if ($prefs === null) {
                return $store;
            }
            // FA returns @$all[$prefs], i.e. null for an unknown key.
            return isset($store[$prefs]) ? $store[$prefs] : null;
        }
    }

    if (!function_exists('get_company_prefs')) {
        function get_company_prefs(): array {
            return get_company_pref(null);
        }
    }

    if (!function_exists('set_company_pref')) {
        function set_company_pref(string $name, $value): void {
            $GLOBALS['__fa_company_prefs'][$name] = $value;
        }
    }

    if (!function_exists('update_company_prefs')) {
        function update_company_prefs(array $prefs): void {
            foreach ($prefs as $k => $v) {
                $GLOBALS['__fa_company_prefs'][(string)$k] = $v;
            }
        }
    }

    /**
     * DELIBERATE DIVERGENCE: real FA declares
     * set_company_pref($pref, $category, $type, $length, $value). This stub
     * keeps the two-argument form that every module in the ecosystem actually
     * calls. Adopting FA's five-argument signature would break all of them at
     * once, so the mismatch is documented here rather than silently "fixed".
     */
}
