<?php
declare(strict_types = 1);

namespace DrdPlus\Tests\Skills\Combined\RollsOnQuality\HandworkRollOnSuccess;

use Granam\DiceRolls\Templates\Rollers\Roller2d6DrdPlus;
use DrdPlus\Person\ProfessionLevels\ProfessionFirstLevel;
use DrdPlus\BaseProperties\Knack;
use DrdPlus\RollsOn\QualityAndSuccess\ExtendedRollOnSuccess;
use DrdPlus\Skills\Combined\Handwork;
use DrdPlus\Skills\Combined\RollsOnQuality\HandworkQuality;
use DrdPlus\Skills\Combined\RollsOnQuality\HandworkRollOnSuccess\HandworkRollOnSuccess;
use DrdPlus\Skills\Combined\RollsOnQuality\HandworkRollOnSuccess\HandworkSimpleRollOnGreatSuccess;
use DrdPlus\Skills\Combined\RollsOnQuality\HandworkRollOnSuccess\HandworkSimpleRollOnLowSuccess;
use DrdPlus\Skills\Combined\RollsOnQuality\HandworkRollOnSuccess\HandworkSimpleRollOnModerateSuccess;
use Granam\Tests\Tools\TestWithMockery;

class HandworkExtendedRollOnSuccessTest extends TestWithMockery
{
    /**
     * @test
     * @dataProvider provideDifficultyModifier
     * @param int $difficultyModification
     */
    public function I_can_create_it_directly_or_easily_by_factory_method(int $difficultyModification)
    {
        $handworkQuality = new HandworkQuality(
            Knack::getIt(10),
            new Handwork($this->createProfessionLevel()),
            (new Roller2d6DrdPlus())->roll()
        );
        $handworkExtendedRollOnSuccess = HandworkRollOnSuccess::createIt($handworkQuality, $difficultyModification);
        self::assertInstanceOf(HandworkRollOnSuccess::class, $handworkExtendedRollOnSuccess);
        self::assertSame($handworkQuality, $handworkExtendedRollOnSuccess->getRollOnQuality());
        $reflection = new \ReflectionClass(ExtendedRollOnSuccess::class);
        $rollsOnSuccess = $reflection->getProperty('rollsOnSuccess');
        $rollsOnSuccess->setAccessible(true);
        self::assertEquals(
            [
                new HandworkSimpleRollOnGreatSuccess($handworkQuality, $difficultyModification),
                new HandworkSimpleRollOnModerateSuccess($handworkQuality, $difficultyModification),
                new HandworkSimpleRollOnLowSuccess($handworkQuality, $difficultyModification),
            ],
            $rollsOnSuccess->getValue($handworkExtendedRollOnSuccess)
        );

        self::assertEquals(
            $handworkExtendedRollOnSuccess,
            new HandworkRollOnSuccess($handworkQuality, $difficultyModification)
        );
    }

    /**
     * @return \Mockery\MockInterface|ProfessionFirstLevel
     */
    private function createProfessionLevel()
    {
        return $this->mockery(ProfessionFirstLevel::class);
    }

    public function provideDifficultyModifier(): array
    {
        return array_map(
            function (int $value) {
                return [$value];
            },
            range(-5, 5, 1)
        );
    }
}