<?php declare(strict_types=1);

namespace DrdPlus\RulesSkeleton\Cache;

use DrdPlus\RulesSkeleton\Request;
use Granam\Strict\Object\StrictObject;

class RequestHashProvider extends StrictObject implements ContentRelatedContextHashProvider
{
    private \DrdPlus\RulesSkeleton\Request $request;
    private \DrdPlus\RulesSkeleton\Cache\ContentIrrelevantRequestAliases $contentIrrelevantRequestAliases;
    private \DrdPlus\RulesSkeleton\Cache\ContentIrrelevantParametersFilter $contentIrrelevantParametersFilter;

    public function __construct(
        Request $request,
        ContentIrrelevantRequestAliases $contentIrrelevantRequestAliases,
        ContentIrrelevantParametersFilter $contentIrrelevantParametersFilter
    )
    {
        $this->contentIrrelevantRequestAliases = $contentIrrelevantRequestAliases;
        $this->contentIrrelevantParametersFilter = $contentIrrelevantParametersFilter;
        $this->request = $request;
    }

    public function getContextHash(): string
    {
        $filteredGetParameters = $this->contentIrrelevantParametersFilter
            ->filterContentIrrelevantParameters($this->request->getValuesFromGet());
        $contentRelevantPath = $this->contentIrrelevantRequestAliases->getTruePath(
            $this->getUnifiedRequestPath(),
            $filteredGetParameters
        );
        $contentRelevantGetParameters = $this->contentIrrelevantRequestAliases->getTrueParameters(
            $this->getUnifiedRequestPath(),
            $filteredGetParameters
        );
        return \md5($contentRelevantPath . \serialize($contentRelevantGetParameters));
    }

    private function getUnifiedRequestPath(): string
    {
        return rtrim($this->request->getRequestPath(), '/');
    }

}
