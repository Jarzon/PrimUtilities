<?php
declare(strict_types=1);

use PHPUnit\Framework\TestCase;

Class View extends \Prim\View {
    use \PrimUtilities\Localization;

    public function __construct($root)
    {

    }
}

class LocalizationTest extends TestCase
{
    public function testGetLanguage()
    {
        $view = new View('');

        $view->messages = [
            'languages' => ['en', 'fr'],
            'test' => ['testEN', 'testFR']
        ];

        $view->setMessagesLanguage();

        $this->assertEquals('en', $view->getLanguage());

        return $view;
    }

    /**
     * @depends testGetLanguage
     */
    public function testTranslate($view)
    {
        $this->assertEquals('testEN', $view->translate('test'));
    }
}