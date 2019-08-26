<?php declare(strict_types=1);

namespace DrdPlus\Person\ProfessionLevels;

use DrdPlus\Codes\Properties\PropertyCode;
use DrdPlus\BaseProperties\BaseProperty;
use DrdPlus\BaseProperties\Charisma;
use DrdPlus\BaseProperties\Intelligence;
use DrdPlus\BaseProperties\Knack;
use DrdPlus\BaseProperties\Will;
use Granam\Strict\Object\StrictObject;
use Granam\Tools\ValueDescriber;

class ProfessionLevels extends StrictObject implements \IteratorAggregate
{
    /**
     * @var ProfessionZeroLevel
     */
    private $professionZeroLevel;

    /**
     * @var ProfessionFirstLevel
     */
    private $professionFirstLevel;

    /**
     * @var ProfessionNextLevel[]
     */
    private $professionNextLevels = [];

    /**
     * @param ProfessionZeroLevel $professionZeroLevel
     * @param ProfessionFirstLevel $professionFirstLevel
     * @param array $professionNextLevels
     * @return static|ProfessionLevels
     * @throws \DrdPlus\Person\ProfessionLevels\Exceptions\MultiProfessionsAreProhibited
     */
    public static function createIt(
        ProfessionZeroLevel $professionZeroLevel,
        ProfessionFirstLevel $professionFirstLevel,
        array $professionNextLevels = []
    )
    {
        $professionLevels = new static($professionZeroLevel, $professionFirstLevel);
        foreach ($professionNextLevels as $professionNextLevel) {
            $professionLevels->addLevel($professionNextLevel);
        }

        return $professionLevels;
    }

    public function __construct(ProfessionZeroLevel $professionZeroLevel, ProfessionFirstLevel $professionFirstLevel)
    {
        $this->professionZeroLevel = $professionZeroLevel;
        $this->professionFirstLevel = $professionFirstLevel;
    }

    /**
     * All levels, achieved at any profession, unsorted
     *
     * @return array|ProfessionLevel[]
     */
    public function getProfessionNextLevels(): array
    {
        return $this->professionNextLevels;
    }

    /**
     * @return \ArrayObject|\Traversable|ProfessionLevel[]
     */
    public function getIterator(): \Traversable
    {
        return new \ArrayObject($this->getSortedProfessionLevels());
    }

    /**
     * @return array|ProfessionLevel[]
     */
    public function getSortedProfessionLevels(): array
    {
        $levels = $this->getProfessionNextLevels();
        $levels = $this->sortByLevelRank($levels);
        array_unshift($levels, $this->getFirstLevel());
        array_unshift($levels, $this->getZeroLevel());

        return $levels;
    }

    /**
     * @param array|ProfessionLevel[] $professionLevels
     * @return array
     */
    private function sortByLevelRank(array $professionLevels): array
    {
        \usort($professionLevels, function (ProfessionLevel $aLevel, ProfessionLevel $anotherLevel) {
            $difference = $aLevel->getLevelRank()->getValue() - $anotherLevel->getLevelRank()->getValue();

            return $difference <=> 0;
        });

        return $professionLevels;
    }

    public function getZeroLevel(): ProfessionZeroLevel
    {
        return $this->professionZeroLevel;
    }

    public function getFirstLevel(): ProfessionFirstLevel
    {
        return $this->professionFirstLevel;
    }

    /**
     * @param ProfessionNextLevel $newLevel
     * @throws \DrdPlus\Person\ProfessionLevels\Exceptions\MultiProfessionsAreProhibited
     * @throws \DrdPlus\Person\ProfessionLevels\Exceptions\InvalidLevelRank
     * @throws \DrdPlus\Person\ProfessionLevels\Exceptions\TooHighPrimaryPropertyIncrease
     * @throws \DrdPlus\Person\ProfessionLevels\Exceptions\TooHighSecondaryPropertyIncrease
     */
    public function addLevel(ProfessionNextLevel $newLevel)
    {
        $this->checkProhibitedMultiProfession($newLevel);
        $this->checkNewLevelSequence($newLevel);
        $this->checkPropertiesIncrementSequence($newLevel);

        $this->professionNextLevels[] = $newLevel;
        $newLevel->setProfessionLevels($this);
    }

    /**
     * @param ProfessionLevel $newLevel
     * @throws \DrdPlus\Person\ProfessionLevels\Exceptions\MultiProfessionsAreProhibited
     */
    private function checkProhibitedMultiProfession(ProfessionLevel $newLevel)
    {
        // zero level is not checked - you could be anything before heroic live. cook, bartender, beggar ...
        if ($newLevel->getProfession()->getValue() !== $this->getFirstLevel()->getProfession()->getValue()) {
            throw new Exceptions\MultiProfessionsAreProhibited(
                'New level has to be of same profession as first level.'
                . ' Expected ' . ValueDescriber::describe($this->getFirstLevel()->getProfession()->getValue())
                . ', got ' . ValueDescriber::describe($newLevel->getProfession()->getValue())
            );
        }
    }

