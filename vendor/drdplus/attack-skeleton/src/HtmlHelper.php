<?php declare(strict_types=1);

namespace DrdPlus\AttackSkeleton;

use DrdPlus\RulesSkeleton\Configurations\Dirs;
use DrdPlus\RulesSkeleton\Environment;
use Granam\Integer\IntegerInterface;
use Granam\Integer\Tools\ToInteger;

/**
 * @method static HtmlHelper createFromGlobals(Dirs $dirs, Environment $environment)
 */
class HtmlHelper extends \DrdPlus\RulesSkeleton\HtmlHelper
{
    public const CLASS_INCREASED = 'increased';
    public const CLASS_DECREASED = 'decreased';

    /**
     * @param int|IntegerInterface $previous
     * @param int|IntegerInterface $current
     * @return string
     */
    public function getCssClassForChangedValue($previous, $current): string
    {
        if (ToInteger::toInteger($previous) < ToInteger::toInteger($current)) {
            return self::CLASS_INCREASED;
        }
        if (ToInteger::toInteger($previous) > ToInteger::toInteger($current)) {
            return self::CLASS_DECREASED;
        }

        return '';
    }

    public function getChecked($current, $expected): string
    {
        return (string)$current === (string)$expected
            ? 'checked'
            : '';
    }

    public function getSelected($current, $expected): string
    {
        return (string)$current === (string)$expected
            ? 'selected'
            : '';
    }

    public function getDisabled(bool $canUseIt): string
    {
        return $canUseIt
            ? ''
            : 'disabled';
    }

    /**
     * @param array $additionalParameters
     * @param array|null $getParameters
     * @return string
     */
    public function getLocalUrlWithQuery(array $additionalParameters = [], array $getParameters = null): string
    {
        $parameters = $getParameters ?? $_GET;
        if ($additionalParameters !== []) {
            foreach ($additionalParameters as $name => $value) {
                $parameters[$name] = $value;
            }
        }
        $query = '';
        if ($parameters !== []) {
            $query = '?' . http_build_query($parameters);
        }

        return $query;
    }

    /**
     * @param int|IntegerInterface $integer
     * @return string
     */
    public function formatInteger($integer): string
    {
        $integer = ToInteger::toInteger($integer);
        return $integer >= 0
            ? ('+' . $integer)
            : (string)$integer;
    }

    public function getLocalUrlToAction(string $action): string
    {
        return $this->getLocalUrlWithQuery([AttackRequest::ACTION => $action]);
    }

    public function getLocalUrlToCancelAction(): string
    {
        return $this->getLocalUrlToAction('');
    }
}
