<?php
declare(strict_types = 1);

namespace DrdPlus\Background;

use DrdPlus\Codes\History\FateCode;
use DrdPlus\Background\BackgroundParts\SkillPointsFromBackground;
use DrdPlus\Background\BackgroundParts\Possession;
use DrdPlus\Background\BackgroundParts\Ancestry;
use DrdPlus\Tables\Tables;
use Granam\Integer\PositiveInteger;
use Granam\Strict\Object\StrictObject;

class Background extends StrictObject
{
    /**
     * @var BackgroundPoints
     */
    private $backgroundPoints;

    /**
     * @var Ancestry
     */
    private $ancestry;

    /**
     * @var SkillPointsFromBackground
     */
    private $skillPointsFromBackground;

    /**
     * @var Possession
     */
    private $possession;

    /**
     * @param FateCode $fateCode
     * @param PositiveInteger $forAncestrySpentBackgroundPoints
     * @param PositiveInteger $forPossessionSpentBackgroundPoints
     * @param PositiveInteger $forSkillPointsSpentBackgroundPoints
     * @param Tables $tables
     * @return Background
     * @throws \DrdPlus\Background\Exceptions\TooMuchSpentBackgroundPoints
     */
    public static function createIt(
        FateCode $fateCode,
        PositiveInteger $forAncestrySpentBackgroundPoints,
        PositiveInteger $forPossessionSpentBackgroundPoints,
        PositiveInteger $forSkillPointsSpentBackgroundPoints,
        Tables $tables
    ): Background
    {
        $availableBackgroundPoints = BackgroundPoints::getIt($fateCode, $tables);
        $ancestry = Ancestry::getIt($forAncestrySpentBackgroundPoints, $tables);
        $backgroundSkillPoints = SkillPointsFromBackground::getIt($forSkillPointsSpentBackgroundPoints, $ancestry, $tables);
        $possession = Possession::getIt($forPossessionSpentBackgroundPoints, $ancestry, $tables);

        return new static(
            $availableBackgroundPoints,
            $ancestry,
            $backgroundSkillPoints,
            $possession
        );
    }

    /**
     * @param BackgroundPoints $backgroundPoints
     * @param Ancestry $ancestry
     * @param SkillPointsFromBackground $skillPointsFromBackground
     * @param Possession $possession
     * @throws \DrdPlus\Background\Exceptions\TooMuchSpentBackgroundPoints
     */
    private function __construct(
        BackgroundPoints $backgroundPoints,
        Ancestry $ancestry,
        SkillPointsFromBackground $skillPointsFromBackground,
        Possession $possession
    )
    {
        $this->checkSumOfSpentBackgroundPoints($backgroundPoints, $ancestry, $skillPointsFromBackground, $possession);
        $this->backgroundPoints = $backgroundPoints;
        $this->ancestry = $ancestry;
        $this->skillPointsFromBackground = $skillPointsFromBackground;
        $this->possession = $possession;
    }

    /**
     * @param BackgroundPoints $backgroundPoints
     * @param Ancestry $ancestry
     * @param SkillPointsFromBackground $backgroundSkillPoints
     * @param Possession $possessionValue
     * @throws \DrdPlus\Background\Exceptions\TooMuchSpentBackgroundPoints
     */
    private function checkSumOfSpentBackgroundPoints(
        BackgroundPoints $backgroundPoints,
        Ancestry $ancestry,
        SkillPointsFromBackground $backgroundSkillPoints,
        Possession $possessionValue
    ): void
    {
        $sumOfSpentBackgroundPoints = $this->sumSpentPoints($ancestry, $backgroundSkillPoints, $possessionValue);
        if ($sumOfSpentBackgroundPoints > $backgroundPoints->getValue()) {
            throw new Exceptions\TooMuchSpentBackgroundPoints(
                "Available background points are {$backgroundPoints->getValue()},"
                . " sum of spent background points is {$sumOfSpentBackgroundPoints}"
            );
        }
    }

    /**
     * @param Ancestry $ancestry
     * @param SkillPointsFromBackground $backgroundSkillPoints
     * @param Possession $possessionValue
     * @return int
     */
    private function sumSpentPoints(
        Ancestry $ancestry,
        SkillPointsFromBackground $backgroundSkillPoints,
        Possession $possessionValue
    ): int
    {
        return $ancestry->getSpentBackgroundPoints()->getValue()
            + $backgroundSkillPoints->getSpentBackgroundPoints()->getValue()
            + $possessionValue->getSpentBackgroundPoints()->getValue();
    }

    public function getBackgroundPoints(): BackgroundPoints
    {
        return $this->backgroundPoints;
    }

    public function getAncestry(): Ancestry
    {
        return $this->ancestry;
    }

    public function getSkillPointsFromBackground(): SkillPointsFromBackground
    {
        return $this->skillPointsFromBackground;
    }

    public function getPossession(): Possession
    {
        return $this->possession;
    }

    public function getRemainingBackgroundPoints(): int
    {
        return $this->getBackgroundPoints()->getValue() - $this->sumSpentPoints(
                $this->getAncestry(), $this->getSkillPointsFromBackground(), $this->getPossession()
            );
    }

}