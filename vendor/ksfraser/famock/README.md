# FAMock

FrontAccounting function mocks for unit testing PHP modules that depend on FrontAccounting functions.

## Purpose

When writing unit tests for FrontAccounting modules, you often need to mock FA-specific functions that are not available in the test environment. FAMock provides stub implementations of these functions so your tests can run without requiring a full FA installation.

## Installation

### Option 1: Packagist (when published)
```bash
composer require ksfraser/famock --dev
```

### Option 2: GitHub Repository (requires authentication)
Add to your `composer.json`:

```json
{
    "repositories": [
        {
            "type": "vcs",
            "url": "https://github.com/ksfraser/FAMock.git"
        }
    ],
    "require-dev": {
        "ksfraser/famock": "dev-main"
    }
}
```

**Note:** Requires GitHub authentication token for private repositories.

### Option 3: Local Path (recommended for development)
```json
{
    "repositories": [
        {
            "type": "path",
            "url": "../path/to/FAMock-repo"
        }
    ],
    "require-dev": {
        "ksfraser/famock": "*"
    }
}
```

## Usage

In your test bootstrap or test file:

```php
require_once 'vendor/ksfraser/famock/php/FAMock.php';
```

This will load all FA function stubs.

## Available Stubs

### Database Functions (`FaDbStubs.php`)
- `db_query()` - Mock database queries
- `db_escape()` - Mock string escaping
- `db_fetch()` - Mock result fetching

### UI Functions (`FaUIStubs.php`)
- `start_form()`, `end_form()` - Form HTML output
- `start_table()`, `end_table()` - Table HTML output
- `text_row()`, `check_row()` - Form input helpers
- `display_notification()`, `display_error()` - Message display
- `page()`, `end_page()` - Page structure

### Hook System (`FaHookStubs.php`)
- `fa_hooks()` - Mock hook manager
- `$path_to_root` - Mock global path variable

### Security Functions (`FaSecurityStubs.php`)
- `user_check_access()` - Always returns true
- `add_access_extensions()` - No-op

### Session/Company (`FaSessionStubs.php`)
- `get_company_pref()` - Mock company preferences
- `$_SESSION['wa_current_user']` - Mock user session

### Preferences (`FaUpdateOnlyStubs.php`)
- `get_company_prefs()` - Get mock preferences
- `update_company_prefs()` - Update mock preferences

### Transaction Write Functions (`FaWriteStubs.php`)
- `write_bank_transaction()` - Returns `[4, 777]` (ST_BANKTRANSFER, trans #777)
- `qe_to_cart()` - Loads a Quick Entry amount into an `items_cart`
- `write_supp_payment()` - Returns payment id 555
- `my_write_customer_payment()` - Returns payment id 666
- `add_cust_allocation()`, `update_debtor_trans_allocation()` - Allocation pass-throughs
- `update_transactions()`, `set_bank_partner_data()`, `set_partner_data()`, `update_partner_data()` - Module-side state writers
- `get_trans_counterparty()` - Returns a list with one person row
- `get_supplier()` - Supplier lookup with `payable_account` / dimensions
- `user_numeric()`, `end_fiscalyear()` - Format / fiscal helpers
- `items_cart` class - GL item accumulator (`add_gl_item()`, `gl_items_total()`)
- `_Refs` class + global `$Refs` - Document reference numbering (`get_next()`)
- Every write is recorded in `$GLOBALS['__fa_write_log']`; set
  `$GLOBALS['__fa_write_throwable'][$fn] = 'message'` to make an individual
  stub throw so error paths can be driven without replacing the stub.

## Mock Classes

- `Ksfraser\FAMock\MockDatabase` - PHPUnit mock implementing DatabaseInterface

## Contributing

When adding new FA function mocks:

1. Create a new stub file following the SRP pattern (e.g., `FaNewCategoryStubs.php`)
2. Use `if (!function_exists('function_name'))` to check if already defined
3. Add the file to `FAMock.php`
4. Update this README

## Publishing

To publish to Packagist:
1. Push to GitHub: `git push origin main`
2. Go to https://packagist.org/packages/submit
3. Submit the GitHub repository URL: `https://github.com/ksfraser/FAMock`
4. Tag a release: `git tag v0.2.0 && git push --tags`

Once published, users can install with: `composer require ksfraser/famock --dev`

**Current Status:** FAMock is ready for publishing but currently uses local path installation for development.