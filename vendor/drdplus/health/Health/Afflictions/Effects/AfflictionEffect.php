<?php declare(strict_types=1);

namespace DrdPlus\Health\Afflictions\Effects;

use Granam\ScalarEnum\ScalarEnum;

abstract class AfflictionEffect extends ScalarEnum
{
    /**
     * Even if affected creature success on roll against trap, comes this effect into play.
     * @return bool
     */
    abstract public function isEffectiveEvenOnSuccessAgainstTrap(): bool;
}