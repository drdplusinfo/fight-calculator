<?php
declare(strict_types=1);

namespace DrdPlus\Skills\Combined;

use DrdPlus\Codes\Skills\CombinedSkillCode;

/**
 * @link https://pph.drdplus.info/#prvni_pomoc
 */
class FirstAid extends CombinedSkill
{
    public const FIRST_AID = CombinedSkillCode::FIRST_AID;

    public function getName(): string
    {
        return self::FIRST_AID;
    }

    public function getMinimalWoundsLeftAfterFirstAidHeal(): int
    {
        $currentSkillRankValue = $this->getCurrentSkillRank()->getValue();
        if ($currentSkillRankValue === 0) {
            return 5;
        }

        return 4 - $currentSkillRankValue;
    }

    /**
     * @link https://pph.drdplus.info/#vypocet_vylecenych_bodu_zraneni
     * @return int negative
     */
    public function getHealingPowerToBasicWounds(): int
    {
        $currentSkillRankValue = $this->getCurrentSkillRank()->getValue();
        if ($currentSkillRankValue === 0) {
            return -20;
        }

        return (2 * $currentSkillRankValue) - 8; // results into negative integer
    }

    /**
     * @link https://pph.drdplus.info/#velikost_postizeni
     * @return int
     */
    public function getBleedingLoweringValue(): int
    {
        $value = 1 - $this->getCurrentSkillRank()->getValue();
        if ($value > -1) { // only 1- is accepted (that means only on skill rank 2+ can be bleeding lowered)
            return 0;
        }

        // lower is better
        return $value;
    }

    /**
     * @link https://pph.drdplus.info/#velikost_postizeni
     * @return int
     */
    public function getPoisonLoweringValue(): int
    {
        $value = 1 - $this->getCurrentSkillRank()->getValue();
        if ($value > -2) { // only 2- is accepted (that means only on skill rank 3 can be poison lowered)
            return 0;
        }

        // lower is better
        return $value;
    }
}