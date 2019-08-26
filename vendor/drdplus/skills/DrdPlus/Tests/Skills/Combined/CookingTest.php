<?php declare(strict_types=1);

declare(strict_types = 1);

namespace DrdPlus\Tests\Skills\Combined;

use Granam\DiceRolls\Templates\Rollers\Roller2d6DrdPlus;
use DrdPlus\HuntingAndFishing\CatchProcessingQuality;
use DrdPlus\BaseProperties\Knack;
use DrdPlus\Skills\Combined\Cooking;

class CookingTest extends WithBonusFromCombinedTest
{
    /**
     * @test
     */
    public function I_can_use_it_as_cooking_for_hunting_and_fishing_catch_processing()
    {
        self::assertTrue(
            is_a(Cooking::class, \DrdPlus\HuntingAndFishing\Cooking::class, true),
            'Skill ' . Cooking::class . ' has to implement ' . \DrdPlus\HuntingAndFishing\Cooking::class
        );
    }

    /**
     * @test
     */
    public function I_can_create_catch_processing_quality_easier_by_this()
    {
        $cooking = new Cooking($this->createProfessionLevel());
        $knack = Knack::getIt(5);
        $roll2D6DrdPlus = Roller2d6DrdPlus::getIt()->roll();
        $catchProcessingQuality = $cooking->createCatchProcessingQuality($knack, $roll2D6DrdPlus);
        self::assertEquals(new CatchProcessingQuality($knack, $cooking, $roll2D6DrdPlus), $catchProcessingQuality);

        $cooking->increaseSkillRank($this->createSkillPoint());
        $higherCatchProcessingQuality = $cooking->createCatchProcessingQuality($knack, $roll2D6DrdPlus);
        self::assertNotEquals($catchProcessingQuality, $higherCatchProcessingQuality);
        self::assertEquals(new CatchProcessingQuality($knack, $cooking, $roll2D6DrdPlus), $higherCatchProcessingQuality);
    }
}