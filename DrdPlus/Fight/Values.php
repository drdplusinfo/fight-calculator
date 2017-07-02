<?php
namespace DrdPlus\Fight;

use DrdPlus\Background\BackgroundParts\Ancestry;
use DrdPlus\Background\BackgroundParts\SkillPointsFromBackground;
use DrdPlus\Codes\Armaments\BodyArmorCode;
use DrdPlus\Codes\Armaments\HelmCode;
use DrdPlus\Codes\Armaments\ShieldCode;
use DrdPlus\Codes\Armaments\WeaponlikeCode;
use DrdPlus\Codes\ItemHoldingCode;
use DrdPlus\Codes\ProfessionCode;
use DrdPlus\Codes\Skills\CombinedSkillCode;
use DrdPlus\Codes\Skills\PhysicalSkillCode;
use DrdPlus\Codes\Skills\PsychicalSkillCode;
use DrdPlus\Codes\Skills\SkillCode;
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
use DrdPlus\Tables\Tables;
use Granam\Integer\PositiveIntegerObject;
use Granam\Strict\Object\StrictObject;
use Granam\String\StringTools;


abstract class Values extends StrictObject
{

    public function getFightProperties(
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

}