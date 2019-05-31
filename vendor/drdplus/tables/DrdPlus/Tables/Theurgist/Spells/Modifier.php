<?php
declare(strict_types=1);

namespace DrdPlus\Tables\Theurgist\Spells;

use DrdPlus\Codes\Theurgist\ModifierCode;
use DrdPlus\Codes\Theurgist\ModifierMutableSpellParameterCode;
use DrdPlus\Tables\Tables;
use DrdPlus\Tables\Theurgist\Spells\SpellParameters\SpellAttack;
use DrdPlus\Tables\Theurgist\Spells\SpellParameters\CastingRounds;
use DrdPlus\Tables\Theurgist\Spells\SpellParameters\Noise;
use DrdPlus\Tables\Theurgist\Spells\SpellParameters\NumberOfConditions;
use DrdPlus\Tables\Theurgist\Spells\SpellParameters\DifficultyChange;
use DrdPlus\Tables\Theurgist\Spells\SpellParameters\EpicenterShift;
use DrdPlus\Tables\Theurgist\Spells\SpellParameters\Grafts;
use DrdPlus\Tables\Theurgist\Spells\SpellParameters\Invisibility;
use DrdPlus\Tables\Theurgist\Spells\SpellParameters\NumberOfSituations;
use DrdPlus\Tables\Theurgist\Spells\SpellParameters\Partials\CastingParameter;
use DrdPlus\Tables\Theurgist\Spells\SpellParameters\NumberOfWaypoints;
use DrdPlus\Tables\Theurgist\Spells\SpellParameters\SpellPower;
use DrdPlus\Tables\Theurgist\Spells\SpellParameters\Quality;
use DrdPlus\Tables\Theurgist\Spells\SpellParameters\SpellRadius;
use DrdPlus\Tables\Theurgist\Spells\SpellParameters\Realm;
use DrdPlus\Tables\Theurgist\Spells\SpellParameters\RealmsAffection;
use DrdPlus\Tables\Theurgist\Spells\SpellParameters\Resistance;
use DrdPlus\Tables\Theurgist\Spells\SpellParameters\SpellSpeed;
use DrdPlus\Tables\Theurgist\Spells\SpellParameters\Threshold;
use Granam\Integer\Tools\ToInteger;
use Granam\Strict\Object\StrictObject;
use Granam\String\StringTools;
use Granam\Tools\ValueDescriber;

class Modifier extends StrictObject
{
    use ToFlatArrayTrait;

    /** @var ModifierCode */
    private $modifierCode;
    /** @var Tables */
    private $tables;
    /** @var array|int[] */
    private $modifierSpellParameterChanges;
    /** @var array|SpellTrait[] */
    private $modifierSpellTraits;

    /**
     * @param ModifierCode $modifierCode
     * @param Tables $tables
     * @param array|int[] $modifierSpellParameterValues spell parameters current values (delta will be calculated from them)
     * by @param array|SpellTrait[] $modifierSpellTraits
     * @throws \DrdPlus\Tables\Theurgist\Spells\Exceptions\UselessValueForUnusedSpellParameter
     * @throws \DrdPlus\Tables\Theurgist\Spells\Exceptions\UnknownModifierParameter
     * @throws \DrdPlus\Tables\Theurgist\Spells\Exceptions\InvalidValueForModifierParameter
     * @throws \DrdPlus\Tables\Theurgist\Spells\Exceptions\InvalidSpellTrait
     * @see ModifierMutableSpellParameterCode value indexed its value change
     */
    public function __construct(
        ModifierCode $modifierCode,
        Tables $tables,
        array $modifierSpellParameterValues,
        array $modifierSpellTraits
    )
    {
        $this->modifierCode = $modifierCode;
        $this->tables = $tables;
        $this->modifierSpellParameterChanges = $this->sanitizeSpellParameterChanges($modifierSpellParameterValues);
        $this->modifierSpellTraits = $this->getCheckedSpellTraits($this->toFlatArray($modifierSpellTraits));
    }

