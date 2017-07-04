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
     * @return mixed
     */
    public function getValue(string $name)
    {
        if (array_key_exists($name, $this->valuesFromInput)) {
            return $this->valuesFromInput[$name];
        }

        return $this->historyValues->getValue($name);
    }
}