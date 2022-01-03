<?php declare(strict_types=1);

namespace Tests\DrdPlus\RulesSkeleton\Web\Tools;

use DrdPlus\RulesSkeleton\HtmlHelper;
use DrdPlus\RulesSkeleton\Web\Tools\RulesMainBodyPreProcessor;
use Tests\DrdPlus\RulesSkeleton\Partials\AbstractContentTest;
use Granam\WebContentBuilder\HtmlDocument;

class RulesMainBodyPreProcessorTest extends AbstractContentTest
{
    /**
     * @test
     */
    public function I_can_be_lazy_and_let_fill_table_of_content_anchors_automatically()
    {
        $htmlDocument = new HtmlDocument($this->getContentWithoutAnchorsInTableOfContents());
        $rulesMainBodyPreProcessor = new RulesMainBodyPreProcessor($this->getHtmlHelper(), $this->getRequest());
        $rulesMainBodyPreProcessor->processDocument($htmlDocument);
        self::assertSame(
            $this->getExpectedContentWithAnchorsInTableOfContents(),
            html_entity_decode($htmlDocument->saveHTML())
        );
    }

    protected function getContentWithoutAnchorsInTableOfContents(): string
    {
        return $this->fetchFile(__DIR__ . '/html/contentWithoutAnchorsInTableOfContents.php');
    }

    protected function fetchFile(string $phpFile): string
    {
        ob_start();
        include $phpFile;
        return ob_get_clean();
    }

    protected function getExpectedContentWithAnchorsInTableOfContents(): string
    {
        return $this->fetchFile(__DIR__ . '/html/expectedContentWithAutomaticAnchorsInTableOfContents.php');
    }

    /**
     * @test
     * @dataProvider provideRequestPath
     * @param string $path
     * @param string $expectedClassFromPath
     */
    public function I_can_target_body_by_html_class_created_from_request_path(string $path, string $expectedClassFromPath)
    {
        $htmlDocument = new HtmlDocument(<<<HTML
<!DOCTYPE html>
<html lang="en">
  <body>Some handsome</body>
</html>
HTML
        );
        $rulesMainBodyPreProcessor = new RulesMainBodyPreProcessor($this->getHtmlHelper(), $this->createRequest([], $path));
        $rulesMainBodyPreProcessor->processDocument($htmlDocument);
        self::assertTrue($htmlDocument->body->classList->contains($expectedClassFromPath));
    }

    public function provideRequestPath(): array
    {
        return [
            'root' => ['/', HtmlHelper::CLASS_ROOT_PATH_ROUTE],
            'nothing' => ['', HtmlHelper::CLASS_ROOT_PATH_ROUTE],
            'some route' => ['route far far away', HtmlHelper::CLASS_ROOTED_FROM_PATH_PREFIX. '_route_far_far_away'],
        ];
    }
}
