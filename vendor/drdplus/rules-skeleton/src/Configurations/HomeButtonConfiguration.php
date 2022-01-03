<?php declare(strict_types=1);

namespace DrdPlus\RulesSkeleton\Configurations;

class HomeButtonConfiguration extends AbstractShowOnConfiguration
{
    public const TARGET = 'target';
    public const IMAGE = 'image';

    public function __construct(array $values, array $pathToHomeButton)
    {
        parent::__construct($values, $pathToHomeButton);
        if ($this->isShown()) {
            $values = $this->ensureTarget($values, $pathToHomeButton);
            $values = $this->ensureImage($values, $pathToHomeButton);
            $this->replaceValues($values);
        }
    }

    protected function replaceValues(array $valuesToReplace)
    {
        $this->values = array_merge($this->values, $valuesToReplace);
    }

    protected function ensureTarget(array $values, array $pathToMenu): array
    {
        $values = $this->ensureConfigurationValue(static::TARGET, $values, '/');
        $this->guardConfigurationValueIsSet(static::TARGET, $values, $pathToMenu);
        $this->guardConfigurationValueIsNonEmptyString(static::TARGET, $values, $pathToMenu);
        return $values;
    }

    protected function ensureImage(array $values, array $pathToMenu): array
    {
        $values = $this->ensureConfigurationValue(
            static::IMAGE,
            $values,
            str_replace(
                __DIR__ . '/../../..',
                '',
                __DIR__ . '/../../../images/generic/skeleton/drdplus-dragon-menu-2x22.png'
            )
        );
        $this->guardConfigurationValueIsSet(static::IMAGE, $values, $pathToMenu);
        $this->guardConfigurationValueIsNonEmptyString(static::IMAGE, $values, $pathToMenu);
        return $values;
    }

    public function getTarget(): string
    {
        return $this->getValues()[self::TARGET] ?? '';
    }

    public function getImage(): string
    {
        return $this->getValues()[self::IMAGE] ?? '';
    }
}
