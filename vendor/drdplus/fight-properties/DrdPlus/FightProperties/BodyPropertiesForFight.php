<?php
namespace DrdPlus\FightProperties;

use DrdPlus\BaseProperties\Agility;
use DrdPlus\BaseProperties\Charisma;
use DrdPlus\BaseProperties\Intelligence;
use DrdPlus\BaseProperties\Knack;
use DrdPlus\BaseProperties\Strength;
use DrdPlus\BaseProperties\Will;
use DrdPlus\Properties\Body\Height;
use DrdPlus\Properties\Body\Size;
use DrdPlus\Properties\Combat\BaseProperties;
use DrdPlus\Properties\Derived\Speed;
use Granam\Strict\Object\StrictObject;

class BodyPropertiesForFight extends StrictObject implements BaseProperties
{
    /**
     * @var Strength
     */
    private $strength;
    /**
     * @var Strength
     */
    private $strengthOfOffhand;
    /**
     * @var Agility
     */
    private $agility;
    /**
     * @var Knack
     */
    private $knack;
    /**
     * @var Will
     */
    private $will;
    /**
     * @var Intelligence
     */
    private $intelligence;
    /**
     * @var Charisma
     */
    private $charisma;
    /**
     * @var Size
     */
    private $size;
    /**
     * @var Height
     */
    private $height;
    /**
     * @var Speed
     */
    private $speed;

    public function __construct(
        Strength $strength,
        Agility $agility,
        Knack $knack,
        Will $will,
        Intelligence $intelligence,
        Charisma $charisma,
        Size $size,
        Height $height,
        Speed $speed
    )
    {
        $this->strength = $strength;
        $this->agility = $agility;
        $this->knack = $knack;
        $this->will = $will;
        $this->intelligence = $intelligence;
        $this->charisma = $charisma;
        $this->size = $size;
        $this->height = $height;
        $this->speed = $speed;
    }

    public function getStrength(): Strength
    {
        return $this->strength;
    }

    public function getStrengthOfMainHand(): Strength
    {
        return $this->getStrength();
    }

    public function getStrengthOfOffhand(): Strength
    {
        if ($this->strengthOfOffhand === null) {
            $this->strengthOfOffhand = $this->getStrength()->sub(2); // offhand has a malus to strength (try to carry you purchase in offhand sometimes...)
        }

        return $this->strengthOfOffhand;
    }

    public function getAgility(): Agility
    {
        return $this->agility;
    }

    public function getKnack(): Knack
    {
        return $this->knack;
    }

    public function getWill(): Will
    {
        return $this->will;
    }

    public function getIntelligence(): Intelligence
    {
        return $this->intelligence;
    }

    public function getCharisma(): Charisma
    {
        return $this->charisma;
    }

    public function getSize(): Size
    {
        return $this->size;
    }

    public function getHeight(): Height
    {
        return $this->height;
    }

    public function getSpeed(): Speed
    {
        return $this->speed;
    }

}