<?php
declare(strict_types = 1);

namespace DrdPlus\Tests\Skills\Combined;

use DrdPlus\Codes\Properties\PropertyCode;
use DrdPlus\Tests\Skills\SkillTest;

class CombinedSkillTest extends SkillTest
{
    /**
     * @return array|string[]
     */
    protected function getExpectedRelatedPropertyCodes(): array
    {
        return [PropertyCode::KNACK, PropertyCode::CHARISMA];
    }
}