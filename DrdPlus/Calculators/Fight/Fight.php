<?php
namespace DrdPlus\Calculators\Fight;

use DrdPlus\Background\BackgroundParts\Ancestry;
use DrdPlus\Background\BackgroundParts\SkillPointsFromBackground;
use DrdPlus\Calculators\AttackSkeleton\AttackForCalculator;
use DrdPlus\Calculators\AttackSkeleton\CurrentProperties;
use DrdPlus\Calculators\AttackSkeleton\CurrentValues;
use DrdPlus\Calculators\AttackSkeleton\CustomArmamentsService;
use DrdPlus\Calculators\AttackSkeleton\PreviousArmaments;
use DrdPlus\Calculators\AttackSkeleton\PreviousProperties;
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
use DrdPlus\Configurator\Skeleton\History;
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
use DrdPlus\Tables\Tables;
use Granam\Integer\PositiveIntegerObject;
use Granam\String\StringTools;

class Fight extends AttackForCalculator
{
    use UsingSkills;

    /** @var History */
    private $history;
    /** @var PreviousArmamentsWithSkills */
    private $previousArmamentsWithSkills;

    /**
     * @param CurrentValues $currentValues
     * @param CurrentProperties $currentProperties
     * @param History $history
     * @param PreviousProperties $previousProperties
     * @param CustomArmamentsService $customArmamentsService
     * @param Tables $tables
     * @throws \DrdPlus\Calculators\AttackSkeleton\Exceptions\BrokenNewArmamentValues
     */
    public function __construct(
        CurrentValues $currentValues,
        CurrentProperties $currentProperties,
        History $history,
        PreviousProperties $previousProperties,
        CustomArmamentsService $customArmamentsService,
        Tables $tables
    )
    {
        parent::__construct($currentValues, $history, $customArmamentsService, $tables);
        $this->history = $history;
        $this->previousArmamentsWithSkills = new PreviousArmamentsWithSkills($history, $previousProperties, $tables);
        $this->registerCustomArmaments($currentValues, $customArmamentsService);
    }

    /**
     * @return PreviousArmamentsWithSkills
     */
    public function getPreviousArmaments(): PreviousArmaments
    {
        return $this->previousArmamentsWithSkills;
    }

    /**
     * @return FightProperties
     * @throws \DrdPlus\Codes\Exceptions\ThereIsNoOppositeForTwoHandsHolding
     * @throws \DrdPlus\Tables\Armaments\Exceptions\UnknownWeaponlike
     * @throws \DrdPlus\Tables\Armaments\Exceptions\CanNotHoldWeaponByOneHand
     * @throws \DrdPlus\Tables\Armaments\Exceptions\CanNotHoldWeaponByTwoHands
     * @throws \DrdPlus\Codes\Exceptions\ThereIsNoOppositeForTwoHandsHolding
     */
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

    /**
     * @return FightProperties
     * @throws \DrdPlus\Calculators\Fight\Exceptions\UnknownSkill
     * @throws \DrdPlus\Tables\Armaments\Exceptions\UnknownWeaponlike
     * @throws \DrdPlus\Tables\Armaments\Exceptions\CanNotHoldWeaponByOneHand
     * @throws \DrdPlus\Tables\Armaments\Exceptions\CanNotHoldWeaponByTwoHands
     * @throws \DrdPlus\Codes\Exceptions\ThereIsNoOppositeForTwoHandsHolding
     */
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
            $this->getCurrentProperties()->getCurrentStrength(),
            $this->getCurrentProperties()->getCurrentAgility(),
            $this->getCurrentProperties()->getCurrentKnack(),
            $this->getCurrentProperties()->getCurrentWill(),
            $this->getCurrentProperties()->getCurrentIntelligence(),
            $this->getCurrentProperties()->getCurrentCharisma(),
            $this->getCurrentProperties()->getCurrentSize(),
            $this->getCurrentProperties()->getCurrentHeightInCm(),
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
        /** @noinspection ExceptionsAnnotatingAndHandlingInspection */
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

