<?php
declare(strict_types = 1);

namespace DrdPlus\Skills\Physical;

/**
 * @link https://pph.drdplus.info/#boj_se_zbrani
 */
class FightWithMacesAndClubs extends FightWithWeaponsUsingPhysicalSkill
{
    public const FIGHT_WITH_MACES_AND_CLUBS = 'fight_with_maces_and_clubs';

    public function getName(): string
    {
        return self::FIGHT_WITH_MACES_AND_CLUBS;
    }

}