    /**
     * @param ProfessionLevel $newLevel
     * @throws \DrdPlus\Person\ProfessionLevels\Exceptions\InvalidLevelRank
     */
    private function checkNewLevelSequence(ProfessionLevel $newLevel)
    {
        if ($newLevel->getLevelRank()->getValue() !== $this->getCurrentLevel()->getLevelRank()->getValue() + 1) {
            throw new Exceptions\InvalidLevelRank(
                'Unexpected rank of given profession level.'
                . ' Expected ' . ($this->getCurrentLevel()->getLevelRank()->getValue() + 1)
                . ', got ' . $newLevel->getLevelRank()->getValue()
            );
        }
    }

    /**
     * @param ProfessionLevel $newLevel
     * @throws \DrdPlus\Person\ProfessionLevels\Exceptions\TooHighPrimaryPropertyIncrease
     * @throws \DrdPlus\Person\ProfessionLevels\Exceptions\TooHighSecondaryPropertyIncrease
     */
    private function checkPropertiesIncrementSequence(ProfessionLevel $newLevel)
    {
        $this->checkPropertyIncrementSequence($newLevel, $newLevel->getStrengthIncrement());
        $this->checkPropertyIncrementSequence($newLevel, $newLevel->getAgilityIncrement());
        $this->checkPropertyIncrementSequence($newLevel, $newLevel->getKnackIncrement());
        $this->checkPropertyIncrementSequence($newLevel, $newLevel->getWillIncrement());
        $this->checkPropertyIncrementSequence($newLevel, $newLevel->getIntelligenceIncrement());
        $this->checkPropertyIncrementSequence($newLevel, $newLevel->getCharismaIncrement());
    }

    /**
     * @param ProfessionLevel $newLevel
     * @param BaseProperty $propertyIncrement
     * @throws \DrdPlus\Person\ProfessionLevels\Exceptions\TooHighPrimaryPropertyIncrease
     * @throws \DrdPlus\Person\ProfessionLevels\Exceptions\TooHighSecondaryPropertyIncrease
     */
    private function checkPropertyIncrementSequence(ProfessionLevel $newLevel, BaseProperty $propertyIncrement)
    {
        if ($propertyIncrement->getValue() > 0) {
            if ($newLevel->isPrimaryProperty(PropertyCode::getIt($propertyIncrement->getCode()))) {
                $this->checkPrimaryPropertyIncrementInARow($propertyIncrement);
            } else {
                $this->checkSecondaryPropertyIncrementInARow($propertyIncrement);
            }
        }
    }

    /**
     * @param BaseProperty $propertyIncrement
     * @return bool
     * @throws \DrdPlus\Person\ProfessionLevels\Exceptions\TooHighPrimaryPropertyIncrease
     */
    private function checkPrimaryPropertyIncrementInARow(BaseProperty $propertyIncrement): bool
    {
        $previousLevels = $this->getProfessionNextLevels();
        $previousNextLevelsCount = \count($previousLevels);
        // main property can be increased twice in a row
        if ($previousNextLevelsCount < 2) {
            return true;
        }
        $lastPrevious = \end($previousLevels);
        if (!$this->hasIncrementSameProperty($lastPrevious, $propertyIncrement)) {
            return true;
        }
        $lastButOnePreviousKey = \array_keys($previousLevels)[$previousNextLevelsCount - 2];
        /** @var ProfessionLevel $lastPrevious */
        $lastButOnePrevious = $previousLevels[$lastButOnePreviousKey];
        if (!$this->hasIncrementSameProperty($lastButOnePrevious, $propertyIncrement)) {
            return true;
        }
        throw new Exceptions\TooHighPrimaryPropertyIncrease(
            'Primary property can not be increased more than twice in a row'
            . ", got {$propertyIncrement->getCode()} to increase."
        );
    }

    /**
     * @param ProfessionLevel $testedProfessionLevel
     * @param BaseProperty $patternPropertyIncrement
     * @return bool
     */
    private function hasIncrementSameProperty(ProfessionLevel $testedProfessionLevel, BaseProperty $patternPropertyIncrement): bool
    {
        return $this->getSamePropertyIncrement($testedProfessionLevel, $patternPropertyIncrement)->getValue() > 0;
    }

    /**
     * @param ProfessionLevel $searchedThroughProfessionLevel
     * @param BaseProperty $patternPropertyIncrement
     * @return Charisma|Intelligence|Knack|Will
     */
    private function getSamePropertyIncrement(ProfessionLevel $searchedThroughProfessionLevel, BaseProperty $patternPropertyIncrement)
    {
        return $searchedThroughProfessionLevel->getBasePropertyIncrement(
            PropertyCode::getIt($patternPropertyIncrement->getCode())
        );
    }

    /**
     * @param BaseProperty $propertyIncrement
     * @return bool
     * @throws \DrdPlus\Person\ProfessionLevels\Exceptions\TooHighSecondaryPropertyIncrease
     */
    private function checkSecondaryPropertyIncrementInARow(BaseProperty $propertyIncrement): bool
    {
        $nextLevels = $this->getProfessionNextLevels();
        // secondary property has to be increased at least alternately
        if (\count($nextLevels) === 0) {
            return true;
        }
        if (!$this->hasIncrementSameProperty(\end($nextLevels), $propertyIncrement)) {
            return true;
        }
        throw new Exceptions\TooHighSecondaryPropertyIncrease(
            'Secondary property increase has to be at least alternately'
            . ", got {$propertyIncrement->getCode()} again to increase."
        );
    }

