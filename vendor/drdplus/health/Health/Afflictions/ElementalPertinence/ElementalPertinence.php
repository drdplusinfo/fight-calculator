<?php declare(strict_types=1);

namespace DrdPlus\Health\Afflictions\ElementalPertinence;

use Granam\StringEnum\StringEnum;
use Granam\String\StringTools;

/**
 * @method static ElementalPertinence getEnum($enumValue)
 */
abstract class ElementalPertinence extends StringEnum
{
    public const MINUS = '-';

    /**
     * @return ElementalPertinence
     */
    protected static function getMinus()
    {
        return static::getEnum(self::MINUS . static::getPertinenceCode());
    }

    public static function getPertinenceCode(): string
    {
        return \preg_replace('~_pertinence$~', '', StringTools::camelCaseToSnakeCasedBasename(static::class));
    }

    /**
     * @return bool
     */
    public function isMinus(): bool
    {
        return strpos($this->getValue(), self::MINUS) === 0;
    }

    public const PLUS = '+';

    /**
     * @return ElementalPertinence
     */
    protected static function getPlus()
    {
        return static::getEnum(self::PLUS . static::getPertinenceCode());
    }

    public function isPlus(): bool
    {
        return strpos($this->getValue(), self::PLUS) === 0;
    }

}