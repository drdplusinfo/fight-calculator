<?php declare(strict_types=1);

namespace DrdPlus\Person\ProfessionLevels;

use DrdPlus\Professions\Profession;
use DrdPlus\BaseProperties\Agility;
use DrdPlus\BaseProperties\BaseProperty;
use DrdPlus\BaseProperties\Charisma;
use DrdPlus\BaseProperties\Intelligence;
use DrdPlus\BaseProperties\Knack;
use DrdPlus\BaseProperties\Strength;
use DrdPlus\BaseProperties\Will;

class ProfessionNextLevel extends ProfessionLevel
{
    /**
     * @var ProfessionLevels
     */
    private $professionLevels;

    /**
     * @param Profession $profession
     * @param LevelRank $nextLevelRank
     * @param Strength $strengthIncrement
     * @param Agility $agilityIncrement
     * @param Knack $knackIncrement
     * @param Will $willIncrement
     * @param Intelligence $intelligenceIncrement
     * @param Charisma $charismaIncrement
     * @param \DateTimeImmutable|null $levelUpAt
     * @return ProfessionNextLevel
     * @throws \DrdPlus\Person\ProfessionLevels\Exceptions\MinimumLevelExceeded
     * @throws \DrdPlus\Person\ProfessionLevels\Exceptions\MaximumLevelExceeded
     * @throws \DrdPlus\Person\ProfessionLevels\Exceptions\InvalidNextLevelPropertiesSum
     */
    public static function createNextLevel(
        Profession $profession,
        LevelRank $nextLevelRank,
        Strength $strengthIncrement,
        Agility $agilityIncrement,
        Knack $knackIncrement,
        Will $willIncrement,
        Intelligence $intelligenceIncrement,
        Charisma $charismaIncrement,
        \DateTimeImmutable $levelUpAt = null
    ): ProfessionNextLevel
    {
        return new static(
            $profession, $nextLevelRank, $strengthIncrement, $agilityIncrement, $knackIncrement,
            $willIncrement, $intelligenceIncrement, $charismaIncrement, $levelUpAt
        );
    }

    public const MINIMUM_NEXT_LEVEL = 2;
    public const MAXIMUM_NEXT_LEVEL = 21;

    /**
     * @param LevelRank $levelRank
     * @throws \DrdPlus\Person\ProfessionLevels\Exceptions\MinimumLevelExceeded
     * @throws \DrdPlus\Person\ProfessionLevels\Exceptions\MaximumLevelExceeded
     */
    protected function checkLevelRank(LevelRank $levelRank)
    {
        if ($levelRank->getValue() < self::MINIMUM_NEXT_LEVEL) {
            throw new Exceptions\MinimumLevelExceeded(
                'Next level can not be lesser than ' . self::MINIMUM_NEXT_LEVEL . ", got {$levelRank->getValue()}"
            );
        }
        if ($levelRank->getValue() > self::MAXIMUM_NEXT_LEVEL) {
            throw new Exceptions\MaximumLevelExceeded(
                'Level can not be greater than ' . self::MAXIMUM_NEXT_LEVEL . ", got {$levelRank->getValue()}"
            );
        }
    }

    public const MAX_NEXT_LEVEL_PROPERTY_MODIFIER = 1;

    /**
     * @param BaseProperty $baseProperty
     * @param Profession $profession
     * @throws \DrdPlus\Person\ProfessionLevels\Exceptions\NegativeNextLevelProperty
     * @throws \DrdPlus\Person\ProfessionLevels\Exceptions\TooHighNextLevelPropertyIncrement
     */
    protected function checkPropertyIncrement(BaseProperty $baseProperty, Profession $profession)
    {
        if ($baseProperty->getValue() < 0) {
            throw new Exceptions\NegativeNextLevelProperty(
                "Next level property increment can not be negative, got {$baseProperty->getValue()}"
            );
        }
        if ($baseProperty->getValue() > self::MAX_NEXT_LEVEL_PROPERTY_MODIFIER) {
            throw new Exceptions\TooHighNextLevelPropertyIncrement(
                'Next level property increment has to be at most '
                . self::MAX_NEXT_LEVEL_PROPERTY_MODIFIER . ", got {$baseProperty->getValue()}"
            );
        }
    }

    public function getProfessionLevels(): ?ProfessionLevels
    {
        return $this->professionLevels;
    }

    public function setProfessionLevels(ProfessionLevels $professionLevels): void
    {
        $this->professionLevels = $professionLevels;
    }
}