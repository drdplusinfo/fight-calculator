<?php
declare(strict_types = 1);

namespace DrdPlus\Skills\Physical;

use DrdPlus\Codes\Skills\PhysicalSkillCode;
use DrdPlus\Tables\Tables;
use Granam\Integer\Tools\ToInteger;

/**
 * @link https://pph.drdplus.info/#pouzivani_stitu
 */
class ShieldUsage extends PhysicalSkill
{
    public const SHIELD_USAGE = PhysicalSkillCode::SHIELD_USAGE;

    public function getName(): string
    {
        return self::SHIELD_USAGE;
    }

    public function getBonusToRestriction(): int
    {
        return $this->getCurrentSkillRank()->getValue();
    }

    /**
     * Only for using shield as a weapon!
     *
     * @param Tables $tables
     * @param int $shieldRestriction as a negative number
     * @return int negative number or zero
     * @throws \Granam\Integer\Tools\Exceptions\WrongParameterType
     * @throws \Granam\Integer\Tools\Exceptions\ValueLostOnCast
     * @throws \Granam\Integer\Tools\Exceptions\NegativeIntegerCanNotBePositive
     */
    public function getMalusToFightNumber(Tables $tables, $shieldRestriction): int
    {
        /**
         * using shield as a weapon means using something without skill (zero skill ShieldAsAWeapon respectively)
         *, @see PPH page 86 right column top
         */
        return
            $this->getRestrictionWithShield($tables, $shieldRestriction)
            + $tables->getMissingWeaponSkillTable()->getFightNumberMalusForSkillRank(0);
    }

    /**
     * Applicable to lower shield Restriction (Fight number malus), but can not make it positive.
     *
     * @param Tables $tables
     * @param int $shieldRestriction
     * @return int
     * @throws \Granam\Integer\Tools\Exceptions\WrongParameterType
     * @throws \Granam\Integer\Tools\Exceptions\ValueLostOnCast
     * @throws \Granam\Integer\Tools\Exceptions\NegativeIntegerCanNotBePositive
     */
    public function getRestrictionWithShield(Tables $tables, $shieldRestriction): int
    {
        $malusFromRestriction = ToInteger::toNegativeInteger($shieldRestriction)
            + $tables->getShieldUsageSkillTable()->getRestrictionBonusForSkillRank($this->getCurrentSkillRank());
        if ($malusFromRestriction > 0) {
            return 0; // skill can lower the malus, but can not give bonus
        }

        return $malusFromRestriction;
    }

    /**
     * Only for shield as a weapon!
     *
     * @param Tables $tables
     * @return int
     */
    public function getMalusToAttackNumber(Tables $tables): int
    {
        /**
         * using shield as a weapon means using something without skill (zero skill ShieldAsAWeapon respectively)
         *
         * @see PPH page 86 right column top
         */
        return $tables->getMissingWeaponSkillTable()->getAttackNumberMalusForSkillRank(0);
    }

    /**
     * @param Tables $tables
     * @return int
     */
    public function getMalusToCover(Tables $tables): int
    {
        return $tables->getShieldUsageSkillTable()->getCoverMalusForSkillRank($this->getCurrentSkillRank());
    }

    /**
     * Only for shield as a weapon!
     *
     * @param Tables $tables
     * @return int
     */
    public function getMalusToBaseOfWounds(Tables $tables): int
    {
        /**
         * using shield as a weapon means using something without skill (zero skill ShieldAsAWeapon respectively)
         *
         * @see PPH page 86 right column top
         */
        return $tables->getMissingWeaponSkillTable()->getBaseOfWoundsMalusForSkillRank(0);
    }
}