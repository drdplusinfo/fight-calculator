<?php declare(strict_types=1);

namespace DrdPlus\Health\Afflictions\ElementalPertinence;

use DrdPlus\Codes\ElementCode;

class AirPertinence extends ElementalPertinence
{
    public const AIR = ElementCode::AIR;

    /**
     * @return AirPertinence|ElementalPertinence
     */
    public static function getMinus(): AirPertinence
    {
        return parent::getMinus();
    }

    /**
     * @return AirPertinence|ElementalPertinence
     */
    public static function getPlus(): AirPertinence
    {
        return parent::getPlus();
    }

}