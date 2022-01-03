<?php declare(strict_types=1);

namespace Tests\DrdPlus\RulesSkeleton;

use Tests\DrdPlus\RulesSkeleton\Partials\AbstractContentTest;

class SourceCodeLinksTest extends AbstractContentTest
{
    /**
     * @test
     */
    public function I_can_follow_linked_source_code(): void
    {
        $sourceUrls = $this->getSourceUrls();
        if (\count($sourceUrls) === 0) {
            self::assertFalse(false, 'Nothing to test here');

            return;
        }
        foreach ($sourceUrls as $sourceUrl) {
            $rawSourceCodeUrl = $this->toRawSourceCodeUrl($sourceUrl);
            $response = $this->fetchContentFromUrl($rawSourceCodeUrl, true);
            $responseHttpCode = $response['responseHttpCode'];
            self::assertTrue(
                $responseHttpCode >= 200 && $responseHttpCode < 300,
                "Ugly Response code from $rawSourceCodeUrl"
            );
            self::assertNotEmpty($response, 'Nothing has been fetched from ' . $rawSourceCodeUrl);
            self::assertMatchesRegularExpression(
                '~^<[?]php\s~',
                $response['content'] ?? '',
                'Expected valid PHP file as a source code'
            );
        }
    }

    /**
     * @return array|string[]
     */
    private function getSourceUrls(): array
    {
        $sourceUrls = [];
        foreach ($this->parseSourceUrls($this->getRulesContentForDev()) as $sourceUrl) {
            $sourceUrls[] = $sourceUrl;
        }

        return $sourceUrls;
    }

    /**
     * @param string $html
     * @return array|string[]
     */
    private function parseSourceUrls(string $html): array
    {
        preg_match_all('~data-source-code="(?<links>[^"]+)"~', $html, $matches);

        return $matches['links'];
    }

    /**
     * @param string $link for example https://github.com/jaroslavtyc/drdplus-professions/blob/master/DrdPlus/Professions/Priest.php
     * @return string for example https://raw.githubusercontent.com/jaroslavtyc/drdplus-professions/blob/master/DrdPlus/Professions/Priest.php
     */
    private function toRawSourceCodeUrl(string $link): string
    {
        return preg_replace(
            '~https://github[.]com/((.(?!blob/))+/)(blob/)?~',
            'https://raw.githubusercontent.com/$1',
            $link
        );
    }
}
