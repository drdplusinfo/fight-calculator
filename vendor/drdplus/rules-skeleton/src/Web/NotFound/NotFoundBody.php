<?php declare(strict_types=1);

namespace DrdPlus\RulesSkeleton\Web\NotFound;

use DrdPlus\RulesSkeleton\Request;
use DrdPlus\RulesSkeleton\Web\DebugContacts\DebugContactsBody;
use DrdPlus\RulesSkeleton\Web\RulesBodyInterface;
use Granam\Strict\Object\StrictObject;
use Granam\WebContentBuilder\HtmlDocument;

class NotFoundBody extends StrictObject implements RulesBodyInterface
{
    private \DrdPlus\RulesSkeleton\Request $request;
    private \DrdPlus\RulesSkeleton\Web\DebugContacts\DebugContactsBody $debugContactsBody;

    public function __construct(Request $request, DebugContactsBody $debugContactsBody)
    {
        $this->request = $request;
        $this->debugContactsBody = $debugContactsBody;
    }

    public function __toString()
    {
        return $this->getValue();
    }

    public function getValue(): string
    {
        return <<<HTML
{$this->getNotFoundString()}
HTML;
    }

    private function getNotFoundString(): string
    {
        /** @noinspection PhpUnusedLocalVariableInspection */
        $request = $this->request;
        /** @noinspection PhpUnusedLocalVariableInspection */
        $debugContactsBody = $this->debugContactsBody;
        ob_start();
        include __DIR__ . '/content/not-found.php';
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
