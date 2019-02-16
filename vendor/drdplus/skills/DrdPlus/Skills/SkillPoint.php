<?php
declare(strict_types=1);

namespace DrdPlus\Skills;

use DrdPlus\Codes\Properties\PropertyCode;
use DrdPlus\Background\BackgroundParts\SkillPointsFromBackground;
use DrdPlus\Person\ProfessionLevels\ProfessionFirstLevel;
use DrdPlus\Person\ProfessionLevels\ProfessionLevel;
use DrdPlus\Person\ProfessionLevels\ProfessionNextLevel;
use DrdPlus\Person\ProfessionLevels\ProfessionZeroLevel;
use DrdPlus\Tables\Tables;
use Granam\Integer\IntegerInterface;
use Granam\Integer\PositiveInteger;
use Granam\Integer\Tools\Exceptions\PositiveIntegerCanNotBeNegative;
use Granam\Integer\Tools\ToInteger;
use Granam\Strict\Object\StrictObject;
use Granam\Tools\ValueDescriber;

abstract class SkillPoint extends StrictObject implements PositiveInteger
{
    /**
     * @var integer
     */
    private $value;
    /**
     * @var ProfessionZeroLevel|null
     */
    private $professionZeroLevel;
    /**
     * @var ProfessionFirstLevel|null
     */
    private $professionFirstLevel;
    /**
     * @var ProfessionNextLevel|null
     */
    private $professionNextLevel;
    /**
     * @var SkillPointsFromBackground|null
     */
    private $skillsFromBackground;
    /**
     * @var SkillPoint|null
     */
    private $firstPaidOtherSkillPoint;
    /**
     * @var SkillPoint|null
     */
    private $secondPaidOtherSkillPoint;

    abstract public function getTypeName(): string;

    /**
     * @return array|string[]
     */
    abstract public function getRelatedProperties(): array;

    /**
     * @param ProfessionLevel $professionLevel
     * @return static|SkillPoint
     * @throws \DrdPlus\Skills\Exceptions\UnknownPaymentForSkillPoint
     */
    public static function createZeroSkillPoint(ProfessionLevel $professionLevel): self
    {
        return new static(0 /* skill point value */, $professionLevel);
    }

    /**
     * @param ProfessionFirstLevel $professionFirstLevel
     * @param SkillPointsFromBackground $skillsFromBackground
     * @param Tables $tables
     * @return static|SkillPoint
     * @throws \DrdPlus\Skills\Exceptions\UnknownPaymentForSkillPoint
     */
    public static function createFromFirstLevelSkillPointsFromBackground(
        ProfessionFirstLevel $professionFirstLevel,
        SkillPointsFromBackground $skillsFromBackground,
        Tables $tables
    ): self
    {
        return new static(
            1, // skill point value
            $professionFirstLevel,
            $tables,
            $skillsFromBackground
        );
    }

    /**
     * @param ProfessionFirstLevel $professionFirstLevel
     * @param SkillPoint $firstPaidSkillPoint
     * @param SkillPoint $secondPaidSkillPoint
     * @param Tables $tables
     * @return static|SkillPoint
     * @throws \DrdPlus\Skills\Exceptions\UnknownPaymentForSkillPoint
     */
    public static function createFromFirstLevelCrossTypeSkillPoints(
        ProfessionFirstLevel $professionFirstLevel,
        SkillPoint $firstPaidSkillPoint,
        SkillPoint $secondPaidSkillPoint,
        Tables $tables
    ): self
    {
        return new static(
            1, // skill point value
            $professionFirstLevel,
            $tables,
            null /* background skill points */,
            $firstPaidSkillPoint,
            $secondPaidSkillPoint
        );
    }

    /**
     * @param ProfessionNextLevel $professionNextLevel
     * @param Tables $tables
     * @return static|SkillPoint
     * @throws \DrdPlus\Skills\Exceptions\UnknownPaymentForSkillPoint
     */
    public static function createFromNextLevelPropertyIncrease(
        ProfessionNextLevel $professionNextLevel,
        Tables $tables
    ): self
    {
        return new static(1 /* skill point value */, $professionNextLevel, $tables);
    }

