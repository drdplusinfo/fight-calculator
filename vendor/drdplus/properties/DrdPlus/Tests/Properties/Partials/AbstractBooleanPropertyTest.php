<?php declare(strict_types = 1);

declare(strict_types=1);

namespace DrdPlus\Tests\Properties\Partials;

use DrdPlus\Properties\Native\NativeProperty;
use DrdPlus\Tests\BaseProperties\Partials\AbstractSimplePropertyTest;

abstract class AbstractBooleanPropertyTest extends AbstractSimplePropertyTest
{
    /**
     * @return bool[]
     */
    protected function getValuesForTest(): array
    {
        return [true, false];
    }

    /**
     * @test
     */
    public function I_can_get_history_of_its_creation()
    {
        /** @var NativeProperty $propertyClass */
        $propertyClass = self::getSutClass();
        $property = false;
        foreach ($this->getValuesForTest() as $value) {
            /** @var NativeProperty $property */
            $property = $propertyClass::getIt($value);
            self::assertSame($value, $property->getValue());
        }

        return $property;
    }
}