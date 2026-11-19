<?php
declare(strict_types=1);

namespace Ksfraser\FAMock\Tests\Unit;

use PHPUnit\Framework\TestCase;

final class FaDbStubsTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $GLOBALS['__fa_table'] = [];
        $GLOBALS['__fa_result_set'] = [];
        $GLOBALS['__fa_result_pos'] = [];
        unset($GLOBALS['__fa_last_sql'], $GLOBALS['__fa_last_update_matched']);
    }

    public function testStubsLoadedAndTbPrefDefined(): void
    {
        $this->assertTrue(($GLOBALS['__fa_db_stubs_loaded'] ?? false) === true);
        $this->assertTrue(defined('TB_PREF'));
        $this->assertSame('0_', TB_PREF);
    }

    public function testDbEscapeAddsSlashesAndWrapsInQuotes(): void
    {
        // FA's real db_escape() returns the value already quoted, so a module
        // can call it bare: "WHERE name = " . db_escape($name).
        $this->assertSame("'O\\'Reilly'", db_escape("O'Reilly"));
        $this->assertSame("'plain'", db_escape('plain'));
    }

    public function testDbQueryInsertAndSelectCursorBehavior(): void
    {
        $sql = "INSERT INTO 0_prefs (pref_name, pref_value) VALUES ('a','b')";
        $res = db_query($sql);
        $this->assertSame($sql, $res);
        $this->assertSame($sql, $GLOBALS['__fa_last_sql']);

        $sel = "SELECT pref_name, pref_value FROM 0_prefs";
        $r1 = db_fetch(db_query($sel));
        $this->assertIsArray($r1);
        $this->assertSame('a', $r1['pref_name']);
        $this->assertSame('b', $r1['pref_value']);

        $r2 = db_fetch($sel);
        $this->assertFalse($r2);

        // Repeat should reset cursor and return row again.
        $r3 = db_fetch(db_query($sel));
        $this->assertIsArray($r3);
        $this->assertSame('a', $r3['pref_name']);
    }

    public function testDbQueryUpdateSetsMatchedFlagAndUpdatesRow(): void
    {
        db_query("INSERT INTO 0_prefs (pref_name, pref_value) VALUES ('a','b')");

        $upd = "UPDATE 0_prefs SET pref_value = 'c' WHERE pref_name = 'a'";
        db_query($upd);

        $this->assertTrue($GLOBALS['__fa_last_update_matched'] ?? false);

        $row = db_fetch("SELECT pref_value FROM 0_prefs WHERE pref_name = 'a'");
        $this->assertIsArray($row);
        $this->assertSame('c', $row['pref_value']);
    }

    public function testDbQueryDeleteRemovesRow(): void
    {
        db_query("INSERT INTO 0_prefs (pref_name, pref_value) VALUES ('a','b')");
        db_query("DELETE FROM 0_prefs WHERE pref_name = 'a'");

        $row = db_fetch("SELECT pref_name, pref_value FROM 0_prefs WHERE pref_name = 'a'");
        $this->assertFalse($row);
    }

    public function testDbFetchLikeAndSelect1Forms(): void
    {
        db_query("INSERT INTO 0_prefs (pref_name, pref_value) VALUES ('abc_x','1')");
        db_query("INSERT INTO 0_prefs (pref_name, pref_value) VALUES ('abc_y','2')");

        $r1 = db_fetch("SELECT 1 FROM 0_prefs WHERE pref_name LIKE 'abc_%'");
        $this->assertSame(['1' => 1], $r1);
        $r2 = db_fetch("SELECT 1 FROM 0_prefs WHERE pref_name LIKE 'abc_%'");
        $this->assertSame(['1' => 1], $r2);
        $r3 = db_fetch("SELECT 1 FROM 0_prefs WHERE pref_name LIKE 'abc_%'");
        $this->assertFalse($r3);
    }

    public function testDbFetchAssocAliasesDbFetch(): void
    {
        db_query("INSERT INTO 0_prefs (pref_name, pref_value) VALUES ('a','b')");
        $row = db_fetch_assoc("SELECT pref_name, pref_value FROM 0_prefs");
        $this->assertSame('a', $row['pref_name']);
    }
}
