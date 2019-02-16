<?php
declare(strict_types=1);

namespace DrdPlus\RollsOn\QualityAndSuccess;

interface RollOnSuccess
{

    public function getRollOnQuality(): RollOnQuality;

    public function isSuccess(): bool;

    /**
     * @return string|int|float|bool
     */
    public function getResult();

    public function isFailure(): bool;

    /**
     * @return string
     */
    public function __toString();

}