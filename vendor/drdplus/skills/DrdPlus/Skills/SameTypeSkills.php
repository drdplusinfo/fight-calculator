<?php declare(strict_types=1);

namespace DrdPlus\Skills;

use DrdPlus\Person\ProfessionLevels\ProfessionLevel;
use Granam\Strict\Object\StrictObject;

abstract class SameTypeSkills extends StrictObject implements \IteratorAggregate, \Countable
{
    public function __construct(ProfessionLevel $professionLevel)
    {
        $this->populateAllSkills($professionLevel);
    }

    abstract protected function populateAllSkills(ProfessionLevel $professionLevel);

    /**
     * @return \Traversable|\ArrayIterator|Skill[]
     */
    abstract public function getIterator(): \Traversable;

    public function getFirstLevelSkillRankSummary(): int
    {
        $firstLevelSkillRankValues = \array_map(
            function (SkillRank $skillRank) {
                return $skillRank->getValue();
            },
            $this->getFirstLevelSkillRanks()
        );
        return (int)\array_sum($firstLevelSkillRankValues);
    }

    /**
     * @return array|SkillRank[]
     */
    protected function getFirstLevelSkillRanks(): array
    {
        return \array_filter(
            $this->getSkillRanks(),
            function (SkillRank $skillRank) {
                return $skillRank->getProfessionLevel()->isFirstLevel();
            }
        );
    }

    public function getNextLevelsSkillRankSummary(): int
    {
        return (int)\array_sum(
            \array_map(
                function (SkillRank $skillRank) {
                    return $skillRank->getValue();
                },
                $this->getNextLevelSkillRanks()
            )
        );
    }

    /**
     * @return array|SkillRank[]
     */
    protected function getNextLevelSkillRanks(): array
    {
        return \array_filter(
            $this->getSkillRanks(),
            function (SkillRank $skillRank) {
                return $skillRank->getProfessionLevel()->isNextLevel();
            }
        );
    }

    /**
     * @return array|SkillRank[]
     */
    private function getSkillRanks(): array
    {
        $skillRanks = [];
        foreach ($this->getIterator() as $skill) {
            foreach ($skill->getSkillRanks() as $skillRank) {
                $skillRanks[] = $skillRank;
            }
        }

        return $skillRanks;
    }

    /**
     * @param int $firstLevelPropertiesSum as a potential of skill points
     * @return int
     */
    protected function getUnusedFirstLevelSkillPointsValue(int $firstLevelPropertiesSum): int
    {
        return $firstLevelPropertiesSum - $this->getFirstLevelSkillRankSummary();
    }

    /**
     * @param int $nextLevelsPropertiesSum as a potential of skill points
     * @return int
     */
    protected function getUnusedNextLevelsSkillPointsValue(int $nextLevelsPropertiesSum): int
    {
        return $nextLevelsPropertiesSum - $this->getNextLevelsSkillRankSummary();
    }

    public function count(): int
    {
        return $this->getIterator()->count();
    }
}