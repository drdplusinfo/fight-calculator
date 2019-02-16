<?php
declare(strict_types=1);

namespace DrdPlus\Skills\Combined;

use DrdPlus\Codes\Properties\PropertyCode;
use DrdPlus\Codes\Skills\SkillTypeCode;
use DrdPlus\Skills\SkillPoint;

class CombinedSkillPoint extends SkillPoint
{

    public const COMBINED = SkillTypeCode::COMBINED;

    public function getTypeName(): string
    {
        return static::COMBINED;
    }

    /**
     * @return array|string[]
     */
    public function getRelatedProperties(): array
    {
        return [PropertyCode::KNACK, PropertyCode::CHARISMA];
    }

}