<?php
namespace DrdPlus\Tests\Calculators\Fight;

use Gt\Dom\HTMLDocument;
use PHPUnit\Framework\TestCase;

class ItWorksTest extends TestCase
{
    protected function setUp()
    {
        $this->setBackupGlobals(true);
    }

    /**
     * Has to run in separate process to be NOT affected by PHPUnit output, blocking headers sending
     *
     * @test
     * @runInSeparateProcess
     */
    public function I_can_load_it_without_error(): void
    {
        $_SERVER['QUERY_STRING'] = '';
        ob_start();
        require __DIR__ . '/../../../../index.php';
        $content = ob_get_clean();
        new HTMLDocument($content);
        self::assertRegExp('~^<!DOCTYPE html>\n.+</html>$~s', $content);
    }
}