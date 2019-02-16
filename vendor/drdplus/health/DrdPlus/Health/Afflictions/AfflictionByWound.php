<?php
namespace DrdPlus\Health\Afflictions;

use DrdPlus\Health\Afflictions\Effects\AfflictionEffect;
use DrdPlus\Health\Afflictions\ElementalPertinence\ElementalPertinence;
use DrdPlus\Health\SeriousWound;

abstract class AfflictionByWound extends Affliction
{
    /**
     * @var SeriousWound
     */
    private $seriousWound;

    protected function __construct(
        SeriousWound $seriousWound, // wound can be healed, but never disappears - just stays healed
        AfflictionProperty $property,
        AfflictionDangerousness $dangerousness,
        AfflictionDomain $domain,
        AfflictionVirulence $virulence,
        AfflictionSource $source,
        AfflictionSize $size,
        ElementalPertinence $elementalPertinence,
        AfflictionEffect $effect,
        \DateInterval $outbreakPeriod,
        AfflictionName $afflictionName
    )
    {
        if ($seriousWound->isOld()) {
            throw new Exceptions\WoundHasToBeFreshForAffliction(
                "Given wound of value {$seriousWound} and origin '{$seriousWound->getWoundOriginCode()}' should be untreated to create an affliction."
            );
        }
        $this->seriousWound = $seriousWound;
        parent::__construct(
            $seriousWound->getHealth(),
            $property,
            $dangerousness,
            $domain,
            $virulence,
            $source,
            $size,
            $elementalPertinence,
            $effect,
            $outbreakPeriod,
            $afflictionName
        );
    }

    public function getSeriousWound(): SeriousWound
    {
        return $this->seriousWound;
    }

    public function getWillMalus(): int
    {
        return 0; // currently no wound affliction can affect will
    }

    public function getIntelligenceMalus(): int
    {
        return 0; // currently no wound affliction can affect will
    }

    public function getCharismaMalus(): int
    {
        return 0; // currently no wound affliction can affect will
    }
}