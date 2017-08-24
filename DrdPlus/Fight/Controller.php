<?php
namespace DrdPlus\Fight;

use DrdPlus\Codes\Armaments\ArmamentCode;
use DrdPlus\Codes\Armaments\BodyArmorCode;
use DrdPlus\Codes\Armaments\HelmCode;
use DrdPlus\Codes\Armaments\MeleeWeaponCode;
use DrdPlus\Codes\Armaments\RangedWeaponCode;
use DrdPlus\Codes\Armaments\ShieldCode;
use DrdPlus\Codes\Armaments\WeaponCategoryCode;
use DrdPlus\Codes\Armaments\WeaponCode;
use DrdPlus\Codes\Armaments\WeaponlikeCode;
use DrdPlus\Codes\ItemHoldingCode;
use DrdPlus\Codes\ProfessionCode;
use DrdPlus\Codes\Properties\PropertyCode;
use DrdPlus\Codes\Skills\CombinedSkillCode;
use DrdPlus\Codes\Skills\PhysicalSkillCode;
use DrdPlus\Codes\Skills\PsychicalSkillCode;
use DrdPlus\Codes\Skills\SkillCode;
use DrdPlus\Configurator\Skeleton\History;
use DrdPlus\FightProperties\FightProperties;
use DrdPlus\Properties\Base\Agility;
use DrdPlus\Properties\Base\Charisma;
use DrdPlus\Properties\Base\Intelligence;
use DrdPlus\Properties\Base\Knack;
use DrdPlus\Properties\Base\Strength;
use DrdPlus\Properties\Base\Will;
use DrdPlus\Properties\Body\HeightInCm;
use DrdPlus\Properties\Body\Size;
use DrdPlus\Tables\Tables;
use Granam\Integer\IntegerInterface;
use Granam\Integer\Tools\ToInteger;
use Granam\String\StringTools;

class Controller extends \DrdPlus\Configurator\Skeleton\Controller
{
    const MELEE_WEAPON = 'melee_weapon';
    const RANGED_WEAPON = 'ranged_weapon';
    const STRENGTH = PropertyCode::STRENGTH;
    const AGILITY = PropertyCode::AGILITY;
    const KNACK = PropertyCode::KNACK;
    const WILL = PropertyCode::WILL;
    const INTELLIGENCE = PropertyCode::INTELLIGENCE;
    const CHARISMA = PropertyCode::CHARISMA;
    const SIZE = PropertyCode::SIZE;
    const HEIGHT_IN_CM = PropertyCode::HEIGHT_IN_CM;
    const MELEE_WEAPON_HOLDING = 'melee_weapon_holding';
    const RANGED_WEAPON_HOLDING = 'ranged_weapon_holding';
    const PROFESSION = 'profession';
    const MELEE_FIGHT_SKILL = 'melee_fight_skill';
    const MELEE_FIGHT_SKILL_RANK = 'melee_fight_skill_rank';
    const RANGED_FIGHT_SKILL = 'ranged_fight_skill';
    const RANGED_FIGHT_SKILL_RANK = 'ranged_fight_skill_rank';
    const SHIELD = 'shield';
    const SHIELD_USAGE_SKILL_RANK = 'shield_usage_skill_rank';
    const FIGHT_WITH_SHIELDS_SKILL_RANK = 'fight_with_shields_skill_rank';
    const BODY_ARMOR = 'body_armor';
    const ARMOR_SKILL_VALUE = 'armor_skill_value';
    const HELM = 'helm';
    const ON_HORSEBACK = 'on_horseback';
    const RIDING_SKILL_RANK = 'riding_skill_rank';
    const FIGHT_FREE_WILL_ANIMAL = 'fight_free_will_animal';
    const ZOOLOGY_SKILL_RANK = 'zoology_skill_rank';
    const SCROLL_FROM_TOP = 'scroll_from_top';

    /** @var CurrentValues */
    private $currentValues;
    /** @var PreviousValues */
    private $previousValues;

    public function __construct()
    {
        parent::__construct('fight' /* cookies postfix */);
        $this->currentValues = new CurrentValues($_GET, $this->getHistoryWithSkillRanks());
        $this->previousValues = new PreviousValues($_GET);
    }