    /**
     * @param SkillCode $skillWithWeapon
     * @param int $skillRankWithWeapon
     * @param ProfessionCode $professionCode
     * @param int $skillRankWithArmor
     * @param int $shieldUsageSkillRank
     * @param int $ridingSkillRank
     * @param int $zoologySkillRank
     * @return Skills
     * @throws \DrdPlus\Professions\Exceptions\ProfessionNotFound
     */
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
        /** @noinspection ExceptionsAnnotatingAndHandlingInspection */
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
        /** @noinspection ExceptionsAnnotatingAndHandlingInspection */
        $this->addSkillWithWeapon(
            $skillWithWeapon,
            $skillRankWithWeapon,
            $skills,
            $professionFirstLevel,
            $skillPointsFromBackground
        );

        if ($skillRankWithArmor > 0) {
            /** @noinspection ExceptionsAnnotatingAndHandlingInspection */
            $physicalSkillPoint = PhysicalSkillPoint::createFromFirstLevelSkillPointsFromBackground(
                $professionFirstLevel,
                $skillPointsFromBackground,
                Tables::getIt()
            );
            $armorWearing = $skills->getPhysicalSkills()->getArmorWearing();
            while ($skillRankWithArmor-- > 0) {
                /** @noinspection ExceptionsAnnotatingAndHandlingInspection */
                $armorWearing->increaseSkillRank($physicalSkillPoint);
            }
        }
        if ($shieldUsageSkillRank > 0) {
            /** @noinspection ExceptionsAnnotatingAndHandlingInspection */
            $physicalSkillPoint = PhysicalSkillPoint::createFromFirstLevelSkillPointsFromBackground(
                $professionFirstLevel,
                $skillPointsFromBackground,
                Tables::getIt()
            );
            $shieldUsage = $skills->getPhysicalSkills()->getShieldUsage();
            while ($shieldUsageSkillRank-- > 0) {
                /** @noinspection ExceptionsAnnotatingAndHandlingInspection */
                $shieldUsage->increaseSkillRank($physicalSkillPoint);
            }
        }
        if ($ridingSkillRank > 0) {
            /** @noinspection ExceptionsAnnotatingAndHandlingInspection */
            $physicalSkillPoint = PhysicalSkillPoint::createFromFirstLevelSkillPointsFromBackground(
                $professionFirstLevel,
                $skillPointsFromBackground,
                Tables::getIt()
            );
            $riding = $skills->getPhysicalSkills()->getRiding();
            while ($ridingSkillRank-- > 0) {
                /** @noinspection ExceptionsAnnotatingAndHandlingInspection */
                $riding->increaseSkillRank($physicalSkillPoint);
            }
        }
        if ($zoologySkillRank > 0) {
            /** @noinspection ExceptionsAnnotatingAndHandlingInspection */
            $psychicalSkillPoint = PsychicalSkillPoint::createFromFirstLevelSkillPointsFromBackground(
                $professionFirstLevel,
                $skillPointsFromBackground,
                Tables::getIt()
            );
            $zoology = $skills->getPsychicalSkills()->getZoology();
            while ($zoologySkillRank-- > 0) {
                /** @noinspection ExceptionsAnnotatingAndHandlingInspection */
                $zoology->increaseSkillRank($psychicalSkillPoint);
            }
        }

