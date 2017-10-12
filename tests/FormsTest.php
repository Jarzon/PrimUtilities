<?php
declare(strict_types=1);

namespace Tests;

use PHPUnit\Framework\TestCase;
use Tests\Mock\Forms;

use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamDirectory;

class FormsTest extends TestCase
{
    /**
     * @var  vfsStreamDirectory
     */
    private $root;

    public function setUp()
    {
        $this->root = vfsStream::setup('root', null, [
            'temp' => [
                'test.txt' => '',
            ],
            'data' => [
            ],
        ]);
    }

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

    /**
     * @expectedException     \Exception
     * @expectedExceptionMessage form seems to miss enctype attribute
     */
    public function testFileFormMissingEnctype()
    {
        $_FILES = [];

        $forms = new Forms(['test' => '']);

        $forms->file('', 'test', '', '','', false, ['.jpg', '.jpeg']);

        $values = $forms->verification();

        $this->assertEquals('', $values['test']);
    }

    /**
     * @expectedException     \Exception
     * @expectedExceptionMessage test is required
     */
    public function testFileEmptyRequired()
    {
        $_FILES['test'] = [
            'name' => '',
            'type' => '',
            'tmp_name' => '',
            'size' => 4,
            'error' => UPLOAD_ERR_NO_FILE,
        ];

        $forms = new Forms([]);

        $forms->file('', 'test', '', '', '', false, ['.jpg', '.jpeg'], ['required' => 'required']);

        $forms->verification();
    }

    public function testFileEmpty()
    {
        $_FILES['test'] = [
            'name' => '',
            'type' => '',
            'tmp_name' => '',
            'size' => 4,
            'error' => UPLOAD_ERR_NO_FILE,
        ];

        $forms = new Forms([]);

        $forms->file('', 'test', '', '', '', false, ['.jpg', '.jpeg']);

        $values = $forms->verification();

        $this->assertEquals('', $values['test']);
    }

    public function testCheckboxChecked()
    {
        $forms = new Forms(['test' => '1234']);

        $forms->checkbox('', 'test', 'test', 'testy');

        $params = $forms->verification();

        $this->assertEquals('testy', $params['test']);
    }

    public function testCheckboxUnchecked()
    {
        $forms = new Forms([]);

        $forms->checkbox('', 'test', 'test', 'testy');

        $params = $forms->verification();

        $this->assertEquals(false, $params['test']);
    }

    public function testCheckboxCheckedBool()
    {
        $forms = new Forms(['test' => '1234']);

        $forms->checkbox('', 'test', 'test', true, false, []);

        $params = $forms->verification();

        $this->assertEquals(true, $params['test']);
    }

    public function testCheckboxUncheckedBool()
    {
        $forms = new Forms([]);

        $forms->checkbox('', 'test', 'test', true, false, []);

        $params = $forms->verification();

        $this->assertEquals(false, $params['test']);
    }

    public function testRadioValue()
    {
        $forms = new Forms(['test' => 'test']);

        $forms->radio('test', 'test', ['test' => 'test'], 'test');

        $values = $forms->verification();

        $this->assertEquals('test', $values['test']);
    }

    public function testFileValue()
    {
        $_FILES['test'] = [
            'name' => 'test.txt',
            'type' => 'text',
            'tmp_name' => vfsStream::url('root/temp/test.txt'),
            'size' => 4,
            'error' => UPLOAD_ERR_OK,
        ];

        $forms = new Forms(['test' => 'test.txt']);

        $forms->file('', 'test', vfsStream::url('root/data'), '', '', false, ['.txt', '.text']);

        $values = $forms->verification();

        $this->assertTrue(file_exists('vfs://root/data/da39a3ee5e6b4b0d3255bfef95601890afd80709'));
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

        $forms->checkbox('', 'test', '', 'test', true);

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

        $forms->file('', 'test', '', '', '', true, ['.jpg', '.jpeg']);

        $content = $forms->getForms();

        $this->assertEquals('<input multiple="multiple" accept=".jpg, .jpeg" type="file" name="test">', $content[0]['html']);
    }
}