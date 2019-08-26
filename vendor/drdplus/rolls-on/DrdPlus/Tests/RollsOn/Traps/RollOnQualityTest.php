<?php declare(strict_types=1);

namespace DrdPlus\Tests\RollsOn\Traps;

use Granam\DiceRolls\Templates\Rollers\Roller2d6DrdPlus;
use Granam\DiceRolls\Templates\Rolls\Roll2d6DrdPlus;
use DrdPlus\BaseProperties\BaseProperty;
use DrdPlus\Properties\Derived\DerivedProperty;
use DrdPlus\BaseProperties\Property;
use DrdPlus\RollsOn\QualityAndSuccess\RollOnQuality;
use Granam\Tests\Tools\TestWithMockery;

abstract class RollOnQualityTest extends TestWithMockery
{
    /**
     * @test
     */
    public function I_can_use_it()
    {
        /** @var RollOnQuality $rollOnProperty */
        $rollOnProperty = $this->createSutInstance(
            $property = $this->getPropertyInstance($propertyValue = 123),
            $roll = Roller2d6DrdPlus::getIt()->roll()
        );
        $getProperty = $this->getPropertyGetter();
        self::assertSame($property, $rollOnProperty->$getProperty());
        self::assertSame($propertyValue, $rollOnProperty->getPreconditionsSum());
        self::assertSame($roll, $rollOnProperty->getRoll());
        $resultValue = $propertyValue + $roll->getValue();
        self::assertSame($resultValue, $rollOnProperty->getValue());
        self::assertSame((string)$resultValue, (string)$rollOnProperty);
    }

    /**
     * @param Property $property
     * @param Roll2d6DrdPlus $roll2D6DrdPlus
     * @return RollOnQuality
     */
    protected function createSutInstance(Property $property, Roll2d6DrdPlus $roll2D6DrdPlus): RollOnQuality
    {
        $sutClass = self::getSutClass();

        return new $sutClass($property, $roll2D6DrdPlus);
    }

    /**
     * @return string|BaseProperty
     */
    protected function getPropertyClass(): string
    {
        $propertyBasename = preg_replace('~^.+RollOn(.+)$~', '$1', self::getSutClass());
        $basePropertyNamespace = (new \ReflectionClass(BaseProperty::class))->getNamespaceName();
        $basePropertyClassName = $basePropertyNamespace . '\\' . $propertyBasename;
        if (class_exists($basePropertyClassName)) {
            return $basePropertyClassName;
        }
        $derivedPropertyNamespace = (new \ReflectionClass(DerivedProperty::class))->getNamespaceName();

        return $derivedPropertyNamespace . '\\' . $propertyBasename;
    }

    /**
     * @param $value
     * @return \Mockery\MockInterface|Property
     */
    protected function getPropertyInstance($value): Property
    {
        $property = $this->mockery($this->getPropertyClass());
        $property->shouldReceive('getValue')
            ->andReturn($value);

        return $property;
    }

    protected function getPropertyGetter(): string
    {
        $propertyName = preg_replace('~^(?:.+[\\\])?(\w+)$~', '$1', $this->getPropertyClass());

        return 'get' . $propertyName;
    }
}