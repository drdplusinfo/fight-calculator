<?php declare(strict_types=1);

namespace DrdPlus\RulesSkeleton\Configurations;

use Granam\Strict\Object\StrictObject;

abstract class AbstractConfiguration extends StrictObject implements ConfigurationValues
{

    protected ?array $values = null;

    protected function __construct(array $values)
    {
        $this->setValues($values);
    }

    protected function setValues(array $values)
    {
        $this->values = $values;
    }

    public function getValues(): array
    {
        return $this->values;
    }

    protected function ensureConfigurationValue(string $valueKey, array $values, $defaultValue): array
    {
        if (!array_key_exists($valueKey, $values)) {
            $values[$valueKey] = $defaultValue;
        }
        return $values;
    }

    protected function guardConfigurationValueIsSet(string $valueKey, array $values, array $pathToConfiguration): void
    {
        if (($values[$valueKey] ?? null) === null) {
            throw new Exceptions\InvalidConfiguration(
                sprintf(
                    "Expected explicitly defined configuration '%s', got nothing",
                    $this->getConfigurationPath($valueKey, $pathToConfiguration)
                )
            );
        }
    }

    protected function guardConfigurationValueIsBoolean(string $valueKey, array $values, array $pathToConfiguration): void
    {
        if (!is_bool($values[$valueKey])) {
            throw new Exceptions\InvalidConfiguration(
                sprintf(
                    "Expected configuration '%s' to be a boolean, got %s",
                    $this->getConfigurationPath($valueKey, $pathToConfiguration),
                    var_export($values[$valueKey], true)
                )
            );
        }
    }

    protected function getConfigurationPath(string $configurationKey, array $pathToConfiguration): string
    {
        $configurationPath = $pathToConfiguration;
        $configurationPath[] = $configurationKey;
        return implode('.', $configurationPath);
    }

    protected function guardConfigurationValueIsNonEmptyString(string $valueKey, array $values, array $pathToConfiguration): void
    {
        if (!is_string($values[$valueKey]) || $values[$valueKey] === '') {
            throw new Exceptions\InvalidConfiguration(
                sprintf(
                    "Expected configuration '%s' to be a non-empty string, got %s",
                    $this->getConfigurationPath($valueKey, $pathToConfiguration),
                    var_export($values[$valueKey], true)
                )
            );
        }
    }

    protected function guardConfigurationValueIsValidRegexp(string $valueKey, array $values, array $pathToConfiguration): void
    {
        $value = $values[$valueKey];
        if (!preg_match('~^(.).*\1$~', $value)) {
            throw new Exceptions\InvalidConfiguration(
                sprintf(
                    "Expected configuration '%s' to be a valid regexp with leading and trailing delimiters, got %s",
                    $this->getConfigurationPath($valueKey, $pathToConfiguration),
                    var_export($values[$valueKey], true)
                )
            );
        }
    }

    protected function guardConfigurationValueIsObject(string $valueKey, array $values, array $pathToConfiguration): void
    {
        if (!is_array($values[$valueKey])) {
            throw new Exceptions\InvalidConfiguration(
                sprintf(
                    "Expected configuration '%s' to be a non-empty array, got %s",
                    $this->getConfigurationPath($valueKey, $pathToConfiguration),
                    var_export($values[$valueKey], true)
                )
            );
        }
        foreach ($values[$valueKey] as $itemKey => $itemValue) {
            if (!is_string($itemKey)) {
                throw new Exceptions\InvalidConfiguration(
                    sprintf(
                        "Expected configuration '%s' to be an array indexed only by strings, got key %s (with value '%s')",
                        $this->getConfigurationPath($valueKey, $pathToConfiguration),
                        var_export($itemKey, true),
                        $itemValue
                    )
                );
            }
        }
    }

    protected function diveConfigurationStructure(string $oldKey, string $subConfigurationKey, string $newKey, array $values): array
    {
        if (array_key_exists($oldKey, $values)) {
            $values = $this->ensureSubConfigurationSection($subConfigurationKey, $values);
            if (!array_key_exists(MenuConfiguration::POSITION_FIXED, $values[$subConfigurationKey])) {
                $values[$subConfigurationKey][$newKey] = $values[$oldKey];
            }
            unset($values[$oldKey]);
        }
        return $values;
    }

    protected function ensureSubConfigurationSection(string $sectionKey, array $values): array
    {
        if (!array_key_exists($sectionKey, $values)) {
            $values[$sectionKey] = [];
        }
        return $values;
    }
}
