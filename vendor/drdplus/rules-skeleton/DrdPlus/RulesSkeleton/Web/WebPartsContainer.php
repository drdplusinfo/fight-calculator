<?php
declare(strict_types=1);

namespace DrdPlus\RulesSkeleton\Web;

use DrdPlus\RulesSkeleton\ServicesContainer;
use Granam\Strict\Object\StrictObject;

class WebPartsContainer extends StrictObject
{
    /** @var PassBody */
    private $passBody;
    /** @var DebugContactsBody */
    private $debugContactsBody;
    /** @var PdfBody */
    private $pdfBody;
    /** @var RulesMainBody */
    private $rulesMainBody;
    /** @var TablesBody */
    private $tablesBody;
    /** @var ServicesContainer */
    private $servicesContainer;

    public function __construct(ServicesContainer $servicesContainer)
    {
        $this->servicesContainer = $servicesContainer;
    }

    public function getPassBody(): PassBody
    {
        if ($this->passBody === null) {
            $this->passBody = new PassBody($this->servicesContainer->getPass());
        }
        return $this->passBody;
    }

    public function getDebugContactsBody(): DebugContactsBody
    {
        if ($this->debugContactsBody === null) {
            $this->debugContactsBody = new DebugContactsBody();
        }
        return $this->debugContactsBody;
    }

    public function getPdfBody(): PdfBody
    {
        if ($this->pdfBody === null) {
            $this->pdfBody = new PdfBody($this->servicesContainer->getDirs());
        }
        return $this->pdfBody;
    }

    public function getTablesBody(): TablesBody
    {
        if ($this->tablesBody === null) {
            $this->tablesBody = new TablesBody($this->getRulesMainBody(), $this->servicesContainer->getHtmlHelper(), $this->servicesContainer->getRequest());
        }
        return $this->tablesBody;
    }

    public function getRulesMainBody(): RulesMainBody
    {
        if ($this->rulesMainBody === null) {
            $this->rulesMainBody = new RulesMainBody($this->servicesContainer->getWebFiles(), $this->servicesContainer->getWebPartsContainer());
        }
        return $this->rulesMainBody;
    }

}