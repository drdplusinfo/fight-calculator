<?php
declare(strict_types=1);

namespace DrdPlus\Properties\Derived;

use DrdPlus\Codes\Properties\PropertyCode;
use DrdPlus\BaseProperties\Charisma;
use DrdPlus\Properties\Derived\Partials\AspectOfVisage;
use DrdPlus\BaseProperties\Strength;
use DrdPlus\BaseProperties\Will;

/**
 * @method Dangerousness add(int | \Granam\Integer\IntegerInterface $value)
 * @method Dangerousness sub(int | \Granam\Integer\IntegerInterface $value)
 */
class Dangerousness extends AspectOfVisage
{
    /**
     * @param Strength $strength
     * @param Will $will
     * @param Charisma $charisma
     * @return Dangerousness
     */
    public static function getIt(Strength $strength, Will $will, Charisma $charisma): Dangerousness
    {
        return new static($strength, $will, $charisma);
    }

    public function getCode(): PropertyCode
    {
        return PropertyCode::getIt(PropertyCode::DANGEROUSNESS);
    }
}