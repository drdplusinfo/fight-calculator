<?php declare(strict_types=1);

namespace DrdPlus\RulesSkeleton\Web\Gateway;

use DrdPlus\RulesSkeleton\Configurations\Configuration;
use DrdPlus\RulesSkeleton\HtmlHelper;
use DrdPlus\RulesSkeleton\Request;
use DrdPlus\RulesSkeleton\UsagePolicy;
use DrdPlus\RulesSkeleton\Web\RulesBodyInterface;
use Granam\Strict\Object\StrictObject;
use Granam\WebContentBuilder\HtmlDocument;

class GatewayBody extends StrictObject implements RulesBodyInterface
{
    private \DrdPlus\RulesSkeleton\Configurations\Configuration $configuration;
    private \DrdPlus\RulesSkeleton\UsagePolicy $usagePolicy;
    private \DrdPlus\RulesSkeleton\Request $request;

    public function __construct(Configuration $configuration, UsagePolicy $usagePolicy, Request $request)
    {
        $this->configuration = $configuration;
        $this->usagePolicy = $usagePolicy;
        $this->request = $request;
    }

    public function __toString()
    {
        return $this->getValue();
    }

    public function getValue(): string
    {
        $backgroundImageClass = HtmlHelper::CLASS_BACKGROUND_IMAGE;
        return <<<HTML
<div class="main gateway">
  <div class="{$backgroundImageClass}"></div>
  {$this->getTrialTimeExpiredString()}
  {$this->getLicenceManifestString()}
  {$this->getFooterString()}
</div>
HTML;
    }

    private function getTrialTimeExpiredString(): string
    {
        if (!$this->usagePolicy->trialJustExpired()) {
            return '';
        }

        return '<div class="message warning">⌛ Čas tvého testování se naplnil ⌛</div>';
    }

    private function getLicenceManifestString(): string
    {
        /** @noinspection PhpUnusedLocalVariableInspection */
        $configuration = $this->configuration;
        /** @noinspection PhpUnusedLocalVariableInspection */
        $request = $this->request;
        ob_start();
        include __DIR__ . '/content/gateway.php';
        return ob_get_clean();
    }

    private function getFooterString(): string
    {
        ob_start();
        include __DIR__ . '/content/gateway-footer.html';
        return ob_get_clean();
    }

    public function preProcessDocument(HtmlDocument $htmlDocument): HtmlDocument
    {
        return $htmlDocument;
    }

    public function postProcessDocument(HtmlDocument $htmlDocument): HtmlDocument
    {
        return $htmlDocument;
    }
}
