<?php declare(strict_types=1);

namespace Tests\DrdPlus\CalculatorSkeleton;

/**
 * @backupGlobals enabled
 */
class RulesApplicationTest extends \Tests\DrdPlus\RulesSkeleton\RulesApplicationTest
{
    use Partials\CalculatorContentTestTrait;

    /**
     * @test
     * @dataProvider provideRequestType
     * @param string $requestType
     * @throws \ReflectionException
     */
    public function I_will_be_redirected_via_html_meta_on_trial(string $requestType): void
    {
        if (!$this->getTestsConfiguration()->hasProtectedAccess()) {
            self::assertFalse(false, 'No trial here');
            return;
        }
        parent::I_will_be_redirected_via_html_meta_on_trial($requestType);
    }
}
