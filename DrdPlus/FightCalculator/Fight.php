<?php
namespace DrdPlus\FightCalculator;

use DrdPlus\Armourer\Armourer;
use DrdPlus\Background\BackgroundParts\Ancestry;
use DrdPlus\Background\BackgroundParts\SkillPointsFromBackground;
use DrdPlus\CalculatorSkeleton\CurrentValues;
use DrdPlus\CalculatorSkeleton\History;
use DrdPlus\Codes\Armaments\BodyArmorCode;
use DrdPlus\Codes\Armaments\HelmCode;
use DrdPlus\Codes\Armaments\MeleeWeaponCode;
use DrdPlus\Codes\Armaments\ShieldCode;
use DrdPlus\Codes\Armaments\WeaponCategoryCode;
use DrdPlus\Codes\Armaments\WeaponlikeCode;
use DrdPlus\Codes\ItemHoldingCode;
use DrdPlus\Codes\ProfessionCode;
use DrdPlus\Codes\Skills\CombinedSkillCode;
use DrdPlus\Codes\Skills\PhysicalSkillCode;
use DrdPlus\Codes\Skills\PsychicalSkillCode;
use DrdPlus\Codes\Skills\SkillCode;
use DrdPlus\Codes\Units\DistanceUnitCode;
use DrdPlus\CombatActions\CombatActions;
use DrdPlus\FightProperties\BodyPropertiesForFight;
use DrdPlus\FightProperties\FightProperties;
use DrdPlus\Health\Health;
use DrdPlus\Health\Inflictions\Glared;
use DrdPlus\Person\ProfessionLevels\ProfessionFirstLevel;
use DrdPlus\Person\ProfessionLevels\ProfessionLevels;
use DrdPlus\Person\ProfessionLevels\ProfessionZeroLevel;
use DrdPlus\Professions\Commoner;
use DrdPlus\Professions\Profession;
use DrdPlus\BaseProperties\Agility;
use DrdPlus\BaseProperties\Charisma;
use DrdPlus\BaseProperties\Intelligence;
use DrdPlus\BaseProperties\Knack;
use DrdPlus\BaseProperties\Strength;
use DrdPlus\BaseProperties\Will;
use DrdPlus\Properties\Body\Height;
use DrdPlus\Properties\Body\HeightInCm;
use DrdPlus\Properties\Body\Size;
use DrdPlus\Properties\Combat\EncounterRange;
use DrdPlus\Properties\Combat\MaximalRange;
use DrdPlus\Properties\Derived\Speed;
use DrdPlus\Skills\Combined\CombinedSkill;
use DrdPlus\Skills\Combined\CombinedSkillPoint;
use DrdPlus\Skills\Combined\CombinedSkills;
use DrdPlus\Skills\Physical\PhysicalSkill;
use DrdPlus\Skills\Physical\PhysicalSkillPoint;
use DrdPlus\Skills\Physical\PhysicalSkills;
use DrdPlus\Skills\Psychical\PsychicalSkill;
use DrdPlus\Skills\Psychical\PsychicalSkillPoint;
use DrdPlus\Skills\Psychical\PsychicalSkills;
use DrdPlus\Skills\Skills;
use DrdPlus\Tables\Combat\Attacks\AttackNumberByContinuousDistanceTable;
use DrdPlus\Tables\Measurements\Distance\Distance;
use DrdPlus\Tables\Tables;
use Granam\Integer\PositiveIntegerObject;
use Granam\Strict\Object\StrictObject;
use Granam\String\StringTools;

class Fight extends StrictObject
{
    use UsingSkills;

    /** @var PreviousArmamentsWithSkills */
    private $previousArmamentsWithSkills;
    /** @var CurrentArmamentsWithSkills */
    private $currentArmamentsWithSkills;
    /** @var CurrentProperties */
    private $currentProperties;
    /** @var Tables */
    private $tables;
    /** @var Armourer */
    private $armourer;
    /** @var PreviousProperties */
    private $previousProperties;
    /** @var CurrentValues */
    private $currentValues;
    /** @var History */
    private $history;

