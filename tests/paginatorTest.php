<?php

class PaginatorTest extends PHPUnit_Framework_TestCase
{
    public function testGetNumberPages()
    {
        $pagination = new \PrimUtilities\Paginator(1, 100, 10, 3, false);

        $this->assertEquals(10, $pagination->getNumberPages());

        return $pagination;
    }

    /**
     * @depends testGetNumberPages
     */
    public function testGetPage($pagination)
    {
        $this->assertEquals(1, $pagination->getPage());
    }

    /**
     * @depends testGetNumberPages
     */
    public function testGetFirstPageElement($pagination)
    {
        $this->assertEquals(0, $pagination->getFirstPageElement());
    }

    /**
     * @depends testGetNumberPages
     */
    public function testGetLast($pagination)
    {
        $this->assertEquals(10, $pagination->getLast());
    }

    public function testReverse()
    {
        $pagination = new \PrimUtilities\Paginator(2, 100, 10, 3, true);

        return $pagination;
    }

    /**
     * @depends testReverse
     */
    public function testReverseGetFirstPageElement($pagination)
    {
        $this->assertEquals(80, $pagination->getFirstPageElement());
    }

    /**
     * @depends testReverse
     */
    public function testReverseGetLast($pagination)
    {
        $this->assertEquals(90, $pagination->getLast());
    }
}