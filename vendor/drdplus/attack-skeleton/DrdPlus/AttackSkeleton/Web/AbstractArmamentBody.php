<?php
declare(strict_types=1);

namespace DrdPlus\AttackSkeleton\Web;

use Granam\Strict\Object\StrictObject;
use Granam\String\StringInterface;
use Granam\WebContentBuilder\Web\BodyInterface;

abstract class AbstractArmamentBody extends StrictObject implements BodyInterface
{
    public function __toString()
    {
        return $this->getValue();
    }

    protected function getSelected(StringInterface $current, StringInterface $selected): string
    {
        return $current->getValue() === $selected->getValue()
            ? 'selected'
            : '';
    }

    protected function getDisabled(bool $canUseIt): string
    {
        return !$canUseIt
            ? 'disabled'
            : '';
    }

    protected function getUsabilityPictogram(bool $canUseIt): string
    {
        return !$canUseIt
            ? '💪 '
            : '';
    }
}