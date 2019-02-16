<?php
declare(strict_types = 1);

namespace DrdPlus\Tests\Skills\Psychical;

use DrdPlus\Codes\Properties\PropertyCode;
use DrdPlus\Tests\Skills\SkillTest;

class PsychicalSkillTest extends SkillTest
{
    protected function getExpectedRelatedPropertyCodes(): array
    {
        return [PropertyCode::WILL, PropertyCode::INTELLIGENCE];
    }

}