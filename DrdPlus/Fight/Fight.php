<?php
namespace DrdPlus\Fight;

use DrdPlus\Background\BackgroundParts\Ancestry;
use DrdPlus\Background\BackgroundParts\SkillPointsFromBackground;
use DrdPlus\Codes\Armaments\ArmamentCode;
use DrdPlus\Codes\Armaments\BodyArmorCode;
use DrdPlus\Codes\Armaments\HelmCode;
use DrdPlus\Codes\Armaments\MeleeWeaponCode;
use DrdPlus\Codes\Armaments\RangedWeaponCode;
use DrdPlus\Codes\Armaments\ShieldCode;
use DrdPlus\Codes\Armaments\WeaponCategoryCode;
use DrdPlus\Codes\Armaments\WeaponlikeCode;
use DrdPlus\Codes\Body\WoundTypeCode;
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
use DrdPlus\Properties\Base\Agility;
use DrdPlus\Properties\Base\Charisma;
use DrdPlus\Properties\Base\Intelligence;
use DrdPlus\Properties\Base\Knack;
use DrdPlus\Properties\Base\Strength;
use DrdPlus\Properties\Base\Will;
use DrdPlus\Properties\Body\Height;
use DrdPlus\Properties\Body\HeightInCm;
use DrdPlus\Properties\Body\Size;
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
use DrdPlus\Tables\Measurements\Weight\Weight;
use DrdPlus\Tables\Tables;
use Granam\Boolean\Tools\ToBoolean;
use Granam\Integer\PositiveIntegerObject;
use Granam\Integer\Tools\ToInteger;
use Granam\Strict\Object\StrictObject;
use Granam\String\StringTools;

class Fight extends StrictObject
{
    /** @var CurrentValues */
    private $currentValues;
    /** @var CurrentProperties */
    private $currentProperties;
    /** @var PreviousValues */
    private $previousValues;
    /** @var PreviousProperties */
    private $previousProperties;

    public function __construct(
        CurrentValues $currentValues,
        CurrentProperties $currentProperties,
        PreviousValues $previousValues,
        PreviousProperties $previousProperties,
        NewWeaponsService $newWeaponService
    )
    {
        $this->currentValues = $currentValues;
        $this->currentProperties = $currentProperties;
        $this->previousValues = $previousValues;
        $this->previousProperties = $previousProperties;
        $this->registerNewMeleeWeapons($currentValues, $newWeaponService);
    }

    private function registerNewMeleeWeapons(CurrentValues $currentValues, NewWeaponsService $newWeaponsService)
    {
        foreach ($currentValues->getCustomMeleeWeaponsValues() as $currentMeleeWeaponValues) {
            $newWeaponsService->addNewMeleeWeapon(
                $currentMeleeWeaponValues[CurrentValues::CUSTOM_MELEE_WEAPON_NAME],
                WeaponCategoryCode::getIt($currentMeleeWeaponValues[CurrentValues::CUSTOM_MELEE_WEAPON_CATEGORY]),
                Strength::getIt($currentMeleeWeaponValues[CurrentValues::CUSTOM_MELEE_WEAPON_REQUIRED_STRENGTH]),
                ToInteger::toInteger($currentMeleeWeaponValues[CurrentValues::CUSTOM_MELEE_WEAPON_OFFENSIVENESS]),
                ToInteger::toInteger($currentMeleeWeaponValues[CurrentValues::CUSTOM_MELEE_WEAPON_LENGTH]),
                ToInteger::toInteger($currentMeleeWeaponValues[CurrentValues::CUSTOM_MELEE_WEAPON_WOUNDS]),
                WoundTypeCode::getIt($currentMeleeWeaponValues[CurrentValues::CUSTOM_MELEE_WEAPON_WOUND_TYPE]),
                ToInteger::toInteger($currentMeleeWeaponValues[CurrentValues::CUSTOM_MELEE_WEAPON_COVER]),
                new Weight(
                    $currentMeleeWeaponValues[CurrentValues::CUSTOM_MELEE_WEAPON_WEIGHT],
                    Weight::KG,
                    Tables::getIt()->getWeightTable()
                ),
                ToBoolean::toBoolean($currentMeleeWeaponValues[CurrentValues::CUSTOM_MELEE_WEAPON_TWO_HANDED_ONLY])
            );
        }
    }

    public function getMeleeShieldFightProperties(): FightProperties
    {
        return $this->getCurrentFightProperties(
            $this->getSelectedShieldForMelee(),
            $this->getSelectedMeleeShieldHolding(),
            PhysicalSkillCode::getIt(PhysicalSkillCode::FIGHT_WITH_SHIELDS),
            $this->getSelectedFightWithShieldsSkillRank(),
            $this->getSelectedShieldForMelee(),
            $this->getSelectedShieldUsageSkillRank(),
            $this->getSelectedBodyArmor(),
            $this->getSelectedHelm(),
            $this->getCurrentArmorSkillRank(),
            $this->getSelectedProfessionCode(),
            $this->getSelectedOnHorseback(),
            $this->getSelectedRidingSkillRank(),
            $this->getSelectedFightFreeWillAnimal(),
            $this->getSelectedZoologySkillRank()
        );
    }

    public function getCurrentRangedFightProperties(): FightProperties
    {
        return $this->getCurrentFightProperties(
            $this->getSelectedRangedWeapon(),
            $this->getSelectedRangedWeaponHolding(),
            $this->getSelectedRangedSkillCode(),
            $this->getSelectedRangedSkillRank(),
            $this->getSelectedShieldForRanged(),
            $this->getSelectedShieldUsageSkillRank(),
            $this->getSelectedBodyArmor(),
            $this->getSelectedHelm(),
            $this->getCurrentArmorSkillRank(),
            $this->getSelectedProfessionCode(),
            $this->getSelectedOnHorseback(),
            $this->getSelectedRidingSkillRank(),
            $this->getSelectedFightFreeWillAnimal(),
            $this->getSelectedZoologySkillRank()
        );
    }

