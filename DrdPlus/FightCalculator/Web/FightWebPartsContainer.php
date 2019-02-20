<?php
declare(strict_types=1);

namespace DrdPlus\FightCalculator\Web;

use DrdPlus\AttackSkeleton\Web\AttackWebPartsContainer;
use DrdPlus\FightCalculator\FightServicesContainer;

class FightWebPartsContainer extends AttackWebPartsContainer
{
    /** @var ArmorSkillBody */
    private $armorSkillBody;
    /** @var MeleeWeaponSkillBody */
    private $meleeWeaponSkillBody;
    /** @var RangedWeaponSkillBody */
    private $rangedWeaponSkillBody;
    /** @var FightPropertiesBody */
    private $rangedWeaponFightPropertiesBody;
    /** @var RangedTargetBody */
    private $rangedTargetBody;
    /** @var BasicFightPropertiesBody */
    private $basicFightPropertiesBody;
    /** @var ShieldUsageSkillBody */
    private $shieldUsageSkillBody;
    /** @var FightWithShieldSkillBody */
    private $fightWithShieldSkillBody;
    /** @var ShieldFightPropertiesBody */
    private $shieldWithMeleeWeaponBody;
    /** @var ShieldFightPropertiesBody */
    private $shieldWithRangedWeaponBody;
    /** @var ProfessionsBody */
    private $professionsBody;
    /** @var AnimalEnemyBody */
    private $animalEnemyBody;
    /** @var RideBody */
    private $rideBody;
    /** @var FightPropertiesBody */
    private $meleeWeaponFightPropertiesBody;
    /**
     * @var FightServicesContainer
     */
    private $fightServicesContainer;

    public function __construct(FightServicesContainer $fightServicesContainer)
    {
        parent::__construct($fightServicesContainer);
        $this->fightServicesContainer = $fightServicesContainer;
    }

    public function getArmorSkillBody(): ArmorSkillBody
    {
        if ($this->armorSkillBody === null) {
            $this->armorSkillBody = new ArmorSkillBody(
                $this->fightServicesContainer->getCurrentArmamentsWithSkills(),
                $this->fightServicesContainer->getHtmlHelper()
            );
        }
        return $this->armorSkillBody;
    }

    public function getMeleeWeaponFightPropertiesBody(): FightPropertiesBody
    {
        if ($this->meleeWeaponFightPropertiesBody === null) {
            $this->meleeWeaponFightPropertiesBody = new FightPropertiesBody(
                $this->fightServicesContainer->getFight()->getCurrentMeleeWeaponFightProperties(),
                $this->fightServicesContainer->getFight()->getPreviousMeleeWeaponFightProperties(),
                $this->fightServicesContainer->getFight(),
                $this->fightServicesContainer->getHtmlHelper()
            );
        }
        return $this->meleeWeaponFightPropertiesBody;
    }

    public function getRideBody(): RideBody
    {
        if ($this->rideBody === null) {
            $this->rideBody = new RideBody(
                $this->fightServicesContainer->getCurrentArmamentsWithSkills(),
                $this->fightServicesContainer->getHtmlHelper()
            );
        }
        return $this->rideBody;
    }

    public function getProfessionsBody(): ProfessionsBody
    {
        if ($this->professionsBody === null) {
            $this->professionsBody = new ProfessionsBody(
                $this->fightServicesContainer->getCurrentArmamentsWithSkills(),
                $this->fightServicesContainer->getHtmlHelper()
            );
        }
        return $this->professionsBody;
    }

    public function getAnimalEnemyBody(): AnimalEnemyBody
    {
        if ($this->animalEnemyBody === null) {
            $this->animalEnemyBody = new AnimalEnemyBody(
                $this->fightServicesContainer->getCurrentArmamentsWithSkills(),
                $this->fightServicesContainer->getHtmlHelper()
            );
        }
        return $this->animalEnemyBody;
    }

    public function getShieldUsageSkillBody(): ShieldUsageSkillBody
    {
        if ($this->shieldUsageSkillBody === null) {
            $this->shieldUsageSkillBody = new ShieldUsageSkillBody(
                $this->fightServicesContainer->getCurrentArmamentsWithSkills(),
                $this->fightServicesContainer->getFight(),
                $this->fightServicesContainer->getHtmlHelper()
            );
        }
        return $this->shieldUsageSkillBody;
    }

    public function getFightWithShieldSkillBody(): FightWithShieldSkillBody
    {
        if ($this->fightWithShieldSkillBody === null) {
            $this->fightWithShieldSkillBody = new FightWithShieldSkillBody(
                $this->fightServicesContainer->getCurrentArmamentsWithSkills(),
                $this->fightServicesContainer->getFight(),
                $this->fightServicesContainer->getHtmlHelper()
            );
        }
        return $this->fightWithShieldSkillBody;
    }

