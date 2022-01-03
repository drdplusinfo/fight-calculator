<?php declare(strict_types=1);

namespace DrdPlus\AttackSkeleton\Web\AddCustomArmament;

use DrdPlus\AttackSkeleton\HtmlHelper;

trait CancelActionButtonTrait
{
    private function getCancelActionButton(HtmlHelper $frontendHelper): string
    {
        return <<<HTML
<div class="col-sm-1">
  <a class="button cancel" href="{$frontendHelper->getLocalUrlToCancelAction()}">Zrušit</a>
</div>
HTML;
    }
}