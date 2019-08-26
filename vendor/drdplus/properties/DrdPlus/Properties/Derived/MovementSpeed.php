<?php declare(strict_types = 1);

declare(strict_types=1);

namespace DrdPlus\Properties\Derived;

use DrdPlus\Codes\Environment\TerrainCode;
use DrdPlus\Codes\Properties\PropertyCode;
use DrdPlus\Codes\Transport\MovementTypeCode;
use DrdPlus\Properties\Derived\Partials\AbstractDerivedProperty;
use DrdPlus\Tables\Environments\TerrainDifficultyPercents;
use DrdPlus\Tables\Measurements\Speed\SpeedBonus;
use DrdPlus\Calculations\SumAndRound;
use DrdPlus\Tables\Properties\AthleticsInterface;
use DrdPlus\Tables\Tables;

/**
 * See PPH page 112, right column, top, @link https://pph.drdplus.info/#pohybova_rychlost
 * @method MovementSpeed add(int | \Granam\Integer\IntegerInterface $value)
 * @method MovementSpeed sub(int | \Granam\Integer\IntegerInterface $value)
 */
class MovementSpeed extends AbstractDerivedProperty
{
    public static function getIt(Speed $speed): MovementSpeed
    {
        return new static(SumAndRound::half($speed->getValue()));
    }

    public function getSpeedBonus(Tables $tables): SpeedBonus
    {
        return new SpeedBonus($this->getValue(), $tables->getSpeedTable());
    }

    /**
     * @param MovementTypeCode $movementTypeCode
     * @param TerrainCode $terrainCode
     * @param TerrainDifficultyPercents $terrainDifficultyPercents
     * @param Tables $tables
     * @param AthleticsInterface $athletics
     * @return SpeedBonus
     * @throws \DrdPlus\Tables\Body\MovementTypes\Exceptions\UnknownMovementType
     * @throws \DrdPlus\Tables\Environments\Exceptions\UnknownTerrainCode
     * @throws \DrdPlus\Tables\Environments\Exceptions\InvalidTerrainCodeFormat
     */
    public function getCurrentSpeedBonus(
        MovementTypeCode $movementTypeCode,
        TerrainCode $terrainCode,
        TerrainDifficultyPercents $terrainDifficultyPercents,
        AthleticsInterface $athletics,
        Tables $tables
    ): SpeedBonus
    {
        $speedBonusFromMovementType = $tables->getMovementTypesTable()->getSpeedBonus($movementTypeCode);
        $athleticsBonus = 0;
        if (\in_array($movementTypeCode->getValue(), [MovementTypeCode::RUN, MovementTypeCode::SPRINT], true)) {
            $athleticsBonus = $athletics->getAthleticsBonus()->getValue();
        }
        $speedMalusFromTerrain = $tables->getImpassibilityOfTerrainTable()->getSpeedMalusOnTerrain(
            $terrainCode,
            $tables->getSpeedTable(),
            $terrainDifficultyPercents
        );

        return new SpeedBonus(
            $this->getValue() + $speedBonusFromMovementType->getValue() + $athleticsBonus + $speedMalusFromTerrain->getValue(),
            $tables->getSpeedTable()
        );
    }

    public function getCode(): PropertyCode
    {
        return PropertyCode::getIt(PropertyCode::MOVEMENT_SPEED);
    }

}