<?php
declare(strict_types=1);

namespace Tests;

use PHPUnit\Framework\TestCase;
use Tests\Mock\View;

class LocalizationTest extends TestCase
{
    public function testGetLanguage()
    {
        $view = new View();

        $view->setMessagesLanguage();

        $this->assertEquals('en', $view->getLanguage());

        return $view;
    }

    /**
     * @depends testGetLanguage
     */
    public function testTranslate($view)
    {
        $this->assertEquals('Translated test', $view->translate('test'));
    }
}