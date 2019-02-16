<?php
namespace DrdPlus\AttackSkeleton;

use DrdPlus\CalculatorSkeleton\CalculatorApplication;
use DrdPlus\CalculatorSkeleton\CalculatorConfiguration;
use DrdPlus\RulesSkeleton\Dirs;

\error_reporting(-1);
if ((!empty($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR'] === '127.0.0.1') || PHP_SAPI === 'cli') {
    \ini_set('display_errors', '1');
} else {
    \ini_set('display_errors', '0');
}

$documentRoot = $documentRoot ?? (PHP_SAPI !== 'cli' ? \rtrim(\dirname($_SERVER['SCRIPT_FILENAME']), '\/') : \getcwd());

/** @noinspection PhpIncludeInspection */
require_once $documentRoot . '/vendor/autoload.php';

$dirs = Dirs::createFromGlobals();
$htmlHelper = HtmlHelper::createFromGlobals($dirs);
if (PHP_SAPI !== 'cli') {
    \DrdPlus\RulesSkeleton\TracyDebugger::enable($htmlHelper->isInProduction());
}

$calculatorConfiguration = CalculatorConfiguration::createFromYml($dirs);
$servicesContainer = new AttackServicesContainer($calculatorConfiguration, $htmlHelper);
$calculatorApplication = new CalculatorApplication($servicesContainer);
$calculatorApplication->run();