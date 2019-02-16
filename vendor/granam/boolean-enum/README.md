# Enumeration with boolean values

## <span id="usage">Usage</span>
1. [Use enum](#use-enum)
2. [NULL is NULL, not Enum](#null-is-null-enum-can-not-hold-it)
3. [Installation](#installation)

## Use enum
```php
<?php
use \Granam\BooleanEnum\BooleanEnum;

$enum = BooleanEnum::getEnum(true);
echo $enum->getValue(); // 1
var_dump($enum->getValue()); // (bool) true
var_dump($enum->is(true)); // true
var_dump($enum->is(false)); // false
var_dump($enum->is(1)); // false
var_dump($enum->is($enum)); // true
```

## NULL is NULL, enum can not hold it
You **can not** create BooleanEnum with NULL value. Just use NULL directly for such value.

```php
<?php
try {
    \Granam\BooleanEnum\BooleanEnum::getEnum(null);
} catch(\Granam\BooleanEnum\Exceptions\UnexpectedValueToConvert $unexpectedValueToConvert) {
    echo $unexpectedValueToConvert->getMessage(); // Expected boolean or object with __toString method on strict mode, got NULL
}
```

## Installation

```bash
composer.phar require granam/boolean-enum
```

or manually edit composer.json at your project and `"require":` block (extend existing)

```json
"require": {
    "granam/boolean-enum": "dev-master"
}
```