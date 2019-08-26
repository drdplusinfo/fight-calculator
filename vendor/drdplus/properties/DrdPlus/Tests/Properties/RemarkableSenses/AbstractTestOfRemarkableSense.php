<?php declare(strict_types = 1);

declare(strict_types=1);

namespace DrdPlus\Tests\Properties\RemarkableSenses;

use DrdPlus\Codes\Properties\PropertyCode;
use DrdPlus\Properties\RemarkableSenses\RemarkableSenseProperty;
use DrdPlus\Tests\BaseProperties\Partials\AbstractSimplePropertyTest;

abstract class AbstractTestOfRemarkableSense extends AbstractSimplePropertyTest
{
    /**
     * @test
     * @return RemarkableSenseProperty
     */
    public function I_can_get_property_easily(): RemarkableSenseProperty
    {
        $propertyClass = self::getSutClass();
        /** @var RemarkableSenseProperty $propertyClass */
        $property = $propertyClass::getIt();
        self::assertInstanceOf($propertyClass, $property);
        self::assertSame(strtolower($this->getSutBaseName()), $property->getValue());
        self::assertSame(PropertyCode::getIt(strtolower($this->getSutBaseName())), $property->getCode());

        return $property;
    }

    protected function getValuesForTest(): array
    {
        throw new \LogicException('Should not be called');
    }
}