<?php
declare(strict_types=1);

namespace DrdPlus\HuntingAndFishing;

use DrdPlus\Calculations\SumAndRound;
use DrdPlus\BaseProperties\Knack;
use DrdPlus\Properties\Derived\Senses;
use Granam\Integer\IntegerInterface;
use Granam\Strict\Object\StrictObject;

/**
 * See PPH page 132, @link https://pph.drdplus.jaroslavtyc.com/#lov_a_rybolov
 */
class HuntPrerequisite extends StrictObject implements IntegerInterface
{
    /**
     * @var int
     */
    private $value;

    /**
     * @param Knack $knack
     * @param Senses $senses
     * @param WithBonusFromHuntingAndFishingSkill $huntingAndFishingSkillBonus
     * @param BonusFromDmForRolePlaying $bonusFromDmForRolePlaying
     */
    public function __construct(
        Knack $knack,
        Senses $senses,
        WithBonusFromHuntingAndFishingSkill $huntingAndFishingSkillBonus,
        BonusFromDmForRolePlaying $bonusFromDmForRolePlaying
    )
    {
        $this->value = SumAndRound::half($knack->getValue() + $senses->getValue())
            + $huntingAndFishingSkillBonus->getBonusFromSkill() + $bonusFromDmForRolePlaying->getValue();
    }

    public function getValue(): int
    {
        return $this->value;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return (string)$this->getValue();
    }

}