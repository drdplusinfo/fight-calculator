<?php declare(strict_types=1);

namespace DrdPlus\CalculatorSkeleton\Web;

use DrdPlus\RulesSkeleton\Web\Tools\WebPartsContainer;

class CalculatorWebPartsContainer extends WebPartsContainer
{

    private ?HistoryDeletionBody $historyDeletionBody = null;
    private ?CalculatorDebugContactsBody $calculatorDebugContactsBody = null;

    public function getHistoryDeletionBody(): HistoryDeletionBody
    {
        if ($this->historyDeletionBody === null) {
            $this->historyDeletionBody = new HistoryDeletionBody();
        }
        return $this->historyDeletionBody;
    }

    public function getCalculatorDebugContactsBody(): CalculatorDebugContactsBody
    {
        if ($this->calculatorDebugContactsBody === null) {
            $this->calculatorDebugContactsBody = new CalculatorDebugContactsBody();
        }
        return $this->calculatorDebugContactsBody;
    }

}
