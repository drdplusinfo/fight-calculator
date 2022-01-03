<?php declare(strict_types=1);

namespace Granam\BooleanEnum\Exceptions;

use Granam\ScalarEnum\Exceptions\WrongValueForScalarEnum;

class WrongValueForBooleanEnum extends WrongValueForScalarEnum implements Logic
{

}