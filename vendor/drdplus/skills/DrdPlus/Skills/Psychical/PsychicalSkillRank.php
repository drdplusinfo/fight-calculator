<?php
declare(strict_types=1);

namespace DrdPlus\Skills\Psychical;

use DrdPlus\Skills\Skill;
use DrdPlus\Skills\SkillPoint;
use DrdPlus\Skills\SkillRank;
use Granam\Integer\PositiveInteger;

class PsychicalSkillRank extends SkillRank
{
    /**
     * @var PsychicalSkill
     */
    private $psychicalSkill;
    /**
     * @var PsychicalSkillPoint
     */
    private $psychicalSkillPoint;

    /**
     * @param PsychicalSkill $psychicalSkill
     * @param PsychicalSkillPoint $psychicalSkillPoint
     * @param PositiveInteger $requiredRankValue
     * @throws \DrdPlus\Skills\Exceptions\CanNotVerifyOwningSkill
     * @throws \DrdPlus\Skills\Exceptions\CanNotVerifyPaidSkillPoint
     * @throws \DrdPlus\Skills\Exceptions\WastedSkillPoint
     * @throws \DrdPlus\Skills\Exceptions\CanNotUseZeroSkillPointForNonZeroSkillRank
     * @throws \DrdPlus\Skills\Exceptions\UnexpectedRankValue
     */
    public function __construct(
        PsychicalSkill $psychicalSkill,
        PsychicalSkillPoint $psychicalSkillPoint,
        PositiveInteger $requiredRankValue
    )
    {
        $this->psychicalSkill = $psychicalSkill;
        $this->psychicalSkillPoint = $psychicalSkillPoint;
        parent::__construct($psychicalSkill, $psychicalSkillPoint, $requiredRankValue);
    }

    /**
     * @return Skill|PsychicalSkill
     */
    public function getSkill(): Skill
    {
        return $this->psychicalSkill;
    }

    /**
     * @return SkillPoint|PsychicalSkillPoint
     */
    public function getSkillPoint(): SkillPoint
    {
        return $this->psychicalSkillPoint;
    }
}