    public function getGenericFightProperties(): FightProperties
    {
        return $this->getCurrentFightProperties(
            MeleeWeaponCode::getIt(MeleeWeaponCode::HAND),
            ItemHoldingCode::getIt(ItemHoldingCode::MAIN_HAND),
            PsychicalSkillCode::getIt(PsychicalSkillCode::ASTRONOMY), // whatever
            0, // zero skill rank
            ShieldCode::getIt(ShieldCode::WITHOUT_SHIELD),
            $this->getSelectedShieldUsageSkillRank(),
            $this->getSelectedBodyArmor(),
            $this->getSelectedHelm(),
            $this->getCurrentArmorSkillRank(),
            $this->getSelectedProfessionCode(),
            $this->getSelectedOnHorseback(),
            $this->getSelectedRidingSkillRank(),
            $this->getSelectedFightFreeWillAnimal(),
            $this->getSelectedZoologySkillRank()
        );
    }

    private function getCurrentFightProperties(
        WeaponlikeCode $weaponlikeCode,
        ItemHoldingCode $weaponHoldingCode,
        SkillCode $weaponSkillCode,
        int $skillRankWithWeapon,
        ShieldCode $usedShield,
        int $shieldUsageSkillRank,
        BodyArmorCode $bodyArmorCode,
        HelmCode $helmCode,
        int $armorSkillRank,
        ProfessionCode $professionCode,
        bool $onHorseback,
        int $ridingSkillRank,
        bool $fightingFreeWillAnimal,
        int $zoologySkillRank
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
            $shieldUsageSkillRank,
            $bodyArmorCode,
            $helmCode,
            $armorSkillRank,
            $professionCode,
            $onHorseback,
            $ridingSkillRank,
            $fightingFreeWillAnimal,
            $zoologySkillRank
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
        ItemHoldingCode $weaponHolding,
        SkillCode $skillWithWeapon,
        int $skillRankWithWeapon,
        ShieldCode $usedShield,
        int $shieldUsageSkillRank,
        BodyArmorCode $bodyArmorCode,
        HelmCode $helmCode,
        int $skillRankWithArmor,
        ProfessionCode $professionCode,
        bool $fightsOnHorseback,
        int $ridingSkillRank,
        bool $fightFreeWillAnimal,
        int $zoologySkillRank
    ): FightProperties
    {
        return new FightProperties(
            new BodyPropertiesForFight(
                $strength,
                $agility,
                $knack,
                $will,
                $intelligence,
                $charisma,
                $size,
                $height = Height::getIt($heightInCm, Tables::getIt()),
                Speed::getIt($strength, $agility, $height)
            ),
            new CombatActions([], Tables::getIt()),
            $this->createSkills(
                $skillWithWeapon,
                $skillRankWithWeapon,
                $professionCode,
                $skillRankWithArmor,
                $shieldUsageSkillRank,
                $ridingSkillRank,
                $zoologySkillRank
            ),
            $bodyArmorCode,
            $helmCode,
            $professionCode,
            Tables::getIt(),
            $weaponlikeCode,
            $weaponHolding,
            false, // does not fight with two weapons
            $usedShield,
            false, // enemy is not faster
            Glared::createWithoutGlare(new Health()),
            $fightsOnHorseback,
            $fightFreeWillAnimal
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
                Ancestry::getIt(new PositiveIntegerObject(8), Tables::getIt()),
                Tables::getIt()
            ),
            new PhysicalSkills($professionFirstLevel),
            new PsychicalSkills($professionFirstLevel),
            new CombinedSkills($professionFirstLevel),
            Tables::getIt()
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
                Tables::getIt()
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
                Tables::getIt()
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
                Tables::getIt()
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
                Tables::getIt()
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
    )
    {
        if ($skillRankWithWeapon === 0) {
            return;
        }
        if (in_array($skillWithWeapon->getValue(), PhysicalSkillCode::getPossibleValues(), true)) {
            $getSkill = StringTools::assembleGetterForName($skillWithWeapon->getValue());
            /** @var PhysicalSkill $skill */
            $skill = $skills->getPhysicalSkills()->$getSkill();
            $physicalSkillPoint = PhysicalSkillPoint::createFromFirstLevelSkillPointsFromBackground(
                $professionFirstLevel,
                $skillPointsFromBackground,
                Tables::getIt()
            );
            while ($skillRankWithWeapon-- > 0) {
                $skill->increaseSkillRank($physicalSkillPoint);
            }

            return;
        }
        if (in_array($skillWithWeapon->getValue(), PsychicalSkillCode::getPossibleValues(), true)) {
            $getSkill = StringTools::assembleGetterForName($skillWithWeapon->getValue());
            /** @var PsychicalSkill $skill */
            $skill = $skills->getPsychicalSkills()->$getSkill();
            $physicalSkillPoint = PsychicalSkillPoint::createFromFirstLevelSkillPointsFromBackground(
                $professionFirstLevel,
                $skillPointsFromBackground,
                Tables::getIt()
            );
            while ($skillRankWithWeapon-- > 0) {
                $skill->increaseSkillRank($physicalSkillPoint);
            }

            return;
        }
        if (in_array($skillWithWeapon->getValue(), CombinedSkillCode::getPossibleValues(), true)) {
            $getSkill = StringTools::assembleGetterForName($skillWithWeapon->getValue());
            /** @var CombinedSkill $skill */
            $skill = $skills->getCombinedSkills()->$getSkill();
            $combinedSkillPoint = CombinedSkillPoint::createFromFirstLevelSkillPointsFromBackground(
                $professionFirstLevel,
                $skillPointsFromBackground,
                Tables::getIt()
            );
            while ($skillRankWithWeapon-- > 0) {
                $skill->increaseSkillRank($combinedSkillPoint);
            }

            return;
        }
        throw new \LogicException("Given skill with a weapon '{$skillWithWeapon}' does not belong to any skill group");
    }

    public function getPreviousRangedFightProperties(): FightProperties
    {
        return $this->getPreviousFightProperties(
            $this->getPreviousRangedWeapon(),
            $this->getPreviousRangedWeaponHolding(),
            $this->getPreviousRangedSkillCode(),
            $this->getPreviousRangedSkillRank(),
            $this->getPreviousShield(),
            $this->getPreviousShieldUsageSkillRank(),
            $this->getPreviousArmorSkillRank(),
            $this->getPreviousOnHorseback(),
            $this->getPreviousRidingSkillRank(),
            $this->getPreviousFightFreeWillAnimal(),
            $this->getPreviousZoologySkillRank()
        );
    }

