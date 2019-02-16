<?php
namespace DrdPlus\Health;

use DrdPlus\Codes\Body\OrdinaryWoundOriginCode;
use DrdPlus\Codes\Body\SeriousWoundOriginCode;
use DrdPlus\Codes\Body\WoundOriginCode;
use Granam\Integer\IntegerInterface;
use Granam\Strict\Object\StrictObject;

abstract class Wound extends StrictObject implements IntegerInterface
{
    /**
     * @var Health
     */
    private $health;
    /**
     * @var array|PointOfWound[]
     */
    private $pointsOfWound;
    /**
     * @var WoundOriginCode
     */
    private $woundOriginCode;
    /**
     * @var bool
     */
    private $old;

    /**
     * @param Health $health
     * @param WoundSize $woundSize (it can be also zero; usable for afflictions without a damage at all)
     * @param WoundOriginCode $woundOriginCode Ordinary origin is for lesser wound, others for serious wound
     * @throws \DrdPlus\Health\Exceptions\WoundHasToBeCreatedByHealthItself
     */
    protected function __construct(Health $health, WoundSize $woundSize, WoundOriginCode $woundOriginCode)
    {
        $this->checkIfCreatedByGivenHealth($health);
        $this->health = $health;
        $this->pointsOfWound = $this->createPointsOfWound($woundSize);
        $this->woundOriginCode = $woundOriginCode;
        $this->old = false;
    }

    /**
     * @param Health $health
     * @throws \DrdPlus\Health\Exceptions\WoundHasToBeCreatedByHealthItself
     */
    private function checkIfCreatedByGivenHealth(Health $health)
    {
        if (!$health->isOpenForNewWound()) {
            throw new Exceptions\WoundHasToBeCreatedByHealthItself(
                'Given health is not open for new wounds. Every wound has to be created by health itself.'
            );
        }
    }

    /**
     * @param WoundSize $woundSize
     * @return PointOfWound[]|array
     */
    private function createPointsOfWound(WoundSize $woundSize): array
    {
        $pointsOfWound = [];
        for ($wounded = $woundSize->getValue(); $wounded > 0; $wounded--) {
            $pointsOfWound[] = new PointOfWound($this); // implicit value of point of wound is 1
        }
        return $pointsOfWound;
    }

    public function getHealth(): Health
    {
        return $this->health;
    }

    /**
     * @return array|PointOfWound[]
     */
    public function getPointsOfWound(): array
    {
        return $this->pointsOfWound;
    }

    /**
     * @return SeriousWoundOriginCode|OrdinaryWoundOriginCode|WoundOriginCode
     */
    public function getWoundOriginCode(): WoundOriginCode
    {
        return $this->woundOriginCode;
    }

    public function getValue(): int
    {
        // each point has value of 1, therefore count is enough
        return \count($this->getPointsOfWound());
    }

    public function getWoundSize(): WoundSize
    {
        return WoundSize::createIt($this->getValue());
    }

    abstract public function isSerious(): bool;

    abstract public function isOrdinary(): bool;

    /**
     * @param int $healUpToWounds
     * @return int amount of healed points of wound
     */
    public function heal(int $healUpToWounds): int
    {
        $this->setOld(); // any wound is "old", treated and can be healed by regeneration or a true professional only
        // technical note: orphaned points of wound are removed automatically on persistence
        if ($healUpToWounds >= $this->getValue()) { // there is power to heal it all
            $healed = $this->getValue();
            $this->pointsOfWound = []; // unbinds all the points of wound

            return $healed;
        }
        $healed = 0;
        for ($healing = 1; $healing <= $healUpToWounds; $healing++) {
            // removing points one by one
            \array_pop($this->pointsOfWound);
            $healed++;
        }
        return $healed; // just a partial heal
    }

    public function isHealed(): bool
    {
        return $this->getValue() === 0;
    }

    public function isOld(): bool
    {
        return $this->old;
    }

    public function isFresh(): bool
    {
        return !$this->old;
    }

    public function setOld()
    {
        $this->old = true;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return (string)$this->getValue();
    }
}