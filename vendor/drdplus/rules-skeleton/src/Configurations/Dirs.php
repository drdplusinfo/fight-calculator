<?php declare(strict_types=1);

namespace DrdPlus\RulesSkeleton\Configurations;

class Dirs extends \Granam\WebContentBuilder\Dirs
{
    private string $cacheRoot;
    private string $pdfRoot;

    public function __construct(string $projectRoot)
    {
        parent::__construct($projectRoot);
        $this->cacheRoot = $projectRoot . '/cache/' . \PHP_SAPI;
        $this->pdfRoot = $projectRoot . '/pdf';
    }

    public function getCacheRoot(): string
    {
        return $this->cacheRoot;
    }

    public function getPdfRoot(): string
    {
        return $this->pdfRoot;
    }
}
