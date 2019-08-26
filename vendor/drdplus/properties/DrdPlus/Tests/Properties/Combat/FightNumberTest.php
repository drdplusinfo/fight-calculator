<?php declare(strict_types = 1);

declare(strict_types=1);

namespace DrdPlus\Tests\Properties\Combat;

use DrdPlus\Codes\Armaments\MeleeWeaponlikeCode;
use DrdPlus\Codes\Armaments\WeaponlikeCode;
use DrdPlus\Properties\Combat\Fight;
use DrdPlus\Properties\Combat\FightNumber;
use DrdPlus\Tables\Armaments\Partials\MeleeWeaponlikesTable;
use DrdPlus\Tables\Tables;
use DrdPlus\Tests\Properties\Combat\Partials\CharacteristicForGameTest;

class FightNumberTest extends CharacteristicForGameTest
{
    protected function createSut()
    {
        return FightNumber::getIt(
            $this->createFight(123),
            $weaponlikeCode = $this->createWeaponlikeCode(),
            $this->createTables($weaponlikeCode, 456)
        );
    }

    /**
     * @param $value
     * @return \Mockery\MockInterface|Fight
     */
    private function createFight($value)
    {
        $fight = $this->mockery(Fight::class);
        $fight->shouldReceive('getValue')
            ->andReturn($value);
        $fight->shouldReceive('__toString')
            ->andReturn((string)$value);

        return $fight;
    }

    /**
     * @return \Mockery\MockInterface|WeaponlikeCode
     */
    private function createWeaponlikeCode()
    {
        return $this->mockery(WeaponlikeCode::class);
    }

    /**
     * @param WeaponlikeCode $weaponlikeCode
     * @param int $length
     * @return \Mockery\MockInterface|Tables
     */
    private function createTables(WeaponlikeCode $weaponlikeCode, int $length)
    {
        $tables = $this->mockery(Tables::class);
        $tables->shouldReceive('getMeleeWeaponlikeTableByMeleeWeaponlikeCode')
            ->andReturn($meleeWeaponlikesTable = $this->mockery(MeleeWeaponlikesTable::class));
        $meleeWeaponlikesTable->shouldReceive('getLengthOf')
            ->zeroOrMoreTimes()
            ->with($weaponlikeCode)
            ->andReturn($length);

        return $tables;
    }

    /**
     * @test
     */
    public function I_can_get_property_easily(): void
    {
        $fightNumberWithNonMeleeWeapon = FightNumber::getIt(
            $this->createFight(123),
            $weaponlikeCode = $this->createWeaponlikeCode(),
            $this->createTables($weaponlikeCode, 456)
        );
        self::assertSame(
            123,
            $fightNumberWithNonMeleeWeapon->getValue(),
            'Used weapon-like is not a ' . MeleeWeaponlikeCode::class . ' so its length should not be counted'
        );
        /** @var MeleeWeaponlikeCode $meleeWeaponlikeCode */
        $meleeWeaponlikeCode = $this->mockery(MeleeWeaponlikeCode::class);
        $fightNumberWithMeleeWeapon = FightNumber::getIt(
            $this->createFight(123),
            $meleeWeaponlikeCode,
            $this->createTables($meleeWeaponlikeCode, 456)
        );
        self::assertSame(
            579,
            $fightNumberWithMeleeWeapon->getValue(),
            'Used weapon-like is a ' . MeleeWeaponlikeCode::class . ' so its length should be counted'
        );
    }

}