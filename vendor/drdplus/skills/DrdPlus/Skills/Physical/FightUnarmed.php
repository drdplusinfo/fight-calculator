<?php declare(strict_types=1);

declare(strict_types = 1);

namespace DrdPlus\Skills\Physical;

/**
 * @link https://pph.drdplus.info/#boj_se_zbrani
 */
class FightUnarmed extends FightWithWeaponsUsingPhysicalSkill
{
    public const FIGHT_UNARMED = 'fight_unarmed';

    public function getName(): string
    {
        return self::FIGHT_UNARMED;
    }

}