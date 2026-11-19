<?php

declare(strict_types=1);

namespace Ksfraser\FAMock\Tests\Unit;

use PHPUnit\Framework\TestCase;

final class FaWriteStubsTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $GLOBALS['__fa_write_log'] = [];
        $GLOBALS['__fa_write_throwable'] = [];
    }

    public function testStubsAreLoaded(): void
    {
        $this->assertNotEmpty($GLOBALS['__fa_write_stubs_loaded']);
    }

    public function testAllWriteSideStubsAreDefined(): void
    {
        $functions = [
            '__fa_write_log',
            'write_bank_transaction',
            'qe_to_cart',
            'get_trans_counterparty',
            'update_transactions',
            'set_bank_partner_data',
            'set_partner_data',
            'update_partner_data',
            'write_supp_payment',
            'my_write_customer_payment',
            'add_cust_allocation',
            'update_debtor_trans_allocation',
            'user_numeric',
            'end_fiscalyear',
        ];

        foreach ($functions as $fn) {
            $this->assertTrue(function_exists($fn), "$fn should be defined by FaWriteStubs");
        }

        $this->assertTrue(defined('PT_QUICKENTRY'));
        $this->assertTrue(class_exists('items_cart', false));
        $this->assertTrue(class_exists('_Refs', false));
        $this->assertTrue(function_exists('get_bank_account'), 'get_bank_account belongs to FaBusinessStubs');
    }
    
    public function testItemsCartAccumulatesAndTotalsGlItems(): void
    {
        $cart = new \items_cart(2);
        $cart->reference = 'DEP-1';
        $cart->tran_date = '2026-10-20';

        $first = $cart->add_gl_item('1000', 0, 0, 150.00, 'deposit', 'QE');
        $cart->add_gl_item('4400', 0, 0, 0.01, 'Trans Ref');
        $cart->add_gl_item('4400', 0, 0, -0.01, 'Trans Ref');

        $this->assertSame(2, $cart->trans_type);
        $this->assertSame('DEP-1', $cart->reference);
        $this->assertCount(3, $cart->gl_items);
        $this->assertSame('1000', $first['account']);
        $this->assertSame(150.0, $cart->gl_items_total());
    }

    public function testWriteBankTransactionLogsAndReturnsTypeAndNumber(): void
    {
        $cart = new \items_cart(1);

        $trans = write_bank_transaction(1, 0, 2, $cart, '2026-10-20', 'Q', 7);

        $this->assertSame([4, 777], $trans);
        $this->assertArrayHasKey('write_bank_transaction', $GLOBALS['__fa_write_log']);

        [$type, $orderId, $bankAccountId] = $GLOBALS['__fa_write_log']['write_bank_transaction'];

        $this->assertSame(1, $type);
        $this->assertSame(0, $orderId);
        $this->assertSame(2, $bankAccountId);
    }

    public function testQeToCartLoadsTemplateIntoCart(): void
    {
        $cart = new \items_cart(1);

        $rval = qe_to_cart($cart, 4, 120.0, 1, 'template memo');

        $this->assertSame(1, $rval);
        $this->assertArrayHasKey('qe_to_cart', $GLOBALS['__fa_write_log']);
        $this->assertSame(120.0, $cart->gl_items_total());
    }

    public function testUpdateTransactionsLogsPassThrough(): void
    {
        $this->assertTrue(
            update_transactions(42, '1', 1, 777, 1, true, false, 'QE', 7)
        );

        $call = $GLOBALS['__fa_write_log']['update_transactions'];

        $this->assertSame([42, '1', 1, 777, 1, true, false, 'QE', 7], $call);
    }

    public function testCounterpartyLookupReturnsListShape(): void
    {
        $rows = get_trans_counterparty(88, 22);

        // FA-faithful: a list of rows so foreach consumers (e.g. matching) work
        $this->assertSame(10, $rows[0]['person_id']);
        $this->assertSame('supplier', $rows[0]['person_type']);
        $this->assertSame(3, $rows[0]['person_type_id']);
    }

    public function testThrowableFlagDrivesErrorPaths(): void
    {
        $GLOBALS['__fa_write_throwable']['get_trans_counterparty'] = 'db down';

        try {
            get_trans_counterparty(88, 22);
            $this->fail('get_trans_counterparty should have thrown');
        } catch (\RuntimeException $e) {
            $this->assertSame('db down', $e->getMessage());
        }

        $this->assertArrayNotHasKey('get_trans_counterparty', $GLOBALS['__fa_write_log']);
    }

    public function testRefsNumberingGeneratorProvidesUniqueReferences(): void
    {
        $refs = $GLOBALS['Refs'];

        $this->assertInstanceOf('_Refs', $refs);

        $next = $refs->get_next(1);

        $this->assertNotSame($next, $refs->get_next(1));
        $this->assertArrayHasKey('_Refs::get_next', $GLOBALS['__fa_write_log']);
    }

    public function testUserNumericStripsThousandSeparators(): void
    {
        $this->assertSame(125.5, user_numeric('125.50'));
        $this->assertSame(1234.56, user_numeric('1,234.56'));
    }

    public function testNumberFormat2UsesUserDisplayPrecision(): void
    {
        $this->assertSame('1,234.56', number_format2(1234.56));
        $this->assertSame('777', number_format2(777, 0));
    }

    public function testSupplierLookupProvidesPayableAccount(): void
    {
        $supplier = get_supplier(7);

        $this->assertSame('Referenced Supplier', $supplier['supp_name']);
        $this->assertSame('2100', $supplier['payable_account']);
        $this->assertNull(get_supplier(9999));
    }
}
