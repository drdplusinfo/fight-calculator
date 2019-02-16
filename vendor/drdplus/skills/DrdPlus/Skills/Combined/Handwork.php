<?php
declare(strict_types=1);

namespace DrdPlus\Skills\Combined;

use Granam\DiceRolls\Templates\Rolls\Roll2d6DrdPlus;
use DrdPlus\Codes\Skills\CombinedSkillCode;
use DrdPlus\BaseProperties\Knack;
use DrdPlus\Skills\Combined\RollsOnQuality\HandworkQuality;
use DrdPlus\Skills\Combined\RollsOnQuality\HandworkRollOnSuccess\HandworkRollOnSuccess;
use DrdPlus\Skills\WithBonusToKnack;

/**
 * @link https://pph.drdplus.info/#rucni_prace
 */
class Handwork extends CombinedSkill implements WithBonusToKnack
{
    public const HANDWORK = CombinedSkillCode::HANDWORK;

    public function getName(): string
    {
        return self::HANDWORK;
    }

    public function getBonusToKnack(): int
    {
        return 2 * $this->getCurrentSkillRank()->getValue();
    }

    public function createHandworkQuality(Knack $knack, Roll2d6DrdPlus $roll2D6DrdPlus): HandworkQuality
    {
        return new HandworkQuality($knack, $this, $roll2D6DrdPlus);
    }

    public function createHandworkRollOnSuccess(
        Knack $knack,
        Roll2d6DrdPlus $roll2D6DrdPlus,
        int $difficultyModification
    ): HandworkRollOnSuccess
    {
        return HandworkRollOnSuccess::createIt(
            $this->createHandworkQuality($knack, $roll2D6DrdPlus),
            $difficultyModification
        );
    }

}