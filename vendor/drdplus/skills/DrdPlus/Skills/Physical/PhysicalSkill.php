<?php
declare(strict_types=1);

namespace DrdPlus\Skills\Physical;

use DrdPlus\Codes\Properties\PropertyCode;
use DrdPlus\Person\ProfessionLevels\ProfessionLevel;
use DrdPlus\Skills\Skill;
use DrdPlus\Skills\SkillRank;
use Granam\Integer\PositiveIntegerObject;

/**
 * @method PhysicalSkillRank|SkillRank getCurrentSkillRank(): SkillRank
 */
abstract class PhysicalSkill extends Skill
{
    /**
     * @var PhysicalSkillRank[]|array
     */
    private $physicalSkillRanks = [];

    /**
     * @param ProfessionLevel $professionLevel
     * @return PhysicalSkillRank|SkillRank
     * @throws \DrdPlus\Skills\Exceptions\UnknownPaymentForSkillPoint
     * @throws \DrdPlus\Skills\Exceptions\CanNotVerifyOwningSkill
     * @throws \DrdPlus\Skills\Exceptions\CanNotVerifyPaidSkillPoint
     */
    protected function createZeroSkillRank(ProfessionLevel $professionLevel): SkillRank
    {
        return new PhysicalSkillRank(
            $this,
            PhysicalSkillPoint::createZeroSkillPoint($professionLevel),
            new PositiveIntegerObject(0)
        );
    }

    /**
     * @param PhysicalSkillPoint $physicalSkillPoint
     * @throws \DrdPlus\Skills\Exceptions\UnexpectedRankValue
     * @throws \DrdPlus\Skills\Exceptions\CanNotVerifyOwningSkill
     * @throws \DrdPlus\Skills\Exceptions\CanNotVerifyPaidSkillPoint
     */
    public function increaseSkillRank(PhysicalSkillPoint $physicalSkillPoint): void
    {
        $this->addTypeVerifiedSkillRank(
            new PhysicalSkillRank(
                $this,
                $physicalSkillPoint,
                new PositiveIntegerObject($this->getCurrentSkillRank()->getValue() + 1)
            )
        );
    }

    protected function setSkillRank(SkillRank $skillRank)
    {
        $this->physicalSkillRanks[$skillRank->getValue()] = $skillRank;
    }

    /**
     * @return array|PhysicalSkillRank[]
     */
    public function getSkillRanks(): array
    {
        return $this->physicalSkillRanks;
    }

    /**
     * @return string[]
     */
    public function getRelatedPropertyCodes(): array
    {
        return [PropertyCode::STRENGTH, PropertyCode::AGILITY];
    }

    public function isPhysical(): bool
    {
        return true;
    }

    public function isPsychical(): bool
    {
        return false;
    }

    public function isCombined(): bool
    {
        return false;
    }

}