<?php
namespace DrdPlus\Health;

use DrdPlus\Codes\Body\OrdinaryWoundOriginCode;

class OrdinaryWound extends Wound
{
    /**
     * @param Health $health
     * @param WoundSize $woundSize
     * @throws \DrdPlus\Health\Exceptions\WoundHasToBeCreatedByHealthItself
     */
    public function __construct(Health $health, WoundSize $woundSize)
    {
        parent::__construct($health, $woundSize, OrdinaryWoundOriginCode::getIt());
    }

    public function isSerious(): bool
    {
        return false;
    }

    public function isOrdinary(): bool
    {
        return true;
    }
}