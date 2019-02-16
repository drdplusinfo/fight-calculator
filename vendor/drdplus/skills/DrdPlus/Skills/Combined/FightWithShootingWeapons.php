<?php
declare(strict_types=1);

namespace DrdPlus\Skills\Combined;

use DrdPlus\Skills\CausingMalusesToWeaponUsage;
use DrdPlus\Skills\FightWithWeaponlikeSkill;
use DrdPlus\Skills\FightWithWeaponsMissingSkillMalusesTrait;

/**
 * @link https://pph.drdplus.info/#boj_se_strelnymi_zbranemi
 */
abstract class FightWithShootingWeapons extends CombinedSkill implements CausingMalusesToWeaponUsage, FightWithWeaponlikeSkill
{
    use FightWithWeaponsMissingSkillMalusesTrait;
}