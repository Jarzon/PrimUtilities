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
    }

    /**
     * @expectedException     \Exception
     * @expectedExceptionMessage test is required
     */
    public function testLengthNull()
    {
        $forms = new Forms(['test' => '']);

        $forms->text('', 'test', '', '', 10, 4, ['required' => 'required']);

        $forms->verification();
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

    /**
     * @expectedException     \Exception
     * @expectedExceptionMessage error
     */
    public function testRadioValueException()
    {
        $forms = new Forms(['test' => '123456789ab']);

        $forms->radio('test', 'test', ['test' => 'test'], 'test');

        $forms->verification();
    }

    public function testCheckboxChecked()
    {
        $forms = new Forms(['test' => '']);

        $forms->checkbox('test', 'test', 'test');

        $params = $forms->verification();

        $this->assertEquals(true, $params[0]);
    }

    public function testCheckboxUnchecked()
    {
        $forms = new Forms([]);

        $forms->checkbox('test', 'test', 'test');

        $params = $forms->verification();

        $this->assertEquals(false, $params[0]);
    }

    public function testRadioValue()
    {
        $forms = new Forms(['test' => 'test']);

        $forms->radio('test', 'test', ['test' => 'test'], 'test');

        $values = $forms->verification();

        $this->assertEquals('test', $values[0]);
    }

    public function testFileValue()
    {
        $_FILES['test'] = [
            'name' => UPLOAD_ERR_OK,
            'type' => UPLOAD_ERR_OK,
            'tmp_name' => UPLOAD_ERR_OK,
            'size' => UPLOAD_ERR_OK,
            'error' => UPLOAD_ERR_OK,
        ];

        $forms = new Forms([]);

        $forms->file('', 'test', '', false, ['.jpg', '.jpeg']);

        $values = $forms->verification();

        $this->assertEquals('0', $values[0]['location']);
    }

    public function testGetFormsText()
    {
        $forms = new Forms(['test' => 'a']);

        $forms->text('', 'test', '', '', 10, 4);

        $content = $forms->getForms();

        $this->assertEquals('<input minlength="4" maxlength="10" type="text" name="test">', $content[0]['html']);
    }

    public function testGetFormsTextarea()
    {
        $forms = new Forms(['test' => 'a']);

        $forms->textarea('', 'test', '', '', 500, 0);

        $content = $forms->getForms();

        $this->assertEquals('<textarea minlength="0" maxlength="500" name="test"></textarea>', $content[0]['html']);
    }

    public function testGetFormsNumber()
    {
        $forms = new Forms(['test' => 'a']);

        $forms->number('', 'test', '', '', 10, 4);

        $content = $forms->getForms();

        $this->assertEquals('<input min="4" max="10" step="1" type="number" name="test">', $content[0]['html']);
    }

    public function testGetFormsFloat()
    {
        $forms = new Forms(['test' => 'a']);

        $forms->float('', 'test', '', '', 10, 4);

        $content = $forms->getForms();

        $this->assertEquals('<input min="4" max="10" step="0.01" type="number" name="test">', $content[0]['html']);
    }

    public function testGetFormsSelect()
    {
        $forms = new Forms(['test' => 'a']);

        $forms->select('', 'test', '', ['test' => 'test'], 'test');

        $content = $forms->getForms();

        $this->assertEquals('<select name="test"><option value="test" selected="selected">test</option></select>', $content[0]['html']);
    }

    public function testGetFormsRadio()
    {
        $forms = new Forms(['test' => 'a']);

        $forms->radio('test', 'test', ['test' => 'test'], 'test');

        $content = $forms->getForms();

        $this->assertEquals('<input type="radio" name="test" value="test" checked="checked">', $content[0]['html'][0]['input']);
    }

    public function testGetFormsCheckbox()
    {
        $forms = new Forms(['test' => 'a']);

        $forms->checkbox('test', '', 'test', true);

        $content = $forms->getForms();

        $this->assertEquals('<input checked="checked" type="checkbox" value="test" name="test">', $content[0]['html']);
    }

    public function testUpdateValue()
    {
        $forms = new Forms(['test' => 'a']);

        $forms->text('', 'test', '', 'wrong', 10, 4);

        $content = $forms->getForms();

        $this->assertEquals('<input minlength="4" maxlength="10" type="text" value="wrong" name="test">', $content[0]['html']);

        $forms->updateValue('test', 'good');

        $content = $forms->getForms();

        $this->assertEquals('<input minlength="4" maxlength="10" type="text" value="good" name="test">', $content[0]['html']);
    }

    public function testUpdateValuesSelect()
    {
        $forms = new Forms(['test' => 'a']);

        $forms->select('', 'fruits', '', ['apples' => 'apples', 'oranges' => 'oranges']);

        $content = $forms->getForms();

        $this->assertEquals('<select name="fruits"><option value="apples">apples</option><option value="oranges">oranges</option></select>', $content[0]['html']);

        $forms->updateValues(['fruits' => 'oranges']);

        $content = $forms->getForms();

        $this->assertEquals('<select name="fruits"><option value="apples">apples</option><option value="oranges" selected="selected">oranges</option></select>', $content[0]['html']);

    }

    public function testGetFormsFile()
    {
        $forms = new Forms([]);

        $forms->file('', 'test', '', true, ['.jpg', '.jpeg']);

        $content = $forms->getForms();

        $this->assertEquals('<input multiple="multiple" accept=".jpg, .jpeg" type="file" name="test">', $content[0]['html']);
    }
}