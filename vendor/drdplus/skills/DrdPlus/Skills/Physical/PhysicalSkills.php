<?php declare(strict_types=1);

namespace DrdPlus\Skills\Physical;

use DrdPlus\Codes\Armaments\ArmorCode;
use DrdPlus\Codes\Armaments\ProtectiveArmamentCode;
use DrdPlus\Codes\Armaments\ShieldCode;
use DrdPlus\Codes\Armaments\WeaponCode;
use DrdPlus\Codes\Armaments\WeaponlikeCode;
use DrdPlus\Codes\Skills\SkillTypeCode;
use DrdPlus\Person\ProfessionLevels\ProfessionLevel;
use DrdPlus\Person\ProfessionLevels\ProfessionLevels;
use DrdPlus\Skills\SameTypeSkills;
use DrdPlus\Armourer\Armourer;
use DrdPlus\Tables\Tables;

class PhysicalSkills extends SameTypeSkills
{
    public const PHYSICAL = SkillTypeCode::PHYSICAL;

    /**
     * @var ArmorWearing
     */
    private $armorWearing;
    /**
     * @var Athletics
     */
    private $athletics;
    /**
     * @var Blacksmithing
     */
    private $blacksmithing;
    /**
     * @var BoatDriving
     */
    private $boatDriving;
    /**
     * @var CartDriving
     */
    private $cartDriving;
    /**
     * @var CityMoving
     */
    private $cityMoving;
    /**
     * @var ClimbingAndHillwalking
     */
    private $climbingAndHillwalking;
    /**
     * @var FastMarsh
     */
    private $fastMarsh;
    /**
     * @var FightUnarmed
     */
    private $fightUnarmed;
    /**
     * @var FightWithAxes
     */
    private $fightWithAxes;
    /**
     * @var FightWithKnivesAndDaggers
     */
    private $fightWithKnivesAndDaggers;
    /**
     * @var FightWithMacesAndClubs
     */
    private $fightWithMacesAndClubs;
    /**
     * @var FightWithMorningstarsAndMorgensterns
     */
    private $fightWithMorningstarsAndMorgensterns;
    /**
     * @var FightWithSabersAndBowieKnives
     */
    private $fightWithSabersAndBowieKnives;
    /**
     * @var FightWithStaffsAndSpears
     */
    private $fightWithStaffsAndSpears;
    /**
     * @var FightWithShields
     */
    private $fightWithShields;
    /**
     * @var FightWithSwords
     */
    private $fightWithSwords;
    /**
     * @var FightWithThrowingWeapons
     */
    private $fightWithThrowingWeapons;
    /**
     * @var FightWithTwoWeapons
     */
    private $fightWithTwoWeapons;
    /**
     * @var FightWithVoulgesAndTridents
     */
    private $fightWithVoulgesAndTridents;
    /**
     * @var Flying
     */
    private $flying;
    /**
     * @var ForestMoving
     */
    private $forestMoving;
    /**
     * @var MovingInMountains
     */
    private $movingInMountains;
    /**
     * @var Riding
     */
    private $riding;
    /**
     * @var Sailing
     */
    private $sailing;
    /**
     * @var ShieldUsage
     */
    private $shieldUsage;
    /**
     * @var Swimming
     */
    private $swimming;

    protected function populateAllSkills(ProfessionLevel $professionLevel)
    {
        $this->armorWearing = new ArmorWearing($professionLevel);
        $this->athletics = new Athletics($professionLevel);
        $this->blacksmithing = new Blacksmithing($professionLevel);
        $this->boatDriving = new BoatDriving($professionLevel);
        $this->cartDriving = new CartDriving($professionLevel);
        $this->cityMoving = new CityMoving($professionLevel);
        $this->climbingAndHillwalking = new ClimbingAndHillwalking($professionLevel);
        $this->fastMarsh = new FastMarsh($professionLevel);
        $this->fightUnarmed = new FightUnarmed($professionLevel);
        $this->fightWithAxes = new FightWithAxes($professionLevel);
        $this->fightWithKnivesAndDaggers = new FightWithKnivesAndDaggers($professionLevel);
        $this->fightWithMacesAndClubs = new FightWithMacesAndClubs($professionLevel);
        $this->fightWithMorningstarsAndMorgensterns = new FightWithMorningstarsAndMorgensterns($professionLevel);
        $this->fightWithSabersAndBowieKnives = new FightWithSabersAndBowieKnives($professionLevel);
        $this->fightWithStaffsAndSpears = new FightWithStaffsAndSpears($professionLevel);
        $this->fightWithShields = new FightWithShields($professionLevel);
        $this->fightWithSwords = new FightWithSwords($professionLevel);
        $this->fightWithThrowingWeapons = new FightWithThrowingWeapons($professionLevel);
        $this->fightWithTwoWeapons = new FightWithTwoWeapons($professionLevel);
        $this->fightWithVoulgesAndTridents = new FightWithVoulgesAndTridents($professionLevel);
        $this->flying = new Flying($professionLevel);
        $this->forestMoving = new ForestMoving($professionLevel);
        $this->movingInMountains = new MovingInMountains($professionLevel);
        $this->riding = new Riding($professionLevel);
        $this->sailing = new Sailing($professionLevel);
        $this->shieldUsage = new ShieldUsage($professionLevel);
        $this->swimming = new Swimming($professionLevel);
    }