    protected function createHistory(string $cookiesPostfix, int $cookiesTtl = null): History
    {
        return new HistoryWithSkillRanks(
            [
                self::MELEE_FIGHT_SKILL => self::MELEE_FIGHT_SKILL_RANK,
                self::RANGED_FIGHT_SKILL => self::RANGED_FIGHT_SKILL_RANK,
                self::RANGED_FIGHT_SKILL => self::RANGED_FIGHT_SKILL_RANK,
            ],
            !empty($_POST[self::DELETE_HISTORY]), // clear history?
            $_GET, // values to remember
            !empty($_GET[self::REMEMBER_HISTORY]), // should remember given values
            $cookiesPostfix,
            $cookiesTtl
        );
    }

    /**
     * @return HistoryWithSkillRanks|History
     */
    private function getHistoryWithSkillRanks(): HistoryWithSkillRanks
    {
        return $this->getHistory();
    }

    public function shouldRemember(): bool
    {
        return $this->getHistory()->shouldRemember();
    }

    public function getSelectedScrollFromTop(): int
    {
        return (int)$this->currentValues->getValue(self::SCROLL_FROM_TOP);
    }

    public function getMeleeWeaponCodes(): array
    {
        $weaponCodes = [
            WeaponCategoryCode::AXE => MeleeWeaponCode::getAxeCodes(),
            WeaponCategoryCode::KNIFE_AND_DAGGER => MeleeWeaponCode::getKnifeAndDaggerCodes(),
            WeaponCategoryCode::MACE_AND_CLUB => MeleeWeaponCode::getMaceAndClubCodes(),
            WeaponCategoryCode::MORNINGSTAR_AND_MORGENSTERN => MeleeWeaponCode::getMorningstarAndMorgensternCodes(),
            WeaponCategoryCode::SABER_AND_BOWIE_KNIFE => MeleeWeaponCode::getSaberAndBowieKnifeCodes(),
            WeaponCategoryCode::STAFF_AND_SPEAR => MeleeWeaponCode::getStaffAndSpearCodes(),
            WeaponCategoryCode::SWORD => MeleeWeaponCode::getSwordCodes(),
            WeaponCategoryCode::VOULGE_AND_TRIDENT => MeleeWeaponCode::getVoulgeAndTridentCodes(),
            WeaponCategoryCode::UNARMED => MeleeWeaponCode::getUnarmedCodes(),
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

        return $this->addUsability($meleeWeaponCodes);
    }

    /**
     * @param array|ArmamentCode[] $codes
     * @return array
     */
    private function addUsability(array $codes): array
    {
        $withUsagePossibility = [];
        foreach ($codes as $code) {
            $withUsagePossibility[] = [
                'code' => $code,
                'canUseIt' => $this->canUseArmament($code),
            ];
        }

        return $withUsagePossibility;
    }

    private function canUseArmament(ArmamentCode $armamentCode): bool
    {
        return Tables::getIt()->getArmourer()
            ->canUseArmament($armamentCode, $this->getSelectedStrength(), $this->getSelectedSize());
    }

    private function couldUseArmament(ArmamentCode $armamentCode): bool
    {
        return Tables::getIt()->getArmourer()
            ->canUseArmament($armamentCode, $this->getPreviousStrength(), $this->getPreviousSize());
    }

    public function getRangedWeaponCodes(): array
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

        return $this->addUsability($meleeWeaponCodes);
    }

    /**
     * @return MeleeWeaponCode
     */
    public function getSelectedMeleeWeapon(): MeleeWeaponCode
    {
        $meleeWeaponValue = $this->currentValues->getValue(self::MELEE_WEAPON);
        if (!$meleeWeaponValue) {
            return MeleeWeaponCode::getIt(MeleeWeaponCode::HAND);
        }

        $meleeWeapon = MeleeWeaponCode::getIt($meleeWeaponValue);
        if (!$this->canUseArmament($meleeWeapon)) {
            return MeleeWeaponCode::getIt(MeleeWeaponCode::HAND);
        }

        return $meleeWeapon;
    }

    /**
     * @return MeleeWeaponCode
     */
    private function getPreviousMeleeWeapon(): MeleeWeaponCode
    {
        $meleeWeaponValue = $this->previousValues->getValue(self::MELEE_WEAPON);
        if (!$meleeWeaponValue) {
            $meleeWeapon = $this->getSelectedMeleeWeapon();
        } else {
            $meleeWeapon = MeleeWeaponCode::getIt($meleeWeaponValue);
        }
        if (!$this->couldUseArmament($meleeWeapon)) {
            return MeleeWeaponCode::getIt(MeleeWeaponCode::HAND);
        }

        return $meleeWeapon;
    }

