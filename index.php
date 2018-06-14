<?php
namespace DrdPlus\FightCalculator;

\error_reporting(-1);
if ((!empty($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR'] === '127.0.0.1') || PHP_SAPI === 'cli') {
    \ini_set('display_errors', '1');
} else {
    \ini_set('display_errors', '0');
}
$documentRoot = $documentRoot ?? (PHP_SAPI !== 'cli' ? \rtrim(\dirname($_SERVER['SCRIPT_FILENAME']), '\/') : \getcwd());
$vendorRoot = $vendorRoot ?? $documentRoot . '/vendor';

/** @noinspection PhpIncludeInspection */
include_once $vendorRoot . '/autoload.php';

\error_reporting(-1);
\ini_set('display_errors', '1');

/** @noinspection PhpUnusedLocalVariableInspection */
$controller = new FightController('https://github.com/jaroslavtyc/drd-plus-fight', $documentRoot, $vendorRoot);

/** @noinspection PhpIncludeInspection */
require $vendorRoot . '/drd-plus/calculator-skeleton/index.php';