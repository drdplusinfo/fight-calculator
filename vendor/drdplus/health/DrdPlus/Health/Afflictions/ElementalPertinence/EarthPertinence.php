<?php declare(strict_types=1);

namespace DrdPlus\Health\Afflictions\ElementalPertinence;

use DrdPlus\Codes\ElementCode;

class EarthPertinence extends ElementalPertinence
{
    public const EARTH = ElementCode::EARTH;

    /**
     * @return EarthPertinence|ElementalPertinence
     */
    public static function getMinus(): EarthPertinence
    {
        return parent::getMinus();
    }

    /**
     * @return EarthPertinence|ElementalPertinence
     */
    public static function getPlus(): EarthPertinence
    {
        return parent::getPlus();
    }

}