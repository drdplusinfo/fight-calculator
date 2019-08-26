<?php declare(strict_types=1);

namespace DrdPlus\Skills;

use DrdPlus\Person\ProfessionLevels\ProfessionLevel;
use Granam\Integer\PositiveInteger;
use Granam\Strict\Object\StrictObject;

abstract class SkillRank extends StrictObject implements PositiveInteger
{

    public const MIN_RANK_VALUE = 0; // heard about it
    public const MAX_RANK_VALUE = 3; // great knowledge

    /**
     * @var integer
     */
    private $value;

    /**
     * @param Skill $owningSkill
     * @param SkillPoint $skillPoint
     * @param PositiveInteger $requiredRankValue
     * @throws \DrdPlus\Skills\Exceptions\CanNotVerifyOwningSkill
     * @throws \DrdPlus\Skills\Exceptions\CanNotVerifyPaidSkillPoint
     * @throws \DrdPlus\Skills\Exceptions\WastedSkillPoint
     * @throws \DrdPlus\Skills\Exceptions\CanNotUseZeroSkillPointForNonZeroSkillRank
     * @throws \DrdPlus\Skills\Exceptions\UnexpectedRankValue
     */
    protected function __construct(Skill $owningSkill, SkillPoint $skillPoint, PositiveInteger $requiredRankValue)
    {
        if ($owningSkill !== $this->getSkill()) {
            throw new Exceptions\CanNotVerifyOwningSkill(
                'Skill should be already set in descendant constructor'
            );
        }
        if ($skillPoint !== $this->getSkillPoint()) {
            throw new Exceptions\CanNotVerifyPaidSkillPoint(
                'Skill point should be already set in descendant constructor'
            );
        }
        $this->checkRequiredRankValue($requiredRankValue);
        $this->checkPaymentBySkillPoint($skillPoint, $requiredRankValue);
        $this->value = $requiredRankValue->getValue();
    }

    /**
     * @param PositiveInteger $requiredRankValue
     * @throws \DrdPlus\Skills\Exceptions\UnexpectedRankValue
     */
    private function checkRequiredRankValue(PositiveInteger $requiredRankValue)
    {
        if ($requiredRankValue->getValue() < self::MIN_RANK_VALUE) {
            throw new Exceptions\UnexpectedRankValue(
                'Rank value can not be lower than ' . self::MIN_RANK_VALUE . ', got ' . $requiredRankValue
            );
        }
        if ($requiredRankValue->getValue() > self::MAX_RANK_VALUE) {
            throw new Exceptions\UnexpectedRankValue(
                'Rank value can not be greater than ' . self::MIN_RANK_VALUE . ' got ' . $requiredRankValue
            );
        }
    }

    /**
     * @param SkillPoint $skillPoint
     * @param PositiveInteger $requiredRankValue
     * @throws \DrdPlus\Skills\Exceptions\WastedSkillPoint
     * @throws \DrdPlus\Skills\Exceptions\CanNotUseZeroSkillPointForNonZeroSkillRank
     */
    private function checkPaymentBySkillPoint(SkillPoint $skillPoint, PositiveInteger $requiredRankValue)
    {
        if ($requiredRankValue->getValue() === 0) {
            if ($skillPoint->getValue() > 0) {
                throw new Exceptions\WastedSkillPoint(
                    'There is no reason to spent a non-zero skill point for zero skill rank'
                );
            }
        } else {
            assert($requiredRankValue->getValue() > 0);
            if ($skillPoint->getValue() !== 1) {
                throw new Exceptions\CanNotUseZeroSkillPointForNonZeroSkillRank(
                    'To increase a skill rank a skill point of value 1 is required, got ' . $skillPoint->getValue()
                );
            }
        }
    }

    public function getProfessionLevel(): ProfessionLevel
    {
        return $this->getSkillPoint()->getProfessionLevel();
    }

    abstract public function getSkillPoint(): SkillPoint;

    public function getValue(): int
    {
        return $this->value;
    }

    abstract public function getSkill(): Skill;

    /**
     * @return string
     */
    public function __toString()
    {
        return (string)$this->getValue();
    }
}