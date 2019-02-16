<?php
declare(strict_types = 1);

namespace DrdPlus\Skills\Physical;

/**
 * @link https://pph.drdplus.info/#boj_se_zbrani
 */
class FightWithAxes extends FightWithWeaponsUsingPhysicalSkill
{
    public const FIGHT_WITH_AXES = 'fight_with_axes';

    public function getName(): string
    {
        return self::FIGHT_WITH_AXES;
    }

}