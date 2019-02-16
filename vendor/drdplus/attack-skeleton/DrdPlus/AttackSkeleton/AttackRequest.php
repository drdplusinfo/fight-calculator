<?php
declare(strict_types=1);

namespace DrdPlus\AttackSkeleton;

use DeviceDetector\Parser\Bot;
use DrdPlus\CalculatorSkeleton\CurrentValues;
use DrdPlus\RulesSkeleton\Request;

class AttackRequest extends Request
{
    public const MELEE_WEAPON = 'melee_weapon';
    public const RANGED_WEAPON = 'ranged_weapon';
    public const MELEE_WEAPON_HOLDING = 'melee_weapon_holding';
    public const RANGED_WEAPON_HOLDING = 'ranged_weapon_holding';
    public const SHIELD_HOLDING = 'shield_holding';
    public const SHIELD = 'shield';
    public const BODY_ARMOR = 'body_armor';
    public const HELM = 'helm';
    public const SCROLL_FROM_TOP = 'scroll_from_top';
    // special actions
    public const ACTION = 'action';
    public const ADD_NEW_MELEE_WEAPON = 'add_new_melee_weapon';
    public const ADD_NEW_RANGED_WEAPON = 'add_new_ranged_weapon';
    public const ADD_NEW_SHIELD = 'add_new_shield';
    public const ADD_NEW_BODY_ARMOR = 'add_new_body_armor';
    public const ADD_NEW_HELM = 'add_new_helm';

    /** @var CurrentValues */
    private $currentValues;

    public function __construct(CurrentValues $currentValues, Bot $botParser)
    {
        parent::__construct($botParser);
        $this->currentValues = $currentValues;
    }

    public function getScrollFromTop(): int
    {
        return (int)$this->currentValues->getSelectedValue(self::SCROLL_FROM_TOP);
    }

}