    /**
     * @return RangedWeaponCode
     */
    public function getSelectedRangedWeapon(): RangedWeaponCode
    {
        $rangedWeaponValue = $this->currentValues->getValue(self::RANGED_WEAPON);
        if (!$rangedWeaponValue) {
            return RangedWeaponCode::getIt(RangedWeaponCode::ROCK);
        }

        $rangedWeapon = RangedWeaponCode::getIt($rangedWeaponValue);
        if (!$this->canUseArmament($rangedWeapon)) {
            return RangedWeaponCode::getIt(RangedWeaponCode::ROCK);
        }

        return $rangedWeapon;
    }

    /**
     * @return RangedWeaponCode
     */
    private function getPreviousRangedWeapon(): RangedWeaponCode
    {
        $rangedWeaponValue = $this->previousValues->getValue(self::RANGED_WEAPON);
        if (!$rangedWeaponValue) {
            $rangedWeapon = $this->getSelectedRangedWeapon();
        } else {
            $rangedWeapon = RangedWeaponCode::getIt($rangedWeaponValue);
        }
        if (!$this->couldUseArmament($rangedWeapon)) {
            return RangedWeaponCode::getIt(RangedWeaponCode::ROCK);
        }

        return $rangedWeapon;
    }

    public function getSelectedStrength(): Strength
    {
        return Strength::getIt((int)$this->currentValues->getValue(self::STRENGTH));
    }

    private function getPreviousStrength(): Strength
    {
        return Strength::getIt((int)$this->previousValues->getValue(self::STRENGTH));
    }

    public function getSelectedAgility(): Agility
    {
        return Agility::getIt((int)$this->currentValues->getValue(self::AGILITY));
    }

    private function getPreviousAgility(): Agility
    {
        return Agility::getIt((int)$this->previousValues->getValue(self::AGILITY));
    }

    public function getSelectedKnack(): Knack
    {
        return Knack::getIt((int)$this->currentValues->getValue(self::KNACK));
    }

    private function getPreviousKnack(): Knack
    {
        return Knack::getIt((int)$this->previousValues->getValue(self::KNACK));
    }

    public function getSelectedWill(): Will
    {
        return Will::getIt((int)$this->currentValues->getValue(self::WILL));
    }

    private function getPreviousWill(): Will
    {
        return Will::getIt((int)$this->previousValues->getValue(self::WILL));
    }

    public function getSelectedIntelligence(): Intelligence
    {
        return Intelligence::getIt((int)$this->currentValues->getValue(self::INTELLIGENCE));
    }

    private function getPreviousIntelligence(): Intelligence
    {
        return Intelligence::getIt((int)$this->previousValues->getValue(self::INTELLIGENCE));
    }

    public function getSelectedCharisma(): Charisma
    {
        return Charisma::getIt((int)$this->currentValues->getValue(self::CHARISMA));
    }

    private function getPreviousCharisma(): Charisma
    {
        return Charisma::getIt((int)$this->previousValues->getValue(self::CHARISMA));
    }

    public function getSelectedSize(): Size
    {
        return Size::getIt((int)$this->currentValues->getValue(self::SIZE));
    }

    private function getPreviousSize(): Size
    {
        return Size::getIt((int)$this->previousValues->getValue(self::SIZE));
    }

    public function getSelectedHeightInCm(): HeightInCm
    {
        return HeightInCm::getIt($this->currentValues->getValue(self::HEIGHT_IN_CM) ?? 150);
    }

    private function getPreviousHeightInCm(): HeightInCm
    {
        return HeightInCm::getIt($this->previousValues->getValue(self::HEIGHT_IN_CM) ?? 150);
    }

    public function getMeleeWeaponFightProperties(): FightProperties
    {
        return $this->getCurrentFightProperties(
            $this->getSelectedMeleeWeapon(),
            $this->getSelectedMeleeWeaponHolding(),
            $this->getSelectedMeleeSkillCode(),
            $this->getSelectedMeleeSkillRank(),
            $this->getSelectedShield()
        );
    }

    private function getCurrentFightProperties(
        WeaponlikeCode $weaponlikeCode,
        ItemHoldingCode $weaponHoldingCode,
        SkillCode $fightWithWeaponSkillCode,
        int $skillRank,
        ShieldCode $shieldCode
    ): FightProperties
    {
        return $this->currentValues->getFightProperties(
            $this->getSelectedStrength(),
            $this->getSelectedAgility(),
            $this->getSelectedKnack(),
            $this->getSelectedWill(),
            $this->getSelectedIntelligence(),
            $this->getSelectedCharisma(),
            $this->getSelectedSize(),
            $this->getSelectedHeightInCm(),
            $weaponlikeCode,
            $weaponHoldingCode,
            $fightWithWeaponSkillCode,
            $skillRank,
            $shieldCode,
            $this->getSelectedShieldUsageSkillRank(),
            $this->getSelectedBodyArmor(),
            $this->getSelectedHelm(),
            $this->getSelectedArmorSkillRank(),
            $this->getSelectedProfessionCode(),
            $this->getSelectedOnHorseback(),
            $this->getSelectedRidingSkillRank(),
            $this->getSelectedFightFreeWillAnimal(),
            $this->getSelectedZoologySkillRank()
        );
    }

