<?php
namespace DrdPlus\Fight;

class PreviousValues extends Values
{
    const NEXT_PREVIOUS_VALUES = 'next_previous_values';

    /** @var array */
    private $previousValues;

    /**
     * @param array $currentValues
     */
    public function __construct(array $currentValues)
    {
        $this->previousValues = unserialize(
                $_COOKIE[self::NEXT_PREVIOUS_VALUES]
                ??
                '',
                ['allowed_classes' => false]
            ) ?? [];
        Cookie::setCookie(self::NEXT_PREVIOUS_VALUES, serialize($currentValues));
    }

    public function getValue(string $name)
    {
        return $this->previousValues[$name] ?? null;
    }
}