<?php declare(strict_types=1);

namespace DrdPlus\RulesSkeleton\Web\Tools;

use DrdPlus\RulesSkeleton\Configurations\Configuration;
use DrdPlus\RulesSkeleton\Configurations\Dirs;
use DrdPlus\RulesSkeleton\HtmlHelper;
use DrdPlus\RulesSkeleton\Request;
use DrdPlus\RulesSkeleton\UsagePolicy;
use DrdPlus\RulesSkeleton\Web\DebugContacts\DebugContactsBody;
use DrdPlus\RulesSkeleton\Web\Gateway\GatewayBody;
use DrdPlus\RulesSkeleton\Web\NotFound\NotFoundBody;
use DrdPlus\RulesSkeleton\Web\PdfBody;
use DrdPlus\RulesSkeleton\Web\Main\MainBody;
use DrdPlus\RulesSkeleton\Web\Tables\TablesBody;
use Granam\Strict\Object\StrictObject;

class WebPartsContainer extends StrictObject
{
    private \DrdPlus\RulesSkeleton\Configurations\Configuration $configuration;
    private \DrdPlus\RulesSkeleton\UsagePolicy $usagePolicy;
    private \DrdPlus\RulesSkeleton\Configurations\Dirs $dirs;
    private \DrdPlus\RulesSkeleton\HtmlHelper $htmlHelper;
    private \DrdPlus\RulesSkeleton\Request $request;
    private \DrdPlus\RulesSkeleton\Web\Tools\WebFiles $webFiles;
    private ?\DrdPlus\RulesSkeleton\Web\Gateway\GatewayBody $gatewayBody = null;
    private ?\DrdPlus\RulesSkeleton\Web\DebugContacts\DebugContactsBody $debugContactsBody = null;
    private ?\DrdPlus\RulesSkeleton\Web\PdfBody $pdfBody = null;
    private ?\DrdPlus\RulesSkeleton\Web\Main\MainBody $rulesMainBody = null;
    private ?\DrdPlus\RulesSkeleton\Web\Tools\RulesMainBodyPreProcessor $rulesMainBodyPreProcessor = null;
    private ?\DrdPlus\RulesSkeleton\Web\Tools\TablesBodyPostProcessor $tablesBodyPostProcessor = null;
    private ?\DrdPlus\RulesSkeleton\Web\NotFound\NotFoundBody $notFoundBody = null;
    private ?\DrdPlus\RulesSkeleton\Web\Tables\TablesBody $tablesBody = null;

    public function __construct(
        Configuration $configuration,
        UsagePolicy $usagePolicy,
        WebFiles $webFiles,
        Dirs $dirs,
        HtmlHelper $htmlHelper,
        Request $request
    )
    {
        $this->configuration = $configuration;
        $this->usagePolicy = $usagePolicy;
        $this->dirs = $dirs;
        $this->htmlHelper = $htmlHelper;
        $this->request = $request;
        $this->webFiles = $webFiles;
    }

    public function getGatewayBody(): GatewayBody
    {
        if ($this->gatewayBody === null) {
            $this->gatewayBody = new GatewayBody($this->configuration, $this->usagePolicy, $this->request);
        }
        return $this->gatewayBody;
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
            $this->pdfBody = new PdfBody($this->dirs);
        }
        return $this->pdfBody;
    }

    public function getTablesBody(): TablesBody
    {
        if ($this->tablesBody === null) {
            $this->tablesBody = new TablesBody($this->getRulesMainBody(), $this->getTablesBodyPostProcessor());
        }
        return $this->tablesBody;
    }

    public function getRulesMainBody(): MainBody
    {
        if ($this->rulesMainBody === null) {
            $this->rulesMainBody = new MainBody(
                $this->webFiles,
                $this,
                $this->getRulesMainBodyPreProcessor(),
                $this->getRulesMainBodyPostProcessor()
            );
        }
        return $this->rulesMainBody;
    }

    protected function getRulesMainBodyPreProcessor(): RulesMainBodyPreProcessor
    {
        if (!$this->rulesMainBodyPreProcessor) {
            $this->rulesMainBodyPreProcessor = new RulesMainBodyPreProcessor(
                $this->htmlHelper,
                $this->request
            );
        }
        return $this->rulesMainBodyPreProcessor;
    }

    protected function getRulesMainBodyPostProcessor()
    {
        return null;
    }

    protected function getTablesBodyPostProcessor(): TablesBodyPostProcessor
    {
        if (!$this->tablesBodyPostProcessor) {
            $this->tablesBodyPostProcessor = new TablesBodyPostProcessor($this->request, $this->htmlHelper);
        }
        return $this->tablesBodyPostProcessor;
    }

    public function getNotFoundBody(): NotFoundBody
    {
        if ($this->notFoundBody === null) {
            $this->notFoundBody = new NotFoundBody($this->request, $this->getDebugContactsBody());
        }
        return $this->notFoundBody;
    }

}
