<?php declare(strict_types=1);

namespace DrdPlus\Person\ProfessionLevels;

use DrdPlus\Codes\Properties\PropertyCode;
use DrdPlus\BaseProperties\Agility;
use DrdPlus\BaseProperties\BaseProperty;
use DrdPlus\BaseProperties\Charisma;
use DrdPlus\BaseProperties\Intelligence;
use DrdPlus\BaseProperties\Knack;
use DrdPlus\BaseProperties\Strength;
use DrdPlus\BaseProperties\Will;
use DrdPlus\Professions\Profession;
use Granam\Strict\Object\StrictObject;
use Granam\Tools\ValueDescriber;

abstract class ProfessionLevel extends StrictObject
{
    /**
     * @var Profession
     */
    private $profession;

    /**
     * @var LevelRank
     */
    private $levelRank;

    /**
     * @var \DateTimeImmutable
     */
    private $levelUpAt;

    /**
     * @var Strength
     */
    private $strengthIncrement;

    /**
     * @var Agility
     */
    private $agilityIncrement;

    /**
     * @var Knack
     */
    private $knackIncrement;

    /**
     * @var Will
     */
    private $willIncrement;

    /**
     * @var Intelligence
     */
    private $intelligenceIncrement;

    /**
     * @var Charisma
     */
    private $charismaIncrement;

    /**
     * @param Profession $profession
     * @param LevelRank $levelRank
     * @param Strength $strengthIncrement
     * @param Agility $agilityIncrement
     * @param Knack $knackIncrement
     * @param Will $willIncrement
     * @param Intelligence $intelligenceIncrement
     * @param Charisma $charismaIncrement
     * @param \DateTimeImmutable|null $levelUpAt
     * @throws \DrdPlus\Person\ProfessionLevels\Exceptions\InvalidNextLevelPropertiesSum
     */
    protected function __construct(
        Profession $profession,
        LevelRank $levelRank,
        Strength $strengthIncrement,
        Agility $agilityIncrement,
        Knack $knackIncrement,
        Will $willIncrement,
        Intelligence $intelligenceIncrement,
        Charisma $charismaIncrement,
        \DateTimeImmutable $levelUpAt = null
    )
    {
        $this->checkLevelRank($levelRank);
        $this->checkPropertyIncrement($strengthIncrement, $profession);
        $this->checkPropertyIncrement($agilityIncrement, $profession);
        $this->checkPropertyIncrement($knackIncrement, $profession);
        $this->checkPropertyIncrement($willIncrement, $profession);
        $this->checkPropertyIncrement($intelligenceIncrement, $profession);
        $this->checkPropertyIncrement($charismaIncrement, $profession);
        $this->checkPropertySumIncrement(
            $levelRank,
            $strengthIncrement,
            $agilityIncrement,
            $knackIncrement,
            $willIncrement,
            $intelligenceIncrement,
            $charismaIncrement
        );

        $this->profession = $profession;
        $this->levelRank = $levelRank;
        $this->strengthIncrement = $strengthIncrement;
        $this->agilityIncrement = $agilityIncrement;
        $this->knackIncrement = $knackIncrement;
        $this->willIncrement = $willIncrement;
        $this->intelligenceIncrement = $intelligenceIncrement;
        $this->charismaIncrement = $charismaIncrement;
        /** @noinspection PhpUnhandledExceptionInspection */
        $this->levelUpAt = $levelUpAt ?: new \DateTimeImmutable();
    }

    /**
     * @param LevelRank $levelRank
     */
    abstract protected function checkLevelRank(LevelRank $levelRank);

