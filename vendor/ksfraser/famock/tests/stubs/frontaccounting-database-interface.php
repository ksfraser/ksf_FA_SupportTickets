<?php
declare(strict_types=1);

namespace Ksfraser\Frontaccounting\GenCat;

if (!interface_exists(DatabaseInterface::class)) {
    interface DatabaseInterface
    {
        public function query($query, $error_message = 'Database query failed');

        public function fetch($result);

        public function getTablePrefix();
    }
}