    public function getPreviousMeleeWeaponFightProperties(): FightProperties
    {
        return $this->getPreviousFightProperties(
            $this->getPreviousMeleeWeapon(),
            $this->getPreviousMeleeWeaponHolding(),
            $this->getPreviousMeleeSkillCode(),
            $this->getPreviousMeleeSkillRank(),
            $this->getPreviousShield()
        );
    }

    private function getPreviousFightProperties(
        WeaponlikeCode $weaponlikeCode,
        ItemHoldingCode $weaponHoldingCode,
        SkillCode $fightWithWeaponSkillCode,
        int $skillRank,
        ShieldCode $shieldCode
    ): FightProperties
    {
        return $this->previousValues->getFightProperties(
            $this->getPreviousStrength(),
            $this->getPreviousAgility(),
            $this->getPreviousKnack(),
            $this->getPreviousWill(),
            $this->getPreviousIntelligence(),
            $this->getPreviousCharisma(),
            $this->getPreviousSize(),
            $this->getPreviousHeightInCm(),
            $weaponlikeCode,
            $weaponHoldingCode,
            $fightWithWeaponSkillCode,
            $skillRank,
            $shieldCode,
            $this->getPreviousShieldUsageSkillRank(),
            $this->getPreviousBodyArmor(),
            $this->getPreviousHelm(),
            $this->getPreviousArmorSkillRank(),
            $this->getPreviousProfessionCode(),
            $this->getPreviousOnHorseback(),
            $this->getPreviousRidingSkillRank(),
            $this->getPreviousFightFreeWillAnimal(),
            $this->getPreviousZoologySkillRank()
        );
    }

    public function isTwoHandedOnly(WeaponCode $weaponCode): bool
    {
        return Tables::getIt()->getArmourer()->isTwoHandedOnly($weaponCode);
    }

    public function isOneHandedOnly(WeaponCode $weaponCode): bool
    {
        return Tables::getIt()->getArmourer()->isOneHandedOnly($weaponCode);
    }

    public function getSelectedMeleeWeaponHolding(): ItemHoldingCode
    {
        return $this->getWeaponHolding(
            $this->getSelectedMeleeWeapon(),
            $this->currentValues->getValue(self::MELEE_WEAPON_HOLDING)
        );
    }

    private function getWeaponHolding(WeaponCode $weaponCode, string $weaponHolding): ItemHoldingCode
    {
        if ($this->isTwoHandedOnly($weaponCode)) {
            return ItemHoldingCode::getIt(ItemHoldingCode::TWO_HANDS);
        }
        if ($this->isOneHandedOnly($weaponCode)) {
            return ItemHoldingCode::getIt(ItemHoldingCode::MAIN_HAND);
        }
        if (!$weaponHolding) {
            return ItemHoldingCode::getIt(ItemHoldingCode::MAIN_HAND);
        }

        return ItemHoldingCode::getIt($weaponHolding);
    }

    private function getPreviousMeleeWeaponHolding(): ItemHoldingCode
    {
        return $this->getWeaponHolding(
            $this->getPreviousMeleeWeapon(),
            $this->previousValues->getValue(self::MELEE_WEAPON_HOLDING)
        );
    }

