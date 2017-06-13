<?php
namespace DrdPlus\Fight;

use Granam\Strict\Object\StrictObject;

class Cookie extends StrictObject
{
    public static function setCookie(string $name, $value, int $expire = 0): bool
    {
        $result = setcookie(
            $name,
            $value,
            $expire,
            '/',
            '',
            !empty($_SERVER['HTTPS']), // secure only ?
            true // http only
        );
        if ($result) {
            $_COOKIE[$name] = $value;
        }

        return $result;
    }
}