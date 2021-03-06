<?php
namespace DrdPlus\Fight;

use DrdPlus\AttackSkeleton\HtmlHelper;
use DrdPlus\CalculatorSkeleton\CalculatorApplication;
use DrdPlus\CalculatorSkeleton\CalculatorConfiguration;
use DrdPlus\FightCalculator\FightServicesContainer;
use DrdPlus\RulesSkeleton\Dirs;
use DrdPlus\RulesSkeleton\Environment;
use DrdPlus\RulesSkeleton\TracyDebugger;

error_reporting(-1);
if ((!empty($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR'] === '127.0.0.1') || PHP_SAPI === 'cli') {
    ini_set('display_errors', '1');
} else {
    ini_set('display_errors', '0');
}

require_once __DIR__ . '/vendor/autoload.php';

$dirs = Dirs::createFromGlobals();
$htmlHelper = HtmlHelper::createFromGlobals($dirs, Environment::createFromGlobals());
if (PHP_SAPI !== 'cli') {
    TracyDebugger::enable($htmlHelper->isInProduction());
}

$calculatorConfiguration = CalculatorConfiguration::createFromYml($dirs);
$fightServicesContainer = new FightServicesContainer($calculatorConfiguration, $htmlHelper);
$fightApplication = $rulesApplication ?? new CalculatorApplication($fightServicesContainer);
$fightApplication->run();