<?php declare(strict_types=1);

namespace DrdPlus\Skills\Combined\RollsOnQuality\HandworkRollOnSuccess;

use DrdPlus\Skills\Combined\RollsOnQuality\HandworkQuality;

class HandworkSimpleRollOnModerateSuccess extends HandworkSimpleRollOnSuccess
{
    public const HANDY = 'handy';

    public function __construct(HandworkQuality $handworkQuality, int $difficultyModification)
    {
        parent::__construct(
            17,
            $difficultyModification,
            $handworkQuality,
            self::HANDY,
            HandworkSimpleRollOnLowSuccess::USABLE
        );
    }

}