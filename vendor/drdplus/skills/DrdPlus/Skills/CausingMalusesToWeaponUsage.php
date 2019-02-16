<?php
declare(strict_types=1);

namespace DrdPlus\Skills;

interface CausingMalusesToWeaponUsage
{
    public function getMalusToFightNumber(): int;

    public function getMalusToAttackNumber(): int;

    public function getMalusToCover(): int;

    public function getMalusToBaseOfWounds(): int;
}