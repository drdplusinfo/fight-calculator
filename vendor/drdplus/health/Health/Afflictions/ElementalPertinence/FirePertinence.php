<?php declare(strict_types=1);

namespace DrdPlus\Health\Afflictions\ElementalPertinence;

use DrdPlus\Codes\ElementCode;

class FirePertinence extends ElementalPertinence
{
    public const FIRE = ElementCode::FIRE;

    /**
     * @return FirePertinence|ElementalPertinence
     */
    public static function getMinus(): FirePertinence
    {
        return parent::getMinus();
    }

    /**
     * @return FirePertinence|ElementalPertinence
     */
    public static function getPlus(): FirePertinence
    {
        return parent::getPlus();
    }

}