    public function getShieldWithMeleeWeaponBody(): ShieldFightPropertiesBody
    {
        if ($this->shieldWithMeleeWeaponBody === null) {
            $this->shieldWithMeleeWeaponBody = new ShieldFightPropertiesBody(
                $this->fightServicesContainer->getCurrentArmaments()->getCurrentMeleeShieldHolding(),
                $this->fightServicesContainer->getPreviousArmaments()->getPreviousMeleeShieldHolding(),
                $this->fightServicesContainer->getFight()->getCurrentMeleeShieldFightProperties(),
                $this->fightServicesContainer->getFight()->getPreviousMeleeShieldFightProperties(),
                $this->fightServicesContainer->getFight(),
                $this->fightServicesContainer->getHtmlHelper()
            );
        }
        return $this->shieldWithMeleeWeaponBody;
    }

    public function getShieldWithRangedWeaponBody(): ShieldFightPropertiesBody
    {
        if ($this->shieldWithRangedWeaponBody === null) {
            $this->shieldWithRangedWeaponBody = new ShieldFightPropertiesBody(
                $this->fightServicesContainer->getCurrentArmaments()->getCurrentRangedShieldHolding(),
                $this->fightServicesContainer->getPreviousArmaments()->getPreviousRangedShieldHolding(),
                $this->fightServicesContainer->getFight()->getCurrentRangedShieldFightProperties(),
                $this->fightServicesContainer->getFight()->getPreviousRangedShieldFightProperties(),
                $this->fightServicesContainer->getFight(),
                $this->fightServicesContainer->getHtmlHelper()
            );
        }
        return $this->shieldWithRangedWeaponBody;
    }

    public function getMeleeWeaponSkillBody(): MeleeWeaponSkillBody
    {
        if ($this->meleeWeaponSkillBody === null) {
            $this->meleeWeaponSkillBody = new MeleeWeaponSkillBody(
                $this->fightServicesContainer->getCurrentArmamentsWithSkills(),
                $this->fightServicesContainer->getFight(),
                $this->fightServicesContainer->getHtmlHelper()
            );
        }
        return $this->meleeWeaponSkillBody;
    }

    public function getRangedWeaponSkillBody(): RangedWeaponSkillBody
    {
        if ($this->rangedWeaponSkillBody === null) {
            $this->rangedWeaponSkillBody = new RangedWeaponSkillBody(
                $this->fightServicesContainer->getCurrentArmamentsWithSkills(),
                $this->fightServicesContainer->getFight(),
                $this->fightServicesContainer->getHtmlHelper()
            );
        }
        return $this->rangedWeaponSkillBody;
    }

    public function getRangedWeaponFightPropertiesBody(): FightPropertiesBody
    {
        if ($this->rangedWeaponFightPropertiesBody === null) {
            $this->rangedWeaponFightPropertiesBody = new FightPropertiesBody(
                $this->fightServicesContainer->getFight()->getCurrentRangedWeaponFightProperties(),
                $this->fightServicesContainer->getFight()->getPreviousRangedWeaponFightProperties(),
                $this->fightServicesContainer->getFight(),
                $this->fightServicesContainer->getHtmlHelper()
            );
        }
        return $this->rangedWeaponFightPropertiesBody;
    }

    public function getRangedTargetBody(): RangedTargetBody
    {
        if ($this->rangedTargetBody === null) {
            $this->rangedTargetBody = new RangedTargetBody(
                $this->fightServicesContainer->getFight(),
                $this->getRangedWeaponFightPropertiesBody(),
                $this->fightServicesContainer->getHtmlHelper(),
                $this->fightServicesContainer->getTables()
            );
        }
        return $this->rangedTargetBody;
    }

    public function getBasicFightPropertiesBody(): BasicFightPropertiesBody
    {
        if ($this->basicFightPropertiesBody === null) {
            $this->basicFightPropertiesBody = new BasicFightPropertiesBody(
                $this->fightServicesContainer->getFight(),
                $this->fightServicesContainer->getCurrentArmaments(),
                $this->fightServicesContainer->getPreviousArmaments(),
                $this->fightServicesContainer->getHtmlHelper()
            );
        }
        return $this->basicFightPropertiesBody;
    }

    public function isWithoutShield(): bool
    {
        return $this->fightServicesContainer->getCurrentArmaments()->getCurrentShield()->isUnarmed();
    }
}