<?php
declare(strict_types=1);

namespace Tests;

use PHPUnit\Framework\TestCase;
use Tests\Mock\View;

class LocalizationTest extends TestCase
{
    public function testGetLanguage()
    {
        if(!defined('ROOT')) define('ROOT', '');

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

    /**
     * @depends testGetLanguage
     */
    public function testSetLanguage($view)
    {
        $view->setLanguage('fr');
        $this->assertEquals('Test traduit', $view->translate('test'));
    }
}