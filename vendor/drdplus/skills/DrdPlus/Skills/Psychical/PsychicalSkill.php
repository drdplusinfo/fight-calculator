<?php
declare(strict_types=1);

namespace DrdPlus\Skills\Psychical;

use DrdPlus\Codes\Properties\PropertyCode;
use DrdPlus\Person\ProfessionLevels\ProfessionLevel;
use DrdPlus\Skills\Skill;
use DrdPlus\Skills\SkillRank;
use Granam\Integer\PositiveIntegerObject;

/**
 * @method PsychicalSkillRank|SkillRank getCurrentSkillRank: SkillRank
 */
abstract class PsychicalSkill extends Skill
{

    /**
     * @var PsychicalSkillRank[]|array
     */
    private $psychicalSkillRanks = [];

    /**
     * @param ProfessionLevel $professionLevel
     * @return PsychicalSkillRank|SkillRank
     * @throws \DrdPlus\Skills\Exceptions\UnknownPaymentForSkillPoint
     * @throws \DrdPlus\Skills\Exceptions\CanNotVerifyOwningSkill
     * @throws \DrdPlus\Skills\Exceptions\CanNotVerifyPaidSkillPoint
     */
    protected function createZeroSkillRank(ProfessionLevel $professionLevel): SkillRank
    {
        return new PsychicalSkillRank(
            $this,
            PsychicalSkillPoint::createZeroSkillPoint($professionLevel),
            new PositiveIntegerObject(0)
        );
    }

    /**
     * @param PsychicalSkillPoint $psychicalSkillPoint
     * @throws \DrdPlus\Skills\Exceptions\UnexpectedRankValue
     * @throws \DrdPlus\Skills\Exceptions\CanNotVerifyOwningSkill
     * @throws \DrdPlus\Skills\Exceptions\CanNotVerifyPaidSkillPoint
     */
    public function increaseSkillRank(PsychicalSkillPoint $psychicalSkillPoint)
    {
        $this->addTypeVerifiedSkillRank(
            new PsychicalSkillRank(
                $this,
                $psychicalSkillPoint,
                new PositiveIntegerObject($this->getCurrentSkillRank()->getValue() + 1)
            )
        );
    }

    protected function setSkillRank(SkillRank $skillRank)
    {
        $this->psychicalSkillRanks[$skillRank->getValue()] = $skillRank;
    }

    /**
     * @return array|PsychicalSkillRank[]
     */
    public function getSkillRanks(): array
    {
        return $this->psychicalSkillRanks;
    }

    /**
     * @return string[]
     */
    public function getRelatedPropertyCodes(): array
    {
        return [PropertyCode::INTELLIGENCE, PropertyCode::WILL];
    }

    public function isPsychical(): bool
    {
        return true;
    }

    public function isPhysical(): bool
    {
        return false;
    }

    public function isCombined(): bool
    {
        return false;
    }
}