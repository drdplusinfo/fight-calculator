<?php declare(strict_types = 1);

declare(strict_types=1);

namespace DrdPlus\Properties\Restrictions;

use DrdPlus\Codes\Properties\PropertyCode;
use Granam\BooleanEnum\BooleanEnum;

class RequiresDmAgreement extends BooleanEnum implements RestrictionProperty
{
    /**
     * @param bool $value
     * @return RequiresDmAgreement
     * @throws \Granam\BooleanEnum\Exceptions\WrongValueForBooleanEnum
     */
    public static function getIt($value): RequiresDmAgreement
    {
        return new static($value);
    }

    public function getCode(): PropertyCode
    {
        return PropertyCode::getIt(PropertyCode::REQUIRES_DM_AGREEMENT);
    }

}