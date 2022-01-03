<?php declare(strict_types = 1);

namespace DrdPlus\Professions;

use DrdPlus\Codes\Properties\PropertyCode;

class Wizard extends Profession
{
    /**
     * @return Wizard|Profession
     */
    public static function getIt():Wizard
    {
        return parent::getIt();
    }

    /**
     * @return array|string[]
     */
    public function getPrimaryProperties(): array
    {
        return [PropertyCode::WILL, PropertyCode::INTELLIGENCE];
    }
}