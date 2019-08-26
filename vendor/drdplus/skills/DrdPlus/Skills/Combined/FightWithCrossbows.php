<?php declare(strict_types=1);

namespace DrdPlus\Skills\Combined;

/**
 * @link https://pph.drdplus.info/#boj_se_strelnymi_zbranemi
 */
class FightWithCrossbows extends FightWithShootingWeapons
{
    public const FIGHT_WITH_CROSSBOWS = 'fight_with_crossbows';

    public function getName(): string
    {
        return self::FIGHT_WITH_CROSSBOWS;
    }
}