    /**
     * You can pay by a level (by its property adjustment respectively) or by two another skill points
     * (for example combined and psychical for a new physical).
     *
     * @param int|IntegerInterface $skillPointValue zero or one
     * @param ProfessionLevel $professionLevel
     * @param Tables|null $tables = null
     * @param SkillPointsFromBackground|null $skillsFromBackground = null
     * @param SkillPoint $firstPaidOtherSkillPoint = null
     * @param SkillPoint $secondPaidOtherSkillPoint = null
     * @throws \DrdPlus\Skills\Exceptions\UnexpectedSkillPointValue
     * @throws \DrdPlus\Skills\Exceptions\UnknownPaymentForSkillPoint
     * @throws \Granam\Integer\Tools\Exceptions\WrongParameterType
     * @throws \Granam\Integer\Tools\Exceptions\ValueLostOnCast
     * @throws \DrdPlus\Skills\Exceptions\UnknownProfessionLevelGroup
     * @throws \DrdPlus\Skills\Exceptions\InvalidRelatedProfessionLevel
     * @throws \DrdPlus\Skills\Exceptions\EmptyFirstLevelSkillPointsFromBackground
     * @throws \DrdPlus\Skills\Exceptions\NonSensePaymentBySameType
     * @throws \DrdPlus\Skills\Exceptions\ProhibitedOriginOfPaidBySkillPoint
     * @throws \DrdPlus\Skills\Exceptions\MissingPropertyAdjustmentForPayment
     */
    protected function __construct(
        $skillPointValue,
        ProfessionLevel $professionLevel,
        Tables $tables = null,
        SkillPointsFromBackground $skillsFromBackground = null,
        SkillPoint $firstPaidOtherSkillPoint = null,
        SkillPoint $secondPaidOtherSkillPoint = null
    )
    {
        try {
            $skillPointValue = ToInteger::toPositiveInteger($skillPointValue);
        } catch (PositiveIntegerCanNotBeNegative $positiveIntegerCanNotBeNegative) {
            throw new Exceptions\UnexpectedSkillPointValue(
                'Expected zero or one, got ' . ValueDescriber::describe($skillPointValue)
            );
        }
        if ($professionLevel instanceof ProfessionZeroLevel) {
            $this->professionZeroLevel = $professionLevel;
        } elseif ($professionLevel instanceof ProfessionFirstLevel) {
            $this->professionFirstLevel = $professionLevel;
        } elseif ($professionLevel instanceof ProfessionNextLevel) {
            $this->professionNextLevel = $professionLevel;
        } else {
            throw new Exceptions\UnknownProfessionLevelGroup(
                'Expected one of ' . ProfessionZeroLevel::class . ', ' . ProfessionFirstLevel::class
                . ', ' . ProfessionNextLevel::class . ', got ' . ValueDescriber::describe($professionLevel)
            );
        }
        $this->checkSkillPointPayment(
            $skillPointValue,
            $professionLevel,
            $tables,
            $skillsFromBackground,
            $firstPaidOtherSkillPoint,
            $secondPaidOtherSkillPoint
        );
        $this->value = $skillPointValue;
        $this->skillsFromBackground = $skillsFromBackground;
        $this->firstPaidOtherSkillPoint = $firstPaidOtherSkillPoint;
        $this->secondPaidOtherSkillPoint = $secondPaidOtherSkillPoint;
    }

    /**
     * @param int $skillPointValue
     * @param ProfessionLevel $professionLevel
     * @param Tables|null $tables
     * @param SkillPointsFromBackground|null $skillsFromBackground
     * @param SkillPoint|null $firstPaidOtherSkillPoint
     * @param SkillPoint|null $secondPaidOtherSkillPoint
     * @throws \DrdPlus\Skills\Exceptions\UnknownPaymentForSkillPoint
     * @throws \DrdPlus\Skills\Exceptions\UnexpectedSkillPointValue
     * @throws \DrdPlus\Skills\Exceptions\InvalidRelatedProfessionLevel
     * @throws \DrdPlus\Skills\Exceptions\EmptyFirstLevelSkillPointsFromBackground
     * @throws \DrdPlus\Skills\Exceptions\NonSensePaymentBySameType
     * @throws \DrdPlus\Skills\Exceptions\ProhibitedOriginOfPaidBySkillPoint
     * @throws \DrdPlus\Skills\Exceptions\MissingPropertyAdjustmentForPayment
     */
    private function checkSkillPointPayment(
        $skillPointValue,
        ProfessionLevel $professionLevel,
        Tables $tables = null,
        SkillPointsFromBackground $skillsFromBackground = null,
        SkillPoint $firstPaidOtherSkillPoint = null,
        SkillPoint $secondPaidOtherSkillPoint = null
    )
    {
        if ($skillPointValue === 1) {
            if ($professionLevel instanceof ProfessionFirstLevel) {
                $this->checkFirstLevelPayment(
                    $professionLevel,
                    $tables,
                    $skillsFromBackground,
                    $firstPaidOtherSkillPoint,
                    $secondPaidOtherSkillPoint
                );
            } elseif ($professionLevel instanceof ProfessionNextLevel) {
                $this->checkNextLevelPaymentByPropertyIncrement($professionLevel);
            } else {
                throw new Exceptions\InvalidRelatedProfessionLevel(
                    'For non-zero skill point is needed one of first level or next level of a profession, got '
                    . $professionLevel->getProfession() . ' of level ' . $professionLevel->getLevelRank()
                );
            }
        } elseif ($skillPointValue === 0) {
            return; // ok
        } else {
            throw new Exceptions\UnexpectedSkillPointValue(
                'Expected zero or one, got ' . ValueDescriber::describe($skillPointValue)
            );
        }
    }

