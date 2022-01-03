<?php declare(strict_types=1);

namespace Tests\DrdPlus\RulesSkeleton;

use Tests\DrdPlus\RulesSkeleton\Partials\AbstractContentTest;

class PageTitleTest extends AbstractContentTest
{

    /**
     * @test
     */
    public function Page_has_title(): void
    {
        $expectedPageTitle = $this->getTestsConfiguration()->getExpectedPageTitle();
        $currentPageTitle = $this->getCurrentPageTitle();
        self::assertNotEmpty($currentPageTitle, 'Page title is missing on page');
        self::assertSame($expectedPageTitle, $currentPageTitle, 'Current page title differs from expected one');
        $rulesTitle = $this->getCurrentPageTitle($this->getHtmlDocument());
        self::assertNotEmpty($rulesTitle, 'Rules title is missing');
        $gatewayTitle = $this->getCurrentPageTitle($this->getGatewayDocument());
        self::assertNotEmpty($gatewayTitle, 'Gateway title is missing');
        self::assertSame($rulesTitle, $gatewayTitle, 'Rules and gateway titles should be the same');
    }
}
