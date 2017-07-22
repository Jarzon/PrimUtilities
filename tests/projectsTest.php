<?php

Class ViewMock {

}

class ProjectsTest extends PHPUnit_Framework_TestCase
{
    public function testLengthLowerThatMin()
    {
        $viewMock = new ViewMock();
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
    }

    public function testLengthHigherThatMax()
    {
        $viewMock = new ViewMock();
        $forms = new \PrimUtilities\Forms($viewMock);

        $forms->text('', 'test', '', '', 10, 4);

        $post = [
            'test' => '123456789ab'
        ];

        try {
            $forms->verification($post);
        } catch(\Exception $e) {
            $this->assertEquals('test is too long', $e->getMessage());
        }
    }

    public function testGenerateForms()
    {
        $viewMock = new ViewMock();
        $forms = new \PrimUtilities\Forms($viewMock);

        $forms->text('', 'test', '', '', 10, 4);

        ob_start();
        $forms->generateForms();
        $content = ob_get_contents();
        ob_end_clean();

        // TODO: huh shouldn't echo the content it's inflexible, hard to test and spaces..
        $this->assertEquals('                                    <label>test                                    <input
                        type="text"
                        name="test"
                        value=""
                        class=""                                                                                                                                                
                                                    minlength="4"                            maxlength="10"                                                                            min="4"                            max="10"                        
                    >
                                </label>                ', $content);
    }
}