    /**
     * @param array $spellParameterValues
     * @return array
     * @throws \DrdPlus\Tables\Theurgist\Spells\Exceptions\UselessValueForUnusedSpellParameter
     * @throws \DrdPlus\Tables\Theurgist\Spells\Exceptions\InvalidValueForModifierParameter
     * @throws \DrdPlus\Tables\Theurgist\Spells\Exceptions\UnknownModifierParameter
     */
    private function sanitizeSpellParameterChanges(array $spellParameterValues): array
    {
        $sanitizedChanges = [];
        foreach (ModifierMutableSpellParameterCode::getPossibleValues() as $mutableSpellParameter) {
            if (!array_key_exists($mutableSpellParameter, $spellParameterValues)) {
                $sanitizedChanges[$mutableSpellParameter] = 0; // no change
                continue;
            }
            try {
                $sanitizedValue = ToInteger::toInteger($spellParameterValues[$mutableSpellParameter]);
            } catch (\Granam\Integer\Tools\Exceptions\Exception $exception) {
                throw new Exceptions\InvalidValueForModifierParameter(
                    'Expected integer, got ' . ValueDescriber::describe($spellParameterValues[$mutableSpellParameter])
                    . ' for ' . $mutableSpellParameter . ": '{$exception->getMessage()}'"
                );
            }
            /** like @see getBaseSpellAttack */
            $getBaseParameter = StringTools::assembleGetterForName('base_' . $mutableSpellParameter);
            /** @var CastingParameter $baseParameter */
            $baseParameter = $this->$getBaseParameter();
            if ($baseParameter === null) {
                throw new Exceptions\UselessValueForUnusedSpellParameter(
                    "Casting parameter {$mutableSpellParameter} is not used for modifier {$this->modifierCode}"
                    . ', so given spell parameter value ' . ValueDescriber::describe($spellParameterValues[$mutableSpellParameter])
                    . ' is thrown away'
                );
            }
            $parameterChange = $sanitizedValue - $baseParameter->getDefaultValue();
            $sanitizedChanges[$mutableSpellParameter] = $parameterChange;

            unset($spellParameterValues[$mutableSpellParameter]);
        }
        if (\count($spellParameterValues) > 0) { // there are some remains
            throw new Exceptions\UnknownModifierParameter(
                'Unexpected mutable spell parameter(s) [' . implode(', ', array_keys($spellParameterValues)) . ']. Expected only '
                . implode(', ', ModifierMutableSpellParameterCode::getPossibleValues())
            );
        }

        return $sanitizedChanges;
    }

    /**
     * @param array $spellTraits
     * @return array|SpellTrait[]
     * @throws \DrdPlus\Tables\Theurgist\Spells\Exceptions\InvalidSpellTrait
     */
    private function getCheckedSpellTraits(array $spellTraits): array
    {
        foreach ($spellTraits as $spellTrait) {
            if (!is_a($spellTrait, SpellTrait::class)) {
                throw new Exceptions\InvalidSpellTrait(
                    'Expected instance of ' . static::class . ', got ' . ValueDescriber::describe($spellTrait)
                );
            }
        }

        return $spellTraits;
    }

    /**
     * @return ModifierCode
     */
    public function getModifierCode(): ModifierCode
    {
        return $this->modifierCode;
    }

    public function getDifficultyChange(): DifficultyChange
    {
        $modifierParameters = [
            $this->getSpellAttackWithAddition(),
            $this->getNumberOfConditionsWithAddition(),
            $this->getEpicenterShiftWithAddition(),
            $this->getGraftsWithAddition(),
            $this->getInvisibilityWithAddition(),
            $this->getNumberOfSituationsWithAddition(),
            $this->getNumberOfWaypointsWithAddition(),
            $this->getSpellPowerWithAddition(),
            $this->getNoiseWithAddition(),
            $this->getQualityWithAddition(),
            $this->getSpellRadiusWithAddition(),
            $this->getResistanceWithAddition(),
            $this->getSpellSpeedWithAddition(),
            $this->getThresholdWithAddition(),
        ];
        $modifierParameters = array_filter(
            $modifierParameters,
            function (CastingParameter $modifierParameter = null) {
                return $modifierParameter !== null;
            }
        );
        $parametersDifficultyChangeSum = 0;
        /** @var CastingParameter $parameter */
        foreach ($modifierParameters as $parameter) {
            $parametersDifficultyChangeSum += $parameter->getAdditionByDifficulty()->getCurrentDifficultyIncrement();
        }
        $spellTraitsDifficultyChangeSum = 0;
        foreach ($this->modifierSpellTraits as $spellTrait) {
            $spellTraitsDifficultyChangeSum += $spellTrait->getDifficultyChange()->getValue();
        }
        $difficultyChange = $this->tables->getModifiersTable()->getDifficultyChange($this->getModifierCode());

        return $difficultyChange->add($parametersDifficultyChangeSum + $spellTraitsDifficultyChangeSum);
    }

    public function getCastingRounds(): CastingRounds
    {
        return $this->tables->getModifiersTable()->getCastingRounds($this->getModifierCode());
    }