    public function getUnusedFirstLevelPhysicalSkillPointsValue(ProfessionLevels $professionLevels): int
    {
        return $this->getUnusedFirstLevelSkillPointsValue($this->getFirstLevelPhysicalPropertiesSum($professionLevels));
    }

    private function getFirstLevelPhysicalPropertiesSum(ProfessionLevels $professionLevels): int
    {
        return $professionLevels->getFirstLevelStrengthModifier() + $professionLevels->getFirstLevelAgilityModifier();
    }

    public function getUnusedNextLevelsPhysicalSkillPointsValue(ProfessionLevels $professionLevels): int
    {
        return $this->getUnusedNextLevelsSkillPointsValue($this->getNextLevelsPhysicalPropertiesSum($professionLevels));
    }

    private function getNextLevelsPhysicalPropertiesSum(ProfessionLevels $professionLevels): int
    {
        return $professionLevels->getNextLevelsStrengthModifier() + $professionLevels->getNextLevelsAgilityModifier();
    }

    /**
     * @return \Traversable|\ArrayIterator|PhysicalSkill[]
     */
    public function getIterator(): \Traversable
    {
        return new \ArrayIterator([
            $this->getArmorWearing(),
            $this->getAthletics(),
            $this->getBlacksmithing(),
            $this->getBoatDriving(),
            $this->getCartDriving(),
            $this->getCityMoving(),
            $this->getClimbingAndHillwalking(),
            $this->getFastMarsh(),
            $this->getFightUnarmed(),
            $this->getFightWithAxes(),
            $this->getFightWithKnivesAndDaggers(),
            $this->getFightWithMacesAndClubs(),
            $this->getFightWithMorningstarsAndMorgensterns(),
            $this->getFightWithSabersAndBowieKnives(),
            $this->getFightWithStaffsAndSpears(),
            $this->getFightWithShields(),
            $this->getFightWithSwords(),
            $this->getFightWithThrowingWeapons(),
            $this->getFightWithTwoWeapons(),
            $this->getFightWithVoulgesAndTridents(),
            $this->getFlying(),
            $this->getForestMoving(),
            $this->getMovingInMountains(),
            $this->getRiding(),
            $this->getSailing(),
            $this->getShieldUsage(),
            $this->getSwimming(),
        ]);
    }

    public function getArmorWearing(): ArmorWearing
    {
        return $this->armorWearing;
    }

    public function getAthletics(): Athletics
    {
        return $this->athletics;
    }

    public function getBlacksmithing(): Blacksmithing
    {
        return $this->blacksmithing;
    }

    public function getBoatDriving(): BoatDriving
    {
        return $this->boatDriving;
    }

    public function getCartDriving(): CartDriving
    {
        return $this->cartDriving;
    }

    public function getCityMoving(): CityMoving
    {
        return $this->cityMoving;
    }

    public function getClimbingAndHillwalking(): ClimbingAndHillwalking
    {
        return $this->climbingAndHillwalking;
    }

    public function getFastMarsh(): FastMarsh
    {
        return $this->fastMarsh;
    }

    public function getFightUnarmed(): FightUnarmed
    {
        return $this->fightUnarmed;
    }

    public function getFightWithAxes(): FightWithAxes
    {
        return $this->fightWithAxes;
    }

    public function getFightWithKnivesAndDaggers(): FightWithKnivesAndDaggers
    {
        return $this->fightWithKnivesAndDaggers;
    }

