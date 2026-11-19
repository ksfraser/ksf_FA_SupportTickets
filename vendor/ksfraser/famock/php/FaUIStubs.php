<?php

namespace {
    $GLOBALS['__fa_ui_stubs_loaded'] = true;

    // UI Functions
    if (!function_exists('start_form')) {
        function start_form($multi = false, $action = null, $method = 'POST') {
            echo '<form method="' . $method . '" action="' . ($action ?: $_SERVER['PHP_SELF']) . '">';
        }
    }

    if (!function_exists('end_form')) {
        function end_form() {
            echo '</form>';
        }
    }

    if (!function_exists('start_table')) {
        function start_table($class = '', $params = '') {
            // FA-family faithful: fallback to tablestyle2, honor params.
            echo "<table class='" . ($class ?: 'tablestyle2') . "' $params>\n";
        }
    }

    if (!function_exists('end_table')) {
        function end_table($colspan = 1) {
            echo "</table>\n";
        }
    }

    if (!function_exists('table_section_title')) {
        function table_section_title($title) {
            echo '<tr><th colspan="2">' . $title . '</th></tr>';
        }
    }

    if (!function_exists('table_header')) {
        function table_header($headers) {
            echo '<tr>';
            foreach ($headers as $header) {
                echo '<th>' . $header . '</th>';
            }
            echo '</tr>';
        }
    }

    if (!function_exists('start_row')) {
        function start_row($class = '') {
            echo '<tr' . ($class ? ' class="' . $class . '"' : '') . '>';
        }
    }

    if (!function_exists('end_row')) {
        function end_row() {
            echo '</tr>';
        }
    }

    if (!function_exists('label_cell')) {
        function label_cell($value, $class = '', $colspan = 1) {
            echo '<td' . ($class ? ' class="' . $class . '"' : '') .
                 ($colspan > 1 ? ' colspan="' . $colspan . '"' : '') . '>' . $value . '</td>';
        }
    }

    if (!function_exists('text_row')) {
        function text_row($label, $name, $value = '', $size = 20, $max = 64) {
            echo '<tr><td>' . $label . '</td><td><input type="text" name="' . $name .
                 '" value="' . htmlspecialchars($value) . '" size="' . $size . '" maxlength="' . $max . '"></td></tr>';
        }
    }

    if (!function_exists('small_amount_row')) {
        function small_amount_row($label, $name, $value = 0) {
            echo '<tr><td>' . $label . '</td><td><input type="number" step="0.01" name="' . $name .
                 '" value="' . $value . '"></td></tr>';
        }
    }

    if (!function_exists('check_row')) {
        function check_row($label, $name, $checked = false) {
            echo '<tr><td>' . $label . '</td><td><input type="checkbox" name="' . $name .
                 '" value="1"' . ($checked ? ' checked' : '') . '></td></tr>';
        }
    }

    if (!function_exists('hidden')) {
        function hidden($name, $value) {
            echo '<input type="hidden" name="' . $name . '" value="' . htmlspecialchars($value) . '">';
        }
    }

    if (!function_exists('submit_center')) {
        function submit_center($name, $value, $echo_on_click = true) {
            echo '<tr><td colspan="2" align="center"><input type="submit" name="' . $name .
                 '" value="' . $value . '"></td></tr>';
        }
    }

    if (!function_exists('submit')) {
        function submit($name, $value, $echo_on_click = true) {
            $html = '<input type="submit" name="' . $name . '" value="' . $value . '">';
            if ($echo_on_click) {
                echo $html;
                return '';
            }
            return $html;
        }
    }

    if (!function_exists('edit_button_cell')) {
        function edit_button_cell($name, $value) {
            echo '<td><input type="submit" name="' . $name . '" value="' . $value . '"></td>';
        }
    }

    if (!function_exists('delete_button_cell')) {
        function delete_button_cell($name, $value) {
            echo '<td><input type="submit" name="' . $name . '" value="' . $value . '"></td>';
        }
    }

    // Notification Functions
    if (!function_exists('number_format2')) {
        function number_format2($number, $dec = -1, $decensep = '') {
            // Formatted like FA's user-preference display (2 decimals).
            return number_format($number, $dec === -1 ? 2 : $dec, '.', ',');
        }
    }

    if (!function_exists('display_notification')) {
        function display_notification($message) {
            // In tests, we'll collect notifications instead of outputting them
            if (!isset($GLOBALS['test_notifications'])) {
                $GLOBALS['test_notifications'] = [];
            }
            $GLOBALS['test_notifications'][] = $message;
        }
    }

    if (!function_exists('display_error')) {
        function display_error($message) {
            // In tests, we'll collect errors instead of outputting them
            if (!isset($GLOBALS['test_errors'])) {
                $GLOBALS['test_errors'] = [];
            }
            $GLOBALS['test_errors'][] = $message;
        }
    }