    public function __construct(
        CurrentArmamentsWithSkills $currentArmamentsWithSkills,
        CurrentProperties $currentProperties,
        CurrentValues $currentValues,
        PreviousArmamentsWithSkills $previousArmamentsWithSkills,
        PreviousProperties $previousProperties,
        History $history,
        Armourer $armourer,
        Tables $tables
    )
    {
        $this->currentArmamentsWithSkills = $currentArmamentsWithSkills;
        $this->currentProperties = $currentProperties;
        $this->currentValues = $currentValues;
        $this->previousArmamentsWithSkills = $previousArmamentsWithSkills;
        $this->previousProperties = $previousProperties;
        $this->armourer = $armourer;
        $this->tables = $tables;
        $this->history = $history;
    }

    public function getCurrentMeleeShieldFightProperties(): FightProperties
    {
        return $this->getCurrentFightProperties(
            $this->currentArmamentsWithSkills->getCurrentShieldForMelee(),
            $this->currentArmamentsWithSkills->getCurrentMeleeShieldHolding(),
            PhysicalSkillCode::getIt(PhysicalSkillCode::FIGHT_WITH_SHIELDS),
            $this->currentArmamentsWithSkills->getCurrentFightWithShieldsSkillRank(),
            $this->currentArmamentsWithSkills->getCurrentShieldForMelee()
        );
    }

    public function getCurrentRangedWeaponFightProperties(): FightProperties
    {
        return $this->getCurrentFightProperties(
            $this->currentArmamentsWithSkills->getCurrentRangedWeapon(),
            $this->currentArmamentsWithSkills->getCurrentRangedWeaponHolding(),
            $this->currentArmamentsWithSkills->getCurrentRangedFightSkillCode(),
            $this->currentArmamentsWithSkills->getCurrentRangedFightSkillRank(),
            $this->currentArmamentsWithSkills->getCurrentShieldForRanged()
        );
    }

    public function getCurrentGenericFightProperties(): FightProperties
    {
        return $this->getCurrentFightProperties(
            MeleeWeaponCode::getIt(MeleeWeaponCode::HAND),
            ItemHoldingCode::getIt(ItemHoldingCode::MAIN_HAND),
            PsychicalSkillCode::getIt(PsychicalSkillCode::ASTRONOMY), // whatever
            0, // zero skill rank
            ShieldCode::getIt(ShieldCode::WITHOUT_SHIELD)
        );
    }

    private function getCurrentFightProperties(
        WeaponlikeCode $weaponlikeCode,
        ItemHoldingCode $weaponHoldingCode,
        SkillCode $weaponSkillCode,
        int $skillRankWithWeapon,
        ShieldCode $usedShield
    ): FightProperties
    {
        return $this->getFightProperties(
            $this->currentProperties->getCurrentStrength(),
            $this->currentProperties->getCurrentAgility(),
            $this->currentProperties->getCurrentKnack(),
            $this->currentProperties->getCurrentWill(),
            $this->currentProperties->getCurrentIntelligence(),
            $this->currentProperties->getCurrentCharisma(),
            $this->currentProperties->getCurrentSize(),
            $this->currentProperties->getCurrentHeightInCm(),
            $weaponlikeCode,
            $weaponHoldingCode,
            $weaponSkillCode,
            $skillRankWithWeapon,
            $usedShield,
            $this->currentArmamentsWithSkills->getCurrentShieldUsageSkillRank(),
            $this->currentArmamentsWithSkills->getCurrentBodyArmor(),
            $this->currentArmamentsWithSkills->getCurrentHelm(),
            $this->currentArmamentsWithSkills->getCurrentArmorSkillRank(),
            $this->currentArmamentsWithSkills->getCurrentProfessionCode(),
            $this->currentArmamentsWithSkills->getCurrentOnHorseback(),
            $this->currentArmamentsWithSkills->getCurrentRidingSkillRank(),
            $this->currentArmamentsWithSkills->getCurrentFightingFreeWillAnimal(),
            $this->currentArmamentsWithSkills->getCurrentZoologySkillRank()
        );
    }