    /**
     * @param LevelRank $levelRank
     * @param Strength $strength
     * @param Agility $agility
     * @param Knack $knack
     * @param Will $will
     * @param Intelligence $intelligence
     * @param Charisma $charisma
     * @throws \DrdPlus\Person\ProfessionLevels\Exceptions\InvalidNextLevelPropertiesSum
     */
    private function checkPropertySumIncrement(
        LevelRank $levelRank,
        Strength $strength,
        Agility $agility,
        Knack $knack,
        Will $will,
        Intelligence $intelligence,
        Charisma $charisma
    )
    {
        if ($levelRank->isNextLevel()) { // note: first level properties are covered by one-by-one tests
            $sumOfProperties = $this->sumProperties($strength, $agility, $knack, $will, $intelligence, $charisma);
            if ($sumOfProperties !== $this->getExpectedSumOfNextLevelProperties()) {
                throw new Exceptions\InvalidNextLevelPropertiesSum(
                    "Sum of {$levelRank->getValue()}. level properties should be "
                    . $this->getExpectedSumOfNextLevelProperties()
                    . ', got ' . $sumOfProperties
                );
            }
        }
    }

    public const PRIMARY_PROPERTY_NEXT_LEVEL_INCREMENT_SUM = 1;
    public const SECONDARY_PROPERTY_NEXT_LEVEL_INCREMENT_SUM = 1;

    private function getExpectedSumOfNextLevelProperties(): int
    {
        return static::PRIMARY_PROPERTY_NEXT_LEVEL_INCREMENT_SUM + static::SECONDARY_PROPERTY_NEXT_LEVEL_INCREMENT_SUM;
    }

    private function sumProperties(
        Strength $strengthIncrement,
        Agility $agilityIncrement,
        Knack $knackIncrement,
        Will $willIncrement,
        Intelligence $intelligenceIncrement,
        Charisma $charismaIncrement
    ): int
    {
        return $strengthIncrement->getValue() + $agilityIncrement->getValue() + $knackIncrement->getValue()
            + $willIncrement->getValue() + $intelligenceIncrement->getValue() + $charismaIncrement->getValue();
    }

    abstract protected function checkPropertyIncrement(BaseProperty $baseProperty, Profession $profession);

    public function getLevelUpAt(): \DateTimeImmutable
    {
        return $this->levelUpAt;
    }

    public function getLevelRank(): LevelRank
    {
        return $this->levelRank;
    }

    public function isFirstLevel(): bool
    {
        return $this->getLevelRank()->getValue() === 1;
    }

    public function isNextLevel(): bool
    {
        return $this->getLevelRank()->getValue() > 1;
    }

    public function isPrimaryProperty(PropertyCode $propertyCode): bool
    {
        return static::isProfessionPrimaryProperty($this->getProfession(), $propertyCode);
    }

    protected static function isProfessionPrimaryProperty(Profession $profession, PropertyCode $propertyCode): bool
    {
        return $profession->isPrimaryProperty($propertyCode);
    }

    public function getStrengthIncrement(): Strength
    {
        return $this->strengthIncrement;
    }

    public function getAgilityIncrement(): Agility
    {
        return $this->agilityIncrement;
    }

    public function getKnackIncrement(): Knack
    {
        return $this->knackIncrement;
    }

    public function getWillIncrement(): Will
    {
        return $this->willIncrement;
    }

    public function getIntelligenceIncrement(): Intelligence
    {
        return $this->intelligenceIncrement;
    }

    public function getCharismaIncrement(): Charisma
    {
        return $this->charismaIncrement;
    }

    /**
     * @param PropertyCode $propertyCode
     * @return Agility|Charisma|Intelligence|Knack|Strength|Will|BaseProperty
     * @throws \DrdPlus\Person\ProfessionLevels\Exceptions\UnknownBaseProperty
     */
    public function getBasePropertyIncrement(PropertyCode $propertyCode): BaseProperty
    {
        switch ($propertyCode->getValue()) {
            case PropertyCode::STRENGTH :
                return $this->getStrengthIncrement();
            case PropertyCode::AGILITY :
                return $this->getAgilityIncrement();
            case PropertyCode::KNACK :
                return $this->getKnackIncrement();
            case PropertyCode::WILL :
                return $this->getWillIncrement();
            case PropertyCode::INTELLIGENCE :
                return $this->getIntelligenceIncrement();
            case PropertyCode::CHARISMA  :
                return $this->getCharismaIncrement();
            default :
                throw new Exceptions\UnknownBaseProperty(
                    'Unknown property ' . ValueDescriber::describe($propertyCode)
                );
        }
    }

    public function getProfession(): Profession
    {
        return $this->profession;
    }
}