    // Translation Function
    if (!function_exists('_')) {
        function _($text) {
            return $text; // Return as-is for testing
        }
    }

    // Constants
    if (!defined('TABLESTYLE2')) {
        define('TABLESTYLE2', 'tablestyle2');
    }

    // Page Functions
    if (!function_exists('page')) {
        function page($title, $no_menu = false, $is_index = false, $onload = "", $js = "") {
            // Mock page start - in tests we don't need full HTML output
            echo '<!DOCTYPE html><html><head><title>' . $title . '</title></head><body>';
            echo '<h1>' . $title . '</h1>';
        }
    }

    if (!function_exists('end_page')) {
        function end_page($no_menu = false, $is_index = false) {
            // Mock page end
            echo '</body></html>';
        }
    }

    // Form/Input Functions
    if (!function_exists('get_post')) {
        function get_post($name, $default = null) {
            return $_POST[$name] ?? $default;
        }
    }

    if (!function_exists('text_input')) {
        function text_input($name, $value = '', $size = 0, $max = '', $title = '') {
            return "<input type='text' name='$name' value='$value' />";
        }
    }

    if (!function_exists('display_warning')) {
        function display_warning($msg) {
            if (!isset($GLOBALS['test_warnings'])) {
                $GLOBALS['test_warnings'] = [];
            }
            $GLOBALS['test_warnings'][] = $msg;
        }
    }

    // List Functions
    if (!function_exists('supplier_list')) {
        function supplier_list($name, $selected_id = null, $spec_option = false, $submit_on_change = false) {
            return "<select name='$name'></select>";
        }
    }

    if (!function_exists('customer_list')) {
        function customer_list($name, $selected_id = null, $spec_option = false, $submit_on_change = false) {
            return "<select name='$name'></select>";
        }
    }
    // Additional UI Functions
    if (!function_exists('list_updated')) {
        function list_updated(string $name): bool {
            return false;
        }
    }

    if (!function_exists('get_branch')) {
        function get_branch($branch_code): array {
            return [];
        }
    }

    if (!function_exists('set_focus')) {
        function set_focus(string $name): void {
            // No-op in mock
        }
    }

    if (!function_exists('supplier_list_row')) {
        function supplier_list_row(string $label, string $name, $selected_id = null, bool $spec_option = false, bool $submit_on_change = false): void {
            // Mock - output basic HTML structure
            echo "<tr><td>$label</td><td><select name='$name'></select></td></tr>";
        }
    }

    if (!function_exists('customer_list_row')) {
        function customer_list_row(string $label, string $name, $selected_id = null, bool $spec_option = false, bool $submit_on_change = false): void {
            // Mock - output basic HTML structure
            echo "<tr><td>$label</td><td><select name='$name'></select></td></tr>";
        }
    }

    if (!function_exists('customer_branches_list_row')) {
        function customer_branches_list_row(string $label, $customer_id, string $name, $selected_id = null, bool $spec_option = false, bool $enabled = true, bool $submit_on_change = false): void {
            // Mock - output basic HTML structure
            echo "<tr><td>$label</td><td><select name='$name'></select></td></tr>";
        }
    }

    if (!function_exists('textarea_row')) {
        function textarea_row(string $label, string $name, string $value = '', int $rows = 5, int $cols = 40, string $title = ''): void {
            // Mock - output basic HTML structure
            echo "<tr><td>$label</td><td><textarea name='$name' rows='$rows' cols='$cols'>$value</textarea></td></tr>";
        }
    }

    if (!function_exists('label_row')) {
        function label_row($label, $value, $params = ''): void {
            // FA-family faithful: render caller params inside the value cell.
            echo "<tr><td class='label'>" . $label . "</td><td " . $params . ">" . $value . "</td></tr>";
        }
    }

    if (!function_exists('submit_cells')) {
        function submit_cells(string $name, string $value, string $params = ''): void {
            // Mock - output basic HTML structure
            echo "<td><input type='submit' name='$name' value='$value'></td>";
        }
    }

    if (!function_exists('submit_center_first')) {
        function submit_center_first(string $name, string $value, string $params = ''): void {
            // Mock - output basic HTML structure
            echo "<tr><td colspan='2' align='center'><input type='submit' name='$name' value='$value'></td></tr>";
        }
    }

    if (!function_exists('search_partner_by_bank_account')) {
        function search_partner_by_bank_account($partnerType, $bankAccount) {
            return [
                'partner_id' => 1,
                'partner_detail_id' => 1,
                'partner_name' => 'Mock Partner',
                'partner_ref' => 'MOCK001',
            ];
        }
    }

}
