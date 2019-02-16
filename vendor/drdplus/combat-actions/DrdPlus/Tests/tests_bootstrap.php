<?php
require_once __DIR__ . '/../../vendor/autoload.php';

\error_reporting(-1);
\ini_set('display_errors', '1');
\ini_set('xdebug.max_nesting_level', '100');

if (!\ini_get('zend.assertions')) {
    \trigger_error("Assert() wil not be evaluated. Please set in on in php.ini for testing\n", \E_USER_WARNING);
}
\ini_set('assert.exception', '1'); // throw an exception instead of an error