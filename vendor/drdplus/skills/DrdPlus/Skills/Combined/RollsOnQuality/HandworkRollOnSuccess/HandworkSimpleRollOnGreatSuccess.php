<?php
declare(strict_types=1);

namespace DrdPlus\Skills\Combined\RollsOnQuality\HandworkRollOnSuccess;

use DrdPlus\Skills\Combined\RollsOnQuality\HandworkQuality;

class HandworkSimpleRollOnGreatSuccess extends HandworkSimpleRollOnSuccess
{
    public const BRAVURA = 'bravura';

    public function __construct(HandworkQuality $handworkQuality, int $difficultyModification)
    {
        parent::__construct(
            20,
            $difficultyModification,
            $handworkQuality,
            self::BRAVURA,
            HandworkSimpleRollOnModerateSuccess::HANDY
        );
    }

}