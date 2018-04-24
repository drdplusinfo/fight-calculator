<?php
namespace DrdPlus\Tests\Calculators\Fight;

use DrdPlus\Calculators\Fight\Controller;
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

    /**
     * Has to run in separate process to be NOT affected by PHPUnit output, blocking headers sending
     *
     * @test
     * @runInSeparateProcess
     */
    public function I_can_show_form_to_add_new_ranged_weapon(): void
    {
        $this->I_can_show_it_with_a_request(['action' => Controller::ADD_NEW_RANGED_WEAPON]);
    }

    /**
     * @param array $getRequest
     */
    private function I_can_show_it_with_a_request(array $getRequest): void
    {
        $_SERVER['QUERY_STRING'] = '';
        $_GET = $getRequest;
        ob_start();
        require __DIR__ . '/../../../../index.php';
        $content = ob_get_clean();
        new HTMLDocument($content);
        self::assertRegExp('~^<!DOCTYPE html>\n.+</html>$~s', $content);
    }

    /**
     * Has to run in separate process to be NOT affected by PHPUnit output, blocking headers sending
     *
     * @test
     * @runInSeparateProcess
     */
    public function I_can_show_form_to_add_new_melee_weapon(): void
    {
        $this->I_can_show_it_with_a_request(['action' => Controller::ADD_NEW_MELEE_WEAPON]);
    }

    /**
     * Has to run in separate process to be NOT affected by PHPUnit output, blocking headers sending
     *
     * @test
     * @runInSeparateProcess
     */
    public function I_can_show_form_to_add_new_body_armor(): void
    {
        $this->I_can_show_it_with_a_request(['action' => Controller::ADD_NEW_BODY_ARMOR]);
    }

    /**
     * Has to run in separate process to be NOT affected by PHPUnit output, blocking headers sending
     *
     * @test
     * @runInSeparateProcess
     */
    public function I_can_show_form_to_add_new_helm(): void
    {
        $this->I_can_show_it_with_a_request(['action' => Controller::ADD_NEW_HELM]);
    }
}