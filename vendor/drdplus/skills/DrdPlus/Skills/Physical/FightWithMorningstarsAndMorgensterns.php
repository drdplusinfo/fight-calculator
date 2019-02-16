<?php
declare(strict_types = 1);

namespace DrdPlus\Skills\Physical;

/**
 * @link https://pph.drdplus.info/#boj_se_zbrani
 */
class FightWithMorningstarsAndMorgensterns extends FightWithWeaponsUsingPhysicalSkill
{
    public const FIGHT_WITH_MORNINGSTARS_AND_MORGENSTERNS = 'fight_with_morningstars_and_morgensterns';

    public function getName(): string
    {
        return self::FIGHT_WITH_MORNINGSTARS_AND_MORGENSTERNS;
    }

}