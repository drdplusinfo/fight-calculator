<?php
namespace DrdPlus\Professions;

use DrdPlus\Codes\Properties\PropertyCode;

class Theurgist extends Profession
{
    /**
     * @return Theurgist|Profession
     */
    public static function getIt(): Theurgist
    {
        return parent::getIt();
    }

    /**
     * @return array|string[]
     */
    public function getPrimaryProperties(): array
    {
        return [PropertyCode::INTELLIGENCE, PropertyCode::CHARISMA];
    }
}