    public function getFightWithMacesAndClubs(): FightWithMacesAndClubs
    {
        return $this->fightWithMacesAndClubs;
    }

    public function getFightWithMorningstarsAndMorgensterns(): FightWithMorningstarsAndMorgensterns
    {
        return $this->fightWithMorningstarsAndMorgensterns;
    }

    public function getFightWithSabersAndBowieKnives(): FightWithSabersAndBowieKnives
    {
        return $this->fightWithSabersAndBowieKnives;
    }

    public function getFightWithStaffsAndSpears(): FightWithStaffsAndSpears
    {
        return $this->fightWithStaffsAndSpears;
    }

    /**
     * This skill is not part of PPH, but is as crazy as well as possible.
     *
     * @return FightWithShields
     */
    public function getFightWithShields(): FightWithShields
    {
        return $this->fightWithShields;
    }

    public function getFightWithSwords(): FightWithSwords
    {
        return $this->fightWithSwords;
    }

    public function getFightWithThrowingWeapons(): FightWithThrowingWeapons
    {
        return $this->fightWithThrowingWeapons;
    }

    public function getFightWithTwoWeapons(): FightWithTwoWeapons
    {
        return $this->fightWithTwoWeapons;
    }

    public function getFightWithVoulgesAndTridents(): FightWithVoulgesAndTridents
    {
        return $this->fightWithVoulgesAndTridents;
    }

    /**
     * @return array|FightWithWeaponsUsingPhysicalSkill[]
     */
    public function getFightWithWeaponsUsingPhysicalSkills(): array
    {
        return [
            $this->getFightUnarmed(),
            $this->getFightWithAxes(),
            $this->getFightWithKnivesAndDaggers(),
            $this->getFightWithMacesAndClubs(),
            $this->getFightWithMorningstarsAndMorgensterns(),
            $this->getFightWithSabersAndBowieKnives(),
            $this->getFightWithStaffsAndSpears(),
            $this->getFightWithSwords(),
            $this->getFightWithThrowingWeapons(),
            $this->getFightWithTwoWeapons(),
            $this->getFightWithVoulgesAndTridents(),
            $this->getFightWithShields(),
        ];
    }

    public function getFlying(): Flying
    {
        return $this->flying;
    }

    public function getForestMoving(): ForestMoving
    {
        return $this->forestMoving;
    }

    public function getMovingInMountains(): MovingInMountains
    {
        return $this->movingInMountains;
    }

    public function getRiding(): Riding
    {
        return $this->riding;
    }

    public function getSailing(): Sailing
    {
        return $this->sailing;
    }

    public function getShieldUsage(): ShieldUsage
    {
        return $this->shieldUsage;
    }

    public function getSwimming(): Swimming
    {
        return $this->swimming;
    }

    /**
     * Note about SHIELD: "weaponlike" means for attacking. If you provide a shield, it will considered as a weapon for
     * direct attack. If you want malus to a fight number with shield as a protective armament,
     * use @see \DrdPlus\Skills\Physical\PhysicalSkills::getMalusToFightNumberWithProtective
     * And one more note: RESTRICTION from shield is NOT applied (and SHOULD NOT be) if the shield is used as a weapon
     * (total malus is already included in the @see FightWithShields skill).
     *
     * @param WeaponlikeCode $weaponlikeCode
     * @param Tables $tables
     * @param bool $fightsWithTwoWeapons
     * @return int
     * @throws \DrdPlus\Skills\Physical\Exceptions\PhysicalSkillsDoNotKnowHowToUseThatWeapon
     */
    public function getMalusToFightNumberWithWeaponlike(
        WeaponlikeCode $weaponlikeCode,
        Tables $tables,
        $fightsWithTwoWeapons
    ): int
    {
        $fightWithWeaponRankValue = $this->getHighestRankForSuitableFightWithWeapon($weaponlikeCode);
        $malus = $tables->getMissingWeaponSkillTable()->getFightNumberMalusForSkillRank($fightWithWeaponRankValue);
        if ($fightsWithTwoWeapons) {
            $malus += $tables->getMissingWeaponSkillTable()->getFightNumberMalusForSkillRank(
                $this->getFightWithTwoWeapons()->getCurrentSkillRank()->getValue()
            );
        }
        return $malus;
    }