    private function getFightProperties(
        Strength $strength,
        Agility $agility,
        Knack $knack,
        Will $will,
        Intelligence $intelligence,
        Charisma $charisma,
        Size $size,
        HeightInCm $heightInCm,
        WeaponlikeCode $weaponlikeCode,
        ItemHoldingCode $weaponlikeHolding,
        SkillCode $skillWithWeapon,
        int $skillRankWithWeapon,
        ShieldCode $usedShield,
        int $shieldUsageSkillRank,
        BodyArmorCode $wornBodyArmorCode,
        HelmCode $wornHelmCode,
        int $skillRankWithArmor,
        ProfessionCode $professionCode,
        bool $fightsOnHorseback,
        int $ridingSkillRank,
        bool $fightsFreeWillAnimal,
        int $zoologySkillRank
    ): FightProperties
    {
        $bodyPropertiesForFight = new BodyPropertiesForFight(
            $strength,
            $agility,
            $knack,
            $will,
            $intelligence,
            $charisma,
            $size,
            $height = Height::getIt($heightInCm, $this->tables),
            Speed::getIt($strength, $agility, $height)
        );
        $emptyCombatActions = new CombatActions([], $this->tables);
        $skills = $this->createSkills(
            $skillWithWeapon,
            $skillRankWithWeapon,
            $professionCode,
            $skillRankWithArmor,
            $shieldUsageSkillRank,
            $ridingSkillRank,
            $zoologySkillRank
        );
        return new FightProperties(
            $bodyPropertiesForFight,
            $emptyCombatActions,
            $skills,
            $wornBodyArmorCode,
            $wornHelmCode,
            $professionCode,
            $this->tables,
            $this->armourer,
            $weaponlikeCode,
            $weaponlikeHolding,
            false, // does not fight with two weapons
            $usedShield,
            false, // enemy is not faster
            Glared::createWithoutGlare(new Health()),
            $fightsOnHorseback,
            $fightsFreeWillAnimal
        );
    }

    private function createSkills(
        SkillCode $skillWithWeapon,
        int $skillRankWithWeapon,
        ProfessionCode $professionCode,
        int $skillRankWithArmor,
        int $shieldUsageSkillRank,
        int $ridingSkillRank,
        int $zoologySkillRank
    ): Skills
    {
        $professionFirstLevel = ProfessionFirstLevel::createFirstLevel(Profession::getItByCode($professionCode));
        $skills = Skills::createSkills(
            new ProfessionLevels(
                ProfessionZeroLevel::createZeroLevel(Commoner::getIt()),
                $professionFirstLevel
            ),
            $skillPointsFromBackground = SkillPointsFromBackground::getIt(
                new PositiveIntegerObject(8), // just a maximum
                Ancestry::getIt(new PositiveIntegerObject(8), $this->tables),
                $this->tables
            ),
            new PhysicalSkills($professionFirstLevel),
            new PsychicalSkills($professionFirstLevel),
            new CombinedSkills($professionFirstLevel),
            $this->tables
        );
        $this->addSkillWithWeapon(
            $skillWithWeapon,
            $skillRankWithWeapon,
            $skills,
            $professionFirstLevel,
            $skillPointsFromBackground
        );

        if ($skillRankWithArmor > 0) {
            $physicalSkillPoint = PhysicalSkillPoint::createFromFirstLevelSkillPointsFromBackground(
                $professionFirstLevel,
                $skillPointsFromBackground,
                $this->tables
            );
            $armorWearing = $skills->getPhysicalSkills()->getArmorWearing();
            while ($skillRankWithArmor-- > 0) {
                $armorWearing->increaseSkillRank($physicalSkillPoint);
            }
        }
        if ($shieldUsageSkillRank > 0) {
            $physicalSkillPoint = PhysicalSkillPoint::createFromFirstLevelSkillPointsFromBackground(
                $professionFirstLevel,
                $skillPointsFromBackground,
                $this->tables
            );
            $shieldUsage = $skills->getPhysicalSkills()->getShieldUsage();
            while ($shieldUsageSkillRank-- > 0) {
                $shieldUsage->increaseSkillRank($physicalSkillPoint);
            }
        }
        if ($ridingSkillRank > 0) {
            $physicalSkillPoint = PhysicalSkillPoint::createFromFirstLevelSkillPointsFromBackground(
                $professionFirstLevel,
                $skillPointsFromBackground,
                $this->tables
            );
            $riding = $skills->getPhysicalSkills()->getRiding();
            while ($ridingSkillRank-- > 0) {
                $riding->increaseSkillRank($physicalSkillPoint);
            }
        }
        if ($zoologySkillRank > 0) {
            $psychicalSkillPoint = PsychicalSkillPoint::createFromFirstLevelSkillPointsFromBackground(
                $professionFirstLevel,
                $skillPointsFromBackground,
                $this->tables
            );
            $zoology = $skills->getPsychicalSkills()->getZoology();
            while ($zoologySkillRank-- > 0) {
                $zoology->increaseSkillRank($psychicalSkillPoint);
            }
        }

        return $skills;
    }