    public function getFirstLevelStrengthModifier(): int
    {
        return $this->getFirstLevelPropertyModifier(PropertyCode::getIt(PropertyCode::STRENGTH));
    }

    public function getFirstLevelPropertyModifier(PropertyCode $propertyCode): int
    {
        return $this->getFirstLevel()->getBasePropertyIncrement($propertyCode)->getValue();
    }

    public function getFirstLevelAgilityModifier(): int
    {
        return $this->getFirstLevelPropertyModifier(PropertyCode::getIt(PropertyCode::AGILITY));
    }

    public function getFirstLevelKnackModifier(): int
    {
        return $this->getFirstLevelPropertyModifier(PropertyCode::getIt(PropertyCode::KNACK));
    }

    public function getFirstLevelWillModifier(): int
    {
        return $this->getFirstLevelPropertyModifier(PropertyCode::getIt(PropertyCode::WILL));
    }

    public function getFirstLevelIntelligenceModifier(): int
    {
        return $this->getFirstLevelPropertyModifier(PropertyCode::getIt(PropertyCode::INTELLIGENCE));
    }

    public function getFirstLevelCharismaModifier(): int
    {
        return $this->getFirstLevelPropertyModifier(PropertyCode::getIt(PropertyCode::CHARISMA));
    }

    public function getStrengthModifierSummary(): int
    {
        return $this->getPropertyModifierSummary(PropertyCode::getIt(PropertyCode::STRENGTH));
    }

    public function getPropertyModifierSummary(PropertyCode $propertyCode): int
    {
        return \array_sum($this->getLevelsPropertyModifiers($propertyCode));
    }

    /**
     * @param PropertyCode $propertyCode
     * @return int[]|array
     */
    private function getLevelsPropertyModifiers(PropertyCode $propertyCode): array
    {
        return \array_map(
            function (ProfessionLevel $professionLevel) use ($propertyCode) {
                return $professionLevel->getBasePropertyIncrement($propertyCode)->getValue();
            },
            $this->getSortedProfessionLevels()
        );
    }

    public function getNextLevelsPropertyModifier(PropertyCode $propertyCode): int
    {
        return array_sum($this->getNextLevelsPropertyModifiers($propertyCode));
    }

    /**
     * @param PropertyCode $propertyCode
     * @return int[]|array
     */
    private function getNextLevelsPropertyModifiers(PropertyCode $propertyCode): array
    {
        return \array_map(
            function (ProfessionLevel $professionLevel) use ($propertyCode) {
                return $professionLevel->getBasePropertyIncrement($propertyCode)->getValue();
            },
            $this->getProfessionNextLevels()
        );
    }

    public function getAgilityModifierSummary(): int
    {
        return $this->getPropertyModifierSummary(PropertyCode::getIt(PropertyCode::AGILITY));
    }

    public function getKnackModifierSummary(): int
    {
        return $this->getPropertyModifierSummary(PropertyCode::getIt(PropertyCode::KNACK));
    }

    public function getWillModifierSummary(): int
    {
        return $this->getPropertyModifierSummary(PropertyCode::getIt(PropertyCode::WILL));
    }

    public function getIntelligenceModifierSummary(): int
    {
        return $this->getPropertyModifierSummary(PropertyCode::getIt(PropertyCode::INTELLIGENCE));
    }

    public function getCharismaModifierSummary(): int
    {
        return $this->getPropertyModifierSummary(PropertyCode::getIt(PropertyCode::CHARISMA));
    }

    public function getNextLevelsStrengthModifier(): int
    {
        return $this->getNextLevelsPropertyModifier(PropertyCode::getIt(PropertyCode::STRENGTH));
    }

    public function getNextLevelsAgilityModifier(): int
    {
        return $this->getNextLevelsPropertyModifier(PropertyCode::getIt(PropertyCode::AGILITY));
    }

    public function getNextLevelsKnackModifier(): int
    {
        return $this->getNextLevelsPropertyModifier(PropertyCode::getIt(PropertyCode::KNACK));
    }

    public function getNextLevelsWillModifier(): int
    {
        return $this->getNextLevelsPropertyModifier(PropertyCode::getIt(PropertyCode::WILL));
    }

    public function getNextLevelsIntelligenceModifier(): int
    {
        return $this->getNextLevelsPropertyModifier(PropertyCode::getIt(PropertyCode::INTELLIGENCE));
    }

    public function getNextLevelsCharismaModifier(): int
    {
        return $this->getNextLevelsPropertyModifier(PropertyCode::getIt(PropertyCode::CHARISMA));
    }

    public function getCurrentLevel(): ProfessionLevel
    {
        $sortedLevels = $this->getSortedProfessionLevels();

        return \end($sortedLevels);
    }

}