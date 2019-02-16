<?php
declare(strict_types=1);

namespace DrdPlus\RollsOn\QualityAndSuccess;

use DrdPlus\RollsOn\QualityAndSuccess\Requirements\AnimalDefiance;
use DrdPlus\RollsOn\QualityAndSuccess\Requirements\PreviousFailuresCount;
use DrdPlus\RollsOn\QualityAndSuccess\Requirements\Ride;
use DrdPlus\RollsOn\QualityAndSuccess\Requirements\RidingSkill;
use DrdPlus\RollsOn\Traps\RollOnAgility;

/**
 * @method RollOnAgility getRollOnQuality
 * @method string getResult
 */
class RollOnAnimalControl extends ExtendedRollOnSuccess
{
    public const FATAL_FAILURE = 'fatal_failure';
    public const MODERATE_FAILURE = 'moderate_failure';
    public const SUCCESS = SimpleRollOnSuccess::DEFAULT_SUCCESS_RESULT_CODE;

    public function __construct(
        RollOnAgility $rollOnAgility,
        AnimalDefiance $animalDefiance,
        Ride $ride,
        RidingSkill $ridingSkill,
        PreviousFailuresCount $previousFailuresCount
    )
    {
        $toSuccessTrap = $animalDefiance->getValue() + $ride->getValue() - $ridingSkill->getValue() + $previousFailuresCount->getValue();
        $toModerateFailureTrap = $toSuccessTrap - 4;
        parent::__construct(
            new SimpleRollOnSuccess($toModerateFailureTrap, $rollOnAgility, self::MODERATE_FAILURE, self::FATAL_FAILURE),
            new SimpleRollOnSuccess($toSuccessTrap, $rollOnAgility, self::SUCCESS, self::MODERATE_FAILURE)
        );
    }

    public function getRollOnAgility(): RollOnAgility
    {
        return $this->getRollOnQuality();
    }

    public function isModerateFailure(): bool
    {
        return $this->getResult() === self::MODERATE_FAILURE;
    }

    public function isFatalFailure(): bool
    {
        return $this->getResult() === self::FATAL_FAILURE;
    }

    public function isFailure(): bool
    {
        // even moderate failure is failure on riding an animal
        return $this->isFatalFailure() || $this->isModerateFailure();
    }

    public function isSuccess(): bool
    {
        return !$this->isFailure();
    }

}