<?php
namespace DrdPlus\Professions;

use DrdPlus\Codes\Properties\PropertyCode;

class Priest extends Profession
{
    /**
     * @return Priest|Profession
     */
    public static function getIt(): Priest
    {
        return parent::getIt();
    }

    /**
     * @return array|string[]
     */
    public function getPrimaryProperties(): array
    {
        return [PropertyCode::CHARISMA, PropertyCode::WILL];
    }
}