    /**
     * @param ProfessionFirstLevel $professionFirstLevel
     * @param Tables $tables
     * @param SkillPointsFromBackground|null $skillsFromBackground
     * @param SkillPoint|null $firstPaidSkillPoint
     * @param SkillPoint|null $secondPaidSkillPoint
     * @return bool
     * @throws \DrdPlus\Skills\Exceptions\EmptyFirstLevelSkillPointsFromBackground
     * @throws \DrdPlus\Skills\Exceptions\UnknownPaymentForSkillPoint
     * @throws \DrdPlus\Skills\Exceptions\NonSensePaymentBySameType
     * @throws \DrdPlus\Skills\Exceptions\ProhibitedOriginOfPaidBySkillPoint
     */
    private function checkFirstLevelPayment(
        ProfessionFirstLevel $professionFirstLevel,
        Tables $tables,
        SkillPointsFromBackground $skillsFromBackground = null,
        SkillPoint $firstPaidSkillPoint = null,
        SkillPoint $secondPaidSkillPoint = null
    ): bool
    {
        if ($skillsFromBackground) {
            return $this->checkPayByFirstLevelSkillPointsFromBackground($professionFirstLevel, $tables, $skillsFromBackground);
        }
        if ($firstPaidSkillPoint && $secondPaidSkillPoint) {
            return $this->checkPayByOtherFirstLevelSkillPoints($firstPaidSkillPoint, $secondPaidSkillPoint);
        }

        throw new Exceptions\UnknownPaymentForSkillPoint(
            'Unknown payment for skill point on level '
            . $professionFirstLevel->getLevelRank()->getValue()
            . ' of profession ' . $professionFirstLevel->getProfession()->getValue()
        );
    }

    /**
     * @param ProfessionFirstLevel $professionFirstLevel
     * @param Tables $tables
     * @param SkillPointsFromBackground $skillsFromBackground
     * @return bool
     * @throws \DrdPlus\Skills\Exceptions\EmptyFirstLevelSkillPointsFromBackground
     */
    private function checkPayByFirstLevelSkillPointsFromBackground(
        ProfessionFirstLevel $professionFirstLevel,
        Tables $tables,
        SkillPointsFromBackground $skillsFromBackground
    ): bool
    {
        $relatedProperties = $this->sortAlphabetically($this->getRelatedProperties());
        $firstLevelSkillPoints = 0;
        switch ($relatedProperties) {
            case $this->sortAlphabetically([PropertyCode::STRENGTH, PropertyCode::AGILITY]) :
                $firstLevelSkillPoints = $skillsFromBackground->getPhysicalSkillPoints(
                    $professionFirstLevel->getProfession(),
                    $tables
                );
                break;
            case $this->sortAlphabetically([PropertyCode::WILL, PropertyCode::INTELLIGENCE]) :
                $firstLevelSkillPoints = $skillsFromBackground->getPsychicalSkillPoints(
                    $professionFirstLevel->getProfession(),
                    $tables
                );
                break;
            case $this->sortAlphabetically([PropertyCode::KNACK, PropertyCode::CHARISMA]) :
                $firstLevelSkillPoints = $skillsFromBackground->getCombinedSkillPoints(
                    $professionFirstLevel->getProfession(),
                    $tables
                );
                break;
        }
        if ($firstLevelSkillPoints < 1) {
            throw new Exceptions\EmptyFirstLevelSkillPointsFromBackground(
                'First level skill point has to come from the background.'
                . ' No skill point for properties ' . implode(',', $relatedProperties) . ' is available.'
            );
        }

        return true;
    }

