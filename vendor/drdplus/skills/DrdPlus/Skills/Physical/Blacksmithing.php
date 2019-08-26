<?php declare(strict_types=1);

declare(strict_types = 1);

namespace DrdPlus\Skills\Physical;

use Granam\DiceRolls\Templates\Rolls\Roll2d6DrdPlus;
use DrdPlus\Codes\Skills\PhysicalSkillCode;
use DrdPlus\BaseProperties\Knack;
use DrdPlus\Skills\Physical\RollsOnQuality\BlacksmithingQuality;
use DrdPlus\Skills\Physical\RollsOnQuality\RollsOnSuccess\BlacksmithingRollOnSuccess;
use DrdPlus\Skills\WithBonusToKnack;

/**
 * @link https://pph.drdplus.info/#kovarstvi
 */
class Blacksmithing extends PhysicalSkill implements WithBonusToKnack
{
    public const BLACKSMITHING = PhysicalSkillCode::BLACKSMITHING;

    public function getName(): string
    {
        return self::BLACKSMITHING;
    }

    public function getBonusToKnack(): int
    {
        return $this->getCurrentSkillRank()->getValue() * 2;
    }

    /**
     * @param Knack $knack
     * @param Roll2d6DrdPlus $roll2D6DrdPlus
     * @return BlacksmithingQuality
     */
    public function createBlacksmithingQuality(Knack $knack, Roll2d6DrdPlus $roll2D6DrdPlus): BlacksmithingQuality
    {
        return new BlacksmithingQuality($knack, $this, $roll2D6DrdPlus);
    }

    /**
     * @param int $difficulty
     * @param Knack $knack
     * @param Roll2d6DrdPlus $roll2D6DrdPlus
     * @return BlacksmithingRollOnSuccess
     */
    public function createBlacksmithingRollOnSuccess(
        int $difficulty,
        Knack $knack,
        Roll2d6DrdPlus $roll2D6DrdPlus
    ): BlacksmithingRollOnSuccess
    {
        return new BlacksmithingRollOnSuccess($difficulty, $this->createBlacksmithingQuality($knack, $roll2D6DrdPlus));
    }
}