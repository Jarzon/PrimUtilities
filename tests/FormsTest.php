<?php
declare(strict_types=1);

namespace Tests;

use PHPUnit\Framework\TestCase;
use PrimUtilities\Forms;

class FormsTest extends TestCase
{
    /**
     * @expectedException     \Exception
     * @expectedExceptionMessage test is too short
     */
    public function testLengthLowerThatMin()
    {
        $forms = new Forms(['test' => 'a']);

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
        $forms = new Forms(['test' => '123456789ab']);

        $forms->text('', 'test', '', '', 10, 4);

        $forms->verification();
    }

    public function testGetFormsText()
    {
        $forms = new Forms(['test' => 'a']);

        $forms->text('', 'test', '', '', 10, 4);

        $content = $forms->getForms();

        $this->assertEquals('<input class="" minlength="4" maxlength="10" type="text" name="test">', $content[0]['html']);
    }

    public function testGetFormsNumber()
    {
        $forms = new Forms(['test' => 'a']);

        $forms->number('', 'test', '', '', 10, 4);

        $content = $forms->getForms();

        $this->assertEquals('<input class="" min="4" max="10" step="1" type="number" name="test">', $content[0]['html']);
    }

    public function testGetFormsSelect()
    {
        $forms = new Forms(['test' => 'a']);

        $forms->select('', 'test', '', ['test' => 'test'], 'test');

        $content = $forms->getForms();

        $this->assertEquals('<select name="test"><option value="test" selected>test</option></select>', $content[0]['html']);
    }
}