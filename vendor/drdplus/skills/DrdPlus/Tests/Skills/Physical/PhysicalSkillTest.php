<?php declare(strict_types=1);

declare(strict_types = 1);

namespace DrdPlus\Tests\Skills\Physical;

use DrdPlus\Codes\Properties\PropertyCode;
use DrdPlus\Tests\Skills\SkillTest;

class PhysicalSkillTest extends SkillTest
{

    protected function getExpectedRelatedPropertyCodes(): array
    {
        return [PropertyCode::STRENGTH, PropertyCode::AGILITY];
    }

}