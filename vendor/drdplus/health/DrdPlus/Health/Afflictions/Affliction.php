<?php
namespace DrdPlus\Health\Afflictions;

use DrdPlus\Health\Afflictions\Effects\AfflictionEffect;
use DrdPlus\Health\Afflictions\ElementalPertinence\ElementalPertinence;
use DrdPlus\Health\Health;
use Granam\Strict\Object\StrictObject;

abstract class Affliction extends StrictObject
{
    /**
     * @var \DrdPlus\Health\Health
     */
    private $health;
    /**
     * @var AfflictionProperty
     */
    private $property;
    /**
     * @var AfflictionDangerousness
     */
    private $dangerousness;
    /**
     * @var AfflictionDomain
     */
    private $domain;
    /**
     * @var AfflictionVirulence
     */
    private $virulence;
    /**
     * @var AfflictionSource
     */
    private $source;
    /**
     * @var AfflictionSize
     */
    private $afflictionSize;
    /**
     * @var ElementalPertinence
     */
    private $elementalPertinence;
    /**
     * @var AfflictionEffect
     */
    private $afflictionEffect;
    /**
     * @var \DateInterval
     */
    private $outbreakPeriod;
    /**
     * @var AfflictionName
     */
    private $afflictionName;

    /**
     * @param Health $health
     * @param AfflictionProperty $property
     * @param AfflictionDangerousness $dangerousness
     * @param AfflictionDomain $domain
     * @param AfflictionVirulence $virulence
     * @param AfflictionSource $source
     * @param AfflictionSize $size
     * @param ElementalPertinence $elementalPertinence
     * @param AfflictionEffect $effect
     * @param \DateInterval $outbreakPeriod
     * @param AfflictionName $afflictionName
     * @throws \DrdPlus\Health\Exceptions\UnknownAfflictionOriginatingWound
     * @throws \DrdPlus\Health\Exceptions\AfflictionIsAlreadyRegistered
     */
    protected function __construct(
        Health $health,
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
        $health->addAffliction($this);
        $this->property = $property;
        $this->dangerousness = $dangerousness;
        /** @noinspection UnusedConstructorDependenciesInspection */
        $this->health = $health;
        $this->domain = $domain;
        $this->virulence = $virulence;
        $this->source = $source;
        $this->afflictionSize = $size;
        $this->elementalPertinence = $elementalPertinence;
        $this->afflictionEffect = $effect;
        $this->outbreakPeriod = $outbreakPeriod;
        $this->afflictionName = $afflictionName;
    }

    public function getProperty(): AfflictionProperty
    {
        return $this->property;
    }

    public function getDangerousness(): AfflictionDangerousness
    {
        return $this->dangerousness;
    }

    public function getDomain(): AfflictionDomain
    {
        return $this->domain;
    }

    public function getVirulence(): AfflictionVirulence
    {
        return $this->virulence;
    }

    public function getSource(): AfflictionSource
    {
        return $this->source;
    }

    public function getAfflictionSize(): AfflictionSize
    {
        return $this->afflictionSize;
    }

    public function getElementalPertinence(): ElementalPertinence
    {
        return $this->elementalPertinence;
    }

    public function getAfflictionEffect(): AfflictionEffect
    {
        return $this->afflictionEffect;
    }

    public function getOutbreakPeriod(): \DateInterval
    {
        return $this->outbreakPeriod;
    }

    public function getName(): AfflictionName
    {
        return $this->afflictionName;
    }

    abstract public function getHealMalus(): int;

    abstract public function getMalusToActivities(): int;

    abstract public function getStrengthMalus(): int;

    abstract public function getAgilityMalus(): int;

    abstract public function getKnackMalus(): int;

    abstract public function getWillMalus(): int;

    abstract public function getIntelligenceMalus(): int;

    abstract public function getCharismaMalus(): int;
}