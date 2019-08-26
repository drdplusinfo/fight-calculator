<?php declare(strict_types=1);

namespace DrdPlus\Skills;

use DrdPlus\Person\ProfessionLevels\ProfessionLevel;
use Granam\Strict\Object\StrictObject;

abstract class Skill extends StrictObject
{
    public function __construct(ProfessionLevel $professionLevel)
    {
        $this->setSkillRank($this->createZeroSkillRank($professionLevel));
    }

    abstract protected function setSkillRank(SkillRank $skillRank);

    /**
     * @param SkillRank $skillRank
     * @throws \DrdPlus\Skills\Exceptions\UnexpectedRankValue
     * @throws \DrdPlus\Skills\Exceptions\CanNotVerifyOwningSkill
     */
    protected function addTypeVerifiedSkillRank(SkillRank $skillRank)
    {
        $this->guardSkillRankSequence($skillRank);
        $this->guardRelatedSkillOfRank($skillRank);
        $this->setSkillRank($skillRank);
    }

    /**
     * @param SkillRank $skillRank
     * @throws \DrdPlus\Skills\Exceptions\UnexpectedRankValue
     */
    private function guardSkillRankSequence(SkillRank $skillRank)
    {
        if (($this->getMaxSkillRankValue() + 1) !== $skillRank->getValue()) {
            throw new Exceptions\UnexpectedRankValue(
                'New skill rank has to follow rank sequence, expected '
                . ($this->getMaxSkillRankValue() + 1) . ", got {$skillRank->getValue()}"
            );
        }
    }

    /**
     * @param SkillRank $skillRank
     * @throws \DrdPlus\Skills\Exceptions\CanNotVerifyOwningSkill
     */
    private function guardRelatedSkillOfRank(SkillRank $skillRank)
    {
        if ($this !== $skillRank->getSkill()) {
            if (static::class !== \get_class($skillRank->getSkill())) {
                $message = 'New skill rank belongs to different skill class. Expecting ' . static::class . ', got '
                    . \get_class($skillRank->getSkill());
            } else {
                $message = 'New skill rank belongs to different instance of skill class ' . static::class;
            }
            throw new Exceptions\CanNotVerifyOwningSkill($message);
        }
    }

    private function getMaxSkillRankValue(): int
    {
        return $this->getCurrentSkillRank()->getValue();
    }

    /**
     * Gives cloned original skill ranks
     *
     * @return SkillRank[]|array
     */
    abstract public function getSkillRanks(): array;

    public function getCurrentSkillRank(): SkillRank
    {
        $skillRanks = $this->getSkillRanks();
        return \end($skillRanks);
    }

    abstract protected function createZeroSkillRank(ProfessionLevel $professionLevel): SkillRank;

    abstract public function getName(): string;

    /**
     * @return array|string[]
     */
    abstract public function getRelatedPropertyCodes(): array;

    abstract public function isPhysical(): bool;

    abstract public function isPsychical(): bool;

    abstract public function isCombined(): bool;

}