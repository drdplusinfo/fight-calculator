<?php
declare(strict_types = 1);

namespace DrdPlus\Skills\Exceptions;

use DrdPlus\Codes\Armaments\WeaponlikeCode;

class UnknownTypeOfWeapon extends \InvalidArgumentException implements Logic
{

    /**
     * @param string|WeaponlikeCode $message
     * @param int $code
     * @param \Exception $previous
     */
    public function __construct($message = '', $code = 0, \Exception $previous = null)
    {
        if ($message instanceof WeaponlikeCode) {
            $message = "Given weapon-like '{$message}' is of unknown type";
        }
        parent::__construct($message, $code, $previous);
    }

}