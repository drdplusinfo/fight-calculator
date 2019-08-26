<?php declare(strict_types=1);

namespace DrdPlus\Lighting;

use Granam\Integer\IntegerInterface;
use Granam\Integer\Tools\ToInteger;
use Granam\Strict\Object\StrictObject;

/**
 * See PPH page 127 right column, @link https://pph.drdplus.jaroslavtyc.com/#kvalita_osvetleni
 */
class LightingQuality extends StrictObject implements Partials\LightingQualityInterface
{
    /**
     * @var int
     */
    private $value;

    /**
     * @param int|IntegerInterface $value
     * @throws \Granam\Integer\Tools\Exceptions\WrongParameterType
     * @throws \Granam\Integer\Tools\Exceptions\ValueLostOnCast
     */
    public function __construct($value)
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

}