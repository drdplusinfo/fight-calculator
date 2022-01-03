<?php

namespace DrdPlus\RulesSkeleton;

$documentRoot = include __DIR__ . '/_bootstrap.php';

$environment = $environment ?? Environment::createFromGlobals();
if (PHP_SAPI !== 'cli') {
    TracyDebugger::enable($environment->isInProduction());
}
$dirs = $dirs ?? new Configurations\Dirs($documentRoot);
$htmlHelper = $htmlHelper
    ?? HtmlHelper::createFromGlobals($dirs, $environment);

$configuration = $configuration ?? Configurations\Configuration::createFromYml($dirs);
$servicesContainer = $servicesContainer ?? new ServicesContainer($configuration, $environment, $htmlHelper);

return $rulesApplication ?? new RulesApplication($servicesContainer);
