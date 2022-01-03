<?php declare(strict_types=1);

namespace DrdPlus\Health\Afflictions;

use Granam\Integer\IntegerInterface;
use Granam\IntegerEnum\IntegerEnum;

/**
 * @method static getEnum($value)
 */
class AfflictionDangerousness extends IntegerEnum
{
    /**
     * @param int|IntegerInterface $value
     * @return AfflictionDangerousness
     */
    public static function getIt($value): AfflictionDangerousness
    {
        return static::getEnum($value);
    }
}