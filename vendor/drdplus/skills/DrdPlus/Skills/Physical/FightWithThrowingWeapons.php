<?php declare(strict_types=1);

declare(strict_types = 1);

namespace DrdPlus\Skills\Physical;

/**
 * @link https://pph.drdplus.info/#boj_se_zbrani
 */
class FightWithThrowingWeapons extends FightWithWeaponsUsingPhysicalSkill
{
    public const FIGHT_WITH_THROWING_WEAPONS = 'fight_with_throwing_weapons';

    public function getName(): string
    {
        return self::FIGHT_WITH_THROWING_WEAPONS;
    }

}