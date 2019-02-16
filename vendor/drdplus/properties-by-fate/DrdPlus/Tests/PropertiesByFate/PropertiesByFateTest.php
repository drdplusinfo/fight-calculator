<?php
declare(strict_types = 1);

namespace DrdPlus\Tests\PropertiesByFate;

use DrdPlus\Codes\History\FateCode;
use DrdPlus\Codes\Properties\PropertyCode;
use DrdPlus\PropertiesByFate\PropertiesByFate;
use DrdPlus\Professions\Profession;
use Granam\Tests\Tools\TestWithMockery;

abstract class PropertiesByFateTest extends TestWithMockery
{

    /**
     * @param array $primaryPropertyCodes
     * @return Profession|\Mockery\MockInterface
     */
    protected function createProfession(array $primaryPropertyCodes)
    {
        $profession = $this->mockery(Profession::class);
        $profession->shouldReceive('isPrimaryProperty')
            ->with($this->type(PropertyCode::class))
            ->andReturnUsing(function (PropertyCode $propertyCode) use ($primaryPropertyCodes) {
                return in_array($propertyCode->getValue(), $primaryPropertyCodes, true);
            });
        $profession->shouldReceive('getValue')
            ->andReturn('foo');

        return $profession;
    }

    /**
     * @param PropertiesByFate $exceptionalityProperties
     */
    abstract protected function I_get_expected_choice_code(PropertiesByFate $exceptionalityProperties);

    /**
     * @param PropertiesByFate $exceptionalityProperties
     * @param FateCode $expectedFateCode
     */
    abstract protected function I_get_fate_code_created_with(
        PropertiesByFate $exceptionalityProperties,
        FateCode $expectedFateCode
    );

    /**
     * @param PropertiesByFate $propertiesByFate
     */
    protected function I_can_get_property_by_its_code(PropertiesByFate $propertiesByFate)
    {
        self::assertSame($propertiesByFate->getStrength(), $propertiesByFate->getProperty(PropertyCode::getIt(PropertyCode::STRENGTH)));
        self::assertSame($propertiesByFate->getAgility(), $propertiesByFate->getProperty(PropertyCode::getIt(PropertyCode::AGILITY)));
        self::assertSame($propertiesByFate->getKnack(), $propertiesByFate->getProperty(PropertyCode::getIt(PropertyCode::KNACK)));
        self::assertSame($propertiesByFate->getWill(), $propertiesByFate->getProperty(PropertyCode::getIt(PropertyCode::WILL)));
        self::assertSame($propertiesByFate->getIntelligence(), $propertiesByFate->getProperty(PropertyCode::getIt(PropertyCode::INTELLIGENCE)));
        self::assertSame($propertiesByFate->getCharisma(), $propertiesByFate->getProperty(PropertyCode::getIt(PropertyCode::CHARISMA)));
    }

    /**
     * @test
     * @expectedException \DrdPlus\PropertiesByFate\Exceptions\NotFateAffectedProperty
     * @expectedExceptionMessageRegExp ~beauty~
     */
    public function I_can_not_get_property_not_affected_by_fortune()
    {
        /** @var PropertiesByFate $sut */
        $sut = (new \ReflectionClass(self::getSutClass()))->newInstanceWithoutConstructor();
        $sut->getProperty(PropertyCode::getIt(PropertyCode::BEAUTY));
    }

}