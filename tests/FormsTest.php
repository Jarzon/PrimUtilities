<?php
declare(strict_types=1);

namespace Tests;

use PHPUnit\Framework\TestCase;
use Tests\Mock\View;

class FormsTest extends TestCase
{
    public function testLengthLowerThatMin()
    {
        $viewMock = new View('');
        $forms = new \PrimUtilities\Forms($viewMock);

        $forms->text('', 'test', '', '', 10, 4);

        $post = [
            'test' => 'a'
        ];

        try {
            $forms->verification($post);
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
        ob_start();
        $forms->generateForms();
        $content = ob_get_contents();
        ob_end_clean();

        // TODO: huh shouldn't echo the content it's inflexible, hard to test and spaces..
        $this->assertEquals('                                    <label>Translated test                                    <input
                        type="text"
                        name="test"
                        value=""
                        class=""                                                                                                                                                
                                                    minlength="4"                            maxlength="10"                                                                            min="4"                            max="10"                        
                    >
                                </label>                ', $content);
    }
}