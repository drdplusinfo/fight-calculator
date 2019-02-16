<?php
declare(strict_types=1);

namespace DrdPlus\Tests\HuntingAndFishing;

use Granam\DiceRolls\Templates\Rolls\Roll2d6DrdPlus;
use DrdPlus\HuntingAndFishing\CatchProcessingQuality;
use DrdPlus\HuntingAndFishing\Cooking;
use DrdPlus\BaseProperties\Knack;
use Granam\Tests\Tools\TestWithMockery;

class CatchProcessingQualityTest extends TestWithMockery
{
    /**
     * @test
     */
    public function I_can_use_it(): void
    {
        $sut = new CatchProcessingQuality(
            $this->createKnack(123),
            $this->createCooking(456),
            $roll = $this->createRoll2d6Plus(789)
        );
        self::assertSame(123 + 456 + 789, $sut->getValue());
        self::assertSame(123 + 456, $sut->getPreconditionsSum());
        self::assertSame($roll, $sut->getRoll());
    }

    /**
     * @param int $value
     * @return Knack|\Mockery\MockInterface
     */
    protected function createKnack(int $value): Knack
    {
        $knack = $this->mockery(Knack::class);
        $knack->shouldReceive('getValue')
            ->andReturn($value);

        return $knack;
    }

    /**
     * @param int $bonusToRoll
     * @return Cooking|\Mockery\MockInterface
     */
    protected function createCooking(int $bonusToRoll): Cooking
    {
        $cooking = $this->mockery(Cooking::class);
        $cooking->shouldReceive('getBonus')
            ->andReturn($bonusToRoll);

        return $cooking;
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
    public function I_get_whispered_proper_roll_class_by_ide(): void
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
            "You forgot something like\n* See PHP page XYZ, @link https://pph.drdplus.info/#foo_bar"
        );
    }
}