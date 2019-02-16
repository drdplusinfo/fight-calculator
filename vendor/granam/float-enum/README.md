# Enumeration with float values

## <span id="usage">Usage</span>
1. [Use enum](#use-enum)
2. [NULL is NULL, not Enum](#null-is-null-enum-can-not-hold-it)
3. [Installation](#installation)

## Use enum
```php
<?php
use \Granam\FloatEnum\FloatEnum;

$enum = FloatEnum::getEnum(123);
echo $enum->getValue(); // 123
var_dump($enum->getValue()); // (float) 123.0
var_dump($enum->is(123.0)); // true
var_dump($enum->is(123)); // false
var_dump($enum->is('123.0')); // false
var_dump($enum->is($enum)); // true
```

## NULL is NULL, enum can not hold it
You **can not** create FloatEnum with NULL value. Just use NULL directly for such value.

```php
<?php
try {
    \Granam\FloatEnum\FloatEnum::getEnum(null);
} catch(\Granam\FloatEnum\Exceptions\WrongValueForFloatEnum $wrongValueForFloatEnum) {
    echo $wrongValueForFloatEnum->getMessage(); // Expected float or object with __toString method on strict mode, got NULL
}
```

## Installation

```bash
composer.phar require granam/float-enum
```

or manually edit composer.json at your project and `"require":` block (extend existing)

```json
"require": {
    "granam/float-enum": "dev-master"
}
```