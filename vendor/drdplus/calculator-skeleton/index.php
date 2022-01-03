<?php

namespace DrdPlus\CalculatorSkeleton;

use DrdPlus\RulesSkeleton\Configurations\Dirs;
use DrdPlus\RulesSkeleton\Environment;
use DrdPlus\RulesSkeleton\HtmlHelper;
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

$dirs = $dirs ?? new Dirs($documentRoot);
$configuration = $configuration ?? CalculatorConfiguration::createFromYml($dirs);
$environment = Environment::createFromGlobals();
$htmlHelper = $htmlHelper ?? HtmlHelper::createFromGlobals($dirs, $environment, $configuration);

if (PHP_SAPI !== 'cli') {
    TracyDebugger::enable($environment->isInProduction());
}

$servicesContainer = $servicesContainer ?? new CalculatorServicesContainer($configuration, $environment, $htmlHelper);
$calculatorApplication = $rulesApplication ?? $controller ?? new CalculatorApplication($servicesContainer);

$calculatorApplication->run();
