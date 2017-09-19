<?php
namespace DrdPlus\Fight;

class CurrentValues extends Values
{
    /** @var array */
    private $valuesFromInput;
    /**
     * @var HistoryWithSkillRanks
     */
    private $historyValues;

    /**
     * @param array $valuesFromInput
     * @param HistoryWithSkillRanks $historyValues
     */
    public function __construct(array $valuesFromInput, HistoryWithSkillRanks $historyValues)
    {
        $this->valuesFromInput = $valuesFromInput;
        $this->historyValues = $historyValues;
    }

    /**
     * @param string $name
     * @return string|null
     */
    public function getValue(string $name):? string
    {
        if (array_key_exists($name, $this->valuesFromInput)) {
            return $this->valuesFromInput[$name];
        }

        return $this->historyValues->getValue($name);
    }

    /**
     * @param string $name
     * @return null|string
     */
    public function getCurrentOnlyValue(string $name):? string
    {
        return $this->valuesFromInput[$name] ?? null;
    }
}