    private function addSkillWithWeapon(
        SkillCode $skillWithWeapon,
        int $skillRankWithWeapon,
        Skills $skills,
        ProfessionFirstLevel $professionFirstLevel,
        SkillPointsFromBackground $skillPointsFromBackground
    ): void
    {
        if ($skillRankWithWeapon === 0) {
            return;
        }
        if (\in_array($skillWithWeapon->getValue(), PhysicalSkillCode::getPossibleValues(), true)) {
            $getSkill = StringTools::assembleGetterForName($skillWithWeapon->getValue());
            /** @var PhysicalSkill $skill */
            $skill = $skills->getPhysicalSkills()->$getSkill();
            $physicalSkillPoint = PhysicalSkillPoint::createFromFirstLevelSkillPointsFromBackground(
                $professionFirstLevel,
                $skillPointsFromBackground,
                $this->tables
            );
            while ($skillRankWithWeapon-- > 0) {
                $skill->increaseSkillRank($physicalSkillPoint);
            }

            return;
        }
        if (\in_array($skillWithWeapon->getValue(), PsychicalSkillCode::getPossibleValues(), true)) {
            $getSkill = StringTools::assembleGetterForName($skillWithWeapon->getValue());
            /** @var PsychicalSkill $skill */
            $skill = $skills->getPsychicalSkills()->$getSkill();
            $physicalSkillPoint = PsychicalSkillPoint::createFromFirstLevelSkillPointsFromBackground(
                $professionFirstLevel,
                $skillPointsFromBackground,
                $this->tables
            );
            while ($skillRankWithWeapon-- > 0) {
                $skill->increaseSkillRank($physicalSkillPoint);
            }

            return;
        }
        if (\in_array($skillWithWeapon->getValue(), CombinedSkillCode::getPossibleValues(), true)) {
            $getSkill = StringTools::assembleGetterForName($skillWithWeapon->getValue());
            /** @var CombinedSkill $skill */
            $skill = $skills->getCombinedSkills()->$getSkill();
            $combinedSkillPoint = CombinedSkillPoint::createFromFirstLevelSkillPointsFromBackground(
                $professionFirstLevel,
                $skillPointsFromBackground,
                $this->tables
            );
            while ($skillRankWithWeapon-- > 0) {
                $skill->increaseSkillRank($combinedSkillPoint);
            }

            return;
        }
        throw new Exceptions\WeaponSkillWithoutKnownSkillGroup(
            "Given skill with a weapon '{$skillWithWeapon}' does not belong to any skill group"
        );
    }

