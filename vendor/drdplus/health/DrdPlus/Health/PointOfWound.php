<?php declare(strict_types=1);

namespace DrdPlus\Health;

use Granam\Integer\IntegerInterface;
use Granam\Strict\Object\StrictObject;

class PointOfWound extends StrictObject implements IntegerInterface
{

    /**
     * @var Wound
     */
    private $wound;

    public function __construct(Wound $wound)
    {
        $this->wound = $wound;
    }

    public function getWound(): Wound
    {
        return $this->wound;
    }

    public function getValue(): int
    {
        return 1;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return (string)$this->getValue();
    }

}