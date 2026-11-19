<?php

declare(strict_types=1);

namespace Ksfraser\FAMock\Tests\Unit;

use PHPUnit\Framework\TestCase;

final class FaDbStubsChartTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $GLOBALS['__fa_table'] = [];
        $GLOBALS['__fa_result_set'] = [];
        $GLOBALS['__fa_result_pos'] = [];
        $GLOBALS['__fa_last_sql'] = '';
        $GLOBALS['__fa_chart_accounts'] = ['1060', '2100'];
    }

    public function testChartMasterCountActsOnSeededChart(): void
    {
        db_query(sprintf(
            "SELECT COUNT(*) as count FROM %schart_master WHERE account_code = %s",
            TB_PREF,
            db_escape('1060')
        ));

        $row = db_fetch('SELECT COUNT(*) as count FROM 0_chart_master WHERE account_code = \'1060\'');
        $this->assertSame(1, (int) $row['count']);
    }

    public function testUnknownChartMasterCodeIsRejected(): void
    {
        $sql = sprintf(
            "SELECT COUNT(*) as count FROM %schart_master WHERE account_code = %s",
            TB_PREF,
            db_escape('9999')
        );
        db_query($sql);

        $row = db_fetch($sql);
        $this->assertSame(0, (int) $row['count']);
    }
}
