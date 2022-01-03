# Web content builder

## Injecting asset versions

### CSS example

*Example with injecting image md5 sum linked in a CSS file.*

```php
$assetsVersion = new \Granam\WebContentBuilder\AssetsVersion(); // will scan for *.css, *.html and *.md by default,

/** body {
    background-image: url("/img/someone.jpeg");
} */
echo file_get_contents(__DIR__ . '/css/main.css');

$changedFiles = $assetsVersion->addVersionsToAssetLinks(
    __DIR__ /* as document root */,
    ['css' ]/* to document root relative dirs to scan */,
    []/* no dirs to exclude */,
    []/* no exact files to scan */,
    false /* no dry run to change files directly */
);
/** Array (
    [0] => '/home/jaroslav/projects/granam/web-content-builder/css/main.css'
) */
print_r($changedFiles);


/** body {
    background-image: url("/img/someone.jpeg?version=664a924915e642c7dc89af370114629a");
} */
echo file_get_contents(__DIR__ . '/css/main.css');
echo md5_file(__DIR__ . '/img/someone.jpeg'); // 664a924915e642c7dc89af370114629a
```