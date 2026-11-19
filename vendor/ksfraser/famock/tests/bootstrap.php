<?php

// Optional dependency shim for MockDatabase.
if (!interface_exists('Ksfraser\\Frontaccounting\\GenCat\\DatabaseInterface')) {
    require_once __DIR__ . '/stubs/frontaccounting-database-interface.php';
}

$autoload = __DIR__ . '/../vendor/autoload.php';
if (file_exists($autoload)) {
    require_once $autoload;
} else {
    // Ensure global functions/constants stubs are available without composer install.
    require_once __DIR__ . '/../php/FAMock.php';

    $repoDir = dirname(__DIR__);
    spl_autoload_register(static function (string $class) use ($repoDir): void {
        $prefix = 'Ksfraser\\FAMock\\';
        if (strncmp($class, $prefix, strlen($prefix)) !== 0) {
            return;
        }
        $relative = substr($class, strlen($prefix));
        $file = $repoDir . '/php/' . str_replace('\\', '/', $relative) . '.php';
        if (file_exists($file)) {
            require_once $file;
        }
    });
}

// Ensure global functions/constants stubs are loaded via composer "files" autoload when available.
