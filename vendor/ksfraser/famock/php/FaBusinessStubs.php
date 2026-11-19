<?php

namespace {
    $GLOBALS['__fa_business_stubs_loaded'] = true;

    // Business Logic Functions - Data Retrieval
    if (!function_exists('get_supplier_details_all')) {
        function get_supplier_details_all() {
            // Mock database result for suppliers
            $suppliers = [
                [
                    'supplier_id' => 1,
                    'supp_name' => 'Test Supplier 1',
                    'supp_ref' => 'SUP001',
                    'address' => '123 Test St, Test City',
                    'curr_code' => 'USD',
                    'tax_group_id' => 1,
                    'payment_terms' => 1,
                    'credit_limit' => 10000.00,
                    'inactive' => 0
                ],
                [
                    'supplier_id' => 2,
                    'supp_name' => 'Test Supplier 2',
                    'supp_ref' => 'SUP002',
                    'address' => '456 Mock Ave, Mock City',
                    'curr_code' => 'USD',
                    'tax_group_id' => 1,
                    'payment_terms' => 1,
                    'credit_limit' => 5000.00,
                    'inactive' => 0
                ]
            ];

            // Return iterator that mimics db_query result
            return new \ArrayIterator($suppliers);
        }
    }

    if (!function_exists('get_customer_details_all')) {
        function get_customer_details_all() {
            // Mock database result for customers
            $customers = [
                [
                    'debtor_no' => 1,
                    'name' => 'Test Customer 1',
                    'debtor_ref' => 'CUST001',
                    'address' => '789 Test Blvd, Test City',
                    'curr_code' => 'USD',
                    'tax_group_id' => 1,
                    'payment_terms' => 1,
                    'credit_limit' => 15000.00,
                    'inactive' => 0
                ],
                [
                    'debtor_no' => 2,
                    'name' => 'Test Customer 2',
                    'debtor_ref' => 'CUST002',
                    'address' => '321 Mock Lane, Mock City',
                    'curr_code' => 'USD',
                    'tax_group_id' => 1,
                    'payment_terms' => 1,
                    'credit_limit' => 7500.00,
                    'inactive' => 0
                ]
            ];

            // Return iterator that mimics db_query result
            return new \ArrayIterator($customers);
        }
    }

    if (!function_exists('get_quick_entries')) {
        function get_quick_entries(int $type = 0) {
            // Mock database result for quick entries
            $entries = [
                [
                    'id' => 1,
                    'type' => $type,
                    'description' => 'Test Quick Entry 1',
                    'base_desc' => 'Test Base Description 1',
                    'base_amount' => 100.00                ],
                [
                    'id' => 2,
                    'type' => $type,
                    'description' => 'Test Quick Entry 2',
                    'base_desc' => 'Test Base Description 2',
                    'base_amount' => 200.00
                ]
            ];

            // Return iterator that mimics db_query result
            return new \ArrayIterator($entries);
        }
    }

    // Bank/Transaction Functions
    if (!function_exists('get_gl_trans_from_to')) {
        function get_gl_trans_from_to(string $begin, string $end, string $account): float {
            // Mock - return a balance amount
            return 1000.00;
        }
    }

    if (!function_exists('get_bank_gl_account')) {
        function get_bank_gl_account($account): int {
            // Mock - return a GL account ID
            return 1001;
        }
    }

    if (!function_exists('add_bank_trans')) {
        function add_bank_trans(...$args): bool {
            // Mock - always succeed
            return true;
        }
    }

    if (!function_exists('add_comments')) {
        function add_comments(...$args): bool {
            // Mock - always succeed
            return true;
        }
    }

    if (!function_exists('get_bank_account')) {
        function get_bank_account($id) {
            // Mock bank account data
            $accounts = [
                1 => [
                    'id' => 1,
                    'bank_account_name' => 'Test Bank Account 1',
                    'bank_curr_code' => 'USD',
                    'bank_name' => 'Test Bank',
                    'account_code' => '1001',
                    'inactive' => 0
                ],
                2 => [
                    'id' => 2,
                    'bank_account_name' => 'Test Bank Account 2',
                    'bank_curr_code' => 'CAD',
                    'bank_name' => 'Test Bank 2',
                    'account_code' => '1002',
                    'inactive' => 0
                ]
            ];

            return $accounts[$id] ?? null;
        }
    }

    if (!function_exists('get_gl_trans')) {
        function get_gl_trans($trans_type, $trans_no) {
            // Mock GL transactions - return ArrayIterator to mimic db_query result
            return new ArrayIterator([
                [
                    'account' => '1000',
                    'person_id' => 1,
                    'person_type_id' => 2, // customer
                    'memo' => 'Test transaction'
                ]
            ]);
        }
    }

