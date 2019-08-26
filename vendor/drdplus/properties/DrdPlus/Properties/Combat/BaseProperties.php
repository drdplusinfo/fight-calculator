<?php declare(strict_types = 1);

declare(strict_types=1);

namespace DrdPlus\Properties\Combat;

use DrdPlus\BaseProperties\Agility;
use DrdPlus\BaseProperties\Charisma;
use DrdPlus\BaseProperties\Intelligence;
use DrdPlus\BaseProperties\Knack;
use DrdPlus\BaseProperties\Strength;
use DrdPlus\BaseProperties\Will;

interface BaseProperties
{
    /**
     * @return Strength
     */
    public function getStrength(): Strength;

    /**
     * @return Agility
     */
    public function getAgility(): Agility;

    /**
     * @return Knack
     */
    public function getKnack(): Knack;

    /**
     * @return Will
     */
    public function getWill(): Will;

    /**
     * @return Intelligence
     */
    public function getIntelligence(): Intelligence;

    /**
     * @return Charisma
     */
    public function getCharisma(): Charisma;
}