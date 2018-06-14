<?php
global $testsConfiguration;
$testsConfiguration = new \DrdPlus\Tests\FrontendSkeleton\TestsConfiguration();
$testsConfiguration->disableHasCustomBodyContent();
$testsConfiguration->disableHasTables();
$testsConfiguration->disableHasLinksToAltar();
$testsConfiguration->setExpectedWebName('DrD+ kalkulátor pro boj');
$testsConfiguration->setExpectedPageTitle('DrD+ kalkulátor pro boj');
$testsConfiguration->disableHasMoreVersions();