<?php declare(strict_types=1);

namespace DrdPlus\Tests\AttackSkeleton\Web;

use DrdPlus\AttackSkeleton\Web\AbstractArmamentBody;
use DrdPlus\Tests\AttackSkeleton\AbstractAttackTest;
use Granam\WebContentBuilder\HtmlDocument;

abstract class AbstractArmamentBodyTest extends AbstractAttackTest
{
    /**
     * @test
     * @dataProvider provideArmamentBodyAndExpectedContent
     * @param AbstractArmamentBody $armamentBody
     * @param string $expectedContent
     */
    public function I_can_draw_it(AbstractArmamentBody $armamentBody, string $expectedContent): void
    {
        self::assertSame($this->unifyHtml($expectedContent), $this->unifyHtml($armamentBody->getValue()));
    }

    abstract public function provideArmamentBodyAndExpectedContent(): array;

    private function unifyHtml(string $content): string
    {
        $content = \trim($content);
        $document = new HtmlDocument(<<<HTML
<!DOCTYPE html>
<html lang="cs">
<head>
  <title></title>
  <meta charset="utf-8">
</head>
<body>
{$content}
</body>
HTML
        );
        $document->normalize();
        return $document->body->prop_get_innerHTML();
    }
}