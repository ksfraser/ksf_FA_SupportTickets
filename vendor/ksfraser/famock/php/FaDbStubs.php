<?php

namespace {
	$GLOBALS['__fa_db_stubs_loaded'] = true;
	if (!defined('TB_PREF')) {
		define('TB_PREF', '0_');
	}

	$GLOBALS['__fa_table'] = [];
	$GLOBALS['__fa_result_set'] = [];
	$GLOBALS['__fa_result_pos'] = [];

	// FA-faithful: the real db_escape() returns the value ALREADY WRAPPED IN
	// SINGLE QUOTES (includes/db/connect_db_mysqli.inc). Every module in this
	// ecosystem calls it bare -- "WHERE x = " . db_escape($v) -- so a stub that
	// returns only addslashes() yields unquoted, invalid SQL. Keep the quoting.
	if (!function_exists('db_escape')) {
		function db_escape(string $value): string {
			return "'" . addslashes($value) . "'";
		}
	}

	if (!function_exists('db_query')) {
		function db_query(string $sql, $error = null) {
			$GLOBALS['__fa_last_sql'] = $sql;
			$GLOBALS['__fa_last_update_matched'] = false;
			if (stripos($sql, 'INSERT') === 0) {
				if (preg_match("/VALUES\s*\('([^']*)',\s*'([^']*)'\)/", $sql, $m)) {
					$GLOBALS['__fa_table'][] = ['pref_name' => stripslashes($m[1]), 'pref_value' => stripslashes($m[2])];
					$GLOBALS['__fa_last_insert_id'] = count($GLOBALS['__fa_table']);
				}
				$GLOBALS['__fa_result_set'] = [];
				$GLOBALS['__fa_result_pos'] = [];
				return $sql;
			}

			if (stripos($sql, 'UPDATE') === 0) {
				if (preg_match("/SET\s+([a-zA-Z0-9_]+)\s*=\s*'([^']*)'\s+WHERE\s+([a-zA-Z0-9_]+)\s*=\s*'([^']*)'/i", $sql, $m)) {
					$GLOBALS['__fa_last_update_matched'] = true;
					$v = stripslashes($m[2]);
					$k = stripslashes($m[4]);
					foreach ($GLOBALS['__fa_table'] as &$row) {
						if (($row['pref_name'] ?? null) === $k) {
							$row['pref_value'] = $v;
						}
					}
					unset($row);
				}
				$GLOBALS['__fa_result_set'] = [];
				$GLOBALS['__fa_result_pos'] = [];
				return $sql;
			}

			if (stripos($sql, 'DELETE') === 0) {
				if (preg_match("/WHERE\s+[^=]+\s*=\s*'([^']*)'/", $sql, $m)) {
					$k = stripslashes($m[1]);
					$GLOBALS['__fa_table'] = array_values(array_filter(
						$GLOBALS['__fa_table'],
						function($r) use ($k) { return (string)($r['pref_name'] ?? '') !== $k; }
					));
				}
				$GLOBALS['__fa_result_set'] = [];
				$GLOBALS['__fa_result_pos'] = [];
				return $sql;
			}

			// For SELECT and other queries we don't explicitly emulate, reset any cached
			// cursor state so repeated queries behave like fresh result resources.
			if (stripos($sql, 'SELECT') === 0) {
				// Chart of accounts lookup. Modules validate GL account codes
				// with SELECT COUNT(*) FROM {TB_PREF}chart_master WHERE
				// account_code = '<code>'. The test chart is seeded through
				// $GLOBALS['__fa_chart_accounts'] (array of existing codes);
				// default is an empty chart, so every account is rejected
				// unless the fixture seeds it.
				if (preg_match('/chart_master/i', $sql)) {
					$escaped = '';
					if (preg_match('/account_code\s*=\s*\'([^\']*)\'/i', $sql, $m)) {
						$escaped = stripslashes($m[1]);
					}

					$allowed = (array) ($GLOBALS['__fa_chart_accounts'] ?? []);
					$count = in_array($escaped, $allowed, true) ? 1 : 0;

					$GLOBALS['__fa_result_set'][$sql] = [['count' => $count]];
					$GLOBALS['__fa_result_pos'][$sql] = 0;
					return $sql;
				}

				if (stripos($sql, 'FROM test_table') !== false) {
					$GLOBALS['__fa_result_set'][$sql] = [
						['id' => 1, 'name' => 'Test Item'],
						['id' => 2, 'name' => 'Another Item'],
					];
					$GLOBALS['__fa_result_pos'][$sql] = 0;
					return $sql;
				}
				unset($GLOBALS['__fa_result_set'][$sql], $GLOBALS['__fa_result_pos'][$sql]);
			}

			return $sql;
		}
	}