    public function getPreviousRangedWeaponFightProperties(): FightProperties
    {
        return $this->getPreviousFightProperties(
            $this->previousArmamentsWithSkills->getPreviousRangedWeapon(),
            $this->previousArmamentsWithSkills->getPreviousRangedWeaponHolding(),
            $this->previousArmamentsWithSkills->getPreviousRangedFightSkillCode(),
            $this->previousArmamentsWithSkills->getPreviousRangedFightSkillRank(),
            $this->previousArmamentsWithSkills->getPreviousShield()
        );
    }

    private function getPreviousFightProperties(
        WeaponlikeCode $weaponlikeCode,
        ItemHoldingCode $weaponHoldingCode,
        SkillCode $fightWithWeaponSkillCode,
        int $skillRankWithWeapon,
        ShieldCode $previousShield
    ): FightProperties
    {
        return $this->getFightProperties(
            $this->previousProperties->getPreviousStrength(),
            $this->previousProperties->getPreviousAgility(),
            $this->previousProperties->getPreviousKnack(),
            $this->previousProperties->getPreviousWill(),
            $this->previousProperties->getPreviousIntelligence(),
            $this->previousProperties->getPreviousCharisma(),
            $this->previousProperties->getPreviousSize(),
            $this->previousProperties->getPreviousHeightInCm(),
            $weaponlikeCode,
            $weaponHoldingCode,
            $fightWithWeaponSkillCode,
            $skillRankWithWeapon,
            $previousShield,
            $this->previousArmamentsWithSkills->getPreviousShieldUsageSkillRank(),
            $this->previousArmamentsWithSkills->getPreviousBodyArmor(),
            $this->previousArmamentsWithSkills->getPreviousHelm(),
            $this->previousArmamentsWithSkills->getPreviousArmorSkillRank(),
            $this->previousArmamentsWithSkills->getPreviousProfessionCode(),
            $this->previousArmamentsWithSkills->getPreviousOnHorseback(),
            $this->previousArmamentsWithSkills->getPreviousRidingSkillRank(),
            $this->previousArmamentsWithSkills->getPreviousFightingFreeWillAnimal(),
            $this->previousArmamentsWithSkills->getPreviousZoologySkillRank()
        );
    }

    public function getPreviousMeleeShieldFightProperties(): FightProperties
    {
        return $this->getPreviousFightProperties(
            $this->previousArmamentsWithSkills->getPreviousShield(),
            $this->previousArmamentsWithSkills->getPreviousMeleeShieldHolding(),
            PhysicalSkillCode::getIt(PhysicalSkillCode::FIGHT_WITH_SHIELDS),
            $this->previousArmamentsWithSkills->getPreviousFightWithShieldsSkillRank(),
            $this->previousArmamentsWithSkills->getPreviousShield()
        );
    }

    public function getPreviousRangedShieldFightProperties(): FightProperties
    {
        return $this->getPreviousFightProperties(
            $this->previousArmamentsWithSkills->getPreviousShield(),
            $this->previousArmamentsWithSkills->getPreviousRangedShieldHolding(),
            PhysicalSkillCode::getIt(PhysicalSkillCode::FIGHT_WITH_SHIELDS),
            $this->previousArmamentsWithSkills->getPreviousFightWithShieldsSkillRank(),
            $this->previousArmamentsWithSkills->getPreviousShield()
        );
    }

    public function getPreviousGenericFightProperties(): FightProperties
    {
        return $this->getPreviousFightProperties(
            MeleeWeaponCode::getIt(MeleeWeaponCode::HAND),
            ItemHoldingCode::getIt(ItemHoldingCode::MAIN_HAND),
            PsychicalSkillCode::getIt(PsychicalSkillCode::ASTRONOMY), // whatever
            0, // zero skill rank
            ShieldCode::getIt(ShieldCode::WITHOUT_SHIELD)
        );
    }

