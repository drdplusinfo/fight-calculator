Doctrine String enum
=====================

[![Build Status](https://travis-ci.org/jaroslavtyc/doctrineum-string.svg?branch=master)](https://travis-ci.org/jaroslavtyc/doctrineum-string)
[![Test Coverage](https://codeclimate.com/github/jaroslavtyc/doctrineum-string/badges/coverage.svg)](https://codeclimate.com/github/jaroslavtyc/doctrineum-string/coverage)
[![License](https://poser.pugx.org/doctrineum/string/license)](https://packagist.org/packages/doctrineum/string)

## About
Adds [Enum](http://en.wikipedia.org/wiki/Enumerated_type) to [Doctrine ORM](http://www.doctrine-project.org/)
(can be used as a `@Column(type="string_enum")`).

##Usage

```php
<?php

use Doctrine\ORM\Mapping as ORM;
use Doctrineum\String\StringEnum;

/**
 * @ORM\Enity()
 */
class Person
{
    /**
     * @var int
     * @ORM\Id() @ORM\GeneratedValue(strategy="AUTO") @ORM\Column(type="integer")
     */
    private $id;
   
    /**
     * @var StringEnum
     * @ORM\Column(type="string_enum")
     */
    private $name;
    
    public function __construct(StringEnum $name)
    {
        $this->name = $name;
    }

    /**
     * @return StringEnum
     */
    public function getName()
    {
        return $this->name;
    }
}

// ... entity Money using Currency
$trueHero = new Person(StringEnum::getEnum('Don Quixote de La Mancha'));

/** @var \Doctrine\ORM\EntityManager $entityManager */
$entityManager->persist($trueHero);
$entityManager->flush();
$entityManager->clear();

/** @var Currency[] $StarTracks */
$sirs = $entityManager->createQuery(
    "SELECT p FROM Person p WHERE p.name LIKE 'Don %'"
)->getResult();

var_dump($sirs[0]->getName()->getValue()); // 'Don Quixote de La Mancha';
```

##Installation

Add it to your list of Composer dependencies (or by manual edit your composer.json, the `require` section)

```sh
composer require jaroslavtyc/doctrineum-string
```

## Doctrine integration

Register new DBAL type:

```php
<?php

use Doctrineum\String\StringEnumType;

StringEnumType::registerSelf();
```

When using Symfony with Doctrine you can do the same as above by configuration:

```yaml
# app/config/config.yml

# Doctrine Configuration
doctrine:
    dbal:
        # ...
        mapping_types:
            string_enum: string_enum
        types:
            string_enum: Doctrineum\String\StringEnumType
```
