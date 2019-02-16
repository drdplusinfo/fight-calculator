<?php
namespace DrdPlus\Health;

use DrdPlus\Codes\Body\SeriousWoundOriginCode;

class SeriousWound extends Wound
{
    /**
     * @param Health $health
     * @param WoundSize $woundSize
     * @param SeriousWoundOriginCode $seriousWoundOriginCode
     * @throws \DrdPlus\Health\Exceptions\WoundHasToBeCreatedByHealthItself
     */
    public function __construct(Health $health, WoundSize $woundSize, SeriousWoundOriginCode $seriousWoundOriginCode)
    {
        parent::__construct($health, $woundSize, $seriousWoundOriginCode);
    }

    public function isSerious(): bool
    {
        return true;
    }

    public function isOrdinary(): bool
    {
        return false;
    }
}