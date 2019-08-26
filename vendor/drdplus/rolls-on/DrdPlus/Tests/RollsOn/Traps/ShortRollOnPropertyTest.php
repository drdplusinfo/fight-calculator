<?php declare(strict_types=1);

namespace DrdPlus\Tests\RollsOn\Traps;

use Granam\DiceRolls\Templates\Rolls\Roll1d6;
use DrdPlus\BaseProperties\BaseProperty;
use DrdPlus\BaseProperties\Property;
use DrdPlus\RollsOn\QualityAndSuccess\RollOnQuality;
use DrdPlus\RollsOn\Traps\ShortRollOnProperty;
use Granam\Tests\Tools\TestWithMockery;

abstract class ShortRollOnPropertyTest extends TestWithMockery
{
    /**
     * @test
     */
    public function I_can_use_it()
    {
        /** @var ShortRollOnProperty $sutClass */
        $sutClass = self::getSutClass();
        /** @var ShortRollOnProperty $shortRollOnProperty */
        $shortRollOnProperty = new $sutClass(
            $property = $this->createProperty(123),
            $roll1d6 = $this->createRoll1d6()
        );
        self::assertInstanceOf(RollOnQuality::class, $shortRollOnProperty);
        $getProperty = 'get' . $this->getPropertyName();
        self::assertSame($property, $shortRollOnProperty->$getProperty());
        self::assertSame(123, $shortRollOnProperty->getPreconditionsSum());
        self::assertSame($roll1d6, $shortRollOnProperty->getRoll());
    }

    /**
     * @param int $value
     * @return \Mockery\MockInterface|Property
     */
    private function createProperty($value)
    {
        $baseProperty = $this->mockery($this->getRelatedBasePropertyClass());
        $baseProperty->shouldReceive('getValue')
            ->andReturn($value);

        return $baseProperty;
    }

    /**
     * @return string
     */
    private function getRelatedBasePropertyClass(): string
    {
        // like \DrdPlus\Properties\Base
        $basePropertyNamespace = (new \ReflectionClass(BaseProperty::class))->getNamespaceName();

        // like \DrdPlus\BaseProperties\Intelligence
        return $basePropertyNamespace . '\\' . $this->getPropertyName();
    }

    private $propertyName;

    /**
     * @return string
     */
    private function getPropertyName(): string
    {
        if ($this->propertyName === null) {
            preg_match('~(?<propertyName>[A-Z][a-z]+)$~', self::getSutClass(), $matches);
            // like Intelligence
            $this->propertyName = $matches['propertyName'];
        }

        return $this->propertyName;
    }

    /**
     * @return \Mockery\MockInterface|Roll1d6
     */
    private function createRoll1d6()
    {
        return $this->mockery(Roll1d6::class);
    }
}