    public function getPreviousMeleeWeaponFightProperties(): FightProperties
    {
        return $this->getPreviousFightProperties(
            $this->previousArmamentsWithSkills->getPreviousMeleeWeapon(),
            $this->previousArmamentsWithSkills->getPreviousMeleeWeaponHolding(),
            $this->getPreviousMeleeSkillCode(),
            $this->getPreviousMeleeSkillRank(),
            $this->previousArmamentsWithSkills->getPreviousShield()
        );
    }

    public function getCurrentRangedShieldFightProperties(): FightProperties
    {
        return $this->getCurrentFightProperties(
            $this->currentArmamentsWithSkills->getCurrentShieldForRanged(),
            $this->currentArmamentsWithSkills->getCurrentRangedShieldHolding(),
            PhysicalSkillCode::getIt(PhysicalSkillCode::FIGHT_WITH_SHIELDS),
            $this->currentArmamentsWithSkills->getCurrentFightWithShieldsSkillRank(),
            $this->currentArmamentsWithSkills->getCurrentShieldForRanged()
        );
    }

    public function getCurrentMeleeWeaponFightProperties(): FightProperties
    {
        return $this->getCurrentFightProperties(
            $this->currentArmamentsWithSkills->getCurrentMeleeWeapon(),
            $this->currentArmamentsWithSkills->getCurrentMeleeWeaponHolding(),
            $this->currentArmamentsWithSkills->getCurrentMeleeFightSkillCode(),
            $this->currentArmamentsWithSkills->getCurrentMeleeFightSkillRank(),
            $this->currentArmamentsWithSkills->getCurrentShieldForMelee()
        );
    }

    private function getPreviousMeleeSkillCode(): SkillCode
    {
        return $this->getSkill(
            $this->history->getValue(FightRequest::MELEE_FIGHT_SKILL),
            PhysicalSkillCode::getIt(PhysicalSkillCode::FIGHT_UNARMED)
        );
    }

    private function getPreviousMeleeSkillRank(): int
    {
        return (int)$this->history->getValue(FightRequest::MELEE_FIGHT_SKILL_RANK);
    }

    public function getSkillForArmor(): PhysicalSkillCode
    {
        return PhysicalSkillCode::getIt(PhysicalSkillCode::ARMOR_WEARING);
    }

    /**
     * @return array|SkillCode[]
     */
    public function getPossibleRangedFightSkills(): array
    {
        return $this->getSkillsForCategories(WeaponCategoryCode::getRangedWeaponCategoryValues());
    }

    /**
     * @return array|SkillCode[]
     */
    public function getPossibleMeleeFightSkills(): array
    {
        return $this->getSkillsForCategories(WeaponCategoryCode::getMeleeWeaponCategoryValues());
    }

    /**
     * @param array|string $weaponCategoryValues
     * @return array|SkillCode[]
     */
    private function getSkillsForCategories(array $weaponCategoryValues): array
    {
        $fightWithCategories = [];
        $fightWithPhysical = \array_map(
            function (string $skillName) {
                return PhysicalSkillCode::getIt($skillName);
            },
            $this->filterForCategories(PhysicalSkillCode::getPossibleValues(), $weaponCategoryValues)
        );
        $fightWithCategories = \array_merge($fightWithCategories, $fightWithPhysical);
        $fightWithPsychical = \array_map(
            function (string $skillName) {
                return PsychicalSkillCode::getIt($skillName);
            },
            $this->filterForCategories(PsychicalSkillCode::getPossibleValues(), $weaponCategoryValues)
        );
        $fightWithCategories = \array_merge($fightWithCategories, $fightWithPsychical);
        $fightWithCombined = \array_map(
            function (string $skillName) {
                return CombinedSkillCode::getIt($skillName);
            },
            $this->filterForCategories(CombinedSkillCode::getPossibleValues(), $weaponCategoryValues)
        );
        $fightWithCategories = \array_merge($fightWithCategories, $fightWithCombined);

        return $fightWithCategories;
    }

