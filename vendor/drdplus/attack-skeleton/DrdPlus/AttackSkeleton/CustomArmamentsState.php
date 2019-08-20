<?php declare(strict_types=1);

namespace DrdPlus\AttackSkeleton;

use DrdPlus\CalculatorSkeleton\CurrentValues;
use Granam\Strict\Object\StrictObject;

class CustomArmamentsState extends StrictObject
{
    /**
     * @var CurrentValues
     */
    private $currentValues;

    public function __construct(CurrentValues $currentValues)
    {
        $this->currentValues = $currentValues;
    }

    public function isAddingNewMeleeWeapon(): bool
    {
        return $this->currentValues->getSelectedValue(AttackRequest::ACTION) === AttackRequest::ADD_NEW_MELEE_WEAPON;
    }

    public function isAddingNewRangedWeapon(): bool
    {
        return $this->currentValues->getSelectedValue(AttackRequest::ACTION) === AttackRequest::ADD_NEW_RANGED_WEAPON;
    }

    public function isAddingNewBodyArmor(): bool
    {
        return $this->currentValues->getSelectedValue(AttackRequest::ACTION) === AttackRequest::ADD_NEW_BODY_ARMOR;
    }

    public function isAddingNewHelm(): bool
    {
        return $this->currentValues->getSelectedValue(AttackRequest::ACTION) === AttackRequest::ADD_NEW_HELM;
    }

    public function isAddingNewShield(): bool
    {
        return $this->currentValues->getSelectedValue(AttackRequest::ACTION) === AttackRequest::ADD_NEW_SHIELD;
    }
}