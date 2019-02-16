<?php
declare(strict_types = 1);

namespace DrdPlus\Skills\Physical;

/**
 * @link https://pph.drdplus.info/#boj_se_zbrani
 */
class FightWithVoulgesAndTridents extends FightWithWeaponsUsingPhysicalSkill
{
    public const FIGHT_WITH_VOULGES_AND_TRIDENTS = 'fight_with_voulges_and_tridents';

    public function getName(): string
    {
        return self::FIGHT_WITH_VOULGES_AND_TRIDENTS;
    }

}