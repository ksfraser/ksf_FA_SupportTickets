<?php
declare(strict_types=1);

namespace Ksfraser\FAMock\Tests\Unit;

use PHPUnit\Framework\TestCase;

/**
 * Guards the root cause behind the two Famock suite failures this change fixed.
 *
 * get_company_pref() and get_company_prefs() each had three and two competing
 * implementations respectively, spread across FaConstantStubs.php,
 * FaBusinessStubs.php, FaSessionStubs.php and FaUpdateOnlyStubs.php. Every one
 * sat behind a function_exists() guard, so which behaviour a dependent received
 * was decided by the require order inside FAMock.php rather than by intent -
 * and the copies that lost were unreachable while still reading as if they were
 * the contract.
 *
 * Because the stubs are loaded as global functions, PHP itself cannot detect a
 * duplicate; it silently keeps the first. These tests make the duplication a
 * hard failure instead.
 */
final class CompanyPrefStubUniquenessTest extends TestCase
{
    /** Globals that must exist exactly once across the whole stub set. */
    private const SINGLE_DEFINITION_FUNCTIONS = [
        'get_company_pref',
        'get_company_prefs',
        'set_company_pref',
        'update_company_prefs',
    ];

    /**
     * @return array<string, array<int, string>> function name => declaring files
     */
    private function declarations(): array
    {
        $found = array();
        foreach (glob(__DIR__ . '/../../php/*.php') as $file) {
            $source = (string) file_get_contents($file);
            // Strip comments so a function mentioned in a docblock is not
            // mistaken for a declaration.
            $code = preg_replace('#/\*.*?\*/#s', '', $source);
            $code = preg_replace('#//[^\n]*#', '', (string) $code);
            if (preg_match_all('/\bfunction\s+([a-zA-Z_][a-zA-Z0-9_]*)\s*\(/', (string) $code, $m)) {
                foreach ($m[1] as $name) {
                    $found[$name][] = basename($file);
                }
            }
        }
        return $found;
    }

    /**
     * @dataProvider singleDefinitionProvider
     */
    public function testCompanyPrefFunctionIsDeclaredExactlyOnce(string $function): void
    {
        $found = $this->declarations();
        $files = $found[$function] ?? [];

        $this->assertSame(
            array(),
            array_diff($files, ['FaConstantStubs.php']),
            sprintf(
                '%s() must be declared only in FaConstantStubs.php, but is declared in: %s. '
                . 'Guarded duplicates are resolved by require order, not by intent.',
                $function,
                implode(', ', $files) ?: '(none)'
            )
        );
    }

    /**
     * @return array<string, array{0: string}>
     */
    public function singleDefinitionProvider(): array
    {
        $cases = array();
        foreach (self::SINGLE_DEFINITION_FUNCTIONS as $function) {
            $cases[$function] = [$function];
        }
        return $cases;
    }

    /**
     * The canonical home must be one of the files dependents already load.
     *
     * Many bootstraps require individual stub files rather than going through
     * FAMock.php (FaDbStubs, FaBusinessStubs, FaConstantStubs, FaDateStubs is
     * the common pattern). Moving these stubs into a brand-new file made 44
     * ksf_FA_Square tests fail with "undefined function", because Square loads
     * FaConstantStubs.php and never learns about a new file.
     */
    public function testCanonicalHomeIsLoadedByExistingBootstrapPattern(): void
    {
        $constants = __DIR__ . '/../../php/FaConstantStubs.php';
        $this->assertFileExists($constants);

        require_once $constants;

        foreach (self::SINGLE_DEFINITION_FUNCTIONS as $function) {
            $this->assertTrue(
                function_exists($function),
                $function . '() must be available from FaConstantStubs.php alone'
            );
        }
    }

    /**
     * FA 2.4.3 admin/db/company_db.inc declares get_company_pref($prefs = null)
     * and returns @$all[$prefs] - i.e. null for an unknown key, with no $default
     * parameter and no coercion. Pin that, because it is precisely what the old
     * stubs got wrong.
     */
    public function testUnknownPreferenceReturnsNullLikeFrontAccounting(): void
    {
        require_once __DIR__ . '/../../php/FaConstantStubs.php';

        $GLOBALS['__fa_company_prefs'] = [];
        $GLOBALS['__fa_prefs'] = [];

        $this->assertNull(get_company_pref('definitely_not_a_real_preference'));

        set_company_pref('round_trip_pref', 'value');
        $this->assertSame('value', get_company_pref('round_trip_pref'));
        $this->assertSame('value', get_company_prefs()['round_trip_pref']);

        $GLOBALS['__fa_company_prefs'] = [];
        $GLOBALS['__fa_prefs'] = [];
    }

    /**
     * Dependents reset state between tests by assigning the historical store
     * global directly (ksf_bank_import does exactly this in setUp). Writes must
     * therefore still land in that global, or preferences leak between tests.
     */
    public function testWritesLandInTheGlobalDependentsResetByName(): void
    {
        require_once __DIR__ . '/../../php/FaConstantStubs.php';

        $GLOBALS['__fa_company_prefs'] = [];
        $GLOBALS['__fa_prefs'] = [];

        set_company_pref('leak_check', 'x');
        $this->assertArrayHasKey(
            'leak_check',
            $GLOBALS['__fa_company_prefs'],
            'set_company_pref() must write __fa_company_prefs so dependent setUp() resets keep working'
        );

        // Emulate a dependent's reset and confirm the value is really gone.
        $GLOBALS['__fa_company_prefs'] = [];
        $this->assertNull(get_company_pref('leak_check'));

        $GLOBALS['__fa_company_prefs'] = [];
        $GLOBALS['__fa_prefs'] = [];
        unset($GLOBALS['_test_company_prefs']);
    }

    /**
     * The legacy seeding store ($_test_company_prefs) is honoured as a fallback
     * for keys the canonical stores do not hold. Ecosystem helper files still
     * seed that global, and because famock wins the function_exists race their
     * seeds would otherwise be silently invisible.
     */
    public function testLegacyTestGlobalSeedsAKeyTheCanonicalStoresLack(): void
    {
        require_once __DIR__ . '/../../php/FaConstantStubs.php';

        $GLOBALS['__fa_company_prefs'] = [];
        $GLOBALS['__fa_prefs'] = [];
        $GLOBALS['_test_company_prefs'] = ['legacy_pref' => 'legacy_value'];

        $this->assertSame('legacy_value', get_company_pref('legacy_pref'));
        $this->assertSame('legacy_value', get_company_prefs()['legacy_pref']);

        unset($GLOBALS['_test_company_prefs']);
        $this->assertNull(get_company_pref('legacy_pref'));
    }

    /**
     * A value written through the famock API always wins over a stale legacy
     * seed for the same key, so resets of either store cannot be overridden by
     * leftovers from the other.
     */
    public function testCanonicalWritesWinOverAStaleLegacySeed(): void
    {
        require_once __DIR__ . '/../../php/FaConstantStubs.php';

        $GLOBALS['__fa_company_prefs'] = [];
        $GLOBALS['__fa_prefs'] = [];
        $GLOBALS['_test_company_prefs'] = ['conflict_pref' => 'stale'];

        set_company_pref('conflict_pref', 'fresh');
        $this->assertSame('fresh', get_company_pref('conflict_pref'));

        // The legacy store alone cannot resurrect the key once the canonical
        // stores were emptied by a dependent's reset.
        $GLOBALS['__fa_company_prefs'] = [];
        unset($GLOBALS['_test_company_prefs']);
        $this->assertNull(get_company_pref('conflict_pref'));
    }
}