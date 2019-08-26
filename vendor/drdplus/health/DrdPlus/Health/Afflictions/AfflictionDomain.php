<?php declare(strict_types=1);

namespace DrdPlus\Health\Afflictions;

use DrdPlus\Codes\Body\AfflictionByWoundDomainCode;
use Granam\String\StringInterface;
use Granam\StringEnum\StringEnum;
use Granam\Tools\ValueDescriber;

/**
 * @method static AfflictionDomain getEnum($value)
 */
class AfflictionDomain extends StringEnum
{
    /**
     * @param string|StringInterface $domainCode
     * @return AfflictionDomain
     */
    public static function getIt($domainCode): AfflictionDomain
    {
        return static::getEnum($domainCode);
    }

    public const PHYSICAL = AfflictionByWoundDomainCode::PHYSICAL;

    public static function getPhysicalDomain(): AfflictionDomain
    {
        return static::getEnum(self::PHYSICAL);
    }

    public const PSYCHICAL = AfflictionByWoundDomainCode::PSYCHICAL;

    public static function getPsychicalDomain(): AfflictionDomain
    {
        return static::getEnum(self::PSYCHICAL);
    }

    protected static function convertToEnumFinalValue($enumValue): string
    {
        $finalValue = parent::convertToEnumFinalValue($enumValue);
        if (!\in_array($finalValue, AfflictionByWoundDomainCode::getPossibleValues(), true)) {
            throw new Exceptions\UnknownAfflictionDomain('unexpected affliction domain ' . ValueDescriber::describe($enumValue));
        }

        return $finalValue;
    }

}