        return $skills;
    }

    /**
     * @param SkillCode $skillWithWeapon
     * @param int $skillRankWithWeapon
     * @param Skills $skills
     * @param ProfessionFirstLevel $professionFirstLevel
     * @param SkillPointsFromBackground $skillPointsFromBackground
     * @throws \DrdPlus\Calculators\Fight\Exceptions\WeaponSkillWithoutKnownSkillGroup
     */
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
            /** @noinspection ExceptionsAnnotatingAndHandlingInspection */
            $getSkill = StringTools::assembleGetterForName($skillWithWeapon->getValue());
            /** @var PhysicalSkill $skill */
            $skill = $skills->getPhysicalSkills()->$getSkill();
            /** @noinspection ExceptionsAnnotatingAndHandlingInspection */
            $physicalSkillPoint = PhysicalSkillPoint::createFromFirstLevelSkillPointsFromBackground(
                $professionFirstLevel,
                $skillPointsFromBackground,
                Tables::getIt()
            );
            while ($skillRankWithWeapon-- > 0) {
                /** @noinspection ExceptionsAnnotatingAndHandlingInspection */
                $skill->increaseSkillRank($physicalSkillPoint);
            }

            return;
        }
        if (\in_array($skillWithWeapon->getValue(), PsychicalSkillCode::getPossibleValues(), true)) {
            /** @noinspection ExceptionsAnnotatingAndHandlingInspection */
            $getSkill = StringTools::assembleGetterForName($skillWithWeapon->getValue());
            /** @var PsychicalSkill $skill */
            $skill = $skills->getPsychicalSkills()->$getSkill();
            /** @noinspection ExceptionsAnnotatingAndHandlingInspection */
            $physicalSkillPoint = PsychicalSkillPoint::createFromFirstLevelSkillPointsFromBackground(
                $professionFirstLevel,
                $skillPointsFromBackground,
                Tables::getIt()
            );
            while ($skillRankWithWeapon-- > 0) {
                /** @noinspection ExceptionsAnnotatingAndHandlingInspection */
                $skill->increaseSkillRank($physicalSkillPoint);
            }

            return;
        }
        if (\in_array($skillWithWeapon->getValue(), CombinedSkillCode::getPossibleValues(), true)) {
            /** @noinspection ExceptionsAnnotatingAndHandlingInspection */
            $getSkill = StringTools::assembleGetterForName($skillWithWeapon->getValue());
            /** @var CombinedSkill $skill */
            $skill = $skills->getCombinedSkills()->$getSkill();
            /** @noinspection ExceptionsAnnotatingAndHandlingInspection */
            $combinedSkillPoint = CombinedSkillPoint::createFromFirstLevelSkillPointsFromBackground(
                $professionFirstLevel,
                $skillPointsFromBackground,
                Tables::getIt()
            );
            while ($skillRankWithWeapon-- > 0) {
                /** @noinspection ExceptionsAnnotatingAndHandlingInspection */
                $skill->increaseSkillRank($combinedSkillPoint);
            }

            return;
        }
        throw new Exceptions\WeaponSkillWithoutKnownSkillGroup(
            "Given skill with a weapon '{$skillWithWeapon}' does not belong to any skill group"
        );
    }

    /**
     * @return FightProperties
     * @throws \DrdPlus\Tables\Armaments\Exceptions\UnknownArmament
     * @throws \DrdPlus\Calculators\Fight\Exceptions\UnknownSkill
     * @throws \DrdPlus\Tables\Armaments\Exceptions\UnknownWeaponlike
     * @throws \DrdPlus\Tables\Armaments\Exceptions\CanNotHoldWeaponByOneHand
     * @throws \DrdPlus\Tables\Armaments\Exceptions\CanNotHoldWeaponByTwoHands
     * @throws \DrdPlus\Codes\Exceptions\ThereIsNoOppositeForTwoHandsHolding
     */
    public function getPreviousRangedFightProperties(): FightProperties
    {
        return $this->getPreviousFightProperties(
            $this->getPreviousArmaments()->getPreviousRangedWeapon(),
            $this->getPreviousArmaments()->getPreviousRangedWeaponHolding(),
            $this->getPreviousArmaments()->getPreviousRangedSkillCode(),
            $this->getPreviousArmaments()->getPreviousRangedSkillRank(),
            $this->getPreviousArmaments()->getPreviousShield(),
            $this->getPreviousArmaments()->getPreviousShieldUsageSkillRank(),
            $this->getPreviousArmaments()->getPreviousArmorSkillRank(),
            $this->getPreviousArmaments()->getPreviousOnHorseback(),
            $this->getPreviousArmaments()->getPreviousRidingSkillRank(),
            $this->getPreviousArmaments()->getPreviousFightFreeWillAnimal(),
            $this->getPreviousArmaments()->getPreviousZoologySkillRank()
        );
    }

    /**
     * @param WeaponlikeCode $weaponlikeCode
     * @param ItemHoldingCode $weaponHoldingCode
     * @param SkillCode $fightWithWeaponSkillCode
     * @param int $skillRankWithWeapon
     * @param ShieldCode $previousShield
     * @param int $shieldUsageSkillRank
     * @param int $armorSkillRank
     * @param bool $onHorseback
     * @param int $ridingSkillRank
     * @param bool $fightingFreeWillAnimal
     * @param int $zoologySkillRank
     * @return FightProperties
     * @throws \DrdPlus\Tables\Armaments\Exceptions\UnknownArmament
     */
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
            $this->getPreviousProperties()->getPreviousStrength(),
            $this->getPreviousProperties()->getPreviousAgility(),
            $this->getPreviousProperties()->getPreviousKnack(),
            $this->getPreviousProperties()->getPreviousWill(),
            $this->getPreviousProperties()->getPreviousIntelligence(),
            $this->getPreviousProperties()->getPreviousCharisma(),
            $this->getPreviousProperties()->getPreviousSize(),
            $this->getPreviousProperties()->getPreviousHeightInCm(),
            $weaponlikeCode,
            $weaponHoldingCode,
            $fightWithWeaponSkillCode,
            $skillRankWithWeapon,
            $previousShield,
            $shieldUsageSkillRank,
            $this->getPreviousArmaments()->getPreviousBodyArmor(),
            $this->getPreviousArmaments()->getPreviousHelm(),
            $armorSkillRank,
            $this->getPreviousProfessionCode(),
            $onHorseback,
            $ridingSkillRank,
            $fightingFreeWillAnimal,
            $zoologySkillRank
        );
    }

    /**
     * @return FightProperties
     * @throws \DrdPlus\Codes\Exceptions\ThereIsNoOppositeForTwoHandsHolding
     * @throws \DrdPlus\Tables\Armaments\Exceptions\UnknownWeaponlike
     * @throws \DrdPlus\Tables\Armaments\Exceptions\CanNotHoldWeaponByOneHand
     * @throws \DrdPlus\Tables\Armaments\Exceptions\CanNotHoldWeaponByTwoHands
     * @throws \DrdPlus\Tables\Armaments\Exceptions\UnknownArmament
     */
    public function getPreviousMeleeShieldFightProperties(): FightProperties
    {
        return $this->getPreviousFightProperties(
            $this->getPreviousArmaments()->getPreviousShield(),
            $this->getPreviousArmaments()->getPreviousMeleeShieldHolding(),
            PhysicalSkillCode::getIt(PhysicalSkillCode::FIGHT_WITH_SHIELDS),
            $this->getPreviousArmaments()->getPreviousFightWithShieldsSkillRank(),
            $this->getPreviousArmaments()->getPreviousShield(),
            $this->getPreviousArmaments()->getPreviousShieldUsageSkillRank(),
            $this->getPreviousArmaments()->getPreviousArmorSkillRank(),
            $this->getPreviousArmaments()->getPreviousOnHorseback(),
            $this->getPreviousArmaments()->getPreviousRidingSkillRank(),
            $this->getPreviousArmaments()->getPreviousFightFreeWillAnimal(),
            $this->getPreviousArmaments()->getPreviousZoologySkillRank()
        );
    }

    /**
     * @return FightProperties
     * @throws \DrdPlus\Codes\Exceptions\ThereIsNoOppositeForTwoHandsHolding
     * @throws \DrdPlus\Tables\Armaments\Exceptions\UnknownWeaponlike
     * @throws \DrdPlus\Tables\Armaments\Exceptions\CanNotHoldWeaponByOneHand
     * @throws \DrdPlus\Tables\Armaments\Exceptions\CanNotHoldWeaponByTwoHands
     * @throws \DrdPlus\Tables\Armaments\Exceptions\UnknownArmament
     */
    public function getPreviousRangedShieldFightProperties(): FightProperties
    {
        return $this->getPreviousFightProperties(
            $this->getPreviousArmaments()->getPreviousShield(),
            $this->getPreviousArmaments()->getPreviousRangedShieldHolding(),
            PhysicalSkillCode::getIt(PhysicalSkillCode::FIGHT_WITH_SHIELDS),
            $this->getPreviousArmaments()->getPreviousFightWithShieldsSkillRank(),
            $this->getPreviousArmaments()->getPreviousShield(),
            $this->getPreviousArmaments()->getPreviousShieldUsageSkillRank(),
            $this->getPreviousArmaments()->getPreviousArmorSkillRank(),
            $this->getPreviousArmaments()->getPreviousOnHorseback(),
            $this->getPreviousArmaments()->getPreviousRidingSkillRank(),
            $this->getPreviousArmaments()->getPreviousFightFreeWillAnimal(),
            $this->getPreviousArmaments()->getPreviousZoologySkillRank()
        );
    }

    /**
     * @return FightProperties
     * @throws \DrdPlus\Tables\Armaments\Exceptions\UnknownArmament
     */
    public function getPreviousGenericFightProperties(): FightProperties
    {
        return $this->getPreviousFightProperties(
            MeleeWeaponCode::getIt(MeleeWeaponCode::HAND),
            ItemHoldingCode::getIt(ItemHoldingCode::MAIN_HAND),
            PsychicalSkillCode::getIt(PsychicalSkillCode::ASTRONOMY), // whatever
            0, // zero skill rank
            ShieldCode::getIt(ShieldCode::WITHOUT_SHIELD),
            $this->getPreviousArmaments()->getPreviousShieldUsageSkillRank(),
            $this->getPreviousArmaments()->getPreviousArmorSkillRank(),
            $this->getPreviousArmaments()->getPreviousOnHorseback(),
            $this->getPreviousArmaments()->getPreviousRidingSkillRank(),
            $this->getPreviousArmaments()->getPreviousFightFreeWillAnimal(),
            $this->getPreviousArmaments()->getPreviousZoologySkillRank()
        );
    }

    /**
     * @return FightProperties
     * @throws \DrdPlus\Tables\Armaments\Exceptions\UnknownArmament
     * @throws \DrdPlus\Codes\Exceptions\ThereIsNoOppositeForTwoHandsHolding
     * @throws \DrdPlus\Tables\Armaments\Exceptions\UnknownWeaponlike
     * @throws \DrdPlus\Tables\Armaments\Exceptions\CanNotHoldWeaponByOneHand
     * @throws \DrdPlus\Tables\Armaments\Exceptions\CanNotHoldWeaponByTwoHands
     * @throws \DrdPlus\Calculators\Fight\Exceptions\UnknownSkill
     */
    public function getPreviousMeleeWeaponFightProperties(): FightProperties
    {
        return $this->getPreviousFightProperties(
            $this->getPreviousArmaments()->getPreviousMeleeWeapon(),
            $this->getPreviousArmaments()->getPreviousMeleeWeaponHolding(),
            $this->getPreviousMeleeSkillCode(),
            $this->getPreviousMeleeSkillRank(),
            $this->getPreviousArmaments()->getPreviousShield(),
            $this->getPreviousArmaments()->getPreviousShieldUsageSkillRank(),
            $this->getPreviousArmaments()->getPreviousArmorSkillRank(),
            $this->getPreviousArmaments()->getPreviousOnHorseback(),
            $this->getPreviousArmaments()->getPreviousRidingSkillRank(),
            $this->getPreviousArmaments()->getPreviousFightFreeWillAnimal(),
            $this->getPreviousArmaments()->getPreviousZoologySkillRank()
        );
    }

    /**
     * @return FightProperties
     * @throws \DrdPlus\Codes\Exceptions\ThereIsNoOppositeForTwoHandsHolding
     * @throws \DrdPlus\Tables\Armaments\Exceptions\UnknownWeaponlike
     * @throws \DrdPlus\Tables\Armaments\Exceptions\CanNotHoldWeaponByOneHand
     * @throws \DrdPlus\Tables\Armaments\Exceptions\CanNotHoldWeaponByTwoHands
     * @throws \DrdPlus\Codes\Exceptions\ThereIsNoOppositeForTwoHandsHolding
     */
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

    /**
     * @return FightProperties
     * @throws \DrdPlus\Tables\Armaments\Exceptions\UnknownWeaponlike
     * @throws \DrdPlus\Tables\Armaments\Exceptions\CanNotHoldWeaponByOneHand
     * @throws \DrdPlus\Tables\Armaments\Exceptions\CanNotHoldWeaponByTwoHands
     * @throws \DrdPlus\Codes\Exceptions\ThereIsNoOppositeForTwoHandsHolding
     * @throws \DrdPlus\Calculators\Fight\Exceptions\UnknownSkill
     */
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

    /**
     * @return SkillCode
     * @throws \DrdPlus\Calculators\Fight\Exceptions\UnknownSkill
     */
    private function getPreviousMeleeSkillCode(): SkillCode
    {
        return $this->getSelectedSkill($this->getHistory()->getValue(Controller::MELEE_FIGHT_SKILL));
    }

    private function getPreviousMeleeSkillRank(): int
    {
        return (int)$this->getHistory()->getValue(Controller::MELEE_FIGHT_SKILL_RANK);
    }

    /**
     * @return History
     */
    private function getHistory(): History
    {
        return $this->history;
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

    /**
     * @return SkillCode
     * @throws \DrdPlus\Calculators\Fight\Exceptions\UnknownSkill
     */
    public function getSelectedMeleeSkillCode(): SkillCode
    {
        return $this->getSelectedSkill($this->getCurrentValues()->getValue(Controller::MELEE_FIGHT_SKILL));
    }

    public function getSelectedMeleeSkillRank(): int
    {
        return (int)$this->getCurrentValues()->getValue(Controller::MELEE_FIGHT_SKILL_RANK);
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
                /** @noinspection ExceptionsAnnotatingAndHandlingInspection */
                return StringTools::toConstant(WeaponCategoryCode::getIt($categoryName)->translateTo('en', 4));
            },
            $weaponCategoryValues
        );

        return \array_filter($fightWith, function (string $skillName) use ($categoryNames) {
            $categoryFromSkill = \str_replace(['fight_with_', 'fight_' /*without weapon */], '', $skillName);

            return \in_array($categoryFromSkill, $categoryNames, true);
        });
    }

    /**
     * @return SkillCode
     * @throws \DrdPlus\Calculators\Fight\Exceptions\UnknownSkill
     */
    public function getSelectedRangedSkillCode(): SkillCode
    {
        return $this->getSelectedSkill($this->getCurrentValues()->getValue(Controller::RANGED_FIGHT_SKILL));
    }

    public function getSelectedShieldUsageSkillRank(): int
    {
        return (int)$this->getCurrentValues()->getValue(Controller::SHIELD_USAGE_SKILL_RANK);
    }

    public function getCurrentArmorSkillRank(): int
    {
        return (int)$this->getCurrentValues()->getValue(Controller::ARMOR_SKILL_VALUE);
    }

    public function getSelectedRangedSkillRank(): int
    {
        return (int)$this->getCurrentValues()->getValue(Controller::RANGED_FIGHT_SKILL_RANK);
    }

    public function getSelectedFightWithShieldsSkillRank(): int
    {
        return (int)$this->getCurrentValues()->getValue(Controller::FIGHT_WITH_SHIELDS_SKILL_RANK);
    }

    public function getSelectedProfessionCode(): ProfessionCode
    {
        $selectedProfession = $this->getCurrentValues()->getValue(Controller::PROFESSION);
        if (!$selectedProfession) {
            return ProfessionCode::getIt(ProfessionCode::COMMONER);
        }

        return ProfessionCode::getIt($selectedProfession);
    }

    private function getPreviousProfessionCode(): ProfessionCode
    {
        $previousProfession = $this->getHistory()->getValue(Controller::PROFESSION);
        if (!$previousProfession) {
            return $this->getSelectedProfessionCode();
        }

        return ProfessionCode::getIt($previousProfession);
    }

    public function getSelectedOnHorseback(): bool
    {
        return (bool)$this->getCurrentValues()->getValue(Controller::ON_HORSEBACK);
    }

    public function getSelectedRidingSkillRank(): int
    {
        return (int)$this->getCurrentValues()->getValue(Controller::RIDING_SKILL_RANK);
    }

    public function getSelectedFightFreeWillAnimal(): bool
    {
        return (bool)$this->getCurrentValues()->getValue(Controller::FIGHT_FREE_WILL_ANIMAL);
    }

    public function getSelectedZoologySkillRank(): int
    {
        return (int)$this->getCurrentValues()->getValue(Controller::ZOOLOGY_SKILL_RANK);
    }

    /**
     * @return Distance
     * @throws \DrdPlus\Calculators\Fight\Exceptions\UnknownSkill
     * @throws \DrdPlus\Tables\Armaments\Exceptions\UnknownWeaponlike
     * @throws \DrdPlus\Tables\Armaments\Exceptions\CanNotHoldWeaponByOneHand
     * @throws \DrdPlus\Tables\Armaments\Exceptions\CanNotHoldWeaponByTwoHands
     * @throws \DrdPlus\Codes\Exceptions\ThereIsNoOppositeForTwoHandsHolding
     */
    public function getCurrentTargetDistance(): Distance
    {
        $distanceValue = $this->getCurrentValues()->getValue(Controller::RANGED_TARGET_DISTANCE);
        if ($distanceValue === null) {
            $distanceValue = AttackNumberByContinuousDistanceTable::DISTANCE_WITH_NO_IMPACT_TO_ATTACK_NUMBER;
        }
        $distanceValue = \min($distanceValue, $this->getCurrentRangedWeaponMaximalRange());

        /** @noinspection ExceptionsAnnotatingAndHandlingInspection */
        return new Distance($distanceValue, DistanceUnitCode::METER, Tables::getIt()->getDistanceTable());
    }

    /**
     * @return float
     * @throws \DrdPlus\Calculators\Fight\Exceptions\UnknownSkill
     * @throws \DrdPlus\Tables\Armaments\Exceptions\UnknownWeaponlike
     * @throws \DrdPlus\Tables\Armaments\Exceptions\CanNotHoldWeaponByOneHand
     * @throws \DrdPlus\Tables\Armaments\Exceptions\CanNotHoldWeaponByTwoHands
     * @throws \DrdPlus\Codes\Exceptions\ThereIsNoOppositeForTwoHandsHolding
     */
    private function getCurrentRangedWeaponMaximalRange(): float
    {
        return $this->getCurrentRangedFightProperties()->getMaximalRange()->getInMeters(Tables::getIt());
    }

    public function getCurrentTargetSize(): Size
    {
        $distanceValue = $this->getCurrentValues()->getValue(Controller::RANGED_TARGET_SIZE);
        if ($distanceValue === null) {
            return Size::getIt(1);
        }

        return Size::getIt($distanceValue);
    }

    /**
     * @return Distance
     * @throws \DrdPlus\Calculators\Fight\Exceptions\UnknownSkill
     * @throws \DrdPlus\Tables\Armaments\Exceptions\UnknownWeaponlike
     * @throws \DrdPlus\Tables\Armaments\Exceptions\CanNotHoldWeaponByOneHand
     * @throws \DrdPlus\Tables\Armaments\Exceptions\CanNotHoldWeaponByTwoHands
     * @throws \DrdPlus\Codes\Exceptions\ThereIsNoOppositeForTwoHandsHolding
     * @throws \DrdPlus\Tables\Armaments\Exceptions\UnknownArmament
     */
    public function getPreviousTargetDistance(): Distance
    {
        $distanceValue = $this->getHistory()->getValue(Controller::RANGED_TARGET_DISTANCE);
        if ($distanceValue === null) {
            $distanceValue = AttackNumberByContinuousDistanceTable::DISTANCE_WITH_NO_IMPACT_TO_ATTACK_NUMBER;
        }
        $distanceValue = \min($distanceValue, $this->getPreviousRangedWeaponMaximalRange());

        /** @noinspection ExceptionsAnnotatingAndHandlingInspection */
        return new Distance($distanceValue, DistanceUnitCode::METER, Tables::getIt()->getDistanceTable());
    }

    /**
     * @return float
     * @throws \DrdPlus\Calculators\Fight\Exceptions\UnknownSkill
     * @throws \DrdPlus\Tables\Armaments\Exceptions\UnknownWeaponlike
     * @throws \DrdPlus\Tables\Armaments\Exceptions\CanNotHoldWeaponByOneHand
     * @throws \DrdPlus\Tables\Armaments\Exceptions\CanNotHoldWeaponByTwoHands
     * @throws \DrdPlus\Codes\Exceptions\ThereIsNoOppositeForTwoHandsHolding
     * @throws \DrdPlus\Tables\Armaments\Exceptions\UnknownArmament
     */
    private function getPreviousRangedWeaponMaximalRange(): float
    {
        return $this->getPreviousRangedFightProperties()->getMaximalRange()->getInMeters(Tables::getIt());
    }

    public function getPreviousTargetSize(): Size
    {
        $distanceValue = $this->getHistory()->getValue(Controller::RANGED_TARGET_SIZE);
        if ($distanceValue === null) {
            return Size::getIt(1);
        }

        return Size::getIt($distanceValue);
    }
}