    if (!function_exists('db_fetch')) {
        function db_fetch($result) {
            // Mock db_fetch - return the current item from ArrayIterator
            if ($result instanceof ArrayIterator) {
                $current = $result->current();
                $result->next();
                return $current;
            }
            return false;
        }
    }

    // Customer / CRM Functions
    if (!function_exists('add_customer')) {
        /**
         * @param string $name
         * @param string $custRef
         * @param string $address
         * @param string $taxId
         * @param string $currCode
         * @param int $dimensionId
         * @param int $dimension2Id
         * @param int $creditStatus
         * @param int $paymentTerms
         * @param float $discount
         * @param float $pymtDiscount
         * @param float $creditLimit
         * @param int $salesType
         * @param string $notes
         * @return void
         */
        function add_customer(
            string $name, string $custRef, string $address, string $taxId,
            string $currCode, int $dimensionId, int $dimension2Id,
            int $creditStatus, int $paymentTerms, float $discount,
            float $pymtDiscount, float $creditLimit, int $salesType, string $notes
        ): void {
            $GLOBALS['__fa_last_insert_id'] = (int)($GLOBALS['__fa_last_insert_id'] ?? 0) + 1;
        }
    }

    if (!function_exists('add_branch')) {
        /**
         * @param int $debtorNo
         * @param string $brName
         * @param string $branchRef
         * @param string $brAddress
         * @param int $salesman
         * @param int $area
         * @param int $taxGroupId
         * @param string $defaultLocation
         * @param string $salesDiscountAct
         * @param string $receivablesAct
         * @param string $promptPaymentAct
         * @param string $brPostAddress
         * @param int $disableTrans
         * @param int $inactive
         * @param string $defaultShipVia
         * @param string $notes
         * @return void
         */
        function add_branch(
            int $debtorNo, string $brName, string $branchRef, string $brAddress,
            int $salesman, int $area, int $taxGroupId, string $defaultLocation,
            string $salesDiscountAct, string $receivablesAct, string $promptPaymentAct,
            string $brPostAddress, int $disableTrans, int $inactive,
            string $defaultShipVia, string $notes
        ): void {
            $GLOBALS['__fa_last_insert_id'] = (int)($GLOBALS['__fa_last_insert_id'] ?? 0) + 1;
        }
    }

    if (!function_exists('update_customer')) {
        function update_customer(
            int $customerId, string $name, string $custRef, string $address,
            string $taxId, string $currCode, int $dimensionId, int $dimension2Id,
            int $creditStatus, int $paymentTerms, float $discount,
            float $pymtDiscount, float $creditLimit, int $salesType, string $notes
        ): void {
            // no-op mock
        }
    }

    if (!function_exists('update_record_status')) {
        function update_record_status(int $id, int $inactive, string $table, string $key): void {
            // no-op mock
        }
    }


    if (!function_exists('get_company_currency')) {
        function get_company_currency(): string {
            return 'CAD';
        }
    }

    // Mock FrontAccounting classes
    if (!class_exists('fa_bank_transfer')) {
        class fa_bank_transfer {
            private $data = [];

            public function set($key, $value) {
                $this->data[$key] = $value;
            }

            public function get($key) {
                return $this->data[$key] ?? null;
            }

            public function write() {
                // Set trans_no for the mock
                $this->data['trans_no'] = 123;
                return 123; // Mock transaction ID
            }

            public function getNextRef() {
                return 'BT001'; // Mock reference number
            }

            public function add_bank_transfer() {
                // Set trans_no and trans_type for the mock
                $this->data['trans_no'] = 123;
                $this->data['trans_type'] = 4; // ST_BANKTRANSFER
                return true; // Mock success
            }
        }
    }

    // ==========================================
    // Payment / Transaction Functions
    // ==========================================
    if (!function_exists('begin_transaction')) {
        function begin_transaction(): void {}
    }

    if (!function_exists('commit_transaction')) {
        function commit_transaction(): void {}
    }

    if (!function_exists('hook_db_prewrite')) {
        function hook_db_prewrite($obj, $trans_type): void {}
    }

    if (!function_exists('hook_db_postwrite')) {
        function hook_db_postwrite($obj, $trans_type): void {}
    }

    if (!function_exists('delete_comments')) {
        function delete_comments(int $type, int $typeNo): bool {
            return true;
        }
    }

    if (!function_exists('void_bank_trans')) {
        function void_bank_trans(int $type, int $transNo, bool $isEditing = true): void {}
    }

    if (!function_exists('get_exchange_rate_from_to')) {
        function get_exchange_rate_from_to(string $from, string $to, string $date): float {
            $rates = [
                'USD_CAD' => 1.30,
                'CAD_USD' => 0.77,
                'USD_EUR' => 0.85,
                'EUR_USD' => 1.18,
                'CAD_EUR' => 0.65,
                'EUR_CAD' => 1.54,
            ];
            $key = "{$from}_{$to}";
            return $rates[$key] ?? 1.0;
        }
    }

    if (!function_exists('get_customer_currency')) {
        function get_customer_currency(int $customerId): string {
            return 'CAD';
        }
    }

    if (!function_exists('write_customer_trans')) {
        function write_customer_trans(
            int $transType, int $transNo, int $customerId, int $branchId,
            string $date_, string $ref, float $amount, float $discount = 0.0
        ): int {
            static $nextId = 100;
            return $nextId++;
        }
    }

    if (!function_exists('add_gl_trans')) {
        function add_gl_trans(
            int $type, int $typeNo, string $tranDate, string $account, float $dimensionId,
            float $dimension2Id, string $memo, float $amount, ?string $personCurrency = null,
            string $personType = '', int $personId = 0
        ): float {
            return $amount;
        }
    }

    if (!function_exists('add_gl_trans_customer')) {
        function add_gl_trans_customer(
            int $type, int $typeNo, string $tranDate, string $account, float $dimensionId,
            float $dimension2Id, float $amount, int $customerId, string $errorMsg = ''
        ): float {
            return $amount;
        }
    }

    /**
     * get_company_pref() / get_company_prefs() are defined once, in
     * FaConstantStubs.php, matching FA 2.4.3 admin/db/company_db.inc.
     *
     * The two versions that used to live here were both wrong in ways that
     * mattered, and because this file loads before FaConstantStubs.php in
     * FAMock.php order the get_company_prefs() one actually won:
     *
     *  - it returned a hard-coded CAD / 1100 / 4200 / 1000.0 array. FA has no
     *    built-in preference defaults - it reads the sys_prefs table - so this
     *    reported preferences that no test had ever set, which made "stored
     *    nothing" and "stored a realistic value" indistinguishable, and hid
     *    typos in preference names.
     *  - get_company_pref() typed its parameter as string and returned '' for
     *    an unknown key, where FA returns null. It was additionally dead code.
     *
     * A bootstrap that loads ONLY this file still gets the canonical set.
     */
    require_once __DIR__ . '/FaConstantStubs.php';

    if (!function_exists('get_branch_accounts')) {
        function get_branch_accounts(int $branchId): array {
            return [
                'receivables_account' => '1100',
                'payment_discount_account' => '4205',
                'sales_account' => '4100',
                'sales_discount_account' => '4200',
            ];
        }
    }


    if (!function_exists('get_customer_habit')) {
        function get_customer_habit(int $customerId): array {
            return [
                'dissallow_invoices' => 0,
                'pymt_discount' => 0.0,
            ];
        }
    }

    if (!function_exists('check_reference')) {
        function check_reference(string $ref, int $type, int $transNo = 0): bool {
            return true;
        }
    }

    if (!function_exists('check_num')) {
        function check_num(string $fieldName, float $minValue = 0): bool {
            return true;
        }
    }

    if (!function_exists('db_has_currency_rates')) {
        function db_has_currency_rates(string $currency, string $date, bool $allowFuture = false): bool {
            return true;
        }
    }

    if (!function_exists('is_date_in_fiscalyear')) {
        function is_date_in_fiscalyear(string $date): bool {
            return true;
        }
    }

    if (!function_exists('new_doc_date')) {
        function new_doc_date(?string $date = null): string {
            return $date ?? date('Y-m-d');
        }
    }

    if (!function_exists('get_gl_account')) {
        function get_gl_account(string $accountCode) {
            return [
                'account_code' => $accountCode,
                'account_name' => 'Test Account',
            ];
        }
    }

    if (!function_exists('get_bank_charge_account')) {
        function get_bank_charge_account(int $bankAccountId): string {
            return '4500';
        }
    }

    if (!function_exists('write_customer_payment')) {
        function write_customer_payment(
            int $transNo, int $customerId, int $branchId, int $bankAccount,
            string $date_, string $ref, float $amount, float $discount = 0.0,
            string $memo = '', float $rate = 0.0, float $charge = 0.0,
            float $bankAmount = 0.0
        ): int {
            static $nextId = 200;
            return $nextId++;
        }
    }

    if (!function_exists('add_bank_transfer')) {
        /**
         * @param int $fromAccount
         * @param int $toAccount
         * @param string $date_
         * @param float $amount
         * @param string $ref
         * @param string $memo_
         * @param float $charge
         * @param float $targetAmount
         * @return int
         */
        function add_bank_transfer(
            int $fromAccount, int $toAccount, string $date_, float $amount,
            string $ref, string $memo_, float $charge = 0.0, float $targetAmount = 0.0
        ): int {
            static $nextId = 300;
            return $nextId++;
        }
    }

    if (!function_exists('update_bank_transfer')) {
        /**
         * @param int $transNo
         * @param int $fromAccount
         * @param int $toAccount
         * @param string $date_
         * @param float $amount
         * @param string $ref
         * @param string $memo_
         * @param float $charge
         * @param float $targetAmount
         * @return int
         */
        function update_bank_transfer(
            int $transNo, int $fromAccount, int $toAccount, string $date_, float $amount,
            string $ref, string $memo_, float $charge = 0.0, float $targetAmount = 0.0
        ): int {
            return $transNo;
        }
    }

    if (!function_exists('get_customer_trans')) {
        function get_customer_trans(int $transNo, int $transType): array {
            return [
                'debtor_no' => 1,
                'DebtorName' => 'Test Customer',
                'branch_code' => 1,
                'bank_act' => 1,
                'reference' => 'PAY-001',
                'tran_date' => date('Y-m-d'),
                'Total' => 100.00,
                'ov_discount' => 0.0,
                'bank_amount' => 100.00,
                'curr_code' => 'CAD',
            ];
        }
    }

    if (!function_exists('get_users')) {
    $GLOBALS['__fa_users'] = array(
        array('id' => 1, 'user_id' => 'admin',   'role_id' => 1, 'inactive' => 0),
        array('id' => 2, 'user_id' => 'kevin',   'role_id' => 2, 'inactive' => 0),
        array('id' => 3, 'user_id' => 'sue',     'role_id' => 2, 'inactive' => 1),
    );

    /**
     * Return the seeded user list as an ArrayIterator (db_query-faithful result).
     *
     * @param bool $all When false, inactive users are excluded.
     * @return \ArrayIterator
     */
    function get_users($all = false)
    {
        global $__fa_users;
        $users = array();

        foreach ($__fa_users as $user) {
            if (!$all && !empty($user['inactive'])) {
                continue;
            }
            $users[] = $user;
        }

        return new \ArrayIterator($users);
    }
}

if (!function_exists('get_user')) {
    /**
     * Return one seeded user row by id or user_id, or null.
     *
     * @param mixed $id User id or user_id
     * @return array|null
     */
    function get_user($id)
    {
        global $__fa_users;

        foreach ($__fa_users as $user) {
            if ((int) $user['id'] === (int) $id || $user['user_id'] === $id) {
                return $user;
            }
        }

        return null;
    }
}

// Supplier lookup used by the payment/refund write paths

    if (!function_exists('get_supplier')) {
        function get_supplier($supplier_id) {
            $suppliers = [
                [
                    'supplier_id' => 1,
                    'supp_name' => 'Test Supplier 1',
                    'supp_ref' => 'SUP001',
                    'payable_account' => '2100',
                    'dimension_id' => 0,
                    'dimension2_id' => 0,
                    'curr_code' => 'USD',
                    'inactive' => 0,
                ], [
                    'supplier_id' => 2,
                    'supp_name' => 'Test Supplier 2',
                    'supp_ref' => 'SUP002',
                    'payable_account' => '2100',
                    'dimension_id' => 0,
                    'dimension2_id' => 0,
                    'curr_code' => 'CAD',
                    'inactive' => 0,
                ], [
                    'supplier_id' => 7,
                    'supp_name' => 'Referenced Supplier',
                    'supp_ref' => 'SUP007',
                    'payable_account' => '2100',
                    'dimension_id' => 0,
                    'dimension2_id' => 0,
                    'curr_code' => 'CAD',
                    'inactive' => 0,
                ],
            ];

            foreach ($suppliers as $supplier) {
                if ($supplier['supplier_id'] == $supplier_id) {
                    return $supplier;
                }
            }

            return null;
        }
    }
}

// Define functions in specific namespaces
namespace KsfBankImport\Services {
    if (!function_exists('KsfBankImport\Services\get_exchange_rate_from_to')) {
        function get_exchange_rate_from_to($from, $to, $date) {
            // Mock exchange rates for testing
            $rates = [
                'USD_CAD' => 1.30,
                'CAD_USD' => 0.77,
                'USD_EUR' => 0.85,
                'EUR_USD' => 1.18,
                'CAD_EUR' => 0.65,
                'EUR_CAD' => 1.54,
            ];

            $key = "{$from}_{$to}";
            return $rates[$key] ?? 1.0;
        }
    }
}