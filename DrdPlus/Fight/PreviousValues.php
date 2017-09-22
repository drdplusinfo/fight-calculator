<?php
namespace DrdPlus\Fight;

use DrdPlus\Configurator\Skeleton\Cookie;

class PreviousValues extends Values
{
    const NEXT_PREVIOUS_VALUES = 'next_previous_values';

    /** @var array */
    private $previousValues;

    /**
     * @param array $currentValuesToRemember
     * @param bool $reset
     */
    public function __construct(array $currentValuesToRemember, bool $reset)
    {
        if ($reset) {
            $this->previousValues = [];
            Cookie::setCookie(self::NEXT_PREVIOUS_VALUES, null);
        } else {
            $this->previousValues = unserialize(
                    $_COOKIE[self::NEXT_PREVIOUS_VALUES] ?? '',
                    ['allowed_classes' => false]
                )
                ?? [];
            Cookie::setCookie(self::NEXT_PREVIOUS_VALUES, serialize($currentValuesToRemember));
        }
    }

    /**
     * @param string $name
     * @return mixed|null
     */
    public function getValue(string $name)
    {
        return $this->previousValues[$name] ?? null;
    }
}