    public function getRequiredRealm(): Realm
    {
        return $this->tables->getModifiersTable()->getRealm($this->getModifierCode());
    }

    public function getRealmsAffection(): ?RealmsAffection
    {
        return $this->tables->getModifiersTable()->getRealmsAffection($this->getModifierCode());
    }

    public function getBaseSpellRadius(): ?SpellRadius
    {
        return $this->tables->getModifiersTable()->getSpellRadius($this->modifierCode);
    }

    public function getSpellRadiusWithAddition(): ?SpellRadius
    {
        $baseRadius = $this->getBaseSpellRadius();
        if ($baseRadius === null) {
            return null;
        }

        return $baseRadius->getWithAddition($this->getSpellRadiusAddition());
    }

    public function getSpellRadiusAddition(): int
    {
        return $this->modifierSpellParameterChanges[ModifierMutableSpellParameterCode::SPELL_RADIUS];
    }

    public function getBaseEpicenterShift(): ?EpicenterShift
    {
        return $this->tables->getModifiersTable()->getEpicenterShift($this->modifierCode);
    }

    public function getEpicenterShiftWithAddition(): ?EpicenterShift
    {
        $baseEpicenterShift = $this->getBaseEpicenterShift();
        if ($baseEpicenterShift === null) {
            return null;
        }

        return $baseEpicenterShift->getWithAddition($this->getEpicenterShiftAddition());
    }

    public function getEpicenterShiftAddition(): int
    {
        return $this->modifierSpellParameterChanges[ModifierMutableSpellParameterCode::EPICENTER_SHIFT];
    }

    public function getBaseSpellPower(): ?SpellPower
    {
        return $this->tables->getModifiersTable()->getSpellPower($this->modifierCode);
    }

    public function getSpellPowerWithAddition(): ?SpellPower
    {
        $basePower = $this->getBaseSpellPower();
        if ($basePower === null) {
            return null;
        }

        return $basePower->getWithAddition($this->getSpellPowerAddition());
    }

    public function getSpellPowerAddition(): int
    {
        return $this->modifierSpellParameterChanges[ModifierMutableSpellParameterCode::SPELL_POWER];
    }

    public function getBaseNoise(): ?Noise
    {
        return $this->tables->getModifiersTable()->getNoise($this->modifierCode);
    }

    public function getNoiseWithAddition(): ?Noise
    {
        $baseNoise = $this->getBaseNoise();
        if ($baseNoise === null) {
            return null;
        }

        return $baseNoise->getWithAddition($this->getNoiseAddition());
    }

    public function getNoiseAddition(): int
    {
        return $this->modifierSpellParameterChanges[ModifierMutableSpellParameterCode::NOISE];
    }

    public function getBaseSpellAttack(): ?SpellAttack
    {
        return $this->tables->getModifiersTable()->getSpellAttack($this->modifierCode);
    }

    public function getSpellAttackWithAddition(): ?SpellAttack
    {
        $spellBaseAttack = $this->getBaseSpellAttack();
        if ($spellBaseAttack === null) {
            return null;
        }

        return $spellBaseAttack->getWithAddition($this->getSpellAttackAddition());
    }

    public function getSpellAttackAddition(): int
    {
        return $this->modifierSpellParameterChanges[ModifierMutableSpellParameterCode::SPELL_ATTACK];
    }

    public function getBaseGrafts(): ?Grafts
    {
        return $this->tables->getModifiersTable()->getGrafts($this->modifierCode);
    }

    public function getGraftsWithAddition(): ?Grafts
    {
        $baseGrafts = $this->getBaseGrafts();
        if ($baseGrafts === null) {
            return null;
        }

        return $baseGrafts->getWithAddition($this->getGraftsAddition());
    }

    public function getGraftsAddition(): int
    {
        return $this->modifierSpellParameterChanges[ModifierMutableSpellParameterCode::GRAFTS];
    }

    public function getBaseSpellSpeed(): ?SpellSpeed
    {
        return $this->tables->getModifiersTable()->getSpellSpeed($this->modifierCode);
    }

    public function getSpellSpeedWithAddition(): ?SpellSpeed
    {
        $baseSpellSpeed = $this->getBaseSpellSpeed();
        if ($baseSpellSpeed === null) {
            return null;
        }

        return $baseSpellSpeed->getWithAddition($this->getSpellSpeedAddition());
    }

    public function getSpellSpeedAddition(): int
    {
        return $this->modifierSpellParameterChanges[ModifierMutableSpellParameterCode::SPELL_SPEED];
    }

