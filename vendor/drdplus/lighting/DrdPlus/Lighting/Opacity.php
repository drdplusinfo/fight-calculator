<?php declare(strict_types=1);

namespace DrdPlus\Lighting;

use DrdPlus\Tables\Measurements\Amount\AmountBonus;
use DrdPlus\Tables\Measurements\Distance\Distance;
use DrdPlus\Tables\Tables;
use Granam\Integer\IntegerInterface;
use Granam\Integer\PositiveInteger;
use Granam\Integer\Tools\ToInteger;
use Granam\Strict\Object\StrictObject;

/**
 * See PPH page 129 left column, @link https://pph.drdplus.jaroslavtyc.com/#nepruhledne_prostredi
 */
class Opacity extends StrictObject implements PositiveInteger
{
    /**
     * @var int
     */
    private $value;

    public static function createFromBarrierDensity(
        IntegerInterface $barrierDensity,
        Distance $barrierLength,
        Tables $tables
    ): Opacity
    {
        $amountBonusValue = $barrierDensity->getValue() + $barrierLength->getBonus()->getValue();
        if ($amountBonusValue < -20) { // workaround to avoid unexpected amount bonus value
            return static::createTransparent();
        }

        return new self((new AmountBonus($amountBonusValue, $tables->getAmountTable()))->getAmount()->getValue());
    }

    public static function createTransparent(): Opacity
    {
        return new static(0);
    }

    /**
     * @param IntegerInterface|int $value
     */
    private function __construct($value)
    {
        $this->value = ToInteger::toInteger($value);
    }

    public function getValue(): int
    {
        return $this->value;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return (string)$this->getValue();
    }

    public function getVisibilityMalus(): int
    {
        if ($this->getValue() > 0) {
            return -$this->getValue();
        }
        return 0;
    }

}