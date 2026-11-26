<?php
declare(strict_types=1);

define('ROOT', dirname(__DIR__));

$configFile = ROOT . '/config/config.php';
if (!is_file($configFile)) {
    $configFile = ROOT . '/config/config.example.php';
}
$GLOBALS['config'] = require $configFile;

date_default_timezone_set(config('timezone', 'Africa/Brazzaville'));

require ROOT . '/src/helpers.php';
require ROOT . '/src/db.php';
require ROOT . '/src/security.php';
require ROOT . '/src/auth.php';
require ROOT . '/src/risk.php';

function config(string $key, mixed $default = null): mixed
{
    return $GLOBALS['config'][$key] ?? $default;
}
