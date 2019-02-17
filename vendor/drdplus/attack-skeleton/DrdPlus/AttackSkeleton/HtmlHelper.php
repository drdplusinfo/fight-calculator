<?php
declare(strict_types=1);

namespace DrdPlus\AttackSkeleton;

use Granam\Integer\IntegerInterface;
use Granam\Integer\Tools\ToInteger;

/**
 * @method static HtmlHelper createFromGlobals(\DrdPlus\RulesSkeleton\Dirs $dirs)
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
        return !$canUseIt
            ? 'disabled'
            : '';
    }

    /**
     * @param array $additionalParameters
     * @return string
     */
    public function getLocalUrlWithQuery(array $additionalParameters = []): string
    {
        /** @var array $parameters */
        $parameters = $_GET;
        if ($additionalParameters) {
            foreach ($additionalParameters as $name => $value) {
                $parameters[$name] = $value;
            }
        }
        $queryParts = [];
        foreach ($parameters as $name => $value) {
            if (\is_array($value)) {
                /** @var array $value */
                foreach ($value as $index => $item) {
                    $queryParts[] = \urlencode("{$name}[{$index}]") . '=' . \urlencode((string)$item);
                }
            } else {
                $queryParts[] = \urlencode((string)$name) . '=' . \urlencode((string)$value);
            }
        }
        $query = '';
        if ($queryParts) {
            $query = '?' . \implode('&', $queryParts);
        }

        return $query;
    }

    public function formatInteger(int $integer): string
    {
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