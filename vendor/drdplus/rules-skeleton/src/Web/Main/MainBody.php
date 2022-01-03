<?php declare(strict_types=1);

namespace DrdPlus\RulesSkeleton\Web\Main;

use DrdPlus\RulesSkeleton\Web\RulesBodyInterface;
use DrdPlus\RulesSkeleton\Web\Tools\HtmlDocumentProcessorInterface;
use DrdPlus\RulesSkeleton\Web\Tools\WebPartsContainer;
use Granam\WebContentBuilder\HtmlDocument;
use Granam\WebContentBuilder\Web\Body;
use Granam\WebContentBuilder\Web\WebFiles;

class MainBody extends Body implements RulesBodyInterface
{
    private WebPartsContainer $webPartsContainer;
    private ?HtmlDocumentProcessorInterface $rulesMainBodyPreProcessor;
    private ?HtmlDocumentProcessorInterface $rulesMainBodyPostProcessor;

    public function __construct(
        WebFiles $webFiles,
        WebPartsContainer $webPartsContainer,
        ?HtmlDocumentProcessorInterface $rulesMainBodyPreProcessor,
        ?HtmlDocumentProcessorInterface $htmlDocumentPostProcessor
    )
    {
        parent::__construct($webFiles);
        $this->webPartsContainer = $webPartsContainer;
        $this->rulesMainBodyPreProcessor = $rulesMainBodyPreProcessor;
        $this->rulesMainBodyPostProcessor = $htmlDocumentPostProcessor;
    }

    protected function fetchPhpFileContent(string $file): string
    {
        $content = new class($file, $this->webPartsContainer)
        {
            private string $file;
            private WebPartsContainer $webPartsContainer;

            public function __construct(string $file, WebPartsContainer $webPartsContainer)
            {
                $this->file = $file;
                $this->webPartsContainer = $webPartsContainer;
            }

            public function fetchContent(): string
            {
                $tmp = ['webPartsContainer' => $this->webPartsContainer];
                extract($tmp, \EXTR_SKIP);
                ob_start();
                include $this->file;
                return ob_get_clean();
            }
        };

        return $content->fetchContent();
    }

    public function preProcessDocument(HtmlDocument $htmlDocument): HtmlDocument
    {
        if (!$this->rulesMainBodyPreProcessor) {
            return $htmlDocument;
        }
        return $this->rulesMainBodyPreProcessor->processDocument($htmlDocument);
    }

    public function postProcessDocument(HtmlDocument $htmlDocument): HtmlDocument
    {
        if (!$this->rulesMainBodyPostProcessor) {
            return $htmlDocument;
        }
        return $this->rulesMainBodyPostProcessor->processDocument($htmlDocument);
    }
}
