<?php
namespace DrdPlus\AttackSkeleton;

use DrdPlus\CalculatorSkeleton\CalculatorApplication;
use DrdPlus\CalculatorSkeleton\CalculatorConfiguration;
use DrdPlus\RulesSkeleton\Configurations\Dirs;
use DrdPlus\RulesSkeleton\Environment;
use DrdPlus\RulesSkeleton\TracyDebugger;

error_reporting(-1);
if ((!empty($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR'] === '127.0.0.1') || PHP_SAPI === 'cli') {
    ini_set('display_errors', '1');
} else {
    ini_set('display_errors', '0');
}

$documentRoot = $documentRoot ?? (PHP_SAPI !== 'cli' ? rtrim(dirname($_SERVER['SCRIPT_FILENAME']), '\/') : getcwd());

/** @noinspection PhpIncludeInspection */
require_once $documentRoot . '/vendor/autoload.php';

try {
    $environment = $environment ?? Environment::createFromGlobals();
    if (PHP_SAPI !== 'cli') {
        TracyDebugger::enable($environment->isInProduction());
    }

    $dirs = $dirs ?? Dirs::createFromGlobals();
    $configuration = $configuration ?? CalculatorConfiguration::createFromYml($dirs);
    $htmlHelper = $htmlHelper ?? HtmlHelper::createFromGlobals($dirs, $environment);
    $servicesContainer = $servicesContainer ?? new AttackServicesContainer($configuration, $environment, $htmlHelper);
    $calculatorApplication = $rulesApplication ?? $controller ?? new CalculatorApplication($servicesContainer);

    $calculatorApplication->run();
} catch (\Throwable $throwable) {
    if (!empty($_SERVER['SERVER_PROTOCOL'])) {
        header($_SERVER['SERVER_PROTOCOL'] . ' 500 Internal Server Error', true, 500);
    }
    throw $throwable;
}
