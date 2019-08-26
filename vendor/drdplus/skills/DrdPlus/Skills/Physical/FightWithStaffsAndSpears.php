<?php declare(strict_types=1);

declare(strict_types = 1);

namespace DrdPlus\Skills\Physical;

/**
 * @link https://pph.drdplus.info/#boj_se_zbrani
 */
class FightWithStaffsAndSpears extends FightWithWeaponsUsingPhysicalSkill
{
    public const FIGHT_WITH_STAFFS_AND_SPEARS = 'fight_with_staffs_and_spears';

    public function getName(): string
    {
        return self::FIGHT_WITH_STAFFS_AND_SPEARS;
    }

}