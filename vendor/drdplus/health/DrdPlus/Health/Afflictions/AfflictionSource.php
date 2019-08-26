<?php declare(strict_types=1);

namespace DrdPlus\Health\Afflictions;

use Granam\String\StringInterface;
use Granam\StringEnum\StringEnum;
use Granam\Tools\ValueDescriber;

/**
 * @method static AfflictionSource getEnum($value)
 */
class AfflictionSource extends StringEnum
{
    public const AFFLICTION_SOURCE = 'affliction_source';

    /**
     * @param string|StringInterface $sourceCode
     * @return AfflictionSource
     */
    public static function getIt($sourceCode): AfflictionSource
    {
        return static::getEnum($sourceCode);
    }

    public const EXTERNAL = 'external';

    public static function getExternalSource(): AfflictionSource
    {
        return self::getEnum(self::EXTERNAL);
    }

    public function isExternal(): bool
    {
        return $this->getValue() === self::EXTERNAL;
    }

    public const ACTIVE = 'active';

    public static function getActiveSource(): AfflictionSource
    {
        return self::getEnum(self::ACTIVE);
    }

    public function isActive(): bool
    {
        return $this->getValue() === self::ACTIVE;
    }

    public const PASSIVE = 'passive';

    public static function getPassiveSource(): AfflictionSource
    {
        return self::getEnum(self::PASSIVE);
    }

    public function isPassive(): bool
    {
        return $this->getValue() === self::PASSIVE;
    }

    public const PARTIAL_DEFORMATION = 'partial_deformation';

    public static function getPartialDeformationSource(): AfflictionSource
    {
        return self::getEnum(self::PARTIAL_DEFORMATION);
    }

    public function isPartialDeformation(): bool
    {
        return $this->getValue() === self::PARTIAL_DEFORMATION;
    }

    public const FULL_DEFORMATION = 'full_deformation';

    public static function getFullDeformationSource(): AfflictionSource
    {
        return self::getEnum(self::FULL_DEFORMATION);
    }

    public function isFullDeformation(): bool
    {
        return $this->getValue() === self::FULL_DEFORMATION;
    }

    public function isDeformation(): bool
    {
        return $this->isPartialDeformation() || $this->isFullDeformation();
    }

    /**
     * @param bool|float|int|string $enumValue
     * @return string
     * @throws \DrdPlus\Health\Afflictions\Exceptions\UnknownAfflictionSource
     */
    protected static function convertToEnumFinalValue($enumValue): string
    {
        $enumFinalValue = parent::convertToEnumFinalValue($enumValue);
        if (!in_array($enumFinalValue, [self::EXTERNAL, self::PASSIVE, self::ACTIVE, self::PARTIAL_DEFORMATION, self::FULL_DEFORMATION], true)) {
            throw new Exceptions\UnknownAfflictionSource(
                'Unexpected source of an affliction: ' . ValueDescriber::describe($enumValue)
            );
        }
        return $enumFinalValue;
    }

}