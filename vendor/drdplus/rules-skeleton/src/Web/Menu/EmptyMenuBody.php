<?php declare(strict_types=1);

namespace DrdPlus\RulesSkeleton\Web\Menu;

use Granam\Strict\Object\StrictObject;

class EmptyMenuBody extends StrictObject implements MenuBodyInterface
{

    public function __toString()
    {
        return $this->getValue();
    }

    public function getValue(): string
    {
        return '';
    }

}