    private function filterForCategories(array $skillCodeValues, array $weaponCategoryValues): array
    {
        $fightWith = \array_filter(
            $skillCodeValues,
            function (string $skillName) {
                return strpos($skillName, 'fight_') === 0;
            }
        );
        $categoryNames = \array_map(
            function (string $categoryName) {
                return StringTools::toConstantLikeValue(WeaponCategoryCode::getIt($categoryName)->translateTo('en', 4));
            },
            $weaponCategoryValues
        );

        return \array_filter($fightWith, function (string $skillName) use ($categoryNames) {
            $categoryFromSkill = \str_replace(['fight_with_', 'fight_' /*without weapon */], '', $skillName);

            return \in_array($categoryFromSkill, $categoryNames, true);
        });
    }

    public function getCurrentTargetDistance(): Distance
    {
        $distanceValue = $this->currentValues->getCurrentValue(FightRequest::RANGED_TARGET_DISTANCE);
        if ($distanceValue === null) {
            $distanceValue = AttackNumberByContinuousDistanceTable::DISTANCE_WITH_NO_IMPACT_TO_ATTACK_NUMBER;
        }
        $distanceValue = \min($distanceValue, $this->getCurrentRangedWeaponMaximalRange()->getInMeters($this->tables));

        return new Distance($distanceValue, DistanceUnitCode::METER, Tables::getIt()->getDistanceTable());
    }

    public function getCurrentRangedWeaponMaximalRange(): MaximalRange
    {
        return $this->armourer->getMaximalRangeWithWeaponlike(
            $this->currentArmamentsWithSkills->getCurrentRangedWeapon(),
            $this->currentProperties->getCurrentStrength(),
            $this->currentProperties->getCurrentSpeed()
        );
    }

    public function getCurrentTargetSize(): Size
    {
        $distanceValue = $this->currentValues->getCurrentValue(FightRequest::RANGED_TARGET_SIZE);
        if ($distanceValue === null) {
            return Size::getIt(1);
        }

        return Size::getIt($distanceValue);
    }

    public function getPreviousTargetDistance(): Distance
    {
        $distanceValue = $this->history->getValue(FightRequest::RANGED_TARGET_DISTANCE);
        if ($distanceValue === null) {
            $distanceValue = AttackNumberByContinuousDistanceTable::DISTANCE_WITH_NO_IMPACT_TO_ATTACK_NUMBER;
        }
        $distanceValue = \min($distanceValue, $this->getPreviousRangedWeaponMaximalRange());

        return new Distance($distanceValue, DistanceUnitCode::METER, $this->tables->getDistanceTable());
    }

    private function getPreviousRangedWeaponMaximalRange(): float
    {
        return $this->getPreviousRangedWeaponFightProperties()->getMaximalRange()->getInMeters($this->tables);
    }

    public function getPreviousTargetSize(): Size
    {
        $distanceValue = $this->history->getValue(FightRequest::RANGED_TARGET_SIZE);
        if ($distanceValue === null) {
            return Size::getIt(1);
        }

        return Size::getIt($distanceValue);
    }

    public function getShieldUsageSkillCode(): PhysicalSkillCode
    {
        return PhysicalSkillCode::getIt(PhysicalSkillCode::SHIELD_USAGE);
    }

    public function getFightWithShieldsSkillCode(): PhysicalSkillCode
    {
        return PhysicalSkillCode::getIt(PhysicalSkillCode::FIGHT_WITH_SHIELDS);
    }

    public function getCurrentEncounterRange(): EncounterRange
    {
        return $this->armourer->getEncounterRangeWithWeaponlike(
            $this->currentArmamentsWithSkills->getCurrentRangedWeapon(),
            $this->currentProperties->getCurrentStrength(),
            $this->currentProperties->getCurrentSpeed()
        );
    }

    public function getPreviousEncounterRange(): EncounterRange
    {
        return $this->armourer->getEncounterRangeWithWeaponlike(
            $this->previousArmamentsWithSkills->getPreviousRangedWeapon(),
            $this->previousProperties->getPreviousStrength(),
            $this->previousProperties->getPreviousSpeed()
        );
    }
}