	if (!function_exists('db_insert_id')) {
		function db_insert_id(): int {
			return (int)($GLOBALS['__fa_last_insert_id'] ?? 0);
		}
	}

	if (!function_exists('db_fetch')) {
	function db_fetch($res) {
		// Handle ArrayIterator directly (e.g., from get_customer_details_all())
		if ($res instanceof \ArrayIterator) {
			$current = $res->current();
			$res->next();
			return $current;
		}
		// If it's an array, return the current item and advance
		if (is_array($res) && isset($res['__fa_pos']) && isset($res['__fa_rows'])) {
			$pos = $res['__fa_pos'] ?? 0;
			$rows = $res['__fa_rows'] ?? [];
			if ($pos >= count($rows)) {
				return false;
			}
			$res['__fa_pos'] = $pos + 1;
			return $rows[$pos];
		}
		$sql = (string)$res;
			if (stripos($sql, 'SELECT') !== 0) {
				return false;
			}

			if (!isset($GLOBALS['__fa_result_set'][$sql])) {
				$rows = array_values($GLOBALS['__fa_table']);

				if (preg_match("/WHERE\s+[^=]+\s*=\s*'([^']*)'/", $sql, $m)) {
					$k = stripslashes($m[1]);
					$rows = array_values(array_filter($rows, function($r) use ($k) { return (string)($r['pref_name'] ?? '') === $k; }));
				} elseif (preg_match("/LIKE\s*'([^']*)'/", $sql, $m)) {
					$like = stripslashes($m[1]);
					$prefix = rtrim($like, '%');
					$rows = array_values(array_filter($rows, function($r) use ($prefix) { return strncmp((string)($r['pref_name'] ?? ''), $prefix, strlen($prefix)) === 0; }));
				}

				if (preg_match('/SELECT\s+1\s+FROM/i', $sql)) {
					$rows = array_map(function() { return ['1' => 1]; }, $rows);
				} elseif (preg_match('/SELECT\s+pref_value\s+FROM/i', $sql)) {
					$rows = array_map(function($r) { return ['pref_value' => $r['pref_value'] ?? '']; }, $rows);
				}

				$GLOBALS['__fa_result_set'][$sql] = $rows;
				$GLOBALS['__fa_result_pos'][$sql] = 0;
			}

			$pos = $GLOBALS['__fa_result_pos'][$sql] ?? 0;
			$rows = $GLOBALS['__fa_result_set'][$sql] ?? [];
			if ($pos >= count($rows)) {
				return false;
			}
			$GLOBALS['__fa_result_pos'][$sql] = $pos + 1;
			return $rows[$pos];
		}
	}

	if (!function_exists('db_affected_rows')) {
		function db_affected_rows() {
			// Check for test-specific global first (backwards compatibility)
			global $_test_db_affected_rows;
			if (isset($_test_db_affected_rows)) {
				return (int)$_test_db_affected_rows;
			}
			return $GLOBALS['__fa_last_update_matched'] ? 1 : 0;
		}
	}

	if (!function_exists('db_fetch_assoc')) {
		function db_fetch_assoc($res) {
			// Alias for db_fetch in FA - returns associative array
			return db_fetch($res);
		}
	}

	if (!function_exists('db_num_rows')) {
		function db_num_rows($res): int {
			$sql = (string)$res;
			$rows = $GLOBALS['__fa_result_set'][$sql] ?? [];
			return count($rows);
		}
	}
}
