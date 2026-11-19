<?php

namespace {
    if (!function_exists('begin_month')) {
        function begin_month(string $date): string {
            return date('Y-m-01', strtotime($date));
        }
    }

    if (!function_exists('end_month')) {
        function end_month(string $date): string {
            return date('Y-m-t', strtotime($date));
        }
    }

    if (!function_exists('Today')) {
        function Today(): string {
            return date('Y-m-d');
        }
    }

    if (!function_exists('is_new_reference')) {
        function is_new_reference(string $reference, int $transType): bool {
            // Mock implementation - always return true for testing
            return true;
        }
    }

    if (!function_exists('new_doc_date')) {
        function new_doc_date(): string {
            return date('Y-m-d');
        }
    }

    if (!function_exists('sql2date')) {
        function sql2date(string $date): string {
            return $date;
        }
    }

    if (!function_exists('add_days')) {
        function add_days(string $date, int $days): string {
            $timestamp = strtotime($date);
            if ($timestamp === false) {
                return $date;
            }
            return date('Y-m-d', strtotime("+$days days", $timestamp));
        }
    }
}