<?php declare(strict_types = 1);

namespace DrdPlus\Professions;

use DrdPlus\Codes\Properties\PropertyCode;

class Fighter extends Profession
{
    /**
     * @return Fighter|Profession
     */
    public static function getIt(): Fighter
    {
        return parent::getIt();
    }

    /**
     * @return array|string[]
     */
    public function getPrimaryProperties(): array
    {
        return [PropertyCode::STRENGTH, PropertyCode::AGILITY];
    }
}