    /**
     * @param SkillPoint $firstPaidBySkillPoint
     * @param SkillPoint $secondPaidBySkillPoint
     * @return bool
     * @throws \DrdPlus\Skills\Exceptions\NonSensePaymentBySameType
     * @throws \DrdPlus\Skills\Exceptions\ProhibitedOriginOfPaidBySkillPoint
     */
    private function checkPayByOtherFirstLevelSkillPoints(
        SkillPoint $firstPaidBySkillPoint,
        SkillPoint $secondPaidBySkillPoint
    ): bool
    {
        foreach ([$firstPaidBySkillPoint, $secondPaidBySkillPoint] as $paidBySkillPoint) {
            if (!$paidBySkillPoint->isPaidByFirstLevelSkillPointsFromBackground()) {
                $message = 'Skill point to-pay-with has to origin from first level background skills.';
                if ($paidBySkillPoint->isPaidByNextLevelPropertyIncrease()) {
                    $message .= ' Next level skill point is not allowed to trade.';
                }
                if ($paidBySkillPoint->isPaidByOtherSkillPoints()) {
                    $message .= ' There is no sense to trade first level skill point multiple times.';
                }
                throw new Exceptions\ProhibitedOriginOfPaidBySkillPoint($message);
            }
            if ($paidBySkillPoint->getTypeName() === $this->getTypeName()) {
                throw new Exceptions\NonSensePaymentBySameType(
                    "There is no sense to pay for skill point by another one of the very same type ({$this->getTypeName()})."
                    . ' Got paid skill point from level ' . $paidBySkillPoint->getProfessionLevel()->getLevelRank()
                    . ' of profession ' . $paidBySkillPoint->getProfessionLevel()->getProfession()->getValue() . '.'
                );
            }
        }

        return true;
    }

    /**
     * @param ProfessionNextLevel $professionNextLevel
     * @throws \DrdPlus\Skills\Exceptions\MissingPropertyAdjustmentForPayment
     */
    private function checkNextLevelPaymentByPropertyIncrement(ProfessionNextLevel $professionNextLevel)
    {
        $relatedProperties = $this->sortAlphabetically($this->getRelatedProperties());
        $missingPropertyAdjustment = false;
        switch ($relatedProperties) {
            case $this->sortAlphabetically([PropertyCode::STRENGTH, PropertyCode::AGILITY]) :
                $missingPropertyAdjustment = $professionNextLevel->getStrengthIncrement()->getValue() === 0
                    && $professionNextLevel->getAgilityIncrement()->getValue() === 0;
                break;
            case $this->sortAlphabetically([PropertyCode::WILL, PropertyCode::INTELLIGENCE]) :
                $missingPropertyAdjustment = $professionNextLevel->getWillIncrement()->getValue() === 0
                    && $professionNextLevel->getIntelligenceIncrement()->getValue() === 0;
                break;
            case $this->sortAlphabetically([PropertyCode::KNACK, PropertyCode::CHARISMA]) :
                $missingPropertyAdjustment = $professionNextLevel->getKnackIncrement()->getValue() === 0
                    && $professionNextLevel->getCharismaIncrement()->getValue() === 0;
                break;
        }

        if ($missingPropertyAdjustment) {
            throw new Exceptions\MissingPropertyAdjustmentForPayment(
                'The profession ' . $professionNextLevel->getProfession()->getValue()
                . ' of level ' . $professionNextLevel->getLevelRank()->getValue()
                . ' has to have adjusted either ' . implode(' or ', $this->getRelatedProperties())
            );
        }
    }

    private function sortAlphabetically(array $array): array
    {
        \sort($array);

        return $array;
    }

    public function getValue(): int
    {
        return $this->value;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return (string)$this->getValue();
    }

    public function getProfessionZeroLevel(): ?ProfessionZeroLevel
    {
        return $this->professionZeroLevel;
    }

    public function getProfessionFirstLevel(): ?ProfessionFirstLevel
    {
        return $this->professionFirstLevel;
    }

    public function getProfessionNextLevel(): ?ProfessionNextLevel
    {
        return $this->professionNextLevel;
    }

    /**
     * @return ProfessionLevel|ProfessionZeroLevel|ProfessionFirstLevel|ProfessionNextLevel
     */
    public function getProfessionLevel(): ProfessionLevel
    {
        if ($this->getProfessionZeroLevel()) {
            return $this->getProfessionZeroLevel();
        }
        if ($this->getProfessionFirstLevel()) {
            return $this->getProfessionFirstLevel();
        }
        assert($this->getProfessionNextLevel() !== null);

        return $this->getProfessionNextLevel();
    }

    public function getSkillPointsFromBackground(): ?SkillPointsFromBackground
    {
        return $this->skillsFromBackground;
    }

    public function getFirstPaidOtherSkillPoint(): ?SkillPoint
    {
        return $this->firstPaidOtherSkillPoint;
    }

    public function getSecondPaidOtherSkillPoint(): ?SkillPoint
    {
        return $this->secondPaidOtherSkillPoint;
    }

    public function isPaidByFirstLevelSkillPointsFromBackground(): bool
    {
        return $this->getSkillPointsFromBackground() !== null;
    }

    public function isPaidByOtherSkillPoints(): bool
    {
        return $this->getFirstPaidOtherSkillPoint() && $this->getSecondPaidOtherSkillPoint();
    }

    public function isPaidByNextLevelPropertyIncrease(): bool
    {
        return !$this->isPaidByFirstLevelSkillPointsFromBackground()
            && !$this->isPaidByOtherSkillPoints()
            && $this->getProfessionNextLevel() !== null;
    }

}