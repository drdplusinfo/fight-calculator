<?php declare(strict_types=1);

namespace DrdPlus\Health\Afflictions\ElementalPertinence;

use DrdPlus\Codes\ElementCode;

class WaterPertinence extends ElementalPertinence
{
    public const WATER = ElementCode::WATER;

    /**
     * @return WaterPertinence|ElementalPertinence
     */
    public static function getMinus(): WaterPertinence
    {
        return parent::getMinus();
    }

    /**
     * @return WaterPertinence|ElementalPertinence
     */
    public static function getPlus(): WaterPertinence
    {
        return parent::getPlus();
    }

}