    private function getPreviousFightProperties(
        WeaponlikeCode $weaponlikeCode,
        ItemHoldingCode $weaponHoldingCode,
        SkillCode $fightWithWeaponSkillCode,
        int $skillRankWithWeapon,
        ShieldCode $previousShield,
        int $shieldUsageSkillRank,
        int $armorSkillRank,
        bool $onHorseback,
        int $ridingSkillRank,
        bool $fightingFreeWillAnimal,
        int $zoologySkillRank
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
            $shieldUsageSkillRank,
            $this->getPreviousBodyArmor(),
            $this->getPreviousHelm(),
            $armorSkillRank,
            $this->getPreviousProfessionCode(),
            $onHorseback,
            $ridingSkillRank,
            $fightingFreeWillAnimal,
            $zoologySkillRank
        );
    }

    public function getPreviousMeleeShieldFightProperties(): FightProperties
    {
        return $this->getPreviousFightProperties(
            $this->getPreviousShield(),
            $this->getPreviousMeleeShieldHolding(),
            PhysicalSkillCode::getIt(PhysicalSkillCode::FIGHT_WITH_SHIELDS),
            $this->getPreviousFightWithShieldsSkillRank(),
            $this->getPreviousShield(),
            $this->getPreviousShieldUsageSkillRank(),
            $this->getPreviousArmorSkillRank(),
            $this->getPreviousOnHorseback(),
            $this->getPreviousRidingSkillRank(),
            $this->getPreviousFightFreeWillAnimal(),
            $this->getPreviousZoologySkillRank()
        );
    }

    public function getPreviousRangedShieldFightProperties(): FightProperties
    {
        return $this->getPreviousFightProperties(
            $this->getPreviousShield(),
            $this->getPreviousRangedShieldHolding(),
            PhysicalSkillCode::getIt(PhysicalSkillCode::FIGHT_WITH_SHIELDS),
            $this->getPreviousFightWithShieldsSkillRank(),
            $this->getPreviousShield(),
            $this->getPreviousShieldUsageSkillRank(),
            $this->getPreviousArmorSkillRank(),
            $this->getPreviousOnHorseback(),
            $this->getPreviousRidingSkillRank(),
            $this->getPreviousFightFreeWillAnimal(),
            $this->getPreviousZoologySkillRank()
        );
    }

    public function getPreviousGenericFightProperties(): FightProperties
    {
        return $this->getPreviousFightProperties(
            MeleeWeaponCode::getIt(MeleeWeaponCode::HAND),
            ItemHoldingCode::getIt(ItemHoldingCode::MAIN_HAND),
            PsychicalSkillCode::getIt(PsychicalSkillCode::ASTRONOMY), // whatever
            0, // zero skill rank
            ShieldCode::getIt(ShieldCode::WITHOUT_SHIELD),
            $this->getPreviousShieldUsageSkillRank(),
            $this->getPreviousArmorSkillRank(),
            $this->getPreviousOnHorseback(),
            $this->getPreviousRidingSkillRank(),
            $this->getPreviousFightFreeWillAnimal(),
            $this->getPreviousZoologySkillRank()
        );
    }

    public function getPreviousMeleeWeaponFightProperties(): FightProperties
    {
        return $this->getPreviousFightProperties(
            $this->getPreviousMeleeWeapon(),
            $this->getPreviousMeleeWeaponHolding(),
            $this->getPreviousMeleeSkillCode(),
            $this->getPreviousMeleeSkillRank(),
            $this->getPreviousShield(),
            $this->getPreviousShieldUsageSkillRank(),
            $this->getPreviousArmorSkillRank(),
            $this->getPreviousOnHorseback(),
            $this->getPreviousRidingSkillRank(),
            $this->getPreviousFightFreeWillAnimal(),
            $this->getPreviousZoologySkillRank()
        );
    }

    public function getRangedShieldFightProperties(): FightProperties
    {
        return $this->getCurrentFightProperties(
            $this->getSelectedShieldForRanged(),
            $this->getSelectedRangedShieldHolding(),
            PhysicalSkillCode::getIt(PhysicalSkillCode::FIGHT_WITH_SHIELDS),
            $this->getSelectedFightWithShieldsSkillRank(),
            $this->getSelectedShieldForRanged(),
            $this->getSelectedShieldUsageSkillRank(),
            $this->getSelectedBodyArmor(),
            $this->getSelectedHelm(),
            $this->getCurrentArmorSkillRank(),
            $this->getSelectedProfessionCode(),
            $this->getSelectedOnHorseback(),
            $this->getSelectedRidingSkillRank(),
            $this->getSelectedFightFreeWillAnimal(),
            $this->getSelectedZoologySkillRank()
        );
    }

    public function getMeleeWeaponFightProperties(): FightProperties
    {
        return $this->getCurrentFightProperties(
            $this->getCurrentMeleeWeapon(),
            $this->getCurrentMeleeWeaponHolding(),
            $this->getSelectedMeleeSkillCode(),
            $this->getSelectedMeleeSkillRank(),
            $this->getSelectedShieldForMelee(),
            $this->getSelectedShieldUsageSkillRank(),
            $this->getSelectedBodyArmor(),
            $this->getSelectedHelm(),
            $this->getCurrentArmorSkillRank(),
            $this->getSelectedProfessionCode(),
            $this->getSelectedOnHorseback(),
            $this->getSelectedRidingSkillRank(),
            $this->getSelectedFightFreeWillAnimal(),
            $this->getSelectedZoologySkillRank()
        );
    }

    private function getPreviousMeleeSkillCode(): SkillCode
    {
        return $this->getSelectedSkill($this->previousValues->getValue(Controller::MELEE_FIGHT_SKILL));
    }

    private function getPreviousMeleeSkillRank(): int
    {
        return (int)$this->previousValues->getValue(Controller::MELEE_FIGHT_SKILL_RANK);
    }

    private function getPreviousMeleeWeapon(): MeleeWeaponCode
    {
        $meleeWeaponValue = $this->previousValues->getValue(Controller::MELEE_WEAPON);
        if (!$meleeWeaponValue) {
            return MeleeWeaponCode::getIt(MeleeWeaponCode::HAND);
        }
        $meleeWeapon = MeleeWeaponCode::getIt($meleeWeaponValue);
        $weaponHolding = $this->getWeaponHolding(
            $meleeWeapon,
            $this->previousValues->getValue(Controller::MELEE_WEAPON_HOLDING)
        );
        if (!$this->couldUseWeaponlike($meleeWeapon, $weaponHolding)) {
            return MeleeWeaponCode::getIt(MeleeWeaponCode::HAND);
        }

        return $meleeWeapon;
    }

    private function getWeaponHolding(WeaponlikeCode $weaponlikeCode, string $weaponHolding): ItemHoldingCode
    {
        if ($this->isTwoHandedOnly($weaponlikeCode)) {
            return ItemHoldingCode::getIt(ItemHoldingCode::TWO_HANDS);
        }
        if ($this->isOneHandedOnly($weaponlikeCode)) {
            return ItemHoldingCode::getIt(ItemHoldingCode::MAIN_HAND);
        }
        if (!$weaponHolding) {
            return ItemHoldingCode::getIt(ItemHoldingCode::MAIN_HAND);
        }

        return ItemHoldingCode::getIt($weaponHolding);
    }

    private function isTwoHandedOnly(WeaponlikeCode $weaponlikeCode): bool
    {
        return Tables::getIt()->getArmourer()->isTwoHandedOnly($weaponlikeCode);
    }

    private function isOneHandedOnly(WeaponlikeCode $weaponlikeCode): bool
    {
        return Tables::getIt()->getArmourer()->isOneHandedOnly($weaponlikeCode);
    }

    private function getPreviousMeleeWeaponHolding(): ItemHoldingCode
    {
        $previousMeleeWeaponHoldingValue = $this->previousValues->getValue(Controller::MELEE_WEAPON_HOLDING);
        if ($previousMeleeWeaponHoldingValue === null) {
            return ItemHoldingCode::getIt(ItemHoldingCode::MAIN_HAND);
        }

        return $this->getWeaponHolding($this->getPreviousMeleeWeapon(), $previousMeleeWeaponHoldingValue);
    }

    public function getCurrentMeleeWeapon(): MeleeWeaponCode
    {
        $meleeWeaponValue = $this->currentValues->getValue(Controller::MELEE_WEAPON);
        if (!$meleeWeaponValue) {
            return MeleeWeaponCode::getIt(MeleeWeaponCode::HAND);
        }
        $meleeWeapon = MeleeWeaponCode::getIt($meleeWeaponValue);
        $weaponHolding = $this->getCurrentMeleeWeaponHolding($meleeWeapon);
        if (!$this->canUseWeaponlike($meleeWeapon, $weaponHolding)) {
            return MeleeWeaponCode::getIt(MeleeWeaponCode::HAND);
        }

        return $meleeWeapon;
    }

    private function canUseWeaponlike(WeaponlikeCode $weaponlikeCode, ItemHoldingCode $itemHoldingCode): bool
    {
        return $this->canUseArmament(
            $weaponlikeCode,
            Tables::getIt()->getArmourer()->getStrengthForWeaponOrShield(
                $weaponlikeCode,
                $this->getWeaponHolding($weaponlikeCode, $itemHoldingCode->getValue()),
                $this->currentProperties->getCurrentStrength()
            )
        );
    }

    public function getCurrentMeleeWeaponHolding(MeleeWeaponCode $currentWeapon = null): ItemHoldingCode
    {
        $meleeWeaponHoldingValue = $this->currentValues->getValue(Controller::MELEE_WEAPON_HOLDING);
        if ($meleeWeaponHoldingValue === null) {
            return ItemHoldingCode::getIt(ItemHoldingCode::MAIN_HAND);
        }

        return $this->getWeaponHolding($currentWeapon ?? $this->getCurrentMeleeWeapon(), $meleeWeaponHoldingValue);
    }

    private function getSelectedShieldForMelee(): ShieldCode
    {
        $selectedShield = $this->getSelectedShield();
        if ($selectedShield->isUnarmed()) {
            return $selectedShield;
        }
        if ($this->getCurrentMeleeWeaponHolding()->holdsByTwoHands()
            || !$this->canUseShield($selectedShield, $this->getSelectedMeleeShieldHolding($selectedShield))
        ) {
            return ShieldCode::getIt(ShieldCode::WITHOUT_SHIELD);
        }

        return $selectedShield;
    }

    private function canUseShield(ShieldCode $shieldCode, ItemHoldingCode $itemHoldingCode): bool
    {
        return $this->canUseArmament(
            $shieldCode,
            Tables::getIt()->getArmourer()->getStrengthForWeaponOrShield(
                $shieldCode,
                $itemHoldingCode,
                $this->currentProperties->getCurrentStrength()
            )
        );
    }

    /**
     * @param ShieldCode|null $selectedShield
     * @return ItemHoldingCode
     * @throws \DrdPlus\Codes\Exceptions\ThereIsNoOppositeForTwoHandsHolding
     */
    public function getSelectedMeleeShieldHolding(ShieldCode $selectedShield = null): ItemHoldingCode
    {
        return $this->getShieldHolding(
            $this->getCurrentMeleeWeaponHolding(),
            $this->getCurrentMeleeWeapon(),
            $selectedShield
        );
    }

    public function getSelectedRangedWeapon(): RangedWeaponCode
    {
        $rangedWeaponValue = $this->currentValues->getValue(Controller::RANGED_WEAPON);
        if (!$rangedWeaponValue) {
            return RangedWeaponCode::getIt(RangedWeaponCode::SAND);
        }
        $rangedWeapon = RangedWeaponCode::getIt($rangedWeaponValue);
        $weaponHolding = $this->getWeaponHolding(
            $rangedWeapon,
            $this->currentValues->getValue(Controller::RANGED_WEAPON_HOLDING)
        );
        if (!$this->canUseWeaponlike($rangedWeapon, $weaponHolding)) {
            return RangedWeaponCode::getIt(RangedWeaponCode::SAND);
        }

        return $rangedWeapon;
    }

    public function getSelectedRangedWeaponHolding(): ItemHoldingCode
    {
        $rangedWeaponHoldingValue = $this->currentValues->getValue(Controller::RANGED_WEAPON_HOLDING);
        if ($rangedWeaponHoldingValue === null) {
            return ItemHoldingCode::getIt(ItemHoldingCode::MAIN_HAND);
        }

        return $this->getWeaponHolding($this->getSelectedRangedWeapon(), $rangedWeaponHoldingValue);
    }

    private function getPreviousRangedWeapon(): RangedWeaponCode
    {
        $rangedWeaponValue = $this->previousValues->getValue(Controller::RANGED_WEAPON);
        if (!$rangedWeaponValue) {
            return RangedWeaponCode::getIt(RangedWeaponCode::SAND);
        }
        $rangedWeapon = RangedWeaponCode::getIt($rangedWeaponValue);
        $weaponHolding = $this->getWeaponHolding(
            $rangedWeapon,
            $this->previousValues->getValue(Controller::RANGED_WEAPON_HOLDING)
        );
        if (!$this->couldUseWeaponlike($rangedWeapon, $weaponHolding)) {
            return RangedWeaponCode::getIt(RangedWeaponCode::SAND);
        }

        return $rangedWeapon;
    }

    private function canUseArmament(ArmamentCode $armamentCode, Strength $strengthForArmament): bool
    {
        return Tables::getIt()->getArmourer()
            ->canUseArmament(
                $armamentCode,
                $strengthForArmament,
                $this->currentProperties->getCurrentSize()
            );
    }

    private function couldUseWeaponlike(WeaponlikeCode $weaponlikeCode, ItemHoldingCode $itemHoldingCode): bool
    {
        return $this->couldUseArmament(
            $weaponlikeCode,
            Tables::getIt()->getArmourer()->getStrengthForWeaponOrShield(
                $weaponlikeCode,
                $this->getWeaponHolding($weaponlikeCode, $itemHoldingCode->getValue()),
                $this->previousProperties->getPreviousStrength()
            )
        );
    }

    private function couldUseArmament(ArmamentCode $armamentCode, Strength $strengthForArmament): bool
    {
        return Tables::getIt()->getArmourer()
            ->canUseArmament(
                $armamentCode,
                $strengthForArmament,
                $this->previousProperties->getPreviousSize()
            );
    }

    private function getPreviousRangedWeaponHolding(): ItemHoldingCode
    {
        $rangedWeaponHoldingValue = $this->previousValues->getValue(Controller::RANGED_WEAPON_HOLDING);
        if ($rangedWeaponHoldingValue === null) {
            return ItemHoldingCode::getIt(ItemHoldingCode::MAIN_HAND);
        }

        return $this->getWeaponHolding($this->getPreviousRangedWeapon(), $rangedWeaponHoldingValue);
    }

    private function getSelectedShieldForRanged(): ShieldCode
    {
        $selectedShield = $this->getSelectedShield();
        if ($selectedShield->isUnarmed()) {
            return $selectedShield;
        }
        if ($this->getSelectedRangedWeaponHolding()->holdsByTwoHands()
            || !$this->canUseShield($selectedShield, $this->getSelectedRangedShieldHolding($selectedShield))
        ) {
            return ShieldCode::getIt(ShieldCode::WITHOUT_SHIELD);
        }

        return $selectedShield;
    }

    private function getPreviousShield(): ShieldCode
    {
        $previousShieldValue = $this->previousValues->getValue(Controller::SHIELD);
        if (!$previousShieldValue) {
            return ShieldCode::getIt(ShieldCode::WITHOUT_SHIELD);
        }
        $previousShield = ShieldCode::getIt($previousShieldValue);
        if ($this->getPreviousMeleeWeaponHolding()->holdsByTwoHands()
            || $this->getPreviousRangedWeaponHolding()->holdsByTwoHands()
            || !$this->couldUseShield($previousShield, $this->getPreviousMeleeShieldHolding($previousShield))
            || !$this->couldUseShield($previousShield, $this->getPreviousRangedShieldHolding($previousShield))
        ) {
            return ShieldCode::getIt(ShieldCode::WITHOUT_SHIELD);
        }

        return $previousShield;
    }

    private function couldUseShield(ShieldCode $shieldCode, ItemHoldingCode $itemHoldingCode): bool
    {
        return $this->canUseArmament(
            $shieldCode,
            Tables::getIt()->getArmourer()->getStrengthForWeaponOrShield(
                $shieldCode,
                $itemHoldingCode,
                $this->previousProperties->getPreviousStrength()
            )
        );
    }

    /**
     * @param ShieldCode $shieldCode = null
     * @return ItemHoldingCode
     * @throws \DrdPlus\Codes\Exceptions\ThereIsNoOppositeForTwoHandsHolding
     */
    public function getPreviousRangedShieldHolding(ShieldCode $shieldCode = null): ItemHoldingCode
    {
        return $this->getShieldHolding(
            $this->getPreviousRangedWeaponHolding(),
            $this->getPreviousRangedWeapon(),
            $shieldCode
        );
    }

    /**
     * @param ItemHoldingCode $weaponHolding
     * @param WeaponlikeCode $weaponlikeCode
     * @param ShieldCode|null $shield = null
     * @return ItemHoldingCode
     * @throws \DrdPlus\Codes\Exceptions\ThereIsNoOppositeForTwoHandsHolding
     */
    private function getShieldHolding(ItemHoldingCode $weaponHolding, WeaponlikeCode $weaponlikeCode, ShieldCode $shield = null): ItemHoldingCode
    {
        if ($weaponHolding->holdsByTwoHands()) {
            /** @noinspection ExceptionsAnnotatingAndHandlingInspection */
            if (Tables::getIt()->getArmourer()->canHoldItByTwoHands($shield ?? $this->getSelectedShieldForMelee())) {
                // because two-handed weapon has to be dropped to use shield and then both hands can be used for shield
                return ItemHoldingCode::getIt(ItemHoldingCode::TWO_HANDS);
            }

            return ItemHoldingCode::getIt(ItemHoldingCode::MAIN_HAND);
        }
        /** @noinspection ExceptionsAnnotatingAndHandlingInspection */
        if ($weaponlikeCode->isUnarmed()
            && Tables::getIt()->getArmourer()->canHoldItByTwoHands($shield ?? $this->getSelectedShieldForMelee())
        ) {
            return ItemHoldingCode::getIt(ItemHoldingCode::TWO_HANDS);
        }

        return $weaponHolding->getOpposite();
    }

    /**
     * @param ShieldCode|null $shieldCode = null
     * @return ItemHoldingCode
     * @throws \DrdPlus\Codes\Exceptions\ThereIsNoOppositeForTwoHandsHolding
     */
    public function getPreviousMeleeShieldHolding(ShieldCode $shieldCode = null): ItemHoldingCode
    {
        return $this->getShieldHolding(
            $this->getPreviousMeleeWeaponHolding(),
            $this->getPreviousMeleeWeapon(),
            $shieldCode
        );
    }

    public function getPossibleMeleeWeapons(): array
    {
        $weaponCodes = [
            WeaponCategoryCode::AXE => MeleeWeaponCode::getAxeValues(),
            WeaponCategoryCode::KNIFE_AND_DAGGER => MeleeWeaponCode::getKnifeAndDaggerValues(),
            WeaponCategoryCode::MACE_AND_CLUB => MeleeWeaponCode::getMaceAndClubValues(),
            WeaponCategoryCode::MORNINGSTAR_AND_MORGENSTERN => MeleeWeaponCode::getMorningstarAndMorgensternValues(),
            WeaponCategoryCode::SABER_AND_BOWIE_KNIFE => MeleeWeaponCode::getSaberAndBowieKnifeValues(),
            WeaponCategoryCode::STAFF_AND_SPEAR => MeleeWeaponCode::getStaffAndSpearValues(),
            WeaponCategoryCode::SWORD => MeleeWeaponCode::getSwordValues(),
            WeaponCategoryCode::VOULGE_AND_TRIDENT => MeleeWeaponCode::getVoulgeAndTridentValues(),
            WeaponCategoryCode::UNARMED => MeleeWeaponCode::getUnarmedValues(),
        ];
        foreach ($weaponCodes as &$weaponCodesOfSameCategory) {
            $weaponCodesOfSameCategory = $this->addUsabilityToMeleeWeapons($weaponCodesOfSameCategory);
        }

        return $weaponCodes;
    }

    /**
     * @param array|string[] $meleeWeaponCodeValues
     * @return array
     */
    private function addUsabilityToMeleeWeapons(array $meleeWeaponCodeValues): array
    {
        $meleeWeaponCodes = [];
        foreach ($meleeWeaponCodeValues as $meleeWeaponCodeValue) {
            $meleeWeaponCodes[] = MeleeWeaponCode::getIt($meleeWeaponCodeValue);
        }

        return $this->addWeaponlikeUsability($meleeWeaponCodes, $this->getCurrentMeleeWeaponHolding());
    }

    /**
     * @param array|WeaponlikeCode[] $weaponLikeCode
     * @param ItemHoldingCode $itemHoldingCode
     * @return array
     */
    private function addWeaponlikeUsability(array $weaponLikeCode, ItemHoldingCode $itemHoldingCode): array
    {
        $withUsagePossibility = [];
        foreach ($weaponLikeCode as $code) {
            $withUsagePossibility[] = [
                'code' => $code,
                'canUseIt' => $this->canUseWeaponlike($code, $itemHoldingCode),
            ];
        }

        return $withUsagePossibility;
    }

    /**
     * @return array|RangedWeaponCode[][][]
     */
    public function getPossibleRangedWeapons(): array
    {
        $weaponCodes = [
            WeaponCategoryCode::THROWING_WEAPON => RangedWeaponCode::getThrowingWeaponValues(),
            WeaponCategoryCode::BOW => RangedWeaponCode::getBowValues(),
            WeaponCategoryCode::CROSSBOW => RangedWeaponCode::getCrossbowValues(),
        ];
        foreach ($weaponCodes as &$weaponCodesOfSameCategory) {
            $weaponCodesOfSameCategory = $this->addUsabilityToRangedWeapons($weaponCodesOfSameCategory);
        }

        return $weaponCodes;
    }

    /**
     * @param array|string[] $rangedWeaponCodeValues
     * @return array
     */
    private function addUsabilityToRangedWeapons(array $rangedWeaponCodeValues): array
    {
        $meleeWeaponCodes = [];
        foreach ($rangedWeaponCodeValues as $rangedWeaponCodeValue) {
            $meleeWeaponCodes[] = RangedWeaponCode::getIt($rangedWeaponCodeValue);
        }

        return $this->addWeaponlikeUsability($meleeWeaponCodes, $this->getSelectedRangedWeaponHolding());
    }

    /**
     * @return array
     */
    public function getPossibleBodyArmors(): array
    {
        $bodyArmors = array_map(function (string $armorValue) {
            return BodyArmorCode::getIt($armorValue);
        }, BodyArmorCode::getPossibleValues());

        return $this->addNonWeaponArmamentUsability($bodyArmors);
    }

    /**
     * @return array|HelmCode[][][]
     */
    public function getPossibleHelms(): array
    {
        $helmCodes = array_map(function (string $helmValue) {
            return HelmCode::getIt($helmValue);
        }, HelmCode::getPossibleValues());

        return $this->addNonWeaponArmamentUsability($helmCodes);
    }

    /**
     * @return array|ShieldCode[][][]
     */
    public function getPossibleShields(): array
    {
        $shieldCodes = array_map(function (string $shieldValue) {
            return ShieldCode::getIt($shieldValue);
        }, ShieldCode::getPossibleValues());

        return $this->addNonWeaponArmamentUsability($shieldCodes);
    }

    /**
     * @param array|ArmamentCode[] $armamentCodes
     * @return array
     */
    private function addNonWeaponArmamentUsability(array $armamentCodes): array
    {
        $withUsagePossibility = [];
        foreach ($armamentCodes as $armamentCode) {
            $withUsagePossibility[] = [
                'code' => $armamentCode,
                'canUseIt' => $this->canUseArmament($armamentCode, $this->currentProperties->getCurrentStrength()),
            ];
        }

        return $withUsagePossibility;
    }

    public function getSelectedBodyArmor(): BodyArmorCode
    {
        $selectedBodyArmorValue = $this->currentValues->getValue(Controller::BODY_ARMOR);
        if (!$selectedBodyArmorValue) {
            return BodyArmorCode::getIt(BodyArmorCode::WITHOUT_ARMOR);
        }
        $selectedBodyArmor = BodyArmorCode::getIt($selectedBodyArmorValue);
        if (!$this->canUseArmament($selectedBodyArmor, $this->currentProperties->getCurrentStrength())) {
            return BodyArmorCode::getIt(BodyArmorCode::WITHOUT_ARMOR);
        }

        return BodyArmorCode::getIt($selectedBodyArmorValue);
    }

    private function getPreviousBodyArmor(): BodyArmorCode
    {
        $previousBodyArmorValue = $this->previousValues->getValue(Controller::BODY_ARMOR);
        if (!$previousBodyArmorValue) {
            return BodyArmorCode::getIt(BodyArmorCode::WITHOUT_ARMOR);
        }
        $previousBodyArmor = BodyArmorCode::getIt($previousBodyArmorValue);
        if (!$this->canUseArmament($previousBodyArmor, $this->previousProperties->getPreviousStrength())) {
            return BodyArmorCode::getIt(BodyArmorCode::WITHOUT_ARMOR);
        }

        return $previousBodyArmor;
    }

    public function getProtectionOfPreviousBodyArmor(): int
    {
        return Tables::getIt()->getBodyArmorsTable()->getProtectionOf($this->getPreviousBodyArmor());
    }

    public function getProtectionOfSelectedBodyArmor(): int
    {
        return $this->getProtectionOfBodyArmor($this->getSelectedBodyArmor());
    }

    public function getProtectionOfBodyArmor(BodyArmorCode $bodyArmorCode): int
    {
        return Tables::getIt()->getBodyArmorsTable()->getProtectionOf($bodyArmorCode);
    }

    public function getSelectedHelm(): HelmCode
    {
        $selectedHelmValue = $this->currentValues->getValue(Controller::HELM);
        if (!$selectedHelmValue) {
            return HelmCode::getIt(HelmCode::WITHOUT_HELM);
        }
        $selectedHelm = HelmCode::getIt($selectedHelmValue);
        if (!$this->canUseArmament($selectedHelm, $this->currentProperties->getCurrentStrength())) {
            return HelmCode::getIt(HelmCode::WITHOUT_HELM);
        }

        return HelmCode::getIt($selectedHelmValue);
    }

    public function getSelectedHelmProtection(): int
    {
        return $this->getProtectionOfHelm($this->getSelectedHelm());
    }

    public function getCoverOfShield(ShieldCode $shieldCode): int
    {
        return Tables::getIt()->getShieldsTable()->getCoverOf($shieldCode);
    }

    public function getProtectionOfHelm(HelmCode $helmCode): int
    {
        return Tables::getIt()->getHelmsTable()->getProtectionOf($helmCode);
    }

    /**
     * WITHOUT usability check
     *
     * @return ShieldCode
     */
    public function getSelectedShield(): ShieldCode
    {
        $selectedShieldValue = $this->currentValues->getValue(Controller::SHIELD);
        if (!$selectedShieldValue) {
            return ShieldCode::getIt(ShieldCode::WITHOUT_SHIELD);
        }

        return ShieldCode::getIt($selectedShieldValue);
    }

    /**
     * @param ShieldCode|null $shield = null
     * @return ItemHoldingCode
     * @throws \DrdPlus\Codes\Exceptions\ThereIsNoOppositeForTwoHandsHolding
     */
    public function getSelectedRangedShieldHolding(ShieldCode $shield = null): ItemHoldingCode
    {
        return $this->getShieldHolding(
            $this->getSelectedRangedWeaponHolding(),
            $this->getSelectedRangedWeapon(),
            $shield
        );
    }

    private function getPreviousHelm(): HelmCode
    {
        $previousHelmValue = $this->previousValues->getValue(Controller::HELM);
        if (!$previousHelmValue) {
            return HelmCode::getIt(HelmCode::WITHOUT_HELM);
        }
        $previousHelm = HelmCode::getIt($previousHelmValue);
        if (!$this->canUseArmament($previousHelm, $this->previousProperties->getPreviousStrength())) {
            return HelmCode::getIt(HelmCode::WITHOUT_HELM);
        }

        return $previousHelm;
    }

    public function getPreviousHelmProtection(): int
    {
        return Tables::getIt()->getHelmsTable()->getProtectionOf($this->getPreviousHelm());
    }

    /**
     * @return PhysicalSkillCode
     */
    public function getSkillForArmor(): PhysicalSkillCode
    {
        return PhysicalSkillCode::getIt(PhysicalSkillCode::ARMOR_WEARING);
    }

    /**
     * @return array|SkillCode[]
     */
    public function getSkillsForRanged(): array
    {
        return $this->getSkillsForCategories(WeaponCategoryCode::getRangedWeaponCategoryValues());
    }

    public function getSelectedMeleeSkillCode(): SkillCode
    {
        return $this->getSelectedSkill($this->currentValues->getValue(Controller::MELEE_FIGHT_SKILL));
    }

    public function getSelectedMeleeSkillRank(): int
    {
        return (int)$this->currentValues->getValue(Controller::MELEE_FIGHT_SKILL_RANK);
    }

    /**
     * @return array|SkillCode[]
     */
    public function getPossibleSkillsForMelee(): array
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
        $fightWithPhysical = array_map(
            function (string $skillName) {
                return PhysicalSkillCode::getIt($skillName);
            },
            $this->filterForCategories(PhysicalSkillCode::getPossibleValues(), $weaponCategoryValues)
        );
        $fightWithCategories = array_merge($fightWithCategories, $fightWithPhysical);
        $fightWithPsychical = array_map(
            function (string $skillName) {
                return PsychicalSkillCode::getIt($skillName);
            },
            $this->filterForCategories(PsychicalSkillCode::getPossibleValues(), $weaponCategoryValues)
        );
        $fightWithCategories = array_merge($fightWithCategories, $fightWithPsychical);
        $fightWithCombined = array_map(
            function (string $skillName) {
                return CombinedSkillCode::getIt($skillName);
            },
            $this->filterForCategories(CombinedSkillCode::getPossibleValues(), $weaponCategoryValues)
        );
        $fightWithCategories = array_merge($fightWithCategories, $fightWithCombined);

        return $fightWithCategories;
    }

    private function filterForCategories(array $skillCodeValues, array $weaponCategoryValues): array
    {
        $fightWith = array_filter(
            $skillCodeValues,
            function (string $skillName) {
                return strpos($skillName, 'fight_') === 0;
            }
        );
        $categoryNames = array_map(
            function (string $categoryName) {
                return StringTools::toConstant(WeaponCategoryCode::getIt($categoryName)->translateTo('en', 4));
            },
            $weaponCategoryValues
        );

        return array_filter($fightWith, function (string $skillName) use ($categoryNames) {
            $categoryFromSkill = str_replace(['fight_with_', 'fight_' /*without weapon */], '', $skillName);

            return in_array($categoryFromSkill, $categoryNames, true);
        });
    }

    public function getSelectedRangedSkillCode(): SkillCode
    {
        return $this->getSelectedSkill($this->currentValues->getValue(Controller::RANGED_FIGHT_SKILL));
    }

    private function getSelectedSkill($skillName): SkillCode
    {
        if (!$skillName) {
            return PhysicalSkillCode::getIt(PhysicalSkillCode::FIGHT_UNARMED);
        }

        if (in_array($skillName, PhysicalSkillCode::getPossibleValues(), true)) {
            return PhysicalSkillCode::getIt($skillName);
        }

        if (in_array($skillName, PsychicalSkillCode::getPossibleValues(), true)) {
            return PsychicalSkillCode::getIt($skillName);
        }
        if (in_array($skillName, CombinedSkillCode::getPossibleValues(), true)) {
            return CombinedSkillCode::getIt($skillName);
        }

        throw new \LogicException('Unexpected skill value ' . var_export($skillName, true));
    }

    private function getPreviousRangedSkillCode(): SkillCode
    {
        return $this->getSelectedSkill($this->previousValues->getValue(Controller::RANGED_FIGHT_SKILL));
    }

    public function getSelectedShieldUsageSkillRank(): int
    {
        return (int)$this->currentValues->getValue(Controller::SHIELD_USAGE_SKILL_RANK);
    }

    public function getCurrentArmorSkillRank(): int
    {
        return (int)$this->currentValues->getValue(Controller::ARMOR_SKILL_VALUE);
    }

    public function getSelectedRangedSkillRank(): int
    {
        return (int)$this->currentValues->getValue(Controller::RANGED_FIGHT_SKILL_RANK);
    }

    public function getSelectedFightWithShieldsSkillRank(): int
    {
        return (int)$this->currentValues->getValue(Controller::FIGHT_WITH_SHIELDS_SKILL_RANK);
    }

    public function getSelectedProfessionCode(): ProfessionCode
    {
        $selectedProfession = $this->currentValues->getValue(Controller::PROFESSION);
        if (!$selectedProfession) {
            return ProfessionCode::getIt(ProfessionCode::COMMONER);
        }

        return ProfessionCode::getIt($selectedProfession);
    }

    private function getPreviousProfessionCode(): ProfessionCode
    {
        $previousProfession = $this->previousValues->getValue(Controller::PROFESSION);
        if (!$previousProfession) {
            return $this->getSelectedProfessionCode();
        }

        return ProfessionCode::getIt($previousProfession);
    }

    private function getPreviousShieldUsageSkillRank(): int
    {
        return (int)$this->previousValues->getValue(Controller::SHIELD_USAGE_SKILL_RANK);
    }

    public function getSelectedOnHorseback(): bool
    {
        return (bool)$this->currentValues->getValue(Controller::ON_HORSEBACK);
    }

    private function getPreviousArmorSkillRank(): int
    {
        return (int)$this->previousValues->getValue(Controller::ARMOR_SKILL_VALUE);
    }

    private function getPreviousOnHorseback(): bool
    {
        return (bool)$this->previousValues->getValue(Controller::ON_HORSEBACK);
    }

    private function getPreviousRidingSkillRank(): int
    {
        return (int)$this->previousValues->getValue(Controller::RIDING_SKILL_RANK);
    }

    private function getPreviousFightFreeWillAnimal(): bool
    {
        return (bool)$this->previousValues->getValue(Controller::FIGHT_FREE_WILL_ANIMAL);
    }

    private function getPreviousZoologySkillRank(): int
    {
        return (int)$this->previousValues->getValue(Controller::ZOOLOGY_SKILL_RANK);
    }

    public function getSelectedRidingSkillRank(): int
    {
        return (int)$this->currentValues->getValue(Controller::RIDING_SKILL_RANK);
    }

    public function getSelectedFightFreeWillAnimal(): bool
    {
        return (bool)$this->currentValues->getValue(Controller::FIGHT_FREE_WILL_ANIMAL);
    }

    public function getSelectedZoologySkillRank(): int
    {
        return (int)$this->currentValues->getValue(Controller::ZOOLOGY_SKILL_RANK);
    }

    public function getCurrentTargetDistance(): Distance
    {
        $distanceValue = $this->currentValues->getValue(Controller::RANGED_TARGET_DISTANCE);
        if ($distanceValue === null) {
            $distanceValue = AttackNumberByContinuousDistanceTable::DISTANCE_WITH_NO_IMPACT_TO_ATTACK_NUMBER;
        }
        $distanceValue = min($distanceValue, $this->getCurrentRangedWeaponMaximalRange());

        return new Distance($distanceValue, DistanceUnitCode::METER, Tables::getIt()->getDistanceTable());
    }

    private function getCurrentRangedWeaponMaximalRange(): float
    {
        return $this->getCurrentRangedFightProperties()->getMaximalRange()->getInMeters(Tables::getIt());
    }

    private function getPreviousFightWithShieldsSkillRank(): int
    {
        return (int)$this->previousValues->getValue(Controller::FIGHT_WITH_SHIELDS_SKILL_RANK);
    }

    public function getCurrentTargetSize(): Size
    {
        $distanceValue = $this->currentValues->getValue(Controller::RANGED_TARGET_SIZE);
        if ($distanceValue === null) {
            return Size::getIt(1);
        }

        return Size::getIt($distanceValue);
    }

    public function getPreviousTargetDistance(): Distance
    {
        $distanceValue = $this->previousValues->getValue(Controller::RANGED_TARGET_DISTANCE);
        if ($distanceValue === null) {
            $distanceValue = AttackNumberByContinuousDistanceTable::DISTANCE_WITH_NO_IMPACT_TO_ATTACK_NUMBER;
        }
        $distanceValue = min($distanceValue, $this->getPreviousRangedWeaponMaximalRange());

        return new Distance($distanceValue, DistanceUnitCode::METER, Tables::getIt()->getDistanceTable());
    }

    private function getPreviousRangedWeaponMaximalRange(): float
    {
        return $this->getPreviousRangedFightProperties()->getMaximalRange()->getInMeters(Tables::getIt());
    }

    public function getPreviousTargetSize(): Size
    {
        $distanceValue = $this->previousValues->getValue(Controller::RANGED_TARGET_SIZE);
        if ($distanceValue === null) {
            return Size::getIt(1);
        }

        return Size::getIt($distanceValue);
    }

    private function getPreviousRangedSkillRank(): int
    {
        return (int)$this->previousValues->getValue(Controller::RANGED_FIGHT_SKILL_RANK);
    }

}