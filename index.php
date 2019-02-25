<?php
namespace DrdPlus\Fight;

use DrdPlus\AttackSkeleton\HtmlHelper;
use DrdPlus\CalculatorSkeleton\CalculatorConfiguration;
use DrdPlus\FightCalculator\FightApplication;
use DrdPlus\FightCalculator\FightServicesContainer;
use DrdPlus\RulesSkeleton\Dirs;

\error_reporting(-1);
if ((!empty($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR'] === '127.0.0.1') || PHP_SAPI === 'cli') {
    \ini_set('display_errors', '1');
} else {
    \ini_set('display_errors', '0');
}

require_once __DIR__ . '/vendor/autoload.php';

$dirs = Dirs::createFromGlobals();
$htmlHelper = HtmlHelper::createFromGlobals($dirs);
if (PHP_SAPI !== 'cli') {
    \DrdPlus\RulesSkeleton\TracyDebugger::enable($htmlHelper->isInProduction());
}

$calculatorConfiguration = CalculatorConfiguration::createFromYml($dirs);
$fightServicesContainer = new FightServicesContainer($calculatorConfiguration, $htmlHelper);
$fightApplication = new FightApplication($fightServicesContainer);
$fightApplication->run();