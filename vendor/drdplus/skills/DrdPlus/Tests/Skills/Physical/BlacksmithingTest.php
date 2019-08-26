<?php declare(strict_types=1);

declare(strict_types = 1);

namespace DrdPlus\Tests\Skills\Physical;

use Granam\DiceRolls\Templates\Rollers\Roller2d6DrdPlus;
use DrdPlus\BaseProperties\Knack;
use DrdPlus\Skills\Physical\Blacksmithing;
use DrdPlus\Skills\Physical\RollsOnQuality\BlacksmithingQuality;
use DrdPlus\Skills\Physical\RollsOnQuality\RollsOnSuccess\BlacksmithingRollOnSuccess;
use DrdPlus\Tests\Skills\WithBonusToKnackTest;

class BlacksmithingTest extends WithBonusToKnackTest
{
    use CreatePhysicalSkillPointTrait;

    /**
     * @param int $skillRankValue
     * @return int
     */
    protected function getExpectedBonusFromSkill(int $skillRankValue): int
    {
        return $skillRankValue * 2;
    }

    /**
     * @test
     */
    public function I_can_create_blacksmithing_quality()
    {
        $blacksmithing = new Blacksmithing($this->createProfessionLevel());
        $knack = Knack::getIt(58);
        $roll2D6DrdPlus = Roller2d6DrdPlus::getIt()->roll();
        $blacksmithQuality = $blacksmithing->createBlacksmithingQuality($knack, $roll2D6DrdPlus);
        self::assertEquals(new BlacksmithingQuality($knack, $blacksmithing, $roll2D6DrdPlus), $blacksmithQuality);
    }

    /**
     * @test
     */
    public function I_can_create_blacksmithing_roll_on_success()
    {
        $blacksmithing = new Blacksmithing($this->createProfessionLevel());
        $knack = Knack::getIt(58);
        $roll2D6DrdPlus = Roller2d6DrdPlus::getIt()->roll();
        $blacksmithingRollOnSuccess = $blacksmithing->createBlacksmithingRollOnSuccess(9, $knack, $roll2D6DrdPlus);
        self::assertEquals(
            new BlacksmithingRollOnSuccess(9, $blacksmithing->createBlacksmithingQuality($knack, $roll2D6DrdPlus)),
            $blacksmithingRollOnSuccess
        );
    }
}