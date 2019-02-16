<?php
namespace DrdPlus\Tests\Health\Afflictions;

use DrdPlus\Codes\Body\AfflictionByWoundDomainCode;
use DrdPlus\Health\Afflictions\AfflictionDomain;
use Granam\String\StringTools;
use PHPUnit\Framework\TestCase;

class AfflictionDomainTest extends TestCase
{
    /**
     * @test
     * @dataProvider provideDomain
     * @param string $domainCode
     */
    public function I_can_use_it($domainCode)
    {
        $afflictionDomain = AfflictionDomain::getIt($domainCode);
        self::assertInstanceOf(AfflictionDomain::class, $afflictionDomain);
        $getAfflictionDomain = StringTools::assembleGetterForName($domainCode) . 'Domain';
        self::assertSame($afflictionDomain, AfflictionDomain::$getAfflictionDomain());
        self::assertSame($domainCode, $afflictionDomain->getValue());
    }

    public function provideDomain()
    {
        return [
            [AfflictionByWoundDomainCode::PHYSICAL],
            [AfflictionByWoundDomainCode::PSYCHICAL],
        ];
    }

    /**
     * @test
     * @expectedException \DrdPlus\Health\Afflictions\Exceptions\UnknownAfflictionDomain
     */
    public function I_can_not_create_custom_domain()
    {
        AfflictionDomain::getIt('ethereal');
    }
}