<?php declare(strict_types = 1);

namespace DrdPlus\Tables\Armaments;

use Granam\Integer\PositiveInteger;

interface MissingProtectiveArmamentSkill
{
    public const RESTRICTION_BONUS = 'restriction_bonus';

    /**
     * @param int|PositiveInteger $skillRank
     * @return int
     * @throws \DrdPlus\Tables\Armaments\Partials\Exceptions\UnexpectedSkillRank
     * @throws \Granam\Integer\Tools\Exceptions\WrongParameterType
     * @throws \Granam\Integer\Tools\Exceptions\ValueLostOnCast
     */
    public function getRestrictionBonusForSkillRank($skillRank): int;
}