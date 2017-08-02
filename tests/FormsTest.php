<?php
declare(strict_types=1);

namespace Tests;

use PHPUnit\Framework\TestCase;
use Tests\Mock\View;

class FormsTest extends TestCase
{
    public function testInitialize()
    {
        $viewMock = new View();

        try {
            $forms = new \PrimUtilities\Forms($viewMock);
        } catch(\Exception $e) {
            $this->assertEquals(false, $e->getMessage());
        }
        $this->assertEquals(true, true);


        return $forms;
    }

    /**
     * @depends testInitialize
     */
    public function testLengthLowerThatMin($forms)
    {
        $forms->text('', 'test', '', '', 10, 4);

        $post = [
            'test' => 'a'
        ];

        try {
            $forms->verification($post);

            $content = $forms->generateForms();

            $this->assertEquals('<label>Translated test <input type="text" name="test" value="" class="" minlength="4" maxlength="10" min="4" max="10" > </label>', $content);
        } catch(\Exception $e) {
            $this->assertEquals('test is too short', $e->getMessage());
        }

        return $forms;
    }

    /**
     * @depends testLengthLowerThatMin
     */
    public function testLengthHigherThatMax($forms)
    {
        $post = [
            'test' => '123456789ab'
        ];

        try {
            $forms->verification($post);
        } catch(\Exception $e) {
            $this->assertEquals('test is too long', $e->getMessage());
        }
    }

    /**
     * @depends testLengthLowerThatMin
     */
    public function testGenerateForms($forms)
    {
        $content = $forms->generateForms();

        $this->assertEquals('<label>Translated test <input type="text" name="test" value="" class="" minlength="4" maxlength="10" min="4" max="10" > </label>', $content);
    }
}