    public function getBaseInvisibility(): ?Invisibility
    {
        return $this->tables->getModifiersTable()->getInvisibility($this->modifierCode);
    }

    public function getInvisibilityWithAddition(): ?Invisibility
    {
        $baseInvisibility = $this->getBaseInvisibility();
        if ($baseInvisibility === null) {
            return null;
        }

        return $baseInvisibility->getWithAddition($this->getInvisibilityAddition());
    }

    public function getInvisibilityAddition(): int
    {
        return $this->modifierSpellParameterChanges[ModifierMutableSpellParameterCode::INVISIBILITY];
    }

    public function getBaseQuality(): ?Quality
    {
        return $this->tables->getModifiersTable()->getQuality($this->modifierCode);
    }

    public function getQualityWithAddition(): ?Quality
    {
        $baseQuality = $this->getBaseQuality();
        if ($baseQuality === null) {
            return null;
        }

        return $baseQuality->getWithAddition($this->getQualityAddition());
    }

    public function getQualityAddition(): int
    {
        return $this->modifierSpellParameterChanges[ModifierMutableSpellParameterCode::QUALITY];
    }

    public function getBaseNumberOfConditions(): ?NumberOfConditions
    {
        return $this->tables->getModifiersTable()->getNumberOfConditions($this->modifierCode);
    }

    public function getNumberOfConditionsWithAddition(): ?NumberOfConditions
    {
        $baseConditions = $this->getBaseNumberOfConditions();
        if ($baseConditions === null) {
            return null;
        }

        return $baseConditions->getWithAddition($this->getNumberOfConditionsAddition());
    }

    public function getNumberOfConditionsAddition(): int
    {
        return $this->modifierSpellParameterChanges[ModifierMutableSpellParameterCode::NUMBER_OF_CONDITIONS];
    }

    public function getBaseResistance(): ?Resistance
    {
        return $this->tables->getModifiersTable()->getResistance($this->modifierCode);
    }

    public function getResistanceWithAddition(): ?Resistance
    {
        $baseResistance = $this->getBaseResistance();
        if ($baseResistance === null) {
            return null;
        }

        return $baseResistance->getWithAddition($this->getResistanceAddition());
    }

    public function getResistanceAddition(): int
    {
        return $this->modifierSpellParameterChanges[ModifierMutableSpellParameterCode::RESISTANCE];
    }

    public function getBaseNumberOfSituations(): ?NumberOfSituations
    {
        return $this->tables->getModifiersTable()->getNumberOfSituations($this->modifierCode);
    }

    public function getNumberOfSituationsWithAddition(): ?NumberOfSituations
    {
        $baseNumberOfSituations = $this->getBaseNumberOfSituations();
        if ($baseNumberOfSituations === null) {
            return null;
        }

        return $baseNumberOfSituations->getWithAddition($this->getNumberOfSituationsAddition());
    }

    public function getNumberOfSituationsAddition(): int
    {
        return $this->modifierSpellParameterChanges[ModifierMutableSpellParameterCode::NUMBER_OF_SITUATIONS];
    }

    public function getBaseThreshold(): ?Threshold
    {
        return $this->tables->getModifiersTable()->getThreshold($this->modifierCode);
    }

    public function getThresholdWithAddition(): ?Threshold
    {
        $baseThreshold = $this->getBaseThreshold();
        if ($baseThreshold === null) {
            return null;
        }

        return $baseThreshold->getWithAddition($this->getThresholdAddition());
    }

    public function getThresholdAddition(): int
    {
        return $this->modifierSpellParameterChanges[ModifierMutableSpellParameterCode::THRESHOLD];
    }

    public function getBaseNumberOfWaypoints(): ?NumberOfWaypoints
    {
        return $this->tables->getModifiersTable()->getNumberOfWaypoints($this->modifierCode);
    }

    public function getNumberOfWaypointsWithAddition(): ?NumberOfWaypoints
    {
        $baseNumberOfWaypoints = $this->getBaseNumberOfWaypoints();
        if ($baseNumberOfWaypoints === null) {
            return null;
        }

        return $baseNumberOfWaypoints->getWithAddition($this->getNumberOfWaypointsAddition());
    }

    public function getNumberOfWaypointsAddition(): int
    {
        return $this->modifierSpellParameterChanges[ModifierMutableSpellParameterCode::NUMBER_OF_WAYPOINTS];
    }

    public function __toString()
    {
        return $this->getModifierCode()->getValue();
    }
}