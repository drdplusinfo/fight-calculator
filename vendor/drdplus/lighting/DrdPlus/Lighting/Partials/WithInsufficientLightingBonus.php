<?php declare(strict_types=1);

namespace DrdPlus\Lighting\Partials;

interface WithInsufficientLightingBonus
{
    public function getInsufficientLightingBonus(): int;
}