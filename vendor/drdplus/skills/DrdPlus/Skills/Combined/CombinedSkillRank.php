<?php declare(strict_types=1);

namespace DrdPlus\Skills\Combined;

use DrdPlus\Skills\Skill;
use DrdPlus\Skills\SkillPoint;
use DrdPlus\Skills\SkillRank;
use Granam\Integer\PositiveInteger;

class CombinedSkillRank extends SkillRank
{
    /**
     * @var CombinedSkill
     */
    private $combinedSkill;
    /**
     * @var CombinedSkillPoint
     */
    private $combinedSkillPoint;

    /**
     * @param CombinedSkill $combinedSkill
     * @param CombinedSkillPoint $combinedSkillPoint
     * @param PositiveInteger $requiredRankValue
     * @throws \DrdPlus\Skills\Exceptions\CanNotVerifyOwningSkill
     * @throws \DrdPlus\Skills\Exceptions\CanNotVerifyPaidSkillPoint
     * @throws \DrdPlus\Skills\Exceptions\WastedSkillPoint
     * @throws \DrdPlus\Skills\Exceptions\CanNotUseZeroSkillPointForNonZeroSkillRank
     * @throws \DrdPlus\Skills\Exceptions\UnexpectedRankValue
     */
    public function __construct(
        CombinedSkill $combinedSkill,
        CombinedSkillPoint $combinedSkillPoint,
        PositiveInteger $requiredRankValue
    )
    {
        $this->combinedSkill = $combinedSkill;
        $this->combinedSkillPoint = $combinedSkillPoint;
        parent::__construct($combinedSkill, $combinedSkillPoint, $requiredRankValue);
    }

    /**
     * @return Skill|CombinedSkill
     */
    public function getSkill(): Skill
    {
        return $this->combinedSkill;
    }

    /**
     * @return SkillPoint|CombinedSkillPoint
     */
    public function getSkillPoint(): SkillPoint
    {
        return $this->combinedSkillPoint;
    }

}