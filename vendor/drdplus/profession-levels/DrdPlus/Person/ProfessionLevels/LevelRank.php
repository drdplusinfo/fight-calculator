<?php
declare(strict_types=1);

namespace DrdPlus\Person\ProfessionLevels;

use Granam\Integer\IntegerInterface;
use Granam\IntegerEnum\IntegerEnum;
use Granam\Tools\ValueDescriber;

/**
 * @method static LevelRank getEnum($value)
 */
class LevelRank extends IntegerEnum
{
    /**
     * @param int|IntegerInterface $value
     * @return LevelRank
     */
    public static function getIt($value): LevelRank
    {
        return static::getEnum($value);
    }

    /**
     * @param int|IntegerInterface $value
     * @throws \DrdPlus\Person\ProfessionLevels\Exceptions\InvalidLevelRank
     * @throws \Granam\ScalarEnum\Exceptions\WrongValueForScalarEnum
     */
    public function __construct($value)
    {
        parent::__construct($value);
        if ($this->getValue() < 0) {
            throw new Exceptions\InvalidLevelRank(
                'Level can not be lesser than 0, got ' . ValueDescriber::describe($value)
            );
        }
    }

    public function isZeroLevel(): bool
    {
        return $this->getValue() === 0;
    }

    public function isFirstLevel(): bool
    {
        return $this->getValue() === 1;
    }

    public function isNextLevel(): bool
    {
        return $this->getValue() > 1;
    }
}