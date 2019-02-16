<?php
declare(strict_types=1);

namespace DrdPlus\Skills\Combined;

/**
 * @link https://pph.drdplus.info/#boj_se_strelnymi_zbranemi
 */
class FightWithBows extends FightWithShootingWeapons
{
    public const FIGHT_WITH_BOWS = 'fight_with_bows';

    public function getName(): string
    {
        return self::FIGHT_WITH_BOWS;
    }
}