    /**
     * Note about SHIELD: "fight with" means attacking - for shield standard usage as
     * a protective armament @see \DrdPlus\Skills\Physical\PhysicalSkills::getMalusToFightNumberWithProtective
     *
     * @param WeaponlikeCode $weaponlikeCode
     * @return int
     * @throws \DrdPlus\Skills\Physical\Exceptions\PhysicalSkillsDoNotKnowHowToUseThatWeapon
     */
    private function getHighestRankForSuitableFightWithWeapon(WeaponlikeCode $weaponlikeCode): int
    {
        $rankValues = [];
        if ($weaponlikeCode->isShield()) { // shield as a weapon
            $rankValues[] = $this->getFightWithShields()->getCurrentSkillRank()->getValue();
        }
        if ($weaponlikeCode->isWeapon() && $weaponlikeCode->isMelee()) {
            $weaponlikeCode = $weaponlikeCode->convertToMeleeWeaponCodeEquivalent();
            if ($weaponlikeCode->isAxe()) {
                $rankValues[] = $this->getFightWithAxes()->getCurrentSkillRank()->getValue();
            }
            if ($weaponlikeCode->isKnifeOrDagger()) {
                $rankValues[] = $this->getFightWithKnivesAndDaggers()->getCurrentSkillRank()->getValue();
            }
            if ($weaponlikeCode->isMaceOrClub()) {
                $rankValues[] = $this->getFightWithMacesAndClubs()->getCurrentSkillRank()->getValue();
            }
            if ($weaponlikeCode->isMorningstarOrMorgenstern()) {
                $rankValues[] = $this->getFightWithMorningstarsAndMorgensterns()->getCurrentSkillRank()->getValue();
            }
            if ($weaponlikeCode->isSaberOrBowieKnife()) {
                $rankValues[] = $this->getFightWithSabersAndBowieKnives()->getCurrentSkillRank()->getValue();
            }
            if ($weaponlikeCode->isStaffOrSpear()) {
                $rankValues[] = $this->getFightWithStaffsAndSpears()->getCurrentSkillRank()->getValue();
            }
            if ($weaponlikeCode->isSword()) {
                $rankValues[] = $this->getFightWithSwords()->getCurrentSkillRank()->getValue();
            }
            if ($weaponlikeCode->isUnarmed()) {
                $rankValues[] = $this->getFightUnarmed()->getCurrentSkillRank()->getValue();
            }
            if ($weaponlikeCode->isVoulgeOrTrident()) {
                $rankValues[] = $this->getFightWithVoulgesAndTridents()->getCurrentSkillRank()->getValue();
            }
        }
        if ($weaponlikeCode->isThrowingWeapon()) {
            $rankValues[] = $this->getFightWithThrowingWeapons()->getCurrentSkillRank()->getValue();
        }
        $rankValue = false;
        if (\count($rankValues) > 0) {
            $rankValue = \max($rankValues);
        }
        if (!\is_int($rankValue)) {
            throw new Exceptions\PhysicalSkillsDoNotKnowHowToUseThatWeapon(
                "Given weapon '{$weaponlikeCode}' is not usable by any physical skill"
            );
        }
        return $rankValue;
    }

    /**
     * @param ProtectiveArmamentCode $protectiveArmamentCode
     * @param $armourer $armourer
     * @return int
     * @throws \DrdPlus\Skills\Physical\Exceptions\PhysicalSkillsDoNotKnowHowToUseThatArmament
     */
    public function getMalusToFightNumberWithProtective(
        ProtectiveArmamentCode $protectiveArmamentCode,
        Armourer $armourer
    ): int
    {
        if ($protectiveArmamentCode instanceof ArmorCode) {
            return $armourer->getProtectiveArmamentRestrictionForSkillRank(
                $protectiveArmamentCode,
                $this->getArmorWearing()->getCurrentSkillRank()
            );
        }
        if ($protectiveArmamentCode instanceof ShieldCode) {
            return $armourer->getProtectiveArmamentRestrictionForSkillRank(
                $protectiveArmamentCode,
                $this->getShieldUsage()->getCurrentSkillRank()
            );
        }
        throw new Exceptions\PhysicalSkillsDoNotKnowHowToUseThatArmament(
            "Given protective armament '{$protectiveArmamentCode}' is not usable by any physical skill"
        );
    }