    public function getGenericFightProperties(): FightProperties
    {
        return $this->getCurrentFightProperties(
            MeleeWeaponCode::getIt(MeleeWeaponCode::HAND),
            ItemHoldingCode::getIt(ItemHoldingCode::MAIN_HAND),
            PsychicalSkillCode::getIt(PsychicalSkillCode::ASTRONOMY), // whatever
            0, // zero skill rank
            ShieldCode::getIt(ShieldCode::WITHOUT_SHIELD)
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

    public function getMeleeShieldFightProperties(): FightProperties
    {
        return $this->getCurrentFightProperties(
            $this->getSelectedShield(),
            $this->getCurrentMeleeShieldHolding(),
            PhysicalSkillCode::getIt(PhysicalSkillCode::FIGHT_WITH_SHIELDS),
            $this->getSelectedFightWithShieldsSkillRank(),
            $this->getSelectedShield()
        );
    }

    public function getPreviousMeleeShieldFightProperties(): FightProperties
    {
        return $this->getPreviousFightProperties(
            $this->getPreviousShield(),
            $this->getPreviousMeleeShieldHolding(),
            PhysicalSkillCode::getIt(PhysicalSkillCode::FIGHT_WITH_SHIELDS),
            $this->getPreviousFightWithShieldsSkillRank(),
            $this->getPreviousShield()
        );
    }

    public function getRangedShieldFightProperties(): FightProperties
    {
        return $this->getCurrentFightProperties(
            $this->getSelectedShield(),
            $this->getCurrentRangedShieldHolding(),
            PhysicalSkillCode::getIt(PhysicalSkillCode::FIGHT_WITH_SHIELDS),
            $this->getSelectedFightWithShieldsSkillRank(),
            $this->getSelectedShield()
        );
    }

    public function getPreviousRangedShieldFightProperties(): FightProperties
    {
        return $this->getPreviousFightProperties(
            $this->getPreviousShield(),
            $this->getPreviousRangedShieldHolding(),
            PhysicalSkillCode::getIt(PhysicalSkillCode::FIGHT_WITH_SHIELDS),
            $this->getPreviousFightWithShieldsSkillRank(),
            $this->getPreviousShield()
        );
    }

    /**
     * @return ItemHoldingCode
     * @throws \DrdPlus\Codes\Exceptions\ThereIsNoOppositeForTwoHandsHolding
     */
    public function getCurrentMeleeShieldHolding(): ItemHoldingCode
    {
        return $this->getShieldHolding($this->getSelectedMeleeWeaponHolding(), $this->getSelectedMeleeWeapon());
    }

    /**
     * @return ItemHoldingCode
     * @throws \DrdPlus\Codes\Exceptions\ThereIsNoOppositeForTwoHandsHolding
     */
    public function getPreviousMeleeShieldHolding(): ItemHoldingCode
    {
        return $this->getShieldHolding($this->getPreviousMeleeWeaponHolding(), $this->getPreviousMeleeWeapon());
    }

    /**
     * @param ItemHoldingCode $weaponHolding
     * @param WeaponlikeCode $weaponlikeCode
     * @return ItemHoldingCode
     * @throws \DrdPlus\Codes\Exceptions\ThereIsNoOppositeForTwoHandsHolding
     */
    private function getShieldHolding(ItemHoldingCode $weaponHolding, WeaponlikeCode $weaponlikeCode): ItemHoldingCode
    {
        if ($weaponHolding->holdsByTwoHands()) {
            /** @noinspection ExceptionsAnnotatingAndHandlingInspection */
            if (Tables::getIt()->getArmourer()->canHoldItByTwoHands($this->getSelectedShield())) {
                // because two-handed weapon has to be dropped to use shield and then both hands can be used for shield
                return ItemHoldingCode::getIt(ItemHoldingCode::TWO_HANDS);
            }

            return ItemHoldingCode::getIt(ItemHoldingCode::MAIN_HAND);
        }
        /** @noinspection ExceptionsAnnotatingAndHandlingInspection */
        if ($weaponlikeCode->isUnarmed() && Tables::getIt()->getArmourer()->canHoldItByTwoHands($this->getSelectedShield())) {
            return ItemHoldingCode::getIt(ItemHoldingCode::TWO_HANDS);
        }

        return $weaponHolding->getOpposite();
    }

    /**
     * @return ItemHoldingCode
     * @throws \DrdPlus\Codes\Exceptions\ThereIsNoOppositeForTwoHandsHolding
     */
    public function getCurrentRangedShieldHolding(): ItemHoldingCode
    {
        return $this->getShieldHolding($this->getSelectedRangedWeaponHolding(), $this->getSelectedRangedWeapon());
    }

    /**
     * @return ItemHoldingCode
     * @throws \DrdPlus\Codes\Exceptions\ThereIsNoOppositeForTwoHandsHolding
     */
    public function getPreviousRangedShieldHolding(): ItemHoldingCode
    {
        return $this->getShieldHolding($this->getPreviousRangedWeaponHolding(), $this->getPreviousRangedWeapon());
    }

    public function getRangedFightProperties(): FightProperties
    {
        return $this->getCurrentFightProperties(
            $this->getSelectedRangedWeapon(),
            $this->getSelectedRangedWeaponHolding(),
            $this->getSelectedRangedSkillCode(),
            $this->getSelectedRangedSkillRank(),
            $this->getSelectedShield()
        );
    }

    public function getPreviousRangedFightProperties(): FightProperties
    {
        return $this->getPreviousFightProperties(
            $this->getPreviousRangedWeapon(),
            $this->getPreviousRangedWeaponHolding(),
            $this->getPreviousRangedSkillCode(),
            $this->getPreviousRangedSkillRank(),
            $this->getPreviousShield()
        );
    }

    public function getSelectedRangedWeaponHolding(): ItemHoldingCode
    {
        return $this->getWeaponHolding(
            $this->getSelectedRangedWeapon(),
            $this->currentValues->getValue(self::RANGED_WEAPON_HOLDING)
        );
    }

    private function getPreviousRangedWeaponHolding(): ItemHoldingCode
    {
        return $this->getWeaponHolding(
            $this->getPreviousRangedWeapon(),
            $this->previousValues->getValue(self::RANGED_WEAPON_HOLDING)
        );
    }

    public function getSelectedProfessionCode(): ProfessionCode
    {
        $selectedProfession = $this->currentValues->getValue(self::PROFESSION);
        if (!$selectedProfession) {
            return ProfessionCode::getIt(ProfessionCode::COMMONER);
        }

        return ProfessionCode::getIt($selectedProfession);
    }

    private function getPreviousProfessionCode(): ProfessionCode
    {
        $previousProfession = $this->previousValues->getValue(self::PROFESSION);
        if (!$previousProfession) {
            return $this->getSelectedProfessionCode();
        }

        return ProfessionCode::getIt($previousProfession);
    }

    /**
     * @return array|SkillCode[]
     */
    public function getPossibleSkillsForMelee(): array
    {
        return $this->getPossibleSkillsForCategories(WeaponCategoryCode::getMeleeWeaponCategoryValues());
    }

    /**
     * @param array|string $weaponCategoryValues
     * @return array|SkillCode[]
     */
    private function getPossibleSkillsForCategories(array $weaponCategoryValues): array
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

    /**
     * @return array|SkillCode[]
     */
    public function getPossibleSkillsForRanged(): array
    {
        return $this->getPossibleSkillsForCategories(WeaponCategoryCode::getRangedWeaponCategoryValues());
    }

    public function getSelectedMeleeSkillCode(): SkillCode
    {
        return $this->getSelectedSkill($this->currentValues->getValue(self::MELEE_FIGHT_SKILL));
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

    private function getPreviousMeleeSkillCode(): SkillCode
    {
        return $this->getSelectedSkill($this->previousValues->getValue(self::MELEE_FIGHT_SKILL));
    }

    public function getSelectedRangedSkillCode(): SkillCode
    {
        return $this->getSelectedSkill($this->currentValues->getValue(self::RANGED_FIGHT_SKILL));
    }

    private function getPreviousRangedSkillCode(): SkillCode
    {
        return $this->getSelectedSkill($this->previousValues->getValue(self::RANGED_FIGHT_SKILL));
    }

    public function getSelectedMeleeSkillRank(): int
    {
        return (int)$this->currentValues->getValue(self::MELEE_FIGHT_SKILL_RANK);
    }

    private function getPreviousMeleeSkillRank(): int
    {
        return (int)$this->previousValues->getValue(self::MELEE_FIGHT_SKILL_RANK);
    }

    public function getHistoryMeleeSkillRanksJson(): string
    {
        return $this->arrayToJson($this->getHistoryWithSkillRanks()->getPreviousSkillRanks(self::MELEE_FIGHT_SKILL_RANK));
    }

    private function arrayToJson(array $values): string
    {
        return json_encode($values, JSON_PRETTY_PRINT | JSON_FORCE_OBJECT | JSON_UNESCAPED_UNICODE);
    }

    public function getHistoryRangedSkillRanksJson(): string
    {
        return $this->arrayToJson($this->getHistoryWithSkillRanks()->getPreviousSkillRanks(self::RANGED_FIGHT_SKILL_RANK));
    }

    public function getSelectedRangedSkillRank(): int
    {
        return (int)$this->currentValues->getValue(self::RANGED_FIGHT_SKILL_RANK);
    }

    private function getPreviousRangedSkillRank(): int
    {
        return (int)$this->previousValues->getValue(self::RANGED_FIGHT_SKILL_RANK);
    }

    /**
     * @return array
     */
    public function getPossibleShields(): array
    {
        return array_map(function (string $shieldValue) {
            $shieldCode = ShieldCode::getIt($shieldValue);

            return [
                'code' => $shieldCode,
                'canUseIt' => $this->canUseArmament($shieldCode),
            ];
        }, ShieldCode::getPossibleValues());
    }

    public function getSelectedShield(): ShieldCode
    {
        $selectedShieldValue = $this->currentValues->getValue(self::SHIELD);
        if (!$selectedShieldValue) {
            return ShieldCode::getIt(ShieldCode::WITHOUT_SHIELD);
        }
        $selectedShield = ShieldCode::getIt($selectedShieldValue);
        if (!$this->canUseArmament($selectedShield)) {
            return ShieldCode::getIt(ShieldCode::WITHOUT_SHIELD);
        }

        return $selectedShield;
    }

    private function getPreviousShield(): ShieldCode
    {
        $previousShieldValue = $this->previousValues->getValue(self::SHIELD);
        if (!$previousShieldValue) {
            return ShieldCode::getIt(ShieldCode::WITHOUT_SHIELD);
        }
        $previousShield = ShieldCode::getIt($previousShieldValue);
        if (!$this->canUseArmament($previousShield)) {
            return ShieldCode::getIt(ShieldCode::WITHOUT_SHIELD);
        }

        return $previousShield;
    }

    /**
     * @return PhysicalSkillCode
     */
    public function getShieldUsageSkillCode(): PhysicalSkillCode
    {
        return PhysicalSkillCode::getIt(PhysicalSkillCode::SHIELD_USAGE);
    }

    public function getSelectedShieldUsageSkillRank(): int
    {
        return (int)$this->currentValues->getValue(self::SHIELD_USAGE_SKILL_RANK);
    }

    private function getPreviousShieldUsageSkillRank(): int
    {
        return (int)$this->previousValues->getValue(self::SHIELD_USAGE_SKILL_RANK);
    }

    /**
     * @return PhysicalSkillCode
     */
    public function getFightWithShieldsSkillCode(): PhysicalSkillCode
    {
        return PhysicalSkillCode::getIt(PhysicalSkillCode::FIGHT_WITH_SHIELDS);
    }

    public function getSelectedFightWithShieldsSkillRank(): int
    {
        return (int)$this->currentValues->getValue(self::FIGHT_WITH_SHIELDS_SKILL_RANK);
    }

    private function getPreviousFightWithShieldsSkillRank(): int
    {
        return (int)$this->previousValues->getValue(self::FIGHT_WITH_SHIELDS_SKILL_RANK);
    }

    /**
     * @return array
     */
    public function getPossibleBodyArmors(): array
    {
        return array_map(function (string $armorValue) {
            $bodyArmor = BodyArmorCode::getIt($armorValue);

            return [
                'code' => $bodyArmor,
                'canUseIt' => $this->canUseArmament($bodyArmor),
            ];
        }, BodyArmorCode::getPossibleValues());
    }

    public function getSelectedBodyArmor(): BodyArmorCode
    {
        $selectedBodyArmorValue = $this->currentValues->getValue(self::BODY_ARMOR);
        if (!$selectedBodyArmorValue) {
            return BodyArmorCode::getIt(BodyArmorCode::WITHOUT_ARMOR);
        }
        $selectedBodyArmor = BodyArmorCode::getIt($selectedBodyArmorValue);
        if (!$this->canUseArmament($selectedBodyArmor)) {
            return BodyArmorCode::getIt(BodyArmorCode::WITHOUT_ARMOR);
        }

        return BodyArmorCode::getIt($selectedBodyArmorValue);
    }

    public function getProtectionOfBodyArmor(BodyArmorCode $bodyArmorCode): int
    {
        return Tables::getIt()->getBodyArmorsTable()->getProtectionOf($bodyArmorCode);
    }

    public function getProtectionOfSelectedBodyArmor(): int
    {
        return $this->getProtectionOfBodyArmor($this->getSelectedBodyArmor());
    }

    public function getProtectionOfPreviousBodyArmor(): int
    {
        return Tables::getIt()->getBodyArmorsTable()->getProtectionOf($this->getPreviousBodyArmor());
    }

    private function getPreviousBodyArmor(): BodyArmorCode
    {
        $previousBodyArmorValue = $this->previousValues->getValue(self::BODY_ARMOR);
        if (!$previousBodyArmorValue) {
            return BodyArmorCode::getIt(BodyArmorCode::WITHOUT_ARMOR);
        }
        $previousBodyArmor = BodyArmorCode::getIt($previousBodyArmorValue);
        if (!$this->canUseArmament($previousBodyArmor)) {
            return BodyArmorCode::getIt(BodyArmorCode::WITHOUT_ARMOR);
        }

        return $previousBodyArmor;
    }

    public function getSelectedArmorSkillRank(): int
    {
        return (int)$this->currentValues->getValue(self::ARMOR_SKILL_VALUE);
    }

    private function getPreviousArmorSkillRank(): int
    {
        return (int)$this->previousValues->getValue(self::ARMOR_SKILL_VALUE);
    }

    /**
     * @return PhysicalSkillCode
     */
    public function getPossibleSkillForArmor(): PhysicalSkillCode
    {
        return PhysicalSkillCode::getIt(PhysicalSkillCode::ARMOR_WEARING);
    }

    /**
     * @return array
     */
    public function getPossibleHelms(): array
    {
        return array_map(function (string $helmValue) {
            $helmCode = HelmCode::getIt($helmValue);

            return [
                'code' => $helmCode,
                'canUseIt' => $this->canUseArmament($helmCode),
            ];
        }, HelmCode::getPossibleValues());
    }

    public function getSelectedHelm(): HelmCode
    {
        $selectedHelmValue = $this->currentValues->getValue(self::HELM);
        if (!$selectedHelmValue) {
            return HelmCode::getIt(HelmCode::WITHOUT_HELM);
        }
        $selectedHelm = HelmCode::getIt($selectedHelmValue);
        if (!$this->canUseArmament($selectedHelm)) {
            return HelmCode::getIt(HelmCode::WITHOUT_HELM);
        }

        return HelmCode::getIt($selectedHelmValue);
    }

    public function getCoverOfShield(ShieldCode $shieldCode): int
    {
        return Tables::getIt()->getShieldsTable()->getCoverOf($shieldCode);
    }

    public function getProtectionOfHelm(HelmCode $helmCode): int
    {
        return Tables::getIt()->getHelmsTable()->getProtectionOf($helmCode);
    }

    public function getSelectedHelmProtection(): int
    {
        return $this->getProtectionOfHelm($this->getSelectedHelm());
    }

    private function getPreviousHelm(): HelmCode
    {
        $previousHelmValue = $this->previousValues->getValue(self::HELM);
        if (!$previousHelmValue) {
            return HelmCode::getIt(HelmCode::WITHOUT_HELM);
        }
        $previousHelm = HelmCode::getIt($previousHelmValue);
        if (!$this->canUseArmament($previousHelm)) {
            return HelmCode::getIt(HelmCode::WITHOUT_HELM);
        }

        return $previousHelm;
    }

    public function getPreviousHelmProtection(): int
    {
        return Tables::getIt()->getHelmsTable()->getProtectionOf($this->getPreviousHelm());
    }

    public function getSelectedOnHorseback(): bool
    {
        return (bool)$this->currentValues->getValue(self::ON_HORSEBACK);
    }

    private function getPreviousOnHorseback(): bool
    {
        return (bool)$this->previousValues->getValue(self::ON_HORSEBACK);
    }

    public function getSelectedRidingSkillRank(): int
    {
        return (int)$this->currentValues->getValue(self::RIDING_SKILL_RANK);
    }

    private function getPreviousRidingSkillRank(): int
    {
        return (int)$this->previousValues->getValue(self::RIDING_SKILL_RANK);
    }

    public function getSelectedFightFreeWillAnimal(): bool
    {
        return (bool)$this->currentValues->getValue(self::FIGHT_FREE_WILL_ANIMAL);
    }

    private function getPreviousFightFreeWillAnimal(): bool
    {
        return (bool)$this->previousValues->getValue(self::FIGHT_FREE_WILL_ANIMAL);
    }

    public function getSelectedZoologySkillRank(): int
    {
        return (int)$this->currentValues->getValue(self::ZOOLOGY_SKILL_RANK);
    }

    private function getPreviousZoologySkillRank(): int
    {
        return (int)$this->previousValues->getValue(self::ZOOLOGY_SKILL_RANK);
    }

    /**
     * @param int|IntegerInterface $previous
     * @param int|IntegerInterface $current
     * @return string
     */
    public function getClassForChangedValue($previous, $current): string
    {
        /** @noinspection ExceptionsAnnotatingAndHandlingInspection */
        if (ToInteger::toInteger($previous) < ToInteger::toInteger($current)) {
            return 'increased';
        }
        /** @noinspection ExceptionsAnnotatingAndHandlingInspection */
        if (ToInteger::toInteger($previous) > ToInteger::toInteger($current)) {
            return 'decreased';
        }

        return '';
    }
}