<?php
declare(strict_types=1);

namespace DrdPlus\FightCalculator\Web;

use DrdPlus\Armourer\Armourer;
use DrdPlus\AttackSkeleton\ArmamentsUsabilityMessages;
use DrdPlus\AttackSkeleton\CurrentArmaments;
use DrdPlus\AttackSkeleton\CurrentArmamentsValues;
use DrdPlus\AttackSkeleton\CustomArmamentsState;
use DrdPlus\AttackSkeleton\HtmlHelper;
use DrdPlus\AttackSkeleton\PossibleArmaments;
use DrdPlus\AttackSkeleton\PreviousArmaments;
use DrdPlus\AttackSkeleton\Web\AttackWebPartsContainer;
use DrdPlus\FightCalculator\CurrentArmamentsWithSkills;
use DrdPlus\FightCalculator\CurrentProperties;
use DrdPlus\FightCalculator\Fight;
use DrdPlus\RulesSkeleton\Dirs;
use DrdPlus\RulesSkeleton\Request;
use DrdPlus\RulesSkeleton\Web\Pass;
use DrdPlus\RulesSkeleton\Web\WebFiles;
use DrdPlus\Tables\Tables;

class FightWebPartsContainer extends AttackWebPartsContainer
{
    /** @var ArmorSkillBody */
    private $armorSkillBody;
    /** @var MeleeWeaponSkillBody */
    private $meleeWeaponSkillBody;
    /** @var RangedFightSkillBody */
    private $rangedFightSkillBody;
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
    /** @var CurrentArmamentsWithSkills */
    private $currentArmamentsWithSkills;
    /** @var HtmlHelper */
    private $htmlHelper;
    /** @var Fight */
    private $fight;
    /** @var CurrentArmaments */
    private $currentArmaments;
    /** @var Tables */
    private $tables;
    /**
     * @var PreviousArmaments
     */
    private $previousArmaments;

    public function __construct(
        Pass $pass,
        WebFiles $webFiles,
        Dirs $dirs,
        HtmlHelper $htmlHelper,
        Request $request,
        CurrentProperties $currentProperties,
        CustomArmamentsState $customArmamentsState,
        CurrentArmamentsValues $currentArmamentsValues,
        CurrentArmaments $currentArmaments,
        PossibleArmaments $possibleArmaments,
        ArmamentsUsabilityMessages $armamentsUsabilityMessages,
        Armourer $armourer,
        PreviousArmaments $previousArmaments,
        CurrentArmamentsWithSkills $currentArmamentsWithSkills,
        Fight $fight,
        Tables $tables
    )
    {
        parent::__construct(
            $pass,
            $webFiles,
            $dirs,
            $htmlHelper,
            $request,
            $currentProperties,
            $customArmamentsState,
            $currentArmamentsValues,
            $currentArmaments,
            $possibleArmaments,
            $armamentsUsabilityMessages,
            $armourer
        );
        $this->currentArmamentsWithSkills = $currentArmamentsWithSkills;
        $this->htmlHelper = $htmlHelper;
        $this->fight = $fight;
        $this->currentArmaments = $currentArmaments;
        $this->tables = $tables;
        $this->previousArmaments = $previousArmaments;
    }

    public function getArmorSkillBody(): ArmorSkillBody
    {
        if ($this->armorSkillBody === null) {
            $this->armorSkillBody = new ArmorSkillBody(
                $this->currentArmamentsWithSkills,
                $this->htmlHelper
            );
        }
        return $this->armorSkillBody;
    }

    public function getMeleeWeaponFightPropertiesBody(): FightPropertiesBody
    {
        if ($this->meleeWeaponFightPropertiesBody === null) {
            $this->meleeWeaponFightPropertiesBody = new FightPropertiesBody(
                $this->fight->getCurrentMeleeWeaponFightProperties(),
                $this->fight->getPreviousMeleeWeaponFightProperties(),
                $this->fight,
                $this->htmlHelper
            );
        }
        return $this->meleeWeaponFightPropertiesBody;
    }

    public function getRideBody(): RideBody
    {
        if ($this->rideBody === null) {
            $this->rideBody = new RideBody(
                $this->currentArmamentsWithSkills,
                $this->htmlHelper
            );
        }
        return $this->rideBody;
    }

    public function getProfessionsBody(): ProfessionsBody
    {
        if ($this->professionsBody === null) {
            $this->professionsBody = new ProfessionsBody(
                $this->currentArmamentsWithSkills,
                $this->htmlHelper
            );
        }
        return $this->professionsBody;
    }

    public function getAnimalEnemyBody(): AnimalEnemyBody
    {
        if ($this->animalEnemyBody === null) {
            $this->animalEnemyBody = new AnimalEnemyBody(
                $this->currentArmamentsWithSkills,
                $this->htmlHelper
            );
        }
        return $this->animalEnemyBody;
    }

    public function getShieldUsageSkillBody(): ShieldUsageSkillBody
    {
        if ($this->shieldUsageSkillBody === null) {
            $this->shieldUsageSkillBody = new ShieldUsageSkillBody(
                $this->currentArmamentsWithSkills,
                $this->htmlHelper
            );
        }
        return $this->shieldUsageSkillBody;
    }

    public function getFightWithShieldSkillBody(): FightWithShieldSkillBody
    {
        if ($this->fightWithShieldSkillBody === null) {
            $this->fightWithShieldSkillBody = new FightWithShieldSkillBody(
                $this->currentArmamentsWithSkills,
                $this->htmlHelper
            );
        }
        return $this->fightWithShieldSkillBody;
    }

    public function getShieldWithMeleeWeaponBody(): ShieldFightPropertiesBody
    {
        if ($this->shieldWithMeleeWeaponBody === null) {
            $this->shieldWithMeleeWeaponBody = new ShieldFightPropertiesBody(
                $this->currentArmaments->getCurrentMeleeShieldHolding(),
                $this->previousArmaments->getPreviousMeleeShieldHolding(),
                $this->fight->getCurrentMeleeShieldFightProperties(),
                $this->fight->getPreviousMeleeShieldFightProperties(),
                $this->fight,
                $this->htmlHelper
            );
        }
        return $this->shieldWithMeleeWeaponBody;
    }

    public function getShieldWithRangedWeaponBody(): ShieldFightPropertiesBody
    {
        if ($this->shieldWithRangedWeaponBody === null) {
            $this->shieldWithRangedWeaponBody = new ShieldFightPropertiesBody(
                $this->currentArmaments->getCurrentRangedShieldHolding(),
                $this->previousArmaments->getPreviousRangedShieldHolding(),
                $this->fight->getCurrentRangedShieldFightProperties(),
                $this->fight->getPreviousRangedShieldFightProperties(),
                $this->fight,
                $this->htmlHelper
            );
        }
        return $this->shieldWithRangedWeaponBody;
    }

    public function getMeleeWeaponSkillBody(): MeleeWeaponSkillBody
    {
        if ($this->meleeWeaponSkillBody === null) {
            $this->meleeWeaponSkillBody = new MeleeWeaponSkillBody(
                $this->currentArmamentsWithSkills,
                $this->fight,
                $this->htmlHelper
            );
        }
        return $this->meleeWeaponSkillBody;
    }

    public function getRangedFightSkillBody(): RangedFightSkillBody
    {
        if ($this->rangedFightSkillBody === null) {
            $this->rangedFightSkillBody = new RangedFightSkillBody(
                $this->currentArmamentsWithSkills,
                $this->fight,
                $this->htmlHelper
            );
        }
        return $this->rangedFightSkillBody;
    }

    public function getRangedWeaponFightPropertiesBody(): FightPropertiesBody
    {
        if ($this->rangedWeaponFightPropertiesBody === null) {
            $this->rangedWeaponFightPropertiesBody = new FightPropertiesBody(
                $this->fight->getCurrentRangedWeaponFightProperties(),
                $this->fight->getPreviousRangedWeaponFightProperties(),
                $this->fight,
                $this->htmlHelper
            );
        }
        return $this->rangedWeaponFightPropertiesBody;
    }

    public function getRangedTargetBody(): RangedTargetBody
    {
        if ($this->rangedTargetBody === null) {
            $this->rangedTargetBody = new RangedTargetBody(
                $this->fight,
                $this->getRangedWeaponFightPropertiesBody(),
                $this->htmlHelper,
                $this->tables
            );
        }
        return $this->rangedTargetBody;
    }

    public function getBasicFightPropertiesBody(): BasicFightPropertiesBody
    {
        if ($this->basicFightPropertiesBody === null) {
            $this->basicFightPropertiesBody = new BasicFightPropertiesBody(
                $this->fight,
                $this->currentArmaments,
                $this->previousArmaments,
                $this->htmlHelper
            );
        }
        return $this->basicFightPropertiesBody;
    }

    public function isWithoutShield(): bool
    {
        return $this->currentArmaments->getCurrentShield()->isUnarmed();
    }
}