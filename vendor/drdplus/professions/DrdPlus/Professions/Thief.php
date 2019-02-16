<?php
namespace DrdPlus\Professions;

use DrdPlus\Codes\Properties\PropertyCode;

class Thief extends Profession
{
    /**
     * @return Thief|Profession
     */
    public static function getIt(): Thief
    {
        return parent::getIt();
    }

    /**
     * @return array|string[]
     */
    public function getPrimaryProperties(): array
    {
        return [PropertyCode::AGILITY, PropertyCode::KNACK];
    }
}