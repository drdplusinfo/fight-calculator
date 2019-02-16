<?php
declare(strict_types=1);

namespace DrdPlus\Skills\Combined;

use DrdPlus\Codes\Properties\PropertyCode;
use DrdPlus\Person\ProfessionLevels\ProfessionLevel;
use DrdPlus\Skills\Skill;
use DrdPlus\Skills\SkillRank;
use Granam\Integer\PositiveIntegerObject;

/**
 * @method CombinedSkillRank|SkillRank getCurrentSkillRank(): SkillRank
 */
abstract class CombinedSkill extends Skill
{

    /**
     * @var CombinedSkillRank[]|array
     */
    private $combinedSkillRanks = [];

    /**
     * @param CombinedSkillPoint $combinedSkillPoint
     * @throws \DrdPlus\Skills\Exceptions\UnexpectedRankValue
     * @throws \DrdPlus\Skills\Exceptions\CanNotVerifyOwningSkill
     * @throws \DrdPlus\Skills\Exceptions\CanNotVerifyPaidSkillPoint
     */
    public function increaseSkillRank(CombinedSkillPoint $combinedSkillPoint): void
    {
        $this->addTypeVerifiedSkillRank(
            new CombinedSkillRank(
                $this,
                $combinedSkillPoint,
                new PositiveIntegerObject($this->getCurrentSkillRank()->getValue() + 1)
            )
        );
    }

    /**
     * @param ProfessionLevel $professionLevel
     * @return SkillRank|CombinedSkillRank
     * @throws \DrdPlus\Skills\Exceptions\UnknownPaymentForSkillPoint
     * @throws \DrdPlus\Skills\Exceptions\CanNotVerifyOwningSkill
     * @throws \DrdPlus\Skills\Exceptions\CanNotVerifyPaidSkillPoint
     */
    protected function createZeroSkillRank(ProfessionLevel $professionLevel): SkillRank
    {
        return new CombinedSkillRank(
            $this,
            CombinedSkillPoint::createZeroSkillPoint($professionLevel),
            new PositiveIntegerObject(0)
        );
    }

    protected function setSkillRank(SkillRank $skillRank)
    {
        $this->combinedSkillRanks[$skillRank->getValue()] = $skillRank;
    }

    /**
     * @return array|CombinedSkillRank[]
     */
    public function getSkillRanks(): array
    {
        return $this->combinedSkillRanks;
    }

    /**
     * @return string[]
     */
    public function getRelatedPropertyCodes(): array
    {
        return [PropertyCode::KNACK, PropertyCode::CHARISMA];
    }

    public function isPhysical(): bool
    {
        return false;
    }

    public function isPsychical(): bool
    {
        return false;
    }

    public function isCombined(): bool
    {
        return true;
    }

}