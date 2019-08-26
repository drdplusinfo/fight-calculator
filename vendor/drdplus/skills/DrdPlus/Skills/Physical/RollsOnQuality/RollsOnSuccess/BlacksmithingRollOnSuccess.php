<?php declare(strict_types=1);

namespace DrdPlus\Skills\Physical\RollsOnQuality\RollsOnSuccess;

use DrdPlus\RollsOn\QualityAndSuccess\SimpleRollOnSuccess;
use DrdPlus\Skills\Physical\RollsOnQuality\BlacksmithingQuality;

class BlacksmithingRollOnSuccess extends SimpleRollOnSuccess
{
    /**
     * @param \Granam\Integer\IntegerInterface|int $difficulty
     * @param BlacksmithingQuality $blacksmithQuality
     */
    public function __construct($difficulty, BlacksmithingQuality $blacksmithQuality)
    {
        parent::__construct(
            $difficulty,
            $blacksmithQuality,
            self::DEFAULT_SUCCESS_RESULT_CODE,
            self::DEFAULT_FAILURE_RESULT_CODE
        );
    }

}