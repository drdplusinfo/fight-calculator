<?php declare(strict_types=1);

namespace DrdPlus\RollsOn\Traps;

use DrdPlus\Codes\Properties\RemarkableSenseCode;
use DrdPlus\Codes\RaceCode;
use DrdPlus\Codes\SubRaceCode;
use DrdPlus\Tables\Tables;
use Granam\Integer\PositiveInteger;
use Granam\Strict\Object\StrictObject;

class BonusFromUsedRemarkableSense extends StrictObject implements PositiveInteger
{
    /**
     * @var int
     */
    private $value;

    public function __construct(
        RaceCode $raceCode,
        SubRaceCode $subRaceCode,
        RemarkableSenseCode $usedSenseCode,
        Tables $tables
    )
    {
        $this->value = 0;
        if ($tables->getRacesTable()->getRemarkableSense($raceCode, $subRaceCode) === $usedSenseCode->getValue()) {
            $this->value = 1;
        }
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