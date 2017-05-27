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
}