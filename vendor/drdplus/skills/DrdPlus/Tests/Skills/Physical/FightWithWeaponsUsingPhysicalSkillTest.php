<?php declare(strict_types=1);

declare(strict_types = 1);

namespace DrdPlus\Tests\Skills\Physical;

use DrdPlus\Codes\Properties\PropertyCode;
use DrdPlus\Tests\Skills\Combined\CausingMalusesToWeaponUsageTest;

class FightWithWeaponsUsingPhysicalSkillTest extends CausingMalusesToWeaponUsageTest
{
    protected function getExpectedRelatedPropertyCodes(): array
    {
        return [PropertyCode::STRENGTH, PropertyCode::AGILITY];
    }

}
