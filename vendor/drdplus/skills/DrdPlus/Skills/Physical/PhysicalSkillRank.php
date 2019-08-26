<?php declare(strict_types=1);

declare(strict_types = 1);

namespace DrdPlus\Skills\Physical;

use DrdPlus\Skills\Skill;
use DrdPlus\Skills\SkillPoint;
use DrdPlus\Skills\SkillRank;
use Granam\Integer\PositiveInteger;

class PhysicalSkillRank extends SkillRank
{
    /**
     * @var PhysicalSkill
     */
    private $physicalSkill;
    /**
     * @var PhysicalSkillPoint
     */
    private $physicalSkillPoint;

    /**
     * @param PhysicalSkill $physicalSkill
     * @param PhysicalSkillPoint $physicalSkillPoint
     * @param PositiveInteger $requiredRankValue
     * @throws \DrdPlus\Skills\Exceptions\CanNotVerifyOwningSkill
     * @throws \DrdPlus\Skills\Exceptions\CanNotVerifyPaidSkillPoint
     * @throws \DrdPlus\Skills\Exceptions\WastedSkillPoint
     * @throws \DrdPlus\Skills\Exceptions\CanNotUseZeroSkillPointForNonZeroSkillRank
     * @throws \DrdPlus\Skills\Exceptions\UnexpectedRankValue
     */
    public function __construct(
        PhysicalSkill $physicalSkill,
        PhysicalSkillPoint $physicalSkillPoint,
        PositiveInteger $requiredRankValue
    )
    {
        $this->physicalSkill = $physicalSkill;
        $this->physicalSkillPoint = $physicalSkillPoint;
        parent::__construct($physicalSkill, $physicalSkillPoint, $requiredRankValue);
    }

    /**
     * @return Skill|PhysicalSkill
     */
    public function getSkill(): Skill
    {
        return $this->physicalSkill;
    }

    /**
     * @return SkillPoint|PhysicalSkillPoint
     */
    public function getSkillPoint(): SkillPoint
    {
        return $this->physicalSkillPoint;
    }
}