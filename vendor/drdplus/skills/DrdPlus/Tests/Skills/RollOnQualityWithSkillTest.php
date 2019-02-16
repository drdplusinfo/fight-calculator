<?php
declare(strict_types=1);

namespace DrdPlus\Tests\Skills;

use DrdPlus\BaseProperties\Property;
use Granam\DiceRolls\Templates\Rolls\Roll2d6DrdPlus;
use DrdPlus\RollsOn\QualityAndSuccess\RollOnQuality;
use DrdPlus\Skills\Psychical\RollsOn\MapQuality;
use DrdPlus\Skills\Psychical\RollsOn\RollOnMapUsage;
use DrdPlus\Skills\Skill;
use Granam\Tests\Tools\TestWithMockery;

abstract class RollOnQualityWithSkillTest extends TestWithMockery
{
    /**
     * @test
     * @throws \ReflectionException
     */
    public function I_can_use_it()
    {
        /** @var RollOnMapUsage|MapQuality $sutClass */
        $sutClass = self::getSutClass();
        /** @var RollOnQuality $sut */
        $sut = new $sutClass(
            $this->createProperty(123),
            $this->createSkill(456),
            $roll = $this->createRoll2d6Plus(789)
        );
        self::assertSame(123 + 456 + 789, $sut->getValue());
        self::assertSame(123 + 456, $sut->getPreconditionsSum());
        self::assertSame($roll, $sut->getRoll());
    }

    /**
     * @param int $value
     * @return Property|\Mockery\MockInterface
     * @throws \ReflectionException
     */
    protected function createProperty(int $value): Property
    {
        $property = $this->mockery($this->getPropertyClass());
        $property->shouldReceive('getValue')
            ->andReturn($value);

        return $property;
    }

    /**
     * @return string
     * @throws \ReflectionException
     */
    private function getPropertyClass(): string
    {
        return $this->getSutConstructorParameterClass(0);
    }

    /**
     * @param int $parameterPosition
     * @return string
     * @throws \ReflectionException
     */
    protected function getSutConstructorParameterClass(int $parameterPosition): string
    {
        $reflection = new \ReflectionClass(self::getSutClass());
        $constructor = $reflection->getMethod('__construct');
        $firstParameter = $constructor->getParameters()[$parameterPosition];

        return $firstParameter->getClass()->getName();
    }

    /**
     * @param int $bonusToRoll
     * @return Skill|\Mockery\MockInterface
     * @throws \ReflectionException
     */
    protected function createSkill(int $bonusToRoll): Skill
    {
        $skill = $this->mockery($this->getSutConstructorParameterClass(1));
        $skill->shouldReceive('getBonus')
            ->andReturn($bonusToRoll);

        return $skill;
    }

    /**
     * @param int $value
     * @return Roll2d6DrdPlus|\Mockery\MockInterface
     */
    private function createRoll2d6Plus(int $value)
    {
        $roll2d6DrdPlus = $this->mockery(Roll2d6DrdPlus::class);
        $roll2d6DrdPlus->shouldReceive('getValue')
            ->andReturn($value);

        return $roll2d6DrdPlus;
    }

    /**
     * @test
     * @throws \ReflectionException
     */
    public function I_get_whispered_proper_roll_class_by_ide()
    {
        $reflection = new \ReflectionClass(self::getSutClass());
        self::assertContains(<<<'COMMENT'
 * @method Roll2d6DrdPlus getRoll()
COMMENT
            , $reflection->getDocComment()
        );
        self::assertRegExp(<<<'REGEXP'
~\* See PPH page \d+ (left( column)?( (top|bottom))?|right( column)?( (top|bottom))?)?, @link https://pph\.drdplus\.info/#[a-z_]+~
REGEXP
            , $reflection->getDocComment(),
            "You forgot something like\n* See PPH page XYZ, @link https://pph.drdplus.info/#foo_bar"
        );
    }
}