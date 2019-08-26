<?php declare(strict_types=1);

namespace DrdPlus\Person\ProfessionLevels;

use DrdPlus\Codes\Properties\PropertyCode;
use DrdPlus\Professions\Profession;
use DrdPlus\BaseProperties\Agility;
use DrdPlus\BaseProperties\BaseProperty;
use DrdPlus\BaseProperties\Charisma;
use DrdPlus\BaseProperties\Intelligence;
use DrdPlus\BaseProperties\Knack;
use DrdPlus\BaseProperties\Strength;
use DrdPlus\BaseProperties\Will;

class ProfessionFirstLevel extends ProfessionLevel
{

    public static function createFirstLevel(
        Profession $profession,
        \DateTimeImmutable $levelUpAt = null
    ): ProfessionFirstLevel
    {
        return new static(
            $profession,
            new LevelRank(1),
            Strength::getIt(self::getBasePropertyFirstLevelModifier(PropertyCode::getIt(PropertyCode::STRENGTH), $profession)),
            Agility::getIt(self::getBasePropertyFirstLevelModifier(PropertyCode::getIt(PropertyCode::AGILITY), $profession)),
            Knack::getIt(self::getBasePropertyFirstLevelModifier(PropertyCode::getIt(PropertyCode::KNACK), $profession)),
            Will::getIt(self::getBasePropertyFirstLevelModifier(PropertyCode::getIt(PropertyCode::WILL), $profession)),
            Intelligence::getIt(self::getBasePropertyFirstLevelModifier(PropertyCode::getIt(PropertyCode::INTELLIGENCE), $profession)),
            Charisma::getIt(self::getBasePropertyFirstLevelModifier(PropertyCode::getIt(PropertyCode::CHARISMA), $profession)),
            $levelUpAt
        );
    }

    public const PRIMARY_PROPERTY_FIRST_LEVEL_MODIFIER = 1;

    private static function getBasePropertyFirstLevelModifier(PropertyCode $propertyCode, Profession $profession): int
    {
        return static::isProfessionPrimaryProperty($profession, $propertyCode)
            ? self::PRIMARY_PROPERTY_FIRST_LEVEL_MODIFIER
            : 0;
    }

    /**
     * @param LevelRank $levelRank
     * @throws \DrdPlus\Person\ProfessionLevels\Exceptions\InvalidFirstLevelRank
     */
    protected function checkLevelRank(LevelRank $levelRank)
    {
        if ($levelRank->getValue() !== 1) {
            throw new Exceptions\InvalidFirstLevelRank(
                "First level has to have level rank 1, got {$levelRank->getValue()}"
            );
        }
    }

    /**
     * It is only the increment based on first level of specific profession.
     * There are other increments like race, size etc., solved in different library.
     *
     * @param BaseProperty $baseProperty
     * @param Profession $profession
     * @throws \DrdPlus\Person\ProfessionLevels\Exceptions\InvalidFirstLevelPropertyValue
     */
    protected function checkPropertyIncrement(BaseProperty $baseProperty, Profession $profession)
    {
        $propertyFirstLevelModifier = static::getBasePropertyFirstLevelModifier(
            PropertyCode::getIt($baseProperty->getCode()),
            $profession
        );
        if ($baseProperty->getValue() !== $propertyFirstLevelModifier) {
            throw new Exceptions\InvalidFirstLevelPropertyValue(
                "On first level has to be {$baseProperty->getCode()} of value {$propertyFirstLevelModifier}"
                . ", got {$baseProperty->getValue()}"
            );
        }
    }
}