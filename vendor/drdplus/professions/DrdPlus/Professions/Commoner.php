<?php declare(strict_types = 1);

namespace DrdPlus\Professions;

class Commoner extends Profession
{
    /**
     * @return Commoner|Profession
     */
    public static function getIt(): Commoner
    {
        return parent::getIt();
    }

    /**
     * @return array|string[]
     */
    public function getPrimaryProperties(): array
    {
        return [];
    }
}