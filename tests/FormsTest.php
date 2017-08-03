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

    public function testGetForms()
    {
        $forms = new Forms(['test' => 'a']);

        $forms->text('', 'test', '', '', 10, 4);

        $content = $forms->getForms();

        $this->assertEquals('<input class="" minlength="4" maxlength="10" type="text" name="test">', $content[0]['html']);
    }
}