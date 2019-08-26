<?php declare(strict_types=1);

declare(strict_types = 1);

namespace DrdPlus\Skills\Physical;

/**
 * @link https://pph.drdplus.info/#boj_se_zbrani
 */
class FightWithKnivesAndDaggers extends FightWithWeaponsUsingPhysicalSkill
{
    public const FIGHT_WITH_KNIVES_AND_DAGGERS = 'fight_with_knives_and_daggers';

    public function getName(): string
    {
        return self::FIGHT_WITH_KNIVES_AND_DAGGERS;
    }

}