<?php declare(strict_types=1);

declare(strict_types = 1);

namespace DrdPlus\Tests\Skills\Combined;

use DrdPlus\Codes\Properties\PropertyCode;

class FightWithShootingWeaponsTest extends CausingMalusesToWeaponUsageTest
{
    protected function getExpectedRelatedPropertyCodes(): array
    {
        return [PropertyCode::KNACK, PropertyCode::CHARISMA];
    }

}