    /**
     * Note about SHIELD: "weaponlike" means for attacking - for shield standard usage as
     * a protective armament @see \DrdPlus\Skills\Physical\PhysicalSkills::getMalusToFightNumberWithProtective
     *
     * @param WeaponlikeCode $weaponlikeCode
     * @param Tables $tables
     * @param bool $fightsWithTwoWeapons
     * @return int
     * @throws \DrdPlus\Skills\Physical\Exceptions\PhysicalSkillsDoNotKnowHowToUseThatWeapon
     */
    public function getMalusToAttackNumberWithWeaponlike(
        WeaponlikeCode $weaponlikeCode,
        Tables $tables,
        $fightsWithTwoWeapons
    ): int
    {
        $fightWithWeaponRankValue = $this->getHighestRankForSuitableFightWithWeapon($weaponlikeCode);
        $malus = $tables->getMissingWeaponSkillTable()->getAttackNumberMalusForSkillRank($fightWithWeaponRankValue);
        if ($fightsWithTwoWeapons) {
            $malus += $tables->getMissingWeaponSkillTable()->getAttackNumberMalusForSkillRank(
                $this->getFightWithTwoWeapons()->getCurrentSkillRank()->getValue()
            );
        }
        return $malus;
    }

    /**
     * For SHIELD use @see getMalusToFightNumberWithProtective
     *
     * @param WeaponCode $weaponCode
     * @param Tables $tables
     * @param bool $fightsWithTwoWeapons
     * @return int
     * @throws \DrdPlus\Skills\Physical\Exceptions\PhysicalSkillsDoNotKnowHowToUseThatWeapon
     */
    public function getMalusToCoverWithWeapon(
        WeaponCode $weaponCode,
        Tables $tables,
        $fightsWithTwoWeapons
    ): int
    {
        $fightWithWeaponRankValue = $this->getHighestRankForSuitableFightWithWeapon($weaponCode);
        $malus = $tables->getMissingWeaponSkillTable()->getCoverMalusForSkillRank($fightWithWeaponRankValue);
        if ($fightsWithTwoWeapons) {
            $malus += $tables->getMissingWeaponSkillTable()->getCoverMalusForSkillRank(
                $this->getFightWithTwoWeapons()->getCurrentSkillRank()->getValue()
            );
        }
        return $malus;
    }

    /**
     * Warning: PPH gives you false info about malus to cover with shield (see PPH page 86 right column).
     * Correct is as gives @see \DrdPlus\Tables\Armaments\Shields\ShieldUsageSkillTable
     *
     * @param Tables $tables
     * @return int
     */
    public function getMalusToCoverWithShield(Tables $tables): int
    {
        return $tables->getShieldUsageSkillTable()->getCoverMalusForSkillRank($this->getShieldUsage()->getCurrentSkillRank());
    }

    /**
     * Note about SHIELD: "weaponlike" means for attacking - for shield standard usage as
     * a protective armament @see \DrdPlus\Skills\Physical\PhysicalSkills::getMalusToFightNumberWithProtective
     *
     * @param WeaponlikeCode $weaponlikeCode
     * @param Tables $tables
     * @param bool $fightsWithTwoWeapons
     * @return int
     * @throws \DrdPlus\Skills\Physical\Exceptions\PhysicalSkillsDoNotKnowHowToUseThatWeapon
     */
    public function getMalusToBaseOfWoundsWithWeaponlike(
        WeaponlikeCode $weaponlikeCode,
        Tables $tables,
        bool $fightsWithTwoWeapons
    ): int
    {
        $fightWithWeaponRankValue = $this->getHighestRankForSuitableFightWithWeapon($weaponlikeCode);
        $malus = $tables->getMissingWeaponSkillTable()->getBaseOfWoundsMalusForSkillRank($fightWithWeaponRankValue);
        if ($fightsWithTwoWeapons) {
            $malus += $tables->getMissingWeaponSkillTable()->getBaseOfWoundsMalusForSkillRank(
                $this->getFightWithTwoWeapons()->getCurrentSkillRank()->getValue()
            );
        }
        return $malus;
    }

    public function getMalusToFightNumberWhenRiding(): int
    {
        return $this->getRiding()->getMalusToFightAttackAndDefenseNumber();
    }

    public function getMalusToAttackNumberWhenRiding(): int
    {
        return $this->getRiding()->getMalusToFightAttackAndDefenseNumber();
    }

    public function getMalusToDefenseNumberWhenRiding(): int
    {
        return $this->getRiding()->getMalusToFightAttackAndDefenseNumber();
    }
}