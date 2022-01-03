<?php declare(strict_types=1);

namespace Tests\DrdPlus\RulesSkeleton\Cache;

use DrdPlus\RulesSkeleton\Cache\RequestHashProvider;
use Tests\DrdPlus\RulesSkeleton\Partials\AbstractContentTest;

class RequestHashProviderTest extends AbstractContentTest
{

    /**
     * @test
     */
    public function Hash_reacts_to_request_path()
    {
        $rootRequestHashProvider = new RequestHashProvider(
            $this->createRequest(),
            $this->getContentIrrelevantRequestAliases(),
            $this->getContentIrrelevantParametersFilter()
        );
        $anotherRootRequestHashProvider = new RequestHashProvider(
            $this->createRequest([], ''),
            $this->getContentIrrelevantRequestAliases(),
            $this->getContentIrrelevantParametersFilter()
        );
        self::assertSame($rootRequestHashProvider->getContextHash(), $anotherRootRequestHashProvider->getContextHash());

        $routeRequestHashProvider = new RequestHashProvider(
            $this->createRequest([], '/deep'),
            $this->getContentIrrelevantRequestAliases(),
            $this->getContentIrrelevantParametersFilter()
        );
        self::assertNotSame($rootRequestHashProvider->getContextHash(), $routeRequestHashProvider->getContextHash());
    }
}
