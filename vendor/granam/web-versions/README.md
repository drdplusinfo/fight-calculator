# Git versions

Reads minor versions, like ```1.0, 1.1, 2.0``` and patch versions, like ```1.0.0, 1.0.1, 1.1.0, 2.0.0, 2.0.1``` from given Git repository.

Originally created for a cache invalidation using a Git version as a cache key (that is where the *Web versions* name came from).

## Caveats

- Minor versions are read only from Git branches, filtered for those named version-like (v1.0 or 1.0)
- Patch versions are read only from Git tags, filtered for those named version-like

# Usage

```php
<?php
require_once __DIR__ . '/vendor/autoload.php';

$git = new \Granam\Git\Git();
$webVersions = new \Granam\WebVersions\WebVersions($git, __DIR__, 'dev-branch-name');

print_r($webVersions->getAllMinorVersions()); // Array ( [0] => dev-branch-name [1] => 1.0 )

print_r($webVersions->getAllStableMinorVersions()); // Array ( [0] => 1.0 )

print_r($webVersions->getAllPatchVersions()); // Array ( [0] => dev-branch-name [1] => 1.0.0 )

print_r($webVersions->getAllStablePatchVersions()); // Array ( [0] => 1.0.0 ) 
```

# Install

Easiest via [Composer](https://getcomposer.org/)

```shell script
composer require granam/web-versions
```