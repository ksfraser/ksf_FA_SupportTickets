<?php

namespace {
    $GLOBALS['__fa_write_stubs_loaded'] = true;

    // Shared tracking helper so tests can assert *what* the write stubs were
    // asked to do, and can flip individual stubs into throwing mode to drive
    // error paths without swapping the stub implementation itself.
    //
    //   $GLOBALS['__fa_write_log']['update_transactions'];
    //   $GLOBALS['__fa_write_throwable']['get_trans_counterparty'] = 'db down';
    if (!function_exists('__fa_write_log')) {
        function __fa_write_log(string $fn, array $args): void
        {
            if (isset($GLOBALS['__fa_write_throwable'][$fn])) {
                unset($GLOBALS['__fa_write_log'][$fn]);

                throw new \RuntimeException((string) $GLOBALS['__fa_write_throwable'][$fn]);
            }

            $GLOBALS['__fa_write_log'][$fn] = $args;
        }
    }

    // ==========================================
    // Bank Transaction Write Functions
    // ==========================================
    if (!function_exists('write_bank_transaction')) {
        function write_bank_transaction(
            int $type,
            int $order_id,
            int $bank_account_id,
            object $cart,
            string $trans_date,
            $person_type,
            int $person_id,
            float $charge = 0.0,
            string $reference = '',
            string $memo = '',
            bool $return = true,
            $populate = null
        ) {
            __fa_write_log(__FUNCTION__, [$type, $order_id, $bank_account_id, $cart, $trans_date, $person_type, $person_id, $charge, $reference, $memo, $return]);

            // [trans_type, trans_no] like the real FA implementation.
            return [4, 777];
        }
    }

    if (!function_exists('qe_to_cart')) {
        function qe_to_cart(object $cart, int $quick_entry_id, $amount, int $qe_type, string $memo = ''): int
        {
            __fa_write_log(__FUNCTION__, [$quick_entry_id, $amount, $qe_type, $memo]);

            $cart->add_gl_item('1000', 0, 0, (float) $amount, 'QuickEntry', $memo);

            return 1;
        }
    }

    if (!function_exists('get_trans_counterparty')) {
        function get_trans_counterparty(int $trans_no, int $trans_type): array
        {
            __fa_write_log(__FUNCTION__, [$trans_no, $trans_type]);

            // FA's original walks the result set as a list of rows (one
            // counterparty per transaction), so consumers foreach over it.
            return [
                [
                    'person_id' => 10,
                    'person_type' => 'supplier',
                    'person_type_id' => 3,
                ],
            ];
        }
    }

    if (!function_exists('update_transactions')) {
        function update_transactions(
            int $transaction_id,
            $collection_ids,
            $status,
            $trans_no = null,
            $trans_type = null,
            $matched = false,
            $created = false,
            $partner_code = null,
            $partner_id = null
        ): bool {
            __fa_write_log(__FUNCTION__, func_get_args());

            return true;
        }
    }

    if (!function_exists('set_bank_partner_data')) {
        function set_bank_partner_data(...$args): bool
        {
            __fa_write_log(__FUNCTION__, $args);

            return true;
        }
    }

    if (!function_exists('set_partner_data')) {
        function set_partner_data(...$args): bool
        {
            __fa_write_log(__FUNCTION__, $args);

            return true;
        }
    }

    if (!function_exists('update_partner_data')) {
        function update_partner_data(...$args): bool
        {
            __fa_write_log(__FUNCTION__, $args);

            return true;
        }
    }

    // ==========================================
    // Supplier / Customer Payment Write Functions
    // ==========================================
    if (!function_exists('write_supp_payment')) {
        function write_supp_payment(...$args): int
        {
            __fa_write_log(__FUNCTION__, $args);

            return 555;
        }
    }

    if (!function_exists('my_write_customer_payment')) {
        function my_write_customer_payment(...$args): int
        {
            __fa_write_log(__FUNCTION__, $args);

            return 666;
        }
    }

    if (!function_exists('add_cust_allocation')) {
        function add_cust_allocation(...$args): bool
        {
            __fa_write_log(__FUNCTION__, $args);

            return true;
        }
    }

    if (!function_exists('update_debtor_trans_allocation')) {
        function update_debtor_trans_allocation(...$args): bool
        {
            __fa_write_log(__FUNCTION__, $args);

            return true;
        }
    }

    // ==========================================
    // Number Formatting
    // ==========================================
    if (!function_exists('user_numeric')) {
        function user_numeric($value): float
        {
            return (float) str_replace([',', ' '], ['', ''], (string) $value);
        }
    }

    // ==========================================
    // Fiscal year support
    // ==========================================
    if (!function_exists('end_fiscalyear')) {
        function end_fiscalyear(): string
        {
            // Far past any test date, so callers always enter the fiscal year.
            return '2030-12-31';
        }
    }

    // ==========================================
    // FA Value Objects (items cart)
    // ==========================================
    // The write path of FA passes an items_cart value object between
    // transaction builders and write_bank_transaction(). The real class
    // accumulates GL items and reports their total, which quick-entry
    // callers use as their "template actually loaded" check.
    if (!class_exists('items_cart', false)) {
        class items_cart
        {
            public $trans_type;
            public $reference = '';
            public $tran_date = '';
            public $order_id = 0;
            public $original_amount = 0;
            public $gl_items = [];

            public function __construct(int $trans_type = 1)
            {
                $this->trans_type = $trans_type;
            }

            public function add_gl_item(
                string $account,
                $dim1 = 0,
                $dim2 = 0,
                $amount = 0.0,
                $memo = '',
                $desc = ''
            ) {
                $item = [
                    'account' => $account,
                    'dim1' => $dim1,
                    'dim2' => $dim2,
                    'amount' => $amount,
                    'memo' => $memo,
                    'desc' => $desc,
                ];
                $this->gl_items[] = $item;

                return $item;
            }

            public function count_gl_items(): int
            {
                return count($this->gl_items);
            }

            public function gl_items_total(): float
            {
                $total = 0.0;
                foreach ($this->gl_items as $gl) {
                    $total += $gl['amount'];
                }

                return $total;
            }
        }
    }

    // ==========================================
    // Document reference numbering ($Refs)
    // ==========================================
    // ReferenceNumberService (and anything following FA convention) reaches
    // the numbering generator through the global $Refs object.
    if (!class_exists('_Refs', false)) {
        class _Refs
        {
            private $counter = 1000;

            public function get_next(int $trans_type): string
            {
                __fa_write_log('_Refs::get_next', [$trans_type]);

                return sprintf('ST%d-%04d', $trans_type, $this->counter++);
            }
        }

        $GLOBALS['Refs'] = new _Refs();
    }
}
