<?php
declare(strict_types=1);

namespace Tests;

use PHPUnit\Framework\TestCase;
use PrimUtilities\Forms;
use Tests\Mock\View;

class FormsTest extends TestCase
{
    /**
     * @expectedException     \Exception
     * @expectedExceptionMessage test is too short
     */
    public function testLengthLowerThatMin()
    {
        if(!defined('ROOT')) define('ROOT', '');

        $viewMock = new View();
        $forms = new Forms($viewMock, ['test' => 'a']);

        $forms->text('', 'test', '', '', 10, 4);

        $forms->verification();

        return $forms;
    }

    /**
     * @expectedException     \Exception
     * @expectedExceptionMessage test is too long
     */
    public function testLengthHigherThatMax()
    {
        $viewMock = new View();
        $forms = new Forms($viewMock, ['test' => '123456789ab']);

        $forms->text('', 'test', '', '', 10, 4);

        $forms->verification();
    }

    public function testGenerateForms()
    {
        $viewMock = new View();
        $forms = new Forms($viewMock, ['test' => 'a']);

        $forms->text('', 'test', '', '', 10, 4);

        $content = $forms->generateForms();

        $this->assertEquals('<label>Translated test <input class="" minlength="4" maxlength="10" type